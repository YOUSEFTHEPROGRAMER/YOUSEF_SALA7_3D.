/* =====================================================================
   YOUSEF SALA7 - PRODUCTION APPLICATION
   Modular JavaScript Framework
   ===================================================================== */

// =====================================================================
// CORE MODULE SYSTEM
// =====================================================================

/**
 * Core App Module System
 * Provides modular architecture for managing application state and logic
 */
const AppModules = (() => {
  const modules = {};

  return {
    /**
     * Register a module
     * @param {string} name - Module name
     * @param {function} definition - Module definition function
     */
    register(name, definition) {
      if (modules[name]) {
        console.warn(`Module ${name} already exists`);
        return;
      }
      modules[name] = definition();
      console.log(`✓ Module registered: ${name}`);
    },

    /**
     * Get a registered module
     * @param {string} name - Module name
     * @returns {object} Module instance
     */
    get(name) {
      if (!modules[name]) {
        console.error(`Module ${name} not found`);
        return null;
      }
      return modules[name];
    },

    /**
     * List all registered modules
     */
    list() {
      return Object.keys(modules);
    }
  };
})();

// =====================================================================
// UTILITY FUNCTIONS
// =====================================================================

const Utils = {
  /**
   * Debounce function
   */
  debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
      const later = () => {
        clearTimeout(timeout);
        func(...args);
      };
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
    };
  },

  /**
   * Throttle function
   */
  throttle(func, limit) {
    let inThrottle;
    return function(...args) {
      if (!inThrottle) {
        func.apply(this, args);
        inThrottle = true;
        setTimeout(() => inThrottle = false, limit);
      }
    };
  },

  /**
   * Get element with safety check
   */
  querySelector(selector) {
    try {
      return document.querySelector(selector);
    } catch (error) {
      console.error(`Error selecting ${selector}:`, error);
      return null;
    }
  },

  /**
   * Get all elements matching selector
   */
  querySelectorAll(selector) {
    try {
      return document.querySelectorAll(selector);
    } catch (error) {
      console.error(`Error selecting ${selector}:`, error);
      return [];
    }
  },

  /**
   * Add event listener with error handling
   */
  addEventListener(element, event, handler) {
    if (!element) return;
    element.addEventListener(event, handler);
  },

  /**
   * Remove event listener
   */
  removeEventListener(element, event, handler) {
    if (!element) return;
    element.removeEventListener(event, handler);
  },

  /**
   * Check if element is in viewport
   */
  isInViewport(element) {
    const rect = element.getBoundingClientRect();
    return (
      rect.top < window.innerHeight &&
      rect.left < window.innerWidth &&
      rect.bottom > 0 &&
      rect.right > 0
    );
  },

  /**
   * Smooth scroll to element
   */
  smoothScrollTo(element) {
    if (!element) return;
    element.scrollIntoView({ behavior: 'smooth', block: 'start' });
  },

  /**
   * Add class with animation frame
   */
  addClass(element, className) {
    if (element && !element.classList.contains(className)) {
      element.classList.add(className);
    }
  },

  /**
   * Remove class with animation frame
   */
  removeClass(element, className) {
    if (element && element.classList.contains(className)) {
      element.classList.remove(className);
    }
  },

  /**
   * Toggle class
   */
  toggleClass(element, className) {
    if (element) {
      element.classList.toggle(className);
    }
  },

  /**
   * Has class check
   */
  hasClass(element, className) {
    return element && element.classList.contains(className);
  },

  /**
   * Set multiple attributes
   */
  setAttributes(element, attributes) {
    if (!element) return;
    Object.entries(attributes).forEach(([key, value]) => {
      element.setAttribute(key, value);
    });
  },

  /**
   * Wait for condition
   */
  waitFor(condition, timeout = 5000) {
    return new Promise((resolve, reject) => {
      const start = Date.now();
      const check = () => {
        if (condition()) {
          resolve();
        } else if (Date.now() - start > timeout) {
          reject(new Error('Timeout waiting for condition'));
        } else {
          requestAnimationFrame(check);
        }
      };
      check();
    });
  },

  /**
   * Delay/sleep promise
   */
  delay(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
  },

  /**
   * Format date
   */
  formatDate(date) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(date).toLocaleDateString('en-US', options);
  },

  /**
   * Validate email
   */
  validateEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
  },

  /**
   * Validate phone
   */
  validatePhone(phone) {
    return /^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/.test(phone);
  },

  /**
   * Get random item from array
   */
  getRandomItem(array) {
    return array[Math.floor(Math.random() * array.length)];
  }
};

