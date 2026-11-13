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
  const toggle = document.querySelector('.nav__mobile-toggle');
  const overlay = document.querySelector('.mobile-menu');
  const closeBtn = document.querySelector('.mobile-menu__close');
  const menuItems = document.querySelectorAll('.mobile-menu__item');

  if (!toggle || !overlay) return;

  let isOpen = false;

  const openMenu = () => {
    isOpen = true;
    overlay.classList.add('is-open');
    toggle.setAttribute('aria-expanded', 'true');
    toggle.classList.add('is-active');
    document.body.classList.add('overflow-hidden');

    // Stagger menu items in
    menuItems.forEach((item, i) => {
      item.style.transitionDelay = `${0.05 + i * 0.06}s`;
      item.classList.add('is-visible');
    });

    // Trap focus
    overlay.setAttribute('aria-hidden', 'false');
    overlay.querySelector('a, button')?.focus();
  };

  const closeMenu = () => {
    isOpen = false;
    overlay.classList.remove('is-open');
    toggle.setAttribute('aria-expanded', 'false');
    toggle.classList.remove('is-active');
    document.body.classList.remove('overflow-hidden');

    menuItems.forEach((item) => {
      item.style.transitionDelay = '';
      item.classList.remove('is-visible');
    });

    overlay.setAttribute('aria-hidden', 'true');
    toggle.focus();
  };

  toggle.addEventListener('click', () => (isOpen ? closeMenu() : openMenu()));
  closeBtn?.addEventListener('click', closeMenu);

  // Close on outside click
  overlay.addEventListener('click', (e) => {
    if (e.target === overlay) closeMenu();
  });

  // Close on ESC
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && isOpen) closeMenu();
  });

  // Swipe-to-close (touch: swipe left)
  let touchStartX = 0;
  overlay.addEventListener('touchstart', (e) => {
    touchStartX = e.changedTouches[0].screenX;
  }, { passive: true });
  overlay.addEventListener('touchend', (e) => {
    const diff = touchStartX - e.changedTouches[0].screenX;
    if (diff > 60) closeMenu();
  }, { passive: true });
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
// INIT
// ============================================================

document.addEventListener('DOMContentLoaded', () => {
  initNavigation();
  initMobileMenu();
  initSearch();
  initFlashMessages();
  initForms();
  initProductGallery();
  initLightbox();
  initCatalogueFilters();
  initWhatsApp();
  initScrollProgress();
  initRevealObserver();
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
