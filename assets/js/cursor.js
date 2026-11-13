/**
 * @file cursor.js
 * @description Ultra-fast 120fps hardware-accelerated cursor for Grand Furniture Vizag
 */

'use strict';

class LuxuryCursor {
  constructor() {
    this.dot = null;
    this.ring = null;
    this.label = null;

    this.mouseX = -100;
    this.mouseY = -100;
    this.ringX = -100;
    this.ringY = -100;

    // Responsive lerp factor for instant tactile feel
    this.LERP_FACTOR = 0.22;

    this.isHovering = false;
    this.isHidden = false;
    this.isExpanded = false;
    this.rafId = null;
  }

  _createElement() {
    if (document.querySelector('.cursor__dot')) return;

    this.dot = document.createElement('div');
    this.dot.className = 'cursor__dot';
    this.dot.setAttribute('aria-hidden', 'true');

    this.ring = document.createElement('div');
    this.ring.className = 'cursor__ring';
    this.ring.setAttribute('aria-hidden', 'true');

    this.label = document.createElement('span');
    this.label.className = 'cursor__label';
    this.ring.appendChild(this.label);

    document.body.appendChild(this.dot);
    document.body.appendChild(this.ring);
  }

  _bindEvents() {
    // Immediate dot positioning without waiting for RAF
    window.addEventListener('mousemove', (e) => {
      this.mouseX = e.clientX;
      this.mouseY = e.clientY;

      if (this.dot) {
        this.dot.style.transform = `translate3d(${this.mouseX - 4}px, ${this.mouseY - 4}px, 0)`;
      }

      if (this.isHidden) {
        this.show();
      }
    }, { passive: true });

    document.addEventListener('mouseleave', () => this.hide());
    document.addEventListener('mouseenter', () => this.show());

    // Single efficient event delegation for hover states
    document.addEventListener('mouseover', (e) => {
      const target = e.target;
      const interactive = target.closest('a, button, .btn, .product-card, .collection-card, .inspiration-card, input, select, textarea');
      
      if (interactive) {
        this.isHovering = true;
        this.ring.classList.add('cursor__ring--hover');

        if (interactive.closest('.product-card, .collection-card')) {
          this.setLabel('EXPLORE');
        } else if (interactive.closest('.inspiration-card, figure')) {
          this.setLabel('VIEW');
        } else {
          this.setLabel('');
        }
      }
    }, { passive: true });

    document.addEventListener('mouseout', (e) => {
      const related = e.relatedTarget;
      if (!related || !related.closest('a, button, .btn, .product-card, .collection-card, .inspiration-card, input, select, textarea')) {
        this.isHovering = false;
        this.ring.classList.remove('cursor__ring--hover', 'cursor__ring--expanded');
        this.setLabel('');
      }
    }, { passive: true });
  }

  setLabel(text) {
    if (!this.label) return;
    if (text) {
      this.label.textContent = text;
      this.label.classList.add('cursor__label--visible');
    } else {
      this.label.classList.remove('cursor__label--visible');
      this.label.textContent = '';
    }
  }

  hide() {
    this.isHidden = true;
    if (this.dot) this.dot.classList.add('cursor--hidden');
    if (this.ring) this.ring.classList.add('cursor--hidden');
  }

  show() {
    this.isHidden = false;
    if (this.dot) this.dot.classList.remove('cursor--hidden');
    if (this.ring) this.ring.classList.remove('cursor--hidden');
  }

  update() {
    // Fast lerp interpolation for the follower ring
    this.ringX += (this.mouseX - this.ringX) * this.LERP_FACTOR;
    this.ringY += (this.mouseY - this.ringY) * this.LERP_FACTOR;

    if (this.ring) {
      this.ring.style.transform = `translate3d(${this.ringX}px, ${this.ringY}px, 0) translate(-50%, -50%)`;
    }

    this.rafId = requestAnimationFrame(() => this.update());
  }

  init() {
    this._createElement();
    this._bindEvents();
    this.update();
  }
}

// Only on fine-pointer (mouse) devices
if (window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
  const cursor = new LuxuryCursor();
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => cursor.init());
  } else {
    cursor.init();
  }
}