// =====================================================================
// LOCAL STORAGE SERVICE
// =====================================================================

AppModules.register('storage', () => {
  const PREFIX = 'app_';

  return {
    /**
     * Set item in storage
     */
    setItem(key, value) {
      try {
        localStorage.setItem(PREFIX + key, JSON.stringify(value));
      } catch (error) {
        console.error('Storage error:', error);
      }
    },

    /**
     * Get item from storage
     */
    getItem(key, defaultValue = null) {
      try {
        const item = localStorage.getItem(PREFIX + key);
        return item ? JSON.parse(item) : defaultValue;
      } catch (error) {
        console.error('Storage error:', error);
        return defaultValue;
      }
    },

    /**
     * Remove item from storage
     */
    removeItem(key) {
      try {
        localStorage.removeItem(PREFIX + key);
      } catch (error) {
        console.error('Storage error:', error);
      }
    },

    /**
     * Clear all storage
     */
    clear() {
      try {
        for (let key in localStorage) {
          if (key.startsWith(PREFIX)) {
            localStorage.removeItem(key);
          }
        }
      } catch (error) {
        console.error('Storage error:', error);
      }
    }
  };
});

// =====================================================================
// THEME MANAGER
// ===================================================================== 

AppModules.register('theme', () => {
  const THEME_KEY = 'theme';
  const THEMES = {
    DARK: 'dark',
    LIGHT: 'light'
  };

  return {
    /**
     * Initialize theme
     */
    init() {
      const storage = AppModules.get('storage');
      const savedTheme = storage.getItem(THEME_KEY);
      const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
      const theme = savedTheme || (prefersDark ? THEMES.DARK : THEMES.LIGHT);
      this.set(theme);
    },

    /**
     * Set theme
     */
    set(theme) {
      if (!Object.values(THEMES).includes(theme)) return;
      document.documentElement.setAttribute('data-theme', theme);
      AppModules.get('storage').setItem(THEME_KEY, theme);
      this.updateThemeToggle();
    },

    /**
     * Toggle theme
     */
    toggle() {
      const current = document.documentElement.getAttribute('data-theme');
      const next = current === THEMES.DARK ? THEMES.LIGHT : THEMES.DARK;
      this.set(next);
    },

    /**
     * Get current theme
     */
    getCurrent() {
      return document.documentElement.getAttribute('data-theme') || THEMES.DARK;
    },

    /**
     * Update toggle button
     */
    updateThemeToggle() {
      const toggle = Utils.querySelector('#themeToggle');
      if (toggle) {
        const isDark = this.getCurrent() === THEMES.DARK;
        Utils.setAttributes(toggle, { 'aria-pressed': isDark });
      }
    }
  };
});

// =====================================================================
// NAVIGATION MANAGER
// =====================================================================

