/**
 * @file main.js
 * @description Core JavaScript for Grand Furniture Vizag — luxury furniture brand
 * @brand Grand Furniture Vizag, Visakhapatnam
 */

'use strict';

// ============================================================
// UTILITY FUNCTIONS
// ============================================================

/**
 * Debounce: delay function execution until after wait ms have elapsed
 * since the last call.
 * @param {Function} fn - Function to debounce
 * @param {number} delay - Delay in milliseconds
 * @returns {Function}
 */
const debounce = (fn, delay) => {
  let timer;
  return function (...args) {
    clearTimeout(timer);
    timer = setTimeout(() => fn.apply(this, args), delay);
  };
};

/**
 * Throttle: limit function execution to once per wait ms.
 * @param {Function} fn - Function to throttle
 * @param {number} delay - Throttle window in milliseconds
 * @returns {Function}
 */
const throttle = (fn, delay) => {
  let lastCall = 0;
  return function (...args) {
    const now = Date.now();
    if (now - lastCall >= delay) {
      lastCall = now;
      return fn.apply(this, args);
    }
  };
};

/**
 * Read a browser cookie by name.
 * @param {string} name - Cookie name
 * @returns {string|null}
 */
const getCookie = (name) => {
  const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
  return match ? decodeURIComponent(match[2]) : null;
};

/**
 * Set a browser cookie.
 * @param {string} name - Cookie name
 * @param {string} value - Cookie value
 * @param {number} days - Expiry in days
 */
const setCookie = (name, value, days) => {
  const expires = new Date(Date.now() + days * 864e5).toUTCString();
  document.cookie = `${name}=${encodeURIComponent(value)}; expires=${expires}; path=/; SameSite=Lax`;
};

/**
 * Format a number with Indian comma grouping (e.g. 1,00,000).
 * @param {number|string} n - Number to format
 * @returns {string}
 */
const formatNumber = (n) => {
  const num = parseInt(n, 10);
  if (isNaN(num)) return n;
  return num.toLocaleString('en-IN');
};

/**
 * Read the CSRF token from the meta tag.
 * @returns {string}
 */
const generateCSRFToken = () => {
  const meta = document.querySelector('meta[name="csrf-token"]');
  return meta ? meta.getAttribute('content') : '';
};

// ============================================================
// NAVIGATION
// ============================================================

const initNavigation = () => {
  const nav = document.querySelector('.nav');
  if (!nav) return;

  const NAV_SCROLL_THRESHOLD = 80;

  // ---- Sticky / scrolled state ----
  const handleNavScroll = throttle(() => {
    if (window.scrollY > NAV_SCROLL_THRESHOLD) {
      nav.classList.add('nav--scrolled');
    } else {
      nav.classList.remove('nav--scrolled');
    }
  }, 50);

  window.addEventListener('scroll', handleNavScroll, { passive: true });
  handleNavScroll(); // Run on init

  // ---- Smooth scroll for anchor links ----
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener('click', (e) => {
      const target = document.querySelector(anchor.getAttribute('href'));
      if (target) {
        e.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });

  // ---- Active nav link based on current path ----
  const currentPath = window.location.pathname.replace(/\/$/, '');
  document.querySelectorAll('.nav__link').forEach((link) => {
    const linkPath = new URL(link.href, window.location.origin).pathname.replace(/\/$/, '');
    if (linkPath === currentPath || (currentPath === '' && linkPath === '/index.php')) {
      link.classList.add('nav__link--active');
      link.setAttribute('aria-current', 'page');
    }
  });

  // ---- Desktop dropdown keyboard support ----
  document.querySelectorAll('.nav__dropdown-trigger').forEach((trigger) => {
    trigger.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        const dropdown = trigger.nextElementSibling;
        if (dropdown) {
          const isOpen = dropdown.classList.contains('is-open');
          closeAllDropdowns();
          if (!isOpen) {
            dropdown.classList.add('is-open');
            trigger.setAttribute('aria-expanded', 'true');
          }
        }
      }
      if (e.key === 'Escape') {
        closeAllDropdowns();
      }
    });

    // Desktop hover
    const parent = trigger.closest('.nav__item--dropdown');
    if (parent) {
      parent.addEventListener('mouseenter', () => {
        const dropdown = trigger.nextElementSibling;
        if (dropdown) {
          dropdown.classList.add('is-open');
          trigger.setAttribute('aria-expanded', 'true');
        }
      });
      parent.addEventListener('mouseleave', () => {
        const dropdown = trigger.nextElementSibling;
        if (dropdown) {
          dropdown.classList.remove('is-open');
          trigger.setAttribute('aria-expanded', 'false');
        }
      });
    }
  });

  const closeAllDropdowns = () => {
    document.querySelectorAll('.nav__dropdown').forEach((d) => {
      d.classList.remove('is-open');
    });
    document.querySelectorAll('.nav__dropdown-trigger').forEach((t) => {
      t.setAttribute('aria-expanded', 'false');
    });
  };

  // Close dropdowns on outside click
  document.addEventListener('click', (e) => {
    if (!e.target.closest('.nav__item--dropdown')) {
      closeAllDropdowns();
    }
  });
};

// ============================================================
// MOBILE MENU
// ============================================================

const initMobileMenu = () => {
  const hamburger = document.getElementById('navHamburger') || document.querySelector('.nav__mobile-toggle');
  const overlay = document.getElementById('navMobile') || document.querySelector('.mobile-menu');
  const closeBtn = document.querySelector('.mobile-menu__close');

  if (!hamburger || !overlay) return;

  const toggle = (e) => {
    if (e) {
      e.preventDefault();
      e.stopPropagation();
    }
    const isOpen = hamburger.getAttribute('aria-expanded') === 'true';
    const nextState = !isOpen;
    hamburger.setAttribute('aria-expanded', String(nextState));
    overlay.classList.toggle('is-open', nextState);
    overlay.setAttribute('aria-hidden', String(!nextState));
    document.body.classList.toggle('is-menu-open', nextState);
    document.body.style.overflow = nextState ? 'hidden' : '';
  };

  hamburger.addEventListener('click', toggle);
  hamburger.addEventListener('touchend', (e) => {
    e.preventDefault();
    toggle(e);
  });

  overlay.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', () => {
      hamburger.setAttribute('aria-expanded', 'false');
      overlay.classList.remove('is-open');
      overlay.setAttribute('aria-hidden', 'true');
      document.body.classList.remove('is-menu-open');
      document.body.style.overflow = '';
    });
  });

  if (closeBtn) closeBtn.addEventListener('click', toggle);

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && overlay.classList.contains('is-open')) {
      toggle(e);
    }
  });
};

// ============================================================
// SEARCH
// ============================================================

