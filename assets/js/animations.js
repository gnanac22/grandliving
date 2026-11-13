/**
 * @file animations.js
 * @description GSAP + SplitType + Lenis animation system for Grand Furniture Vizag
 * All animations respect prefers-reduced-motion.
 */

'use strict';

document.addEventListener('DOMContentLoaded', () => {

  // ============================================================
  // MOTION PREFERENCE CHECK
  // ============================================================

  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  if (prefersReducedMotion) {
    // Show all animated elements immediately without motion
    document.querySelectorAll(
      '.reveal, .reveal-text, .reveal-image, .reveal-fade, .stagger-children, [data-count]'
    ).forEach((el) => {
      el.classList.add('is-visible');
      el.style.opacity = '1';
      el.style.transform = 'none';
      el.style.clipPath = 'none';
    });
    // Show page immediately
    document.body.style.visibility = 'visible';
    document.body.style.opacity = '1';
    return; // Skip all GSAP animations
  }

  // ============================================================
  // LIBRARY AVAILABILITY GUARDS
  // ============================================================

  const hasGSAP = typeof gsap !== 'undefined';
  const hasST = hasGSAP && typeof ScrollTrigger !== 'undefined';
  const hasSplitType = typeof SplitType !== 'undefined';
  const hasLenis = typeof Lenis !== 'undefined';

  if (!hasGSAP) {
    console.warn('[Animations] GSAP not loaded — skipping animations.');
    document.body.style.visibility = 'visible';
    document.body.style.opacity = '1';
    return;
  }

  // Register ScrollTrigger plugin
  if (hasST) gsap.registerPlugin(ScrollTrigger);

  // ============================================================
  // LENIS SMOOTH SCROLL
  // ============================================================

  let lenis;

  if (hasLenis) {
    lenis = new Lenis({
      duration: 1.4,
      easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
      direction: 'vertical',
      gestureDirection: 'vertical',
      smooth: true,
      smoothTouch: false,
      touchMultiplier: 2,
    });

    // Connect Lenis to GSAP ticker for synchronized updates
    gsap.ticker.add((time) => lenis.raf(time * 1000));
    gsap.ticker.lagSmoothing(0);

    // Connect Lenis scroll events to ScrollTrigger
    if (hasST) {
      lenis.on('scroll', ScrollTrigger.update);
    }

    window.GrandLenis = lenis;
  }

  // ============================================================
  // PAGE LOADER
  // ============================================================

  const initLoader = () => {
    const loader = document.querySelector('.page-loader');
    if (!loader) return;

    // Skip if user has already visited this session
    if (sessionStorage.getItem('gf_visited')) {
      loader.style.display = 'none';
      document.body.classList.remove('is-loading');
      return;
    }

    const loaderBrand = loader.querySelector('.page-loader__brand');
    const loaderBar = loader.querySelector('.page-loader__bar');

    const tl = gsap.timeline({
      onComplete: () => {
        sessionStorage.setItem('gf_visited', '1');
        document.body.classList.remove('is-loading');
      },
    });

    tl.from(loaderBrand, {
      opacity: 0,
      y: 20,
      duration: 0.6,
      ease: 'power3.out',
    })
      .to(loaderBar, {
        scaleX: 1,
        duration: 0.7,
        ease: 'power2.inOut',
        transformOrigin: 'left center',
      }, '-=0.2')
      .to(loader, {
        yPercent: -100,
        duration: 0.7,
        ease: 'power4.inOut',
        delay: 0.1,
      });
  };

  initLoader();

  // ============================================================
  // HERO ANIMATIONS
  // ============================================================

  const initHeroAnimations = () => {
    const hero = document.querySelector('.hero');
    if (!hero) return;

    const heroBg = hero.querySelector('.hero__bg, .hero__image');
    const heroHeadline = hero.querySelector('.hero__headline, [data-hero-headline]');
    const heroSubtitle = hero.querySelector('.hero__subtitle, [data-hero-subtitle]');
    const heroButtons = hero.querySelectorAll('.hero__cta .btn, [data-hero-btn]');
    const scrollIndicator = hero.querySelector('.hero__scroll-indicator, .scroll-indicator');
    const decorLines = hero.querySelectorAll('.hero__line, .decor-line');

    const tl = gsap.timeline({ delay: 0.1 });

    // 1. Hero background subtle zoom
    if (heroBg) {
      tl.from(heroBg, {
        scale: 1.06,
        duration: 2.0,
        ease: 'power2.out',
      }, 0);
    }

    // 2. Headline: SplitType word split + clip-path reveal
    if (heroHeadline && hasSplitType) {
      const split = new SplitType(heroHeadline, { types: 'words, chars' });
      split.words?.forEach((word) => {
        const wrapper = document.createElement('span');
        wrapper.style.display = 'inline-block';
        wrapper.style.overflow = 'hidden';
        word.parentNode.insertBefore(wrapper, word);
        wrapper.appendChild(word);
      });

      tl.from(split.words, {
        yPercent: 110,
        opacity: 0,
        stagger: 0.08,
        duration: 1.2,
        ease: 'power4.out',
      }, 0.3);
    } else if (heroHeadline) {
      tl.from(heroHeadline, {
        y: 40,
        opacity: 0,
        duration: 1.0,
        ease: 'power3.out',
      }, 0.3);
    }

    // 3. Subtitle
    if (heroSubtitle) {
      tl.from(heroSubtitle, {
        y: 25,
        opacity: 0,
        duration: 0.8,
        ease: 'power3.out',
      }, 0.6);
    }

    // 4. Buttons
    if (heroButtons.length) {
      tl.from(heroButtons, {
        y: 20,
        opacity: 0,
        stagger: 0.12,
        duration: 0.7,
        ease: 'power3.out',
      }, 0.9);
    }

    // 5. Scroll indicator
    if (scrollIndicator) {
      tl.from(scrollIndicator, {
        opacity: 0,
        y: 10,
        duration: 0.5,
      }, 1.2);

      // Infinite bounce
      gsap.to(scrollIndicator, {
        y: 10,
        repeat: -1,
        yoyo: true,
        duration: 1.1,
        ease: 'power1.inOut',
        delay: 1.5,
      });
    }

    // 6. Decorative lines
    if (decorLines.length) {
      tl.from(decorLines, {
        scaleX: 0,
        transformOrigin: 'center center',
        stagger: 0.1,
        duration: 1.2,
        ease: 'expo.out',
      }, 0.8);
    }
  };

  initHeroAnimations();

  // ============================================================
  // NAVBAR STAGGER ON LOAD
  // ============================================================

  const initNavbarAnimation = () => {
    const navItems = document.querySelectorAll('.nav__item, .nav__logo, .nav__actions');
    if (!navItems.length) return;

    gsap.from(navItems, {
      y: -20,
      opacity: 0,
      stagger: 0.07,
      duration: 0.7,
      ease: 'power3.out',
      delay: 0.4,
    });
  };

  initNavbarAnimation();

  // ============================================================
  // SCROLL TRIGGER REVEALS
  // ============================================================

  const initScrollReveals = () => {
    if (!hasST) return;

    // ---- Text reveals with SplitType ----
    document.querySelectorAll('.reveal-text').forEach((el) => {
      if (hasSplitType) {
        const split = new SplitType(el, { types: 'words' });
        split.words?.forEach((word) => {
          const wrapper = document.createElement('span');
          wrapper.style.display = 'inline-block';
          wrapper.style.overflow = 'hidden';
          word.parentNode.insertBefore(wrapper, word);
          wrapper.appendChild(word);
        });

        gsap.from(split.words, {
          yPercent: 100,
          opacity: 0,
          stagger: 0.04,
          duration: 0.9,
          ease: 'power4.out',
          scrollTrigger: {
            trigger: el,
            start: 'top 85%',
            toggleActions: 'play none none none',
          },
        });
      } else {
        gsap.from(el, {
          y: 30,
          opacity: 0,
          duration: 0.8,
          ease: 'power3.out',
          scrollTrigger: {
            trigger: el,
            start: 'top 85%',
          },
        });
      }
    });

    // ---- Image reveals (clip-path bottom → full) ----
    document.querySelectorAll('.reveal-image').forEach((el) => {
      gsap.fromTo(
        el,
        { clipPath: 'inset(100% 0% 0% 0%)', opacity: 0 },
        {
          clipPath: 'inset(0% 0% 0% 0%)',
          opacity: 1,
          duration: 1.2,
          ease: 'power4.out',
          scrollTrigger: {
            trigger: el,
            start: 'top 85%',
            toggleActions: 'play none none none',
          },
        }
      );
    });

    // ---- Fade reveals ----
    document.querySelectorAll('.reveal-fade').forEach((el) => {
      gsap.from(el, {
        y: 30,
        opacity: 0,
        duration: 0.9,
        ease: 'power3.out',
        scrollTrigger: {
          trigger: el,
          start: 'top 88%',
          toggleActions: 'play none none none',
        },
      });
    });

    // ---- Stagger children ----
    document.querySelectorAll('.stagger-children').forEach((parent) => {
      const children = parent.querySelectorAll(':scope > *');
      gsap.from(children, {
        y: 40,
        opacity: 0,
        stagger: 0.1,
        duration: 0.8,
        ease: 'power3.out',
        scrollTrigger: {
          trigger: parent,
          start: 'top 85%',
          toggleActions: 'play none none none',
        },
      });
    });
  };

  initScrollReveals();

  // ============================================================
  // PARALLAX EFFECTS
  // ============================================================

  const initParallax = () => {
    if (!hasST) return;

    // Hero background parallax
    const heroBg = document.querySelector('.hero__bg, .hero__image');
    if (heroBg) {
      gsap.to(heroBg, {
        yPercent: -15,
        ease: 'none',
        scrollTrigger: {
          trigger: '.hero',
          start: 'top top',
          end: 'bottom top',
          scrub: true,
        },
      });
    }

    // Section backgrounds with [data-parallax] attribute
    document.querySelectorAll('[data-parallax]').forEach((el) => {
      const speed = parseFloat(el.dataset.parallaxSpeed) || 0.3;
      gsap.to(el, {
        yPercent: -20 * speed,
        ease: 'none',
        scrollTrigger: {
          trigger: el.closest('section') || el,
          start: 'top bottom',
          end: 'bottom top',
          scrub: true,
        },
      });
    });
  };

  initParallax();

  // ============================================================
  // COUNTER ANIMATIONS
  // ============================================================

  const initCounters = () => {
    if (!hasST) return;

    document.querySelectorAll('[data-count]').forEach((el) => {
      const target = parseFloat(el.dataset.count);
      const suffix = el.dataset.countSuffix || '';
      const prefix = el.dataset.countPrefix || '';
      const decimals = el.dataset.countDecimals ? parseInt(el.dataset.countDecimals) : 0;

      ScrollTrigger.create({
        trigger: el,
        start: 'top 85%',
        once: true,
        onEnter: () => {
          gsap.to({ val: 0 }, {
            val: target,
            duration: 2,
            ease: 'power2.out',
            onUpdate: function () {
              const formatted = decimals > 0
                ? this.targets()[0].val.toFixed(decimals)
                : Math.floor(this.targets()[0].val).toLocaleString('en-IN');
              el.textContent = `${prefix}${formatted}${suffix}`;
            },
          });
        },
      });
    });
  };

  initCounters();

  // ============================================================
  // HORIZONTAL SCROLL SECTION
  // ============================================================

  const initHorizontalScroll = () => {
    const section = document.querySelector('.horizontal-scroll');
    if (!section || !hasST) return;

    const track = section.querySelector('.horizontal-scroll__track');
    if (!track) return;

    const totalWidth = track.scrollWidth - section.offsetWidth;

    gsap.to(track, {
      x: () => -totalWidth,
      ease: 'none',
      scrollTrigger: {
        trigger: section,
        start: 'top top',
        end: () => `+=${totalWidth}`,
        scrub: 1,
        pin: true,
        anticipatePin: 1,
        invalidateOnRefresh: true,
      },
    });
  };

  initHorizontalScroll();

  // ============================================================
  // MAGNETIC BUTTONS
  // ============================================================

  const initMagneticButtons = () => {
    document.querySelectorAll('.btn-primary, [data-magnetic]').forEach((btn) => {
      btn.addEventListener('mousemove', (e) => {
        const rect = btn.getBoundingClientRect();
        const centerX = rect.left + rect.width / 2;
        const centerY = rect.top + rect.height / 2;
        const deltaX = (e.clientX - centerX) * 0.3;
        const deltaY = (e.clientY - centerY) * 0.3;

        // Clamp to 8px max
        const clampedX = Math.max(-8, Math.min(8, deltaX));
        const clampedY = Math.max(-8, Math.min(8, deltaY));

        gsap.to(btn, {
          x: clampedX,
          y: clampedY,
          duration: 0.3,
          ease: 'power2.out',
        });
      });

      btn.addEventListener('mouseleave', () => {
        gsap.to(btn, {
          x: 0,
          y: 0,
          duration: 0.6,
          ease: 'elastic.out(1, 0.5)',
        });
      });
    });
  };

  initMagneticButtons();

  // ============================================================
  // PRODUCT CARD IMAGE HOVER
  // ============================================================

  const initProductCardHover = () => {
    document.querySelectorAll('.product-card').forEach((card) => {
      const img = card.querySelector('.product-card__image img, .product-card__image');

      card.addEventListener('mouseenter', () => {
        if (img) gsap.to(img, { scale: 1.06, duration: 0.6, ease: 'power2.out' });
      });

      card.addEventListener('mouseleave', () => {
        if (img) gsap.to(img, { scale: 1, duration: 0.5, ease: 'power2.out' });
      });
    });
  };

  initProductCardHover();

  // ============================================================
  // SCROLL PROGRESS CSS VARIABLE
  // ============================================================

  const initScrollProgressVar = () => {
    const updateProgress = () => {
      const scrollTop = window.scrollY;
      const docHeight = document.documentElement.scrollHeight - window.innerHeight;
      const pct = docHeight > 0 ? (scrollTop / docHeight) * 100 : 0;
      document.documentElement.style.setProperty('--scroll-progress', `${Math.min(pct, 100)}%`);
    };

    window.addEventListener('scroll', updateProgress, { passive: true });
    updateProgress();
  };

  initScrollProgressVar();

  // ============================================================
  // FOOTER REVEAL
  // ============================================================

  const initFooterReveal = () => {
    if (!hasST) return;

    const footerColumns = document.querySelectorAll('.footer__col, .footer__column');
    if (!footerColumns.length) return;

    gsap.from(footerColumns, {
      y: 50,
      opacity: 0,
      stagger: 0.12,
      duration: 0.9,
      ease: 'power3.out',
      scrollTrigger: {
        trigger: 'footer',
        start: 'top 90%',
        toggleActions: 'play none none none',
      },
    });
  };

  initFooterReveal();

  // ============================================================
  // SECTION TRANSITIONS
  // ============================================================

  const initSectionTransitions = () => {
    if (!hasST) return;

    document.querySelectorAll('section[data-animate]').forEach((section) => {
      const children = section.querySelectorAll(':scope > .container > *');
      if (!children.length) return;

      gsap.from(children, {
        y: 40,
        opacity: 0,
        stagger: 0.1,
        duration: 0.85,
        ease: 'power3.out',
        scrollTrigger: {
          trigger: section,
          start: 'top 80%',
          toggleActions: 'play none none none',
        },
      });
    });
  };

  initSectionTransitions();

  // ============================================================
  // EXPOSE LENIS API GLOBALLY
  // ============================================================

  // Allow smooth scroll-to from other scripts
  window.GrandAnimations = {
    lenis,
    scrollTo: (target, options = {}) => {
      if (lenis) {
        lenis.scrollTo(target, { duration: 1.2, ...options });
      } else {
        const el = typeof target === 'string' ? document.querySelector(target) : target;
        el?.scrollIntoView({ behavior: 'smooth' });
      }
    },
    refresh: () => {
      if (hasST) ScrollTrigger.refresh();
    },
  };

}); // end DOMContentLoaded