AppModules.register('navigation', () => {
  let isScrolling = false;

  return {
    /**
     * Initialize navigation
     */
    init() {
      this.setupMobileMenu();
      this.setupSmoothScroll();
      this.setupNavigation();
    },

    /**
     * Setup mobile menu
     */
    setupMobileMenu() {
      const menuToggle = Utils.querySelector('#menuToggle');
      const navMenu = Utils.querySelector('#navMenu');

      if (!menuToggle || !navMenu) return;

      Utils.addEventListener(menuToggle, 'click', () => {
        const isOpen = Utils.hasClass(navMenu, 'open');
        this.setMenuState(!isOpen);
      });

      // Close menu on nav link click
      const navLinks = Utils.querySelectorAll('.nav-link');
      navLinks.forEach(link => {
        Utils.addEventListener(link, 'click', () => {
          this.setMenuState(false);
        });
      });

      // Close menu on escape
      Utils.addEventListener(document, 'keydown', (e) => {
        if (e.key === 'Escape') {
          this.setMenuState(false);
        }
      });
    },

    /**
     * Set menu state
     */
    setMenuState(isOpen) {
      const menuToggle = Utils.querySelector('#menuToggle');
      const navMenu = Utils.querySelector('#navMenu');

      if (!menuToggle || !navMenu) return;

      if (isOpen) {
        Utils.addClass(navMenu, 'open');
        Utils.addClass(menuToggle, 'open');
        Utils.setAttributes(menuToggle, { 'aria-expanded': 'true' });
      } else {
        Utils.removeClass(navMenu, 'open');
        Utils.removeClass(menuToggle, 'open');
        Utils.setAttributes(menuToggle, { 'aria-expanded': 'false' });
      }
    },

    /**
     * Setup smooth scroll
     */
    setupSmoothScroll() {
      const navLinks = Utils.querySelectorAll('[data-section]');
      
      navLinks.forEach(link => {
        Utils.addEventListener(link, 'click', (e) => {
          e.preventDefault();
          const target = link.getAttribute('href');
          const element = Utils.querySelector(target);
          if (element) {
            Utils.smoothScrollTo(element);
            this.updateActiveNav(target);
          }
        });
      });
    },

    /**
     * Setup navigation
     */
    setupNavigation() {
      Utils.addEventListener(window, 'scroll', Utils.throttle(() => {
        this.updateScrollPosition();
      }, 100));
    },

    /**
     * Update scroll position
     */
    updateScrollPosition() {
      const sections = Utils.querySelectorAll('section[id]');
      let currentSection = null;

      sections.forEach(section => {
        const rect = section.getBoundingClientRect();
        if (rect.top <= 100) {
          currentSection = section.id;
        }
      });

      if (currentSection) {
        this.updateActiveNav(`#${currentSection}`);
        this.updateHeader();
      }
    },

    /**
     * Update active navigation
     */
    updateActiveNav(target) {
      const navLinks = Utils.querySelectorAll('[data-section]');
      navLinks.forEach(link => {
        if (link.getAttribute('href') === target) {
          Utils.addClass(link, 'active');
        } else {
          Utils.removeClass(link, 'active');
        }
      });
    },

    /**
     * Update header on scroll
     */
    updateHeader() {
      const header = Utils.querySelector('#header');
      if (!header) return;

      if (window.scrollY > 50) {
        Utils.addClass(header, 'scrolled');
      } else {
        Utils.removeClass(header, 'scrolled');
      }
    }
  };
});

// =====================================================================
// LOADING SCREEN MANAGER
// =====================================================================

AppModules.register('loading', () => {
  return {
    /**
     * Initialize loading screen
     */
    init() {
      this.hide();
    },

    /**
     * Show loading screen
     */
    show() {
      const screen = Utils.querySelector('#loadingScreen');
      if (screen) {
        Utils.removeClass(screen, 'hidden');
      }
    },

    /**
     * Hide loading screen
     */
    hide() {
      const screen = Utils.querySelector('#loadingScreen');
      if (screen) {
        Utils.addClass(screen, 'hidden');
      }
    },

    /**
     * Hide after delay
     */
    hideAfter(delay) {
      setTimeout(() => this.hide(), delay);
    }
  };
});

// =====================================================================
// ANIMATION ON SCROLL
// =====================================================================

AppModules.register('animations', () => {
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('aos-animate');
        observer.unobserve(entry.target);
      }
    });
  }, {
    threshold: 0.1,
    rootMargin: '50px'
  });

  return {
    /**
     * Initialize animations
     */
    init() {
      this.observeElements();
    },

    /**
     * Observe elements for animation
     */
    observeElements() {
      const elements = Utils.querySelectorAll('[data-aos]');
      elements.forEach(el => observer.observe(el));
    },

    /**
     * Add animation to element
     */
    addAnimation(element, animation) {
      if (element) {
        Utils.setAttributes(element, { 'data-aos': animation });
        observer.observe(element);
      }
    }
  };
});