const initSearch = () => {
  const searchToggle = document.querySelector('.nav__search-toggle');
  const searchBar = document.querySelector('.search-bar');
  const searchInput = document.querySelector('.search-bar__input');
  const searchResults = document.querySelector('.search-bar__results');
  const searchClose = document.querySelector('.search-bar__close');

  if (!searchToggle || !searchBar) return;

  let isSearchOpen = false;

  const openSearch = () => {
    isSearchOpen = true;
    searchBar.classList.add('is-open');
    searchBar.setAttribute('aria-hidden', 'false');
    searchToggle.setAttribute('aria-expanded', 'true');
    searchInput?.focus();
  };

  const closeSearch = () => {
    isSearchOpen = false;
    searchBar.classList.remove('is-open');
    searchBar.setAttribute('aria-hidden', 'true');
    searchToggle.setAttribute('aria-expanded', 'false');
    if (searchResults) {
      searchResults.innerHTML = '';
      searchResults.classList.remove('is-visible');
    }
  };

  searchToggle.addEventListener('click', () => (isSearchOpen ? closeSearch() : openSearch()));
  searchClose?.addEventListener('click', closeSearch);

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && isSearchOpen) closeSearch();
  });

  document.addEventListener('click', (e) => {
    if (isSearchOpen && !searchBar.contains(e.target) && !searchToggle.contains(e.target)) {
      closeSearch();
    }
  });

  // ---- Live Search ----
  if (searchInput && searchResults) {
    const performSearch = debounce(async (query) => {
      if (query.trim().length < 2) {
        searchResults.innerHTML = '';
        searchResults.classList.remove('is-visible');
        return;
      }

      try {
        const res = await fetch(`/search.php?q=${encodeURIComponent(query)}&ajax=1`, {
          headers: { 'X-Requested-With': 'XMLHttpRequest' },
        });
        if (!res.ok) throw new Error('Search failed');
        const data = await res.json();
        renderSearchResults(data);
      } catch (err) {
        console.error('Search error:', err);
      }
    }, 300);

    searchInput.addEventListener('input', (e) => performSearch(e.target.value));

    /**
     * Render search results dropdown.
     * @param {Object} data - JSON response from server
     */
    const renderSearchResults = (data) => {
      if (!data.results || data.results.length === 0) {
        searchResults.innerHTML = `<p class="search-bar__no-results">No results found.</p>`;
      } else {
        searchResults.innerHTML = data.results
          .map(
            (item) => `
            <a href="${item.url}" class="search-bar__result-item">
              ${item.image ? `<img src="${item.image}" alt="${item.name}" class="search-bar__result-img" loading="lazy">` : ''}
              <div class="search-bar__result-info">
                <span class="search-bar__result-name">${item.name}</span>
                ${item.price ? `<span class="search-bar__result-price">₹${formatNumber(item.price)}</span>` : ''}
                ${item.category ? `<span class="search-bar__result-cat">${item.category}</span>` : ''}
              </div>
            </a>
          `
          )
          .join('');
      }
      searchResults.classList.add('is-visible');
    };
  }
};

// ============================================================
// FLASH / TOAST MESSAGES
// ============================================================

const initFlashMessages = () => {
  const AUTO_DISMISS_MS = 4000;

  const dismiss = (el) => {
    el.classList.add('flash--dismissing');
    setTimeout(() => el.remove(), 350);
  };

  document.querySelectorAll('.flash, .toast').forEach((msg) => {
    // Auto-dismiss
    setTimeout(() => dismiss(msg), AUTO_DISMISS_MS);

    // Manual close button
    const closeBtn = msg.querySelector('.flash__close, .toast__close');
    closeBtn?.addEventListener('click', () => dismiss(msg));
  });
};

/**
 * Show a toast message programmatically.
 * @param {string} message - Toast text
 * @param {'success'|'error'|'info'|'warning'} type - Toast type
 */
const showToast = (message, type = 'info') => {
  let container = document.querySelector('.toast-container');
  if (!container) {
    container = document.createElement('div');
    container.className = 'toast-container';
    document.body.appendChild(container);
  }

  const toast = document.createElement('div');
  toast.className = `toast toast--${type}`;
  toast.setAttribute('role', 'alert');
  toast.innerHTML = `
    <span class="toast__message">${message}</span>
    <button class="toast__close" aria-label="Dismiss">&times;</button>
  `;

  container.appendChild(toast);

  // Trigger entrance animation
  requestAnimationFrame(() => toast.classList.add('toast--visible'));

  const dismiss = () => {
    toast.classList.remove('toast--visible');
    setTimeout(() => toast.remove(), 350);
  };

  setTimeout(dismiss, 4000);
  toast.querySelector('.toast__close').addEventListener('click', dismiss);
};

// ============================================================
// FORM VALIDATION & AJAX SUBMISSION
// ============================================================

const initForms = () => {
  /**
   * Validate a single form field.
   * @param {HTMLInputElement|HTMLTextAreaElement|HTMLSelectElement} field
   * @returns {string} Error message, or empty string if valid
   */
  const validateField = (field) => {
    const value = field.value.trim();
    const type = field.type;

    if (field.required && !value) return 'This field is required.';

    if (value) {
      if (type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
        return 'Please enter a valid email address.';
      }
      if (field.dataset.phone === 'true' && !/^[6-9]\d{9}$/.test(value)) {
        return 'Please enter a valid 10-digit Indian mobile number.';
      }
      if (field.minLength > 0 && value.length < field.minLength) {
        return `Minimum ${field.minLength} characters required.`;
      }
      if (field.dataset.match) {
        const matchEl = document.getElementById(field.dataset.match);
        if (matchEl && value !== matchEl.value.trim()) {
          return 'Fields do not match.';
        }
      }
    }

    return '';
  };

  /**
   * Display or clear field error.
   * @param {HTMLElement} field
   * @param {string} error
   */
  const setFieldError = (field, error) => {
    const wrapper = field.closest('.form__field') || field.parentElement;
    let errorEl = wrapper.querySelector('.form__error');

    if (error) {
      field.classList.add('is-invalid');
      field.classList.remove('is-valid');
      field.setAttribute('aria-invalid', 'true');
      if (!errorEl) {
        errorEl = document.createElement('span');
        errorEl.className = 'form__error';
        errorEl.setAttribute('role', 'alert');
        wrapper.appendChild(errorEl);
      }
      errorEl.textContent = error;
    } else {
      field.classList.remove('is-invalid');
      field.classList.add('is-valid');
      field.setAttribute('aria-invalid', 'false');
      errorEl?.remove();
    }
  };

  // ---- Real-time blur validation ----
  document.querySelectorAll('form:not([data-no-validate]) input, form:not([data-no-validate]) textarea, form:not([data-no-validate]) select').forEach((field) => {
    field.addEventListener('blur', () => setFieldError(field, validateField(field)));
    field.addEventListener('input', () => {
      if (field.classList.contains('is-invalid')) {
        setFieldError(field, validateField(field));
      }
    });
  });

  // ---- AJAX form submissions ----
  document.querySelectorAll('form[data-ajax]').forEach((form) => {
    form.addEventListener('submit', async (e) => {
      e.preventDefault();

      // Validate all fields
      let isValid = true;
      form.querySelectorAll('input, textarea, select').forEach((field) => {
        const error = validateField(field);
        setFieldError(field, error);
        if (error) isValid = false;
      });

      if (!isValid) {
        const firstInvalid = form.querySelector('.is-invalid');
        firstInvalid?.focus();
        return;
      }

      const submitBtn = form.querySelector('[type="submit"]');
      const originalText = submitBtn?.textContent || 'Submit';

      // Loading state
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.textContent = 'Sending…';
        submitBtn.classList.add('btn--loading');
      }

      const formData = new FormData(form);
      formData.append('csrf_token', generateCSRFToken());

      try {
        const res = await fetch(form.action || window.location.href, {
          method: 'POST',
          body: formData,
          headers: { 'X-Requested-With': 'XMLHttpRequest' },
        });

        const data = await res.json();

        if (data.success) {
          showToast(data.message || 'Your enquiry has been submitted successfully.', 'success');
          form.reset();
          form.querySelectorAll('.is-valid, .is-invalid').forEach((f) => {
            f.classList.remove('is-valid', 'is-invalid');
          });
          form.querySelectorAll('.form__error').forEach((e) => e.remove());

          // Optional redirect
          if (data.redirect) {
            setTimeout(() => (window.location.href = data.redirect), 1500);
          }
        } else {
          showToast(data.message || 'Something went wrong. Please try again.', 'error');
          // Field-specific errors from server
          if (data.errors) {
            Object.entries(data.errors).forEach(([name, msg]) => {
              const field = form.querySelector(`[name="${name}"]`);
              if (field) setFieldError(field, msg);
            });
          }
        }
      } catch (err) {
        console.error('Form submission error:', err);
        showToast('Network error. Please check your connection and try again.', 'error');
      } finally {
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.textContent = originalText;
          submitBtn.classList.remove('btn--loading');
        }
      }
    });
  });
};

