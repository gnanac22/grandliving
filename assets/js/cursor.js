/**
 * @file cursor.js
 * @description Ultra-fast 120fps hardware-accelerated cursor for Grand Living Vizag
 * Optimized for luxury aesthetic with native I-beam on inputs
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

    this.LERP_FACTOR = 0.22;
    this.isHovering = false;
    this.isHidden = false;
    this.rafId = null;
    this.initialized = false;
  }

  _createElement() {
    if (document.querySelector('.cursor__dot')) {
      this.dot = document.querySelector('.cursor__dot');
      this.ring = document.querySelector('.cursor__ring');
      this.label = document.querySelector('.cursor__label');
      return;
    }

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
    window.addEventListener('mousemove', (e) => {
      this.mouseX = e.clientX;
      this.mouseY = e.clientY;

      // Prevent ring jump on initial mouse entrance
      if (this.ringX === -100 && this.ringY === -100) {
        this.ringX = this.mouseX;
        this.ringY = this.mouseY;
      }

      if (!document.body.classList.contains('has-custom-cursor')) {
        document.body.classList.add('has-custom-cursor');
      }

      if (this.dot) {
        this.dot.style.transform = `translate3d(${this.mouseX - 4}px, ${this.mouseY - 4}px, 0)`;
      }

      if (this.isHidden) {
        this.show();
      }
    }, { passive: true });

    document.addEventListener('mouseleave', () => this.hide());
    document.addEventListener('mouseenter', () => this.show());

    // Single event delegation for hover states
    document.addEventListener('mouseover', (e) => {
      const target = e.target;

      // If hovering over form inputs, hide custom cursor dot so native text cursor shines
      if (target.closest('input, textarea, select, [contenteditable="true"]')) {
        this.hide();
        return;
      }

      const interactive = target.closest('a, button, .btn, .product-card, .collection-card, .inspiration-card, [role="button"]');
      
      if (interactive && this.ring) {
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

      // Leaving form input, bring custom cursor back
      if (e.target.closest('input, textarea, select, [contenteditable="true"]') && (!related || !related.closest('input, textarea, select, [contenteditable="true"]'))) {
        this.show();
      }

      if (!related || !related.closest('a, button, .btn, .product-card, .collection-card, .inspiration-card, [role="button"]')) {
        this.isHovering = false;
        if (this.ring) {
          this.ring.classList.remove('cursor__ring--hover', 'cursor__ring--expanded');
        }
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
    this.ringX += (this.mouseX - this.ringX) * this.LERP_FACTOR;
    this.ringY += (this.mouseY - this.ringY) * this.LERP_FACTOR;

    if (this.ring) {
      this.ring.style.transform = `translate3d(${this.ringX}px, ${this.ringY}px, 0) translate(-50%, -50%)`;
    }

    this.rafId = requestAnimationFrame(() => this.update());
  }

  init() {
    if (this.initialized) return;

    // Don't activate on touch-only mobile devices with small screens
    if (window.matchMedia && window.matchMedia('(pointer: coarse) and (hover: none)').matches) {
      return;
    }
    if ('ontouchstart' in window && window.innerWidth < 768) {
      return;
    }

    this.initialized = true;
    this._createElement();
    this._bindEvents();
    this.update();
  }
}

// Global initializer
window.initLuxuryCursor = function() {
  if (!window._luxuryCursorInstance) {
    window._luxuryCursorInstance = new LuxuryCursor();
    window._luxuryCursorInstance.init();
  }
};

// Initialize automatically on mousemove or DOM ready
if (typeof window !== 'undefined') {
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', window.initLuxuryCursor);
  } else {
    window.initLuxuryCursor();
  }
  // Wake up immediately on first mouse interaction
  window.addEventListener('mousemove', function onFirstMouse() {
    window.initLuxuryCursor();
    window.removeEventListener('mousemove', onFirstMouse);
  }, { passive: true, once: true });
}