// =====================================================================
// CUSTOM CURSOR
// =====================================================================

AppModules.register('cursor', () => {
  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const supportsHover = window.matchMedia('(hover: hover)').matches;

  let mouseX = 0;
  let mouseY = 0;
  let cursorX = 0;
  let cursorY = 0;

  return {
    /**
     * Initialize cursor
     */
    init() {
      if (prefersReducedMotion || !supportsHover) return;

      const cursor = Utils.querySelector('.cursor');
      const cursorAura = Utils.querySelector('.cursor-aura');

      if (!cursor || !cursorAura) return;

      this.setupMouseTracking(cursor, cursorAura);
      this.setupInteractiveElements();
    },

    /**
     * Setup mouse tracking
     */
    setupMouseTracking(cursor, cursorAura) {
      Utils.addEventListener(document, 'mousemove', (e) => {
        mouseX = e.clientX;
        mouseY = e.clientY;

        cursor.style.left = mouseX - 5 + 'px';
        cursor.style.top = mouseY - 5 + 'px';

        this.updateAura(cursorAura);
      });
    },

    /**
     * Update aura position
     */
    updateAura(aura) {
      cursorX += (mouseX - cursorX - 20) * 0.1;
      cursorY += (mouseY - cursorY - 20) * 0.1;

      aura.style.left = cursorX + 'px';
      aura.style.top = cursorY + 'px';

      requestAnimationFrame(() => this.updateAura(aura));
    },

    /**
     * Setup interactive elements
     */
    setupInteractiveElements() {
      const interactiveElements = Utils.querySelectorAll('a, button, .portfolio-item, .project-card, .service-card');
      const cursor = Utils.querySelector('.cursor');
      const aura = Utils.querySelector('.cursor-aura');

      if (!cursor || !aura) return;

      interactiveElements.forEach(el => {
        Utils.addEventListener(el, 'mouseenter', () => {
          cursor.style.transform = 'scale(2.5)';
          aura.style.width = '70px';
          aura.style.height = '70px';
        });

        Utils.addEventListener(el, 'mouseleave', () => {
          cursor.style.transform = 'scale(1)';
          aura.style.width = '40px';
          aura.style.height = '40px';
        });
      });
    }
  };
});

// =====================================================================
// ANIMATED BACKGROUND CANVAS
// =====================================================================

AppModules.register('background', () => {
  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  return {
    /**
     * Initialize background
     */
    init() {
      if (prefersReducedMotion) return;
      this.setupCanvas();
    },

    /**
     * Setup canvas animation
     */
    setupCanvas() {
      const canvas = Utils.querySelector('#bgCanvas');
      if (!canvas) return;

      const ctx = canvas.getContext('2d');
      let width = canvas.width = window.innerWidth;
      let height = canvas.height = window.innerHeight;
      let time = 0;

      // Responsive canvas
      Utils.addEventListener(window, 'resize', () => {
        width = canvas.width = window.innerWidth;
        height = canvas.height = window.innerHeight;
      });

      // Skip for small screens
      if (width < 768) {
        canvas.style.display = 'none';
        return;
      }

      const orbs = [
        { x: 0.15, y: 0.3, r: 0.35, color: [42, 130, 255], speed: 0.0003 },
        { x: 0.8, y: 0.2, r: 0.28, color: [123, 97, 255], speed: 0.0004 },
        { x: 0.5, y: 0.7, r: 0.22, color: [0, 229, 255], speed: 0.0002 }
      ];

      const animate = () => {
        time++;
        ctx.clearRect(0, 0, width, height);

        orbs.forEach((orb, i) => {
          const ox = (orb.x + Math.sin(time * orb.speed + i * 2) * 0.12) * width;
          const oy = (orb.y + Math.cos(time * orb.speed * 1.3 + i) * 0.1) * height;
          const radius = orb.r * Math.min(width, height);

          const grad = ctx.createRadialGradient(ox, oy, 0, ox, oy, radius);
          const [r, g, b] = orb.color;

          grad.addColorStop(0, `rgba(${r},${g},${b},0.09)`);
          grad.addColorStop(1, `rgba(${r},${g},${b},0)`);

          ctx.fillStyle = grad;
          ctx.fillRect(0, 0, width, height);
        });

        requestAnimationFrame(animate);
      };

      animate();
    }
  };
});