// ============================================================
// PRODUCT IMAGE GALLERY (product.php)
// ============================================================

const initProductGallery = () => {
  const mainImage = document.querySelector('.product-gallery__main img');
  const thumbnails = document.querySelectorAll('.product-gallery__thumb');

  if (!mainImage || !thumbnails.length) return;

  let currentIndex = 0;

  /**
   * Switch main image with fade transition.
   * @param {number} index - Thumbnail index to activate
   */
  const switchImage = (index) => {
    const thumb = thumbnails[index];
    if (!thumb) return;

    currentIndex = index;
    const src = thumb.dataset.full || thumb.querySelector('img')?.src || thumb.src;
    const alt = thumb.dataset.alt || thumb.querySelector('img')?.alt || '';

    mainImage.style.opacity = '0';
    setTimeout(() => {
      mainImage.src = src;
      mainImage.alt = alt;
      mainImage.style.opacity = '1';
    }, 220);

    thumbnails.forEach((t, i) => {
      t.classList.toggle('is-active', i === index);
      t.setAttribute('aria-selected', i === index ? 'true' : 'false');
    });
  };

  thumbnails.forEach((thumb, i) => {
    thumb.addEventListener('click', () => switchImage(i));
    thumb.setAttribute('role', 'tab');
    thumb.setAttribute('tabindex', '0');
    thumb.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        switchImage(i);
      }
    });
  });

  // Keyboard arrow navigation
  document.addEventListener('keydown', (e) => {
    if (!document.querySelector('.product-gallery')) return;
    if (e.key === 'ArrowRight') switchImage((currentIndex + 1) % thumbnails.length);
    if (e.key === 'ArrowLeft') switchImage((currentIndex - 1 + thumbnails.length) % thumbnails.length);
  });

  // Lazy loading for thumbnails
  const lazyObserver = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        const img = entry.target.querySelector('img[data-src]');
        if (img) {
          img.src = img.dataset.src;
          img.removeAttribute('data-src');
        }
        lazyObserver.unobserve(entry.target);
      }
    });
  }, { rootMargin: '200px' });

  thumbnails.forEach((thumb) => lazyObserver.observe(thumb));

  // Set initial image
  switchImage(0);
};

// ============================================================
// GALLERY LIGHTBOX (gallery.php)
// ============================================================

const initLightbox = () => {
  const galleryItems = document.querySelectorAll('.gallery-item');
  if (!galleryItems.length) return;

  // Build lightbox DOM
  const lightbox = document.createElement('div');
  lightbox.className = 'lightbox';
  lightbox.setAttribute('role', 'dialog');
  lightbox.setAttribute('aria-modal', 'true');
  lightbox.setAttribute('aria-label', 'Image viewer');
  lightbox.innerHTML = `
    <div class="lightbox__backdrop"></div>
    <div class="lightbox__content">
      <button class="lightbox__close" aria-label="Close">&times;</button>
      <button class="lightbox__prev" aria-label="Previous image">&#8249;</button>
      <div class="lightbox__image-wrap">
        <img class="lightbox__image" src="" alt="">
        <div class="lightbox__caption"></div>
      </div>
      <button class="lightbox__next" aria-label="Next image">&#8250;</button>
      <div class="lightbox__counter"></div>
    </div>
  `;
  document.body.appendChild(lightbox);

  const lbImage = lightbox.querySelector('.lightbox__image');
  const lbCaption = lightbox.querySelector('.lightbox__caption');
  const lbCounter = lightbox.querySelector('.lightbox__counter');
  const lbClose = lightbox.querySelector('.lightbox__close');
  const lbPrev = lightbox.querySelector('.lightbox__prev');
  const lbNext = lightbox.querySelector('.lightbox__next');

  let images = [];
  let currentIdx = 0;

  // Build images array
  galleryItems.forEach((item) => {
    const img = item.querySelector('img');
    images.push({
      src: item.dataset.full || img?.src || '',
      alt: item.dataset.caption || img?.alt || '',
    });
  });

  const openLightbox = (index) => {
    currentIdx = index;
    updateLightboxImage();
    lightbox.classList.add('is-open');
    lightbox.setAttribute('aria-hidden', 'false');
    document.body.classList.add('overflow-hidden');
    lbClose.focus();
  };

  const closeLightbox = () => {
    lightbox.classList.remove('is-open');
    lightbox.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('overflow-hidden');
  };

  const updateLightboxImage = () => {
    const item = images[currentIdx];
    lbImage.style.opacity = '0';
    setTimeout(() => {
      lbImage.src = item.src;
      lbImage.alt = item.alt;
      lbCaption.textContent = item.alt;
      lbCounter.textContent = `${currentIdx + 1} / ${images.length}`;
      lbImage.style.opacity = '1';
    }, 180);
  };

  const goNext = () => {
    currentIdx = (currentIdx + 1) % images.length;
    updateLightboxImage();
  };

  const goPrev = () => {
    currentIdx = (currentIdx - 1 + images.length) % images.length;
    updateLightboxImage();
  };

  galleryItems.forEach((item, i) => {
    item.addEventListener('click', () => openLightbox(i));
    item.setAttribute('role', 'button');
    item.setAttribute('tabindex', '0');
    item.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        openLightbox(i);
      }
    });
  });

  lbClose.addEventListener('click', closeLightbox);
  lbNext.addEventListener('click', goNext);
  lbPrev.addEventListener('click', goPrev);
  lightbox.querySelector('.lightbox__backdrop').addEventListener('click', closeLightbox);

  // Keyboard navigation
  document.addEventListener('keydown', (e) => {
    if (!lightbox.classList.contains('is-open')) return;
    if (e.key === 'Escape') closeLightbox();
    if (e.key === 'ArrowRight') goNext();
    if (e.key === 'ArrowLeft') goPrev();
  });

  // Touch / swipe support
  let touchStartX = 0;
  let touchStartY = 0;
  lightbox.addEventListener('touchstart', (e) => {
    touchStartX = e.changedTouches[0].screenX;
    touchStartY = e.changedTouches[0].screenY;
  }, { passive: true });
  lightbox.addEventListener('touchend', (e) => {
    const dx = touchStartX - e.changedTouches[0].screenX;
    const dy = touchStartY - e.changedTouches[0].screenY;
    if (Math.abs(dx) > Math.abs(dy) && Math.abs(dx) > 50) {
      dx > 0 ? goNext() : goPrev();
    }
  }, { passive: true });
};

// ============================================================
// CATALOGUE FILTERS (catalogue.php)
// ============================================================

const initCatalogueFilters = () => {
  const filtersContainer = document.querySelector('.catalogue-filters');
  if (!filtersContainer) return;

  const params = new URLSearchParams(window.location.search);

  // ---- Category pills ----
  document.querySelectorAll('.filter-pill[data-category]').forEach((pill) => {
    pill.addEventListener('click', (e) => {
      e.preventDefault();
      const cat = pill.dataset.category;
      if (cat) {
        params.set('category', cat);
      } else {
        params.delete('category');
      }
      params.delete('page');
      window.location.href = `${window.location.pathname}?${params.toString()}`;
    });
  });

  // ---- Sort change ----
  const sortSelect = document.querySelector('.catalogue-sort select, [data-sort-select]');
  sortSelect?.addEventListener('change', (e) => {
    params.set('sort', e.target.value);
    params.delete('page');
    window.location.href = `${window.location.pathname}?${params.toString()}`;
  });

  // ---- Search with debounce ----
  const catalogueSearch = document.querySelector('.catalogue-search input, [data-catalogue-search]');
  if (catalogueSearch) {
    const handleSearch = debounce((val) => {
      if (val.trim().length >= 2) {
        params.set('q', val.trim());
      } else {
        params.delete('q');
      }
      params.delete('page');
      window.location.href = `${window.location.pathname}?${params.toString()}`;
    }, 400);

    catalogueSearch.addEventListener('input', (e) => handleSearch(e.target.value));
  }

  // ---- Mobile filter drawer ----
  const filterToggle = document.querySelector('.filter-drawer-toggle');
  const filterDrawer = document.querySelector('.filter-drawer');
  const filterDrawerClose = document.querySelector('.filter-drawer__close');

  if (filterToggle && filterDrawer) {
    filterToggle.addEventListener('click', () => {
      filterDrawer.classList.toggle('is-open');
      document.body.classList.toggle('overflow-hidden', filterDrawer.classList.contains('is-open'));
      filterToggle.setAttribute('aria-expanded', filterDrawer.classList.contains('is-open'));
    });

    filterDrawerClose?.addEventListener('click', () => {
      filterDrawer.classList.remove('is-open');
      document.body.classList.remove('overflow-hidden');
    });

    // Click outside to close
    filterDrawer.addEventListener('click', (e) => {
      if (e.target === filterDrawer) {
        filterDrawer.classList.remove('is-open');
        document.body.classList.remove('overflow-hidden');
      }
    });
  }

  // ---- Active filter count badge ----
  const updateFilterBadge = () => {
    const badge = document.querySelector('.filter-count-badge');
    if (!badge) return;
    const activeCount = [...params.keys()].filter((k) => !['page', 'sort'].includes(k)).length;
    badge.textContent = activeCount;
    badge.classList.toggle('is-visible', activeCount > 0);
  };
  updateFilterBadge();

  // ---- Clear all filters ----
  const clearBtn = document.querySelector('.clear-filters-btn');
  clearBtn?.addEventListener('click', (e) => {
    e.preventDefault();
    window.location.href = window.location.pathname;
  });
};

// ============================================================
// WHATSAPP INTEGRATION
// ============================================================

const initWhatsApp = () => {
  const WHATSAPP_NUMBER = document.querySelector('meta[name="whatsapp-number"]')?.content || '919000000000';

  /**
   * Generate a WhatsApp URL with pre-filled message.
   * @param {string} message
   * @returns {string}
   */
  const whatsAppURL = (message) =>
    `https://wa.me/${WHATSAPP_NUMBER}?text=${encodeURIComponent(message)}`;

  // Product enquiry buttons
  document.querySelectorAll('[data-whatsapp="product"]').forEach((btn) => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const name = btn.dataset.productName || document.querySelector('.product-title')?.textContent?.trim() || 'a product';
      const sku = btn.dataset.sku || '';
      const message = `Hi! I'm interested in *${name}*${sku ? ` (SKU: ${sku})` : ''} from your website. Could you please share more details?`;

      // Click tracking (ready for GA4)
      console.log('[WhatsApp] Product enquiry:', { name, sku });

      window.open(whatsAppURL(message), '_blank', 'noopener,noreferrer');
    });
  });

  // General enquiry buttons
  document.querySelectorAll('[data-whatsapp="general"]').forEach((btn) => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const pageTitle = document.title;
      const message = `Hi! I found your website (${pageTitle}) and would like to know more about your furniture collections.`;

      console.log('[WhatsApp] General enquiry from:', pageTitle);
      window.open(whatsAppURL(message), '_blank', 'noopener,noreferrer');
    });
  });
};

// ============================================================
// SCROLL PROGRESS BAR
// ============================================================

const initScrollProgress = () => {
  const bar = document.querySelector('.scroll-progress');
  if (!bar) return;

  const updateProgress = throttle(() => {
    const scrollTop = window.scrollY;
    const docHeight = document.documentElement.scrollHeight - window.innerHeight;
    const pct = docHeight > 0 ? (scrollTop / docHeight) * 100 : 0;
    bar.style.width = `${Math.min(pct, 100)}%`;
  }, 16);

  window.addEventListener('scroll', updateProgress, { passive: true });
};

// ============================================================
// INTERSECTION OBSERVER REVEALS
// ============================================================

const initRevealObserver = () => {
  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (prefersReducedMotion) {
    // Show all immediately without animation
    document.querySelectorAll('.reveal, .reveal-stagger').forEach((el) => {
      el.classList.add('is-visible');
    });
    return;
  }

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const el = entry.target;
          el.classList.add('is-visible');

          // Stagger children
          if (el.classList.contains('reveal-stagger')) {
            el.querySelectorAll(':scope > *').forEach((child, i) => {
              child.style.transitionDelay = `${i * 0.08}s`;
              child.classList.add('is-visible');
            });
          }

          observer.unobserve(el); // Trigger once
        }
      });
    },
    { threshold: 0.6 }
  );

  document.querySelectorAll('.reveal, .reveal-stagger').forEach((el) => observer.observe(el));
};

// ============================================================
// QUICK SHOWROOM ENQUIRY -> DIRECT WHATSAPP INTEGRATION
// ============================================================