// =====================================================================
// PARTICLES SYSTEM
// =====================================================================

AppModules.register('particles', () => {
  return {
    /**
     * Initialize particles
     */
    init() {
      if (window.innerWidth < 768) return;
      this.createParticles();
    },

    /**
     * Create particles
     */
    createParticles() {
      const container = Utils.querySelector('#particlesContainer');
      if (!container) return;

      const particleCount = Math.min(30, Math.floor(window.innerWidth / 100));

      for (let i = 0; i < particleCount; i++) {
        const particle = document.createElement('div');
        particle.className = 'particle';

        const x = Math.random() * window.innerWidth;
        const y = Math.random() * window.innerHeight;
        const duration = 10 + Math.random() * 15;
        const delay = Math.random() * 5;

        particle.style.left = x + 'px';
        particle.style.top = y + 'px';
        particle.style.animationDuration = duration + 's';
        particle.style.animationDelay = delay + 's';

        container.appendChild(particle);
      }
    }
  };
});

// =====================================================================
// PORTFOLIO FILTER
// =====================================================================

AppModules.register('portfolio', () => {
  return {
    /**
     * Initialize portfolio
     */
    init() {
      this.setupFilters();
    },

    /**
     * Setup filter buttons
     */
    setupFilters() {
      const filterButtons = Utils.querySelectorAll('.filter-btn');
      const portfolioItems = Utils.querySelectorAll('.portfolio-item');

      filterButtons.forEach(btn => {
        Utils.addEventListener(btn, 'click', () => {
          // Update active button
          filterButtons.forEach(b => Utils.removeClass(b, 'active'));
          Utils.addClass(btn, 'active');

          // Filter items
          const filter = btn.getAttribute('data-filter');
          this.filterItems(portfolioItems, filter);
        });
      });
    },

    /**
     * Filter portfolio items
     */
    filterItems(items, filter) {
      items.forEach(item => {
        const itemFilter = item.getAttribute('data-filter');

        if (filter === 'all' || itemFilter === filter) {
          item.style.opacity = '0';
          item.style.pointerEvents = 'none';
          Utils.delay(50).then(() => {
            item.style.display = 'block';
            Utils.delay(10).then(() => {
              item.style.opacity = '1';
              item.style.pointerEvents = 'auto';
              item.style.transition = 'opacity 0.3s ease';
            });
          });
        } else {
          item.style.opacity = '0';
          item.style.pointerEvents = 'none';
          Utils.delay(300).then(() => {
            item.style.display = 'none';
          });
        }
      });
    }
  };
});

// =====================================================================
// TESTIMONIALS CAROUSEL
// =====================================================================

AppModules.register('testimonials', () => {
  let currentIndex = 0;
  const testimonials = [];

  return {
    /**
     * Initialize testimonials
     */
    init() {
      this.setupCarousel();
    },

    /**
     * Setup carousel
     */
    setupCarousel() {
      const container = Utils.querySelector('#testimonialsCarousel');
      if (!container) return;

      const items = Utils.querySelectorAll('.testimonial-card');
      testimonials.push(...items);

      const prevBtn = Utils.querySelector('#prevTestimonial');
      const nextBtn = Utils.querySelector('#nextTestimonial');

      if (prevBtn) Utils.addEventListener(prevBtn, 'click', () => this.prev());
      if (nextBtn) Utils.addEventListener(nextBtn, 'click', () => this.next());

      // Auto rotate
      setInterval(() => this.next(), 8000);
    },

    /**
     * Show testimonial
     */
    show(index) {
      if (testimonials.length === 0) return;

      currentIndex = (index + testimonials.length) % testimonials.length;

      testimonials.forEach((item, i) => {
        if (i === currentIndex) {
          Utils.addClass(item, 'active');
          item.style.opacity = '1';
        } else {
          Utils.removeClass(item, 'active');
          item.style.opacity = '0.5';
        }
      });
    },

    /**
     * Next testimonial
     */
    next() {
      this.show(currentIndex + 1);
    },

    /**
     * Previous testimonial
     */
    prev() {
      this.show(currentIndex - 1);
    }
  };
});