const initQuickEnquiryToWhatsApp = () => {
  const enquiryForms = document.querySelectorAll('form[action*="process-enquiry"]');

  enquiryForms.forEach((form) => {
    form.addEventListener('submit', (e) => {
      const nameInput = form.querySelector('[name="name"]');
      const phoneInput = form.querySelector('[name="phone"]');
      const emailInput = form.querySelector('[name="email"]');
      const msgInput = form.querySelector('[name="message"]');
      const productInput = form.querySelector('[name="product_name"]');
      const subjectInput = form.querySelector('[name="subject"]');

      const name = (nameInput ? nameInput.value : '').trim();
      const phone = (phoneInput ? phoneInput.value : '').trim();
      const email = (emailInput ? emailInput.value : '').trim();
      const message = (msgInput ? msgInput.value : '').trim();
      
      let productName = (productInput ? productInput.value : '').trim();
      if (!productName) {
        const pageH1 = document.querySelector('h1');
        if (pageH1) productName = pageH1.textContent.trim();
      }

      const subject = (subjectInput ? subjectInput.value : '').trim();

      if (!name || !phone) return;

      e.preventDefault();
      e.stopPropagation();

      let waText = '🏛️ *Grand Living Vizag — Quick Showroom Enquiry*\n\n';
      if (productName) {
        waText += '• *Piece:* ' + productName + '\n';
      }
      if (subject && subject !== 'Enquiry for ' + productName) {
        waText += '• *Subject:* ' + subject + '\n';
      }
      waText += '• *Client Name:* ' + name + '\n';
      waText += '• *Phone Number:* ' + phone + '\n';
      if (email) {
        waText += '• *Email:* ' + email + '\n';
      }
      if (message) {
        waText += '• *Enquiry / Requirements:* ' + message + '\n';
      }
      waText += '\n_Sent via Grand Living Showroom Concierge_';

      const whatsappNumber = '919052751666';
      const encodedMsg = encodeURIComponent(waText);
      const waUrl = 'https://wa.me/' + whatsappNumber + '?text=' + encodedMsg;

      const submitBtn = form.querySelector('button[type="submit"]');
      if (submitBtn) {
        const origText = submitBtn.innerHTML;
        submitBtn.innerHTML = 'Connecting to WhatsApp…';
        submitBtn.style.opacity = '0.85';
        setTimeout(() => {
          submitBtn.innerHTML = origText;
          submitBtn.style.opacity = '1';
        }, 4000);
      }

      if (window.location.protocol.startsWith('http') && !window.location.hostname.includes('vercel.app')) {
        try {
          fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
          }).catch(() => {});
        } catch (_) {}
      }

      window.open(waUrl, '_blank');
    });
  });
};

// ============================================================
// ROYALOAK-INSPIRED E-COMMERCE & INTERACTIVE LOGIC
// ============================================================

const initCountdownTimer = () => {
  const countdownEl = document.getElementById('heroCountdown');
  if (!countdownEl) return;

  let totalSeconds = 6 * 3600 + 45 * 60 + 20;

  const update = () => {
    totalSeconds--;
    if (totalSeconds < 0) totalSeconds = 24 * 3600;

    const h = Math.floor(totalSeconds / 3600);
    const m = Math.floor((totalSeconds % 3600) / 60);
    const s = totalSeconds % 60;

    const pad = (n) => (n < 10 ? '0' + n : n);
    countdownEl.textContent = `${pad(h)}h : ${pad(m)}m : ${pad(s)}s`;
  };

  update();
  setInterval(update, 1000);
};

const initHeroSlider = () => {
  const slides = document.querySelectorAll('.hero__slide-item');
  const dots = document.querySelectorAll('.hero-slider-dot');
  if (slides.length <= 1) return;

  let current = 0;
  let timer = null;

  const showSlide = (index) => {
    slides.forEach((s, i) => {
      s.classList.toggle('is-active', i === index);
      s.style.opacity = i === index ? '1' : '0';
      s.style.pointerEvents = i === index ? 'auto' : 'none';
    });
    dots.forEach((d, i) => d.classList.toggle('is-active', i === index));
    current = index;
  };

  const nextSlide = () => {
    showSlide((current + 1) % slides.length);
  };

  dots.forEach((dot, idx) => {
    dot.addEventListener('click', () => {
      clearInterval(timer);
      showSlide(idx);
      timer = setInterval(nextSlide, 5500);
    });
  });

  timer = setInterval(nextSlide, 5500);
};

const initInternationalCollections = () => {
  const tabs = document.querySelectorAll('.intl-tab-btn');
  const cards = document.querySelectorAll('.intl-card');

  if (!tabs.length || !cards.length) return;

  tabs.forEach((tab) => {
    tab.addEventListener('click', () => {
      tabs.forEach((t) => t.classList.remove('is-active'));
      tab.classList.add('is-active');

      const country = tab.dataset.country || 'all';

      cards.forEach((card) => {
        const cardCountry = card.dataset.country || '';
        if (country === 'all' || cardCountry === country) {
          card.style.display = 'flex';
          card.style.opacity = '0';
          setTimeout(() => {
            card.style.transition = 'opacity 0.4s ease';
            card.style.opacity = '1';
          }, 20);
        } else {
          card.style.display = 'none';
        }
      });
    });
  });
};

const initEmiCalculator = () => {
  const backdrop = document.getElementById('emiModalBackdrop');
  const closeBtn = document.getElementById('emiModalClose');
  const emiPills = document.querySelectorAll('.trigger-emi-calc');
  const amountDisplay = document.getElementById('emiModalAmount');
  const emiOutput = document.getElementById('emiMonthlyOutput');
  const tenureSelect = document.getElementById('emiTenureSelect');

  if (!backdrop) return;

  let currentPrincipal = 150000;

  const calculate = () => {
    const months = parseInt(tenureSelect?.value || '6', 10);
    const monthly = Math.round(currentPrincipal / months);
    if (emiOutput) {
      emiOutput.textContent = '₹' + monthly.toLocaleString('en-IN') + ' / month';
    }
    if (amountDisplay) {
      amountDisplay.textContent = '₹' + currentPrincipal.toLocaleString('en-IN');
    }
  };

  emiPills.forEach((pill) => {
    pill.addEventListener('click', (e) => {
      e.preventDefault();
      const price = parseInt(pill.dataset.price || '150000', 10);
      currentPrincipal = price;
      calculate();
      backdrop.classList.add('is-open');
    });
  });

  if (tenureSelect) {
    tenureSelect.addEventListener('change', calculate);
  }

  const close = () => backdrop.classList.remove('is-open');
  if (closeBtn) closeBtn.addEventListener('click', close);
  backdrop.addEventListener('click', (e) => {
    if (e.target === backdrop) close();
  });
};

const initWishlistToggle = () => {
  const btns = document.querySelectorAll('.wishlist-toggle');
  let saved = [];
  try {
    saved = JSON.parse(localStorage.getItem('grand_wishlist') || '[]');
  } catch (err) {}

  btns.forEach((btn) => {
    const id = btn.dataset.productId || btn.closest('.intl-card')?.dataset.id || Math.random().toString();
    if (saved.includes(id)) {
      btn.classList.add('is-active');
      btn.innerHTML = '♥';
    }

    btn.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      const isActive = btn.classList.toggle('is-active');
      if (isActive) {
        btn.innerHTML = '♥';
        if (!saved.includes(id)) saved.push(id);
        if (window.GrandFurniture?.showToast) {
          window.GrandFurniture.showToast('Piece added to your Curated Wishlist!', 'success');
        }
      } else {
        btn.innerHTML = '♡';
        saved = saved.filter((item) => item !== id);
      }
      try {
        localStorage.setItem('grand_wishlist', JSON.stringify(saved));
      } catch (err) {}
    });
  });
};

// ============================================================
// BLINDFOLD SALE OFFERS (5 SCROLLING TIERS)
// ============================================================

function initBlindfoldOffers() {
  const track = document.getElementById('blindfoldCarouselTrack');
  const prevBtn = document.getElementById('blindfoldPrev');
  const nextBtn = document.getElementById('blindfoldNext');
  const filterBtns = document.querySelectorAll('.blindfold-filter-btn');
  const copyBtns = document.querySelectorAll('.blindfold-coupon-copy');

  if (track && prevBtn && nextBtn) {
    prevBtn.addEventListener('click', () => {
      const card = track.querySelector('.blindfold-card');
      const step = (card ? card.offsetWidth : 320) + 20;
      track.scrollBy({ left: -step, behavior: 'smooth' });
    });

    nextBtn.addEventListener('click', () => {
      const card = track.querySelector('.blindfold-card');
      const step = (card ? card.offsetWidth : 320) + 20;
      track.scrollBy({ left: step, behavior: 'smooth' });
    });
  }

  // Filter tabs
  filterBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      filterBtns.forEach(b => b.classList.remove('is-active'));
      btn.classList.add('is-active');
      const slab = btn.getAttribute('data-slab');
      if (!track) return;

      if (slab === 'all') {
        track.scrollTo({ left: 0, behavior: 'smooth' });
      } else {
        const targetCard = track.querySelector(`.blindfold-card[data-slab="${slab}"]`);
        if (targetCard) {
          const offset = targetCard.offsetLeft - track.offsetLeft;
          track.scrollTo({ left: offset, behavior: 'smooth' });
        }
      }
    });
  });

  // Copy offer coupon code
  copyBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      const code = btn.getAttribute('data-code');
      if (!code) return;

      const notifySuccess = () => {
        const orig = btn.textContent;
        btn.textContent = 'COPIED!';
        btn.style.background = '#D5BE9A';
        btn.style.color = '#12100E';
        if (window.GrandFurniture && typeof window.GrandFurniture.showToast === 'function') {
          window.GrandFurniture.showToast(`Coupon "${code}" copied! Claim on WhatsApp.`, 'success');
        }
        setTimeout(() => {
          btn.textContent = orig;
          btn.style.background = '';
          btn.style.color = '';
        }, 2200);
      };

      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(code).then(notifySuccess).catch(() => {
          fallbackCopy(code, notifySuccess);
        });
      } else {
        fallbackCopy(code, notifySuccess);
      }
    });
  });

  function fallbackCopy(text, callback) {
    const ta = document.createElement('textarea');
    ta.value = text;
    ta.style.position = 'fixed';
    ta.style.opacity = '0';
    document.body.appendChild(ta);
    ta.select();
    try {
      document.execCommand('copy');
      callback();
    } catch (e) {}
    document.body.removeChild(ta);
  }
}

// ============================================================
// CUSTOMER AUTHENTICATION (MOBILE LOGIN, REGISTER, & GUEST)
// ============================================================

const GrandAuth = {
  getUser() {
    try {
      const data = localStorage.getItem('grand_user');
      return data ? JSON.parse(data) : null;
    } catch (e) {
      return null;
    }
  },

  setUser(user) {
    try {
      localStorage.setItem('grand_user', JSON.stringify(user));
      window.dispatchEvent(new CustomEvent('grand:auth_change', { detail: user }));
    } catch (e) {}
  },

  // Database of registered accounts in localStorage
  getRegisteredUsers() {
    try {
      const data = localStorage.getItem('grand_registered_users');
      let list = data ? JSON.parse(data) : [];
      if (!list || list.length === 0) {
        // Pre-seed showroom test/admin accounts
        list = [
          {
            firstName: 'Gnanachandra',
            lastName: 'Patron',
            phone: '9052751666',
            pincode: '530016',
            email: 'gnanacreats@grandliving.in',
            hasVoucher: true,
            voucherCode: 'HAPPY500',
            voucherAmount: 500
          },
          {
            firstName: 'Showroom',
            lastName: 'Patron',
            phone: '9876543210',
            pincode: '530016',
            email: 'patron@grandliving.in',
            hasVoucher: true,
            voucherCode: 'HAPPY500',
            voucherAmount: 500
          }
        ];
        localStorage.setItem('grand_registered_users', JSON.stringify(list));
      }
      return list;
    } catch (e) {
      return [];
    }
  },

  findUserByMobile(mobile) {
    if (!mobile) return null;
    const cleanMobile = String(mobile).replace(/\D/g, '').slice(-10);
    const list = this.getRegisteredUsers();
    return list.find(u => {
      const uPhone = String(u.phone).replace(/\D/g, '').slice(-10);
      return uPhone === cleanMobile;
    }) || null;
  },

  registerUser(user) {
    const list = this.getRegisteredUsers();
    const cleanMobile = String(user.phone).replace(/\D/g, '').slice(-10);
    const existingIndex = list.findIndex(u => String(u.phone).replace(/\D/g, '').slice(-10) === cleanMobile);
    
    if (existingIndex >= 0) {
      list[existingIndex] = { ...list[existingIndex], ...user };
    } else {
      list.push(user);
    }
    localStorage.setItem('grand_registered_users', JSON.stringify(list));
    this.setUser(user);
  },

  logout() {
    try {
      localStorage.removeItem('grand_user');
      window.dispatchEvent(new CustomEvent('grand:auth_change', { detail: null }));
      if (window.GrandFurniture?.showToast) {
        window.GrandFurniture.showToast('You have been signed out.', 'info');
      }
    } catch (e) {}
  }
};