// =====================================================================
// CONTACT FORM
// =====================================================================

AppModules.register('contact', () => {
  return {
    /**
     * Initialize contact form
     */
    init() {
      this.setupForm();
    },

    /**
     * Setup form
     */
    setupForm() {
      const form = Utils.querySelector('#contactForm');
      if (!form) return;

      Utils.addEventListener(form, 'submit', (e) => {
        e.preventDefault();
        this.handleSubmit(form);
      });

      // Real-time validation
      const inputs = form.querySelectorAll('input, select, textarea');
      inputs.forEach(input => {
        Utils.addEventListener(input, 'blur', () => {
          this.validateField(input);
        });
      });
    },

    /**
     * Validate field
     */
    validateField(field) {
      const value = field.value.trim();
      let isValid = true;
      let errorMessage = '';

      const name = field.name;
      const type = field.type;

      // Required check
      if (!value && field.hasAttribute('required')) {
        isValid = false;
        errorMessage = `${name.charAt(0).toUpperCase() + name.slice(1)} is required`;
      }

      // Email validation
      if (isValid && type === 'email' && value) {
        isValid = Utils.validateEmail(value);
        errorMessage = 'Please enter a valid email address';
      }

      // Update UI
      const formGroup = field.closest('.form-group');
      const errorEl = formGroup.querySelector('.error-message');

      if (isValid) {
        Utils.removeClass(formGroup, 'error');
        if (errorEl) errorEl.textContent = '';
      } else {
        Utils.addClass(formGroup, 'error');
        if (errorEl) errorEl.textContent = errorMessage;
      }

      return isValid;
    },

    /**
     * Validate entire form
     */
    validateForm(form) {
      const inputs = form.querySelectorAll('input[required], select[required], textarea[required], input[type="email"]');
      let isValid = true;

      inputs.forEach(input => {
        if (!this.validateField(input)) {
          isValid = false;
        }
      });

      return isValid;
    },

    /**
     * Handle form submission
     */
    async handleSubmit(form) {
      if (!this.validateForm(form)) {
        console.warn('Form validation failed');
        return;
      }

      const loading = Utils.querySelector('#formLoading');
      const message = Utils.querySelector('#formMessage');

      if (loading) loading.style.display = 'flex';
      if (message) message.style.display = 'none';

      try {
        // Collect form data
        const formData = new FormData(form);
        const data = Object.fromEntries(formData);

        // Simulate API call
        await Utils.delay(1500);

        // Success
        if (loading) loading.style.display = 'none';
        if (message) {
          message.className = 'form-message success';
          message.textContent = '✓ Message sent successfully! I\'ll get back to you within 24 hours.';
          message.style.display = 'block';
        }

        form.reset();

        // Hide message after 5 seconds
        setTimeout(() => {
          if (message) message.style.display = 'none';
        }, 5000);

      } catch (error) {
        console.error('Form submission error:', error);

        if (loading) loading.style.display = 'none';
        if (message) {
          message.className = 'form-message error';
          message.textContent = '✗ Error sending message. Please try again.';
          message.style.display = 'block';
        }
      }
    }
  };
});

// =====================================================================
// SKILL PROGRESS ANIMATION
// =====================================================================