function initCustomerAuth() {
  // 1. Ensure Auth Modal exists in DOM
  let modalOverlay = document.getElementById('customerAuthModal');
  if (!modalOverlay) {
    modalOverlay = document.createElement('div');
    modalOverlay.id = 'customerAuthModal';
    modalOverlay.className = 'auth-modal-overlay';
    modalOverlay.setAttribute('aria-hidden', 'true');
    modalOverlay.innerHTML = `
      <div class="auth-modal-container" role="dialog" aria-modal="true" aria-labelledby="authModalHeading">
        <button type="button" class="auth-modal-close" id="authModalClose" aria-label="Close modal">&times;</button>
        
        <!-- Left: Royaloak HAPPY500 Promo Image Banner -->
        <div class="auth-modal-promo">
          <div>
            <div class="auth-promo-img-wrap">
              <img src="/assets/images/signup-redeem-500.jpg" alt="Sign Up and Redeem 500 on First Purchase with code HAPPY500">
            </div>
            <div style="background:rgba(213,190,154,0.12);border:1px dashed #D5BE9A;border-radius:8px;padding:12px;text-align:center;">
              <span style="font-size:0.7rem;color:#B7AA98;text-transform:uppercase;letter-spacing:0.1em;display:block;">FIRST PURCHASE BENEFIT</span>
              <strong style="font-family:'Cormorant Garamond',serif;font-size:1.4rem;color:#F5F0E8;display:block;">REDEEM &#8377;500/- OFF</strong>
              <span style="font-size:0.75rem;color:#D5BE9A;font-weight:700;">CODE: HAPPY500 &bull; Min Spend &#8377;25k</span>
            </div>
          </div>
          <ul class="auth-promo-features" style="margin-top:16px;">
            <li><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg> 48-Hour White Glove Vizag Delivery</li>
            <li><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg> 10-Year Solid Teak Frame Warranty</li>
            <li><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg> 0% Interest Free EMI on 500+ Pieces</li>
          </ul>
        </div>

        <!-- Right: Mobile Login / Register Forms -->
        <div class="auth-modal-form-wrap">
          <div class="auth-tabs">
            <button type="button" class="auth-tab-btn is-active" id="tabLoginBtn" data-target="loginFormBox">Sign In</button>
            <button type="button" class="auth-tab-btn" id="tabRegisterBtn" data-target="registerFormBox">Create Account</button>
          </div>

          <!-- TAB 1: MOBILE LOGIN -->
          <div id="loginFormBox">
            <h3 class="auth-form-title" id="authModalHeading">Customer Sign In</h3>
            <p class="auth-form-sub">Enter your 10-digit mobile number to access saved orders and unlocked offers.</p>

            <form id="customerLoginForm">
              <div class="auth-field-group">
                <label class="auth-label" for="loginMobile">Mobile Number *</label>
                <div class="auth-input-wrap">
                  <span class="auth-prefix">+91</span>
                  <input type="tel" id="loginMobile" class="auth-input has-prefix" required pattern="[0-9]{10}" maxlength="10" placeholder="90527 51666">
                </div>
              </div>

              <!-- OTP simulation section (hidden by default) -->
              <div class="auth-field-group" id="loginOtpWrap" style="display:none;">
                <label class="auth-label" for="loginOtp">Enter 4-Digit OTP *</label>
                <input type="text" id="loginOtp" class="auth-input" maxlength="4" placeholder="e.g. 8492 (Demo OTP: 8492)">
                <span style="font-size:0.72rem;color:#25D366;display:block;margin-top:4px;">OTP sent to your mobile. Enter 8492 to verify.</span>
              </div>

              <button type="submit" class="auth-submit-btn" id="loginSubmitBtn">Request OTP / Sign In</button>
            </form>

            <div class="auth-guest-divider">OR</div>

            <button type="button" class="auth-guest-btn" id="modalGuestBtn">
              <span>⚡ Continue as Guest (Buy Without Login)</span>
            </button>
          </div>

          <!-- TAB 2: REGISTER -->
          <div id="registerFormBox" style="display:none;">
            <h3 class="auth-form-title">Create New Account</h3>
            <p class="auth-form-sub">Sign up &amp; instantly unlock &#8377;500/- voucher (Code: HAPPY500).</p>

            <form id="customerRegisterForm">
              <div class="auth-grid-2">
                <div class="auth-field-group">
                  <label class="auth-label" for="regFirstName">First Name *</label>
                  <input type="text" id="regFirstName" class="auth-input" required placeholder="e.g. Rahul">
                </div>
                <div class="auth-field-group">
                  <label class="auth-label" for="regLastName">Last Name *</label>
                  <input type="text" id="regLastName" class="auth-input" required placeholder="e.g. Sharma">
                </div>
              </div>

              <div class="auth-field-group">
                <label class="auth-label" for="regMobile">Mobile Number *</label>
                <div class="auth-input-wrap">
                  <span class="auth-prefix">+91</span>
                  <input type="tel" id="regMobile" class="auth-input has-prefix" required pattern="[0-9]{10}" maxlength="10" placeholder="98765 43210">
                </div>
              </div>

              <div class="auth-grid-2">
                <div class="auth-field-group">
                  <label class="auth-label" for="regPincode">Pincode *</label>
                  <input type="text" id="regPincode" class="auth-input" required pattern="[0-9]{6}" maxlength="6" placeholder="530016">
                </div>
                <div class="auth-field-group">
                  <label class="auth-label" for="regEmail">Email ID *</label>
                  <input type="email" id="regEmail" class="auth-input" required placeholder="rahul@example.com">
                </div>
              </div>

              <div style="background:rgba(213,190,154,0.08);border:1px dashed rgba(213,190,154,0.3);border-radius:6px;padding:8px 12px;margin-bottom:12px;display:flex;align-items:center;gap:8px;">
                <input type="checkbox" id="regApplyCoupon" checked style="accent-color:#D5BE9A;width:16px;height:16px;">
                <label for="regApplyCoupon" style="font-size:0.75rem;color:#F5F0E8;cursor:pointer;">
                  Apply <strong>HAPPY500</strong> (&#8377;500 off on first purchase &ge; &#8377;25,000)
                </label>
              </div>

              <button type="submit" class="auth-submit-btn">Create Account &amp; Claim &#8377;500</button>
            </form>

            <div class="auth-guest-divider">OR</div>

            <button type="button" class="auth-guest-btn" id="modalGuestBtn2">
              <span>⚡ Continue as Guest (Buy Without Login)</span>
            </button>
          </div>

        </div>
      </div>
    `;
    document.body.appendChild(modalOverlay);
  }

  // 2. Setup Open/Close and Switcher
  const closeBtn = document.getElementById('authModalClose');
  const tabLoginBtn = document.getElementById('tabLoginBtn');
  const tabRegisterBtn = document.getElementById('tabRegisterBtn');
  const loginBox = document.getElementById('loginFormBox');
  const regBox = document.getElementById('registerFormBox');
  const guestBtn1 = document.getElementById('modalGuestBtn');
  const guestBtn2 = document.getElementById('modalGuestBtn2');

  function openModal(tab = 'login', guestCallback = null) {
    window._authGuestCallback = guestCallback;
    modalOverlay.style.display = 'flex';
    setTimeout(() => modalOverlay.classList.add('is-active'), 10);
    modalOverlay.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';

    if (tab === 'register') {
      tabRegisterBtn?.click();
    } else {
      tabLoginBtn?.click();
    }
  }

  function closeModal() {
    modalOverlay.classList.remove('is-active');
    modalOverlay.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
    setTimeout(() => {
      modalOverlay.style.display = 'none';
    }, 250);
  }

  closeBtn?.addEventListener('click', closeModal);
  modalOverlay?.addEventListener('click', (e) => {
    if (e.target === modalOverlay) closeModal();
  });
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && modalOverlay.classList.contains('is-active')) {
      closeModal();
    }
  });

  // Tab switching
  tabLoginBtn?.addEventListener('click', () => {
    tabLoginBtn.classList.add('is-active');
    tabRegisterBtn?.classList.remove('is-active');
    if (loginBox) loginBox.style.display = 'block';
    if (regBox) regBox.style.display = 'none';
  });

  tabRegisterBtn?.addEventListener('click', () => {
    tabRegisterBtn.classList.add('is-active');
    tabLoginBtn?.classList.remove('is-active');
    if (loginBox) loginBox.style.display = 'none';
    if (regBox) regBox.style.display = 'block';
  });

  // Continue as Guest handler
  function handleGuest() {
    closeModal();
    if (typeof window._authGuestCallback === 'function') {
      window._authGuestCallback();
      window._authGuestCallback = null;
    } else if (window.GrandFurniture?.showToast) {
      window.GrandFurniture.showToast('Continuing as Guest. You can buy any piece directly!', 'info');
    }
  }
  guestBtn1?.addEventListener('click', handleGuest);
  guestBtn2?.addEventListener('click', handleGuest);

  // 3. Login Form Submit
  const loginForm = document.getElementById('customerLoginForm');
  const loginMobile = document.getElementById('loginMobile');
  const loginOtpWrap = document.getElementById('loginOtpWrap');
  const loginOtp = document.getElementById('loginOtp');
  const loginSubmitBtn = document.getElementById('loginSubmitBtn');

  let otpRequested = false;

  loginForm?.addEventListener('submit', (e) => {
    e.preventDefault();
    const phone = loginMobile?.value.trim().replace(/\D/g, '').slice(-10);
    if (!phone || phone.length !== 10) {
      alert('Please enter a valid 10-digit mobile number.');
      return;
    }

    // CHECK IF USER HAS AN ACCOUNT
    const registeredUser = GrandAuth.findUserByMobile(phone);
    if (!registeredUser) {
      alert(`⚠️ No account found with mobile number +91 ${phone}!\n\nOnly registered customers can sign in. Please create an account first to unlock ₹500 off (Code: HAPPY500).`);
      if (window.GrandFurniture?.showToast) {
        window.GrandFurniture.showToast(`No account found for +91 ${phone}. Please create an account first!`, 'error');
      }
      // Reset OTP view and flip to registration tab
      otpRequested = false;
      if (loginOtpWrap) loginOtpWrap.style.display = 'none';
      if (loginSubmitBtn) loginSubmitBtn.textContent = 'Request OTP / Sign In';
      if (tabRegisterBtn) {
        tabRegisterBtn.click();
        const regPh = document.getElementById('regMobile');
        if (regPh) regPh.value = phone;
      }
      return;
    }

    if (!otpRequested) {
      otpRequested = true;
      loginOtpWrap.style.display = 'block';
      loginOtp.value = '8492';
      loginSubmitBtn.textContent = `Verify OTP & Sign In as ${registeredUser.firstName}`;
      if (window.GrandFurniture?.showToast) {
        window.GrandFurniture.showToast(`OTP sent to +91 ${phone}! Auto-filled demo OTP: 8492`, 'success');
      }
      return;
    }

    // Verify OTP
    const enteredOtp = loginOtp.value.trim();
    if (enteredOtp !== '8492' && enteredOtp.length !== 4) {
      alert('Please enter a valid 4-digit OTP.');
      return;
    }

    // Sign into registered account
    GrandAuth.setUser(registeredUser);
    closeModal();

    if (window.GrandFurniture?.showToast) {
      window.GrandFurniture.showToast(`Welcome back, ${registeredUser.firstName}! Signed in to your account.`, 'success');
    }

    if (typeof window._authGuestCallback === 'function') {
      window._authGuestCallback(registeredUser);
      window._authGuestCallback = null;
    }
  });

  // 4. Registration Form Submit
  const regForm = document.getElementById('customerRegisterForm');
  regForm?.addEventListener('submit', (e) => {
    e.preventDefault();
    const fn = document.getElementById('regFirstName')?.value.trim();
    const ln = document.getElementById('regLastName')?.value.trim();
    const ph = document.getElementById('regMobile')?.value.trim().replace(/\D/g, '').slice(-10);
    const pin = document.getElementById('regPincode')?.value.trim();
    const em = document.getElementById('regEmail')?.value.trim();

    if (!fn || !ln || !ph || !pin || !em) {
      alert('Please fill out all required fields.');
      return;
    }
    if (ph.length !== 10) {
      alert('Please enter a valid 10-digit mobile number.');
      return;
    }
    if (pin.length !== 6) {
      alert('Please enter a valid 6-digit Visakhapatnam/India pincode.');
      return;
    }

    const newUser = {
      firstName: fn,
      lastName: ln,
      phone: ph,
      pincode: pin,
      email: em,
      hasVoucher: true,
      voucherCode: 'HAPPY500',
      voucherAmount: 500,
      registeredAt: new Date().toISOString()
    };
    GrandAuth.registerUser(newUser);
    closeModal();

    if (window.GrandFurniture?.showToast) {
      window.GrandFurniture.showToast(`Account created for ${fn}! ₹500 First Order Voucher (HAPPY500) Unlocked!`, 'success');
    }

    if (typeof window._authGuestCallback === 'function') {
      window._authGuestCallback(newUser);
      window._authGuestCallback = null;
    }
  });

  // 5. Update Navbar User UI
  function syncNavUserUI() {
    const user = GrandAuth.getUser();
    const userBtns = document.querySelectorAll('.nav__user-btn');
    const userNames = document.querySelectorAll('.nav__user-name');
    const userMenus = document.querySelectorAll('.nav__user-menu');

    if (user) {
      userNames.forEach(span => {
        span.textContent = `Hi, ${user.firstName}`;
      });
      userBtns.forEach(btn => {
        btn.setAttribute('title', `Logged in as ${user.firstName} (+91 ${user.phone})`);
        btn.onclick = (e) => {
          e.preventDefault();
          e.stopPropagation();
          const menu = btn.parentElement.querySelector('.nav__user-menu');
          if (menu) {
            menu.classList.toggle('is-active');
          }
        };
      });
    } else {
      userNames.forEach(span => {
        span.textContent = 'Sign In';
      });
      userBtns.forEach(btn => {
        btn.setAttribute('title', 'Sign In or Register');
        btn.onclick = (e) => {
          e.preventDefault();
          openModal('login');
        };
      });
    }
  }

  // Close dropdown on outside click
  document.addEventListener('click', (e) => {
    if (!e.target.closest('.nav__user-btn') && !e.target.closest('.nav__user-menu')) {
      document.querySelectorAll('.nav__user-menu').forEach(m => m.classList.remove('is-active'));
    }
  });

  window.addEventListener('grand:auth_change', syncNavUserUI);
  syncNavUserUI();

  // Expose global methods
  window.GrandAuth = {
    openModal,
    closeModal,
    getUser: GrandAuth.getUser,
    setUser: GrandAuth.setUser,
    logout: GrandAuth.logout
  };
}


// ============================================================
// PRODUCT BUY & GUEST CHECKOUT FLOW
// ============================================================

function initProductBuyFlow() {
  document.querySelectorAll(".btn-buy-now, .btn-buy-piece").forEach(btn => {
    btn.addEventListener("click", (e) => {
      e.preventDefault();
      const prodName = btn.getAttribute("data-product") || document.querySelector("h1")?.textContent.trim() || "Luxury Furniture Piece";
      const prodPrice = btn.getAttribute("data-price") || "";

      const proceedWithPurchase = (customer = null) => {
        let text = `🏛️ *Grand Living Vizag — Order & Reservation Request*\n\n`;
        text += `• *Piece:* ${prodName}\n`;
        if (prodPrice) text += `• *Price:* ${prodPrice}\n`;

        if (customer && customer.firstName) {
          text += `• *Customer:* ${customer.firstName} ${customer.lastName || ""}\n`;
          text += `• *Phone:* +91 ${customer.phone}\n`;
          text += `• *Pincode:* ${customer.pincode}\n`;
          if (customer.hasVoucher) {
            text += `• *Coupon Applied:* HAPPY500 (-₹500 First Order Discount)\n`;
          }
        } else {
          text += `• *Order Mode:* Direct Guest Order (No Account Required)\n`;
        }
        text += `\nPlease confirm stock availability and delivery schedule across Visakhapatnam!`;
        window.open(`https://wa.me/919052751666?text=${encodeURIComponent(text)}`, "_blank");
      };

      const user = window.GrandAuth?.getUser();
      if (user) {
        proceedWithPurchase(user);
      } else {
        window.GrandAuth?.openModal("login", (customer) => {
          proceedWithPurchase(customer);
        });
      }
    });
  });
}

// ============================================================
// INIT
// ============================================================

document.addEventListener('DOMContentLoaded', () => {
  initNavigation();
  initMobileMenu();
  initSearch();
  initFlashMessages();
  initForms();
  initQuickEnquiryToWhatsApp();
  initProductGallery();
  initLightbox();
  initCatalogueFilters();
  initWhatsApp();
  initScrollProgress();
  initRevealObserver();
  initCountdownTimer();
  initHeroSlider();
  initInternationalCollections();
  initEmiCalculator();
  initWishlistToggle();
  initBlindfoldOffers();
  initCustomerAuth();
  initProductBuyFlow();
});

// Export utilities for use in other modules
window.GrandFurniture = {
  debounce,
  throttle,
  getCookie,
  setCookie,
  formatNumber,
  generateCSRFToken,
  showToast,
};