AppModules.register('skills', () => {
  let animated = false;

  return {
    /**
     * Initialize skills
     */
    init() {
      this.observeSkillSection();
    },

    /**
     * Observe skill section
     */
    observeSkillSection() {
      const skillsSection = Utils.querySelector('.section-skills');
      if (!skillsSection) return;

      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting && !animated) {
            animated = true;
            this.animateSkills();
          }
        });
      }, { threshold: 0.5 });

      observer.observe(skillsSection);
    },

    /**
     * Animate skills
     */
    animateSkills() {
      const progressBars = Utils.querySelectorAll('.skill-progress');
      progressBars.forEach((bar) => {
        const progress = bar.style.getPropertyValue('--progress');
        if (progress) {
          bar.style.animation = `fillBar 1s ease-out forwards`;
        }
      });
    }
  };
});

// =====================================================================
// CTA BUTTONS
// =====================================================================

AppModules.register('cta', () => {
  return {
    /**
     * Initialize CTA buttons
     */
    init() {
      this.setupButtons();
    },

    /**
     * Setup CTA buttons
     */
    setupButtons() {
      const primaryCta = Utils.querySelector('#cta-primary');
      const contactCta = Utils.querySelector('#cta-contact');
      const portfolio = Utils.querySelector('#portfolio');
      const contact = Utils.querySelector('#contact');

      if (primaryCta && portfolio) {
        Utils.addEventListener(primaryCta, 'click', () => {
          Utils.smoothScrollTo(portfolio);
        });
      }

      if (contactCta && contact) {
        Utils.addEventListener(contactCta, 'click', () => {
          Utils.smoothScrollTo(contact);
        });
      }

      // View full portfolio button
      const viewPortfolioBtn = Utils.querySelector('button:has-text("View Full Portfolio")');
      if (viewPortfolioBtn && portfolio) {
        Utils.addEventListener(viewPortfolioBtn, 'click', () => {
          Utils.smoothScrollTo(portfolio);
        });
      }

      // Start project button
      const startProjectBtn = Array.from(Utils.querySelectorAll('button')).find(
        btn => btn.textContent.includes('Start a Project') || btn.textContent.includes('Begin Your Journey')
      );
      if (startProjectBtn && contact) {
        Utils.addEventListener(startProjectBtn, 'click', () => {
          Utils.smoothScrollTo(contact);
        });
      }
    }
  };
});

// =====================================================================
// PERFORMANCE MONITORING
// =====================================================================

AppModules.register('performance', () => {
  return {
    /**
     * Initialize performance monitoring
     */
    init() {
      this.logMetrics();
    },

    /**
     * Log performance metrics
     */
    logMetrics() {
      if (!window.performance) return;

      window.addEventListener('load', () => {
        const perfData = performance.timing;
        const pageLoadTime = perfData.loadEventEnd - perfData.navigationStart;

        console.log(`%c⚡ Performance Metrics`, 'color: #2A82FF; font-weight: bold;');
        console.log(`Page Load Time: ${pageLoadTime}ms`);
        console.log(`DOM Ready: ${perfData.domContentLoadedEventEnd - perfData.navigationStart}ms`);
        console.log(`Resource Load: ${perfData.loadEventEnd - perfData.domContentLoadedEventEnd}ms`);
      });
    }
  };
});

// =====================================================================
// SERVICE WORKER (PWA Support)
// =====================================================================

AppModules.register('pwa', () => {
  return {
    /**
     * Initialize PWA
     */
    init() {
      if ('serviceWorker' in navigator) {
        this.registerServiceWorker();
      }
    },

    /**
     * Register service worker
     */
    registerServiceWorker() {
      // Create inline service worker
      const swCode = `
        const CACHE_NAME = 'portfolio-v1';
        const urlsToCache = [
          '/',
          '/styles.css',
          '/app.js'
        ];

        self.addEventListener('install', event => {
          event.waitUntil(
            caches.open(CACHE_NAME)
              .then(cache => cache.addAll(urlsToCache))
          );
        });

        self.addEventListener('fetch', event => {
          event.respondWith(
            caches.match(event.request)
              .then(response => response || fetch(event.request))
              .catch(() => caches.match('/'))
          );
        });
      `;

      const blob = new Blob([swCode], { type: 'application/javascript' });
      const swUrl = URL.createObjectURL(blob);

      navigator.serviceWorker.register(swUrl).then(
        () => console.log('✓ Service Worker registered'),
        () => console.log('✗ Service Worker registration failed')
      );
    }
  };
});

// =====================================================================
// ANALYTICS
// =====================================================================

AppModules.register('analytics', () => {
  return {
    /**
     * Initialize analytics
     */
    init() {
      this.trackPageView();
      this.trackInteractions();
    },

    /**
     * Track page view
     */
    trackPageView() {
      const title = document.title;
      const url = window.location.href;
      console.log(`📊 Page View: ${title}`);
    },

    /**
     * Track interactions
     */
    trackInteractions() {
      const buttons = Utils.querySelectorAll('button:not(.theme-toggle):not(.menu-toggle)');
      buttons.forEach(btn => {
        Utils.addEventListener(btn, 'click', () => {
          console.log(`📊 Button Click: ${btn.textContent.trim()}`);
        });
      });

      const links = Utils.querySelectorAll('a[href^="#"]');
      links.forEach(link => {
        Utils.addEventListener(link, 'click', () => {
          console.log(`📊 Section Link: ${link.getAttribute('href')}`);
        });
      });
    },

    /**
     * Track event
     */
    trackEvent(category, action, label) {
      console.log(`📊 Event: ${category} > ${action} > ${label}`);
    }
  };
});

// =====================================================================
// MAIN APPLICATION
// =====================================================================

const App = (() => {
  return {
    /**
     * Initialize application
     */
    async init() {
      console.log('%c🚀 Initializing Yousef Sala7 Portfolio', 'color: #2A82FF; font-size: 14px; font-weight: bold;');

      try {
        // Core modules
        AppModules.get('loading').init();
        AppModules.get('theme').init();
        AppModules.get('storage').getItem('theme');

        // UI modules
        AppModules.get('navigation').init();
        AppModules.get('cursor').init();
        AppModules.get('background').init();
        AppModules.get('particles').init();

        // Feature modules
        AppModules.get('animations').init();
        AppModules.get('portfolio').init();
        AppModules.get('testimonials').init();
        AppModules.get('contact').init();
        AppModules.get('skills').init();
        AppModules.get('cta').init();

        // Utility modules
        AppModules.get('performance').init();
        AppModules.get('analytics').init();
        AppModules.get('pwa').init();

        // Hide loading screen
        AppModules.get('loading').hideAfter(500);

        console.log('%c✓ Application initialized successfully', 'color: #10B981; font-weight: bold;');
        console.log(`%c📦 Registered modules: ${AppModules.list().join(', ')}`, 'color: #8F9BB3; font-size: 12px;');

      } catch (error) {
        console.error('Application initialization error:', error);
        AppModules.get('loading').hide();
      }
    }
  };
})();

// =====================================================================
// DOCUMENT READY
// =====================================================================

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => App.init());
} else {
  App.init();
}

// =====================================================================
// GLOBAL ERROR HANDLING
// =====================================================================

window.addEventListener('error', (event) => {
  console.error('Global error:', event.error);
});

window.addEventListener('unhandledrejection', (event) => {
  console.error('Unhandled promise rejection:', event.reason);
});

// =====================================================================
// KEYBOARD SHORTCUTS
// ===================================================================== 

document.addEventListener('keydown', (e) => {
  // Cmd/Ctrl + K: Search (future feature)
  if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
    e.preventDefault();
    console.log('Search functionality coming soon');
  }

  // Cmd/Ctrl + Shift + D: Toggle theme
  if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'd') {
    e.preventDefault();
    AppModules.get('theme').toggle();
  }
});

// =====================================================================
// EXPORT FOR TESTING
// =====================================================================

if (typeof module !== 'undefined' && module.exports) {
  module.exports = { AppModules, Utils, App };
}
