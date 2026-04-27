document.addEventListener('DOMContentLoaded', () => {

  /* ============================================================
     THEME TOGGLE
  ============================================================ */
  const html = document.documentElement;
  const themeToggle = document.getElementById('themeToggle');
  const savedTheme = localStorage.getItem('theme') || 'dark';
  html.setAttribute('data-theme', savedTheme);

  themeToggle?.addEventListener('click', () => {
    const current = html.getAttribute('data-theme');
    const next = current === 'dark' ? 'light' : 'dark';
    html.setAttribute('data-theme', next);
    localStorage.setItem('theme', next);
  });

  /* ============================================================
     MOBILE MENU
  ============================================================ */
  const mobileMenuBtn = document.getElementById('mobileMenuBtn');
  const mobileNav = document.getElementById('mobileNav');
  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  function setMenuState(isOpen) {
    mobileMenuBtn?.classList.toggle('open', isOpen);
    mobileNav?.classList.toggle('open', isOpen);
    document.body.classList.toggle('menu-open', isOpen);
    if (mobileMenuBtn) {
      mobileMenuBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
      mobileMenuBtn.setAttribute('aria-label', isOpen ? 'Close navigation menu' : 'Open navigation menu');
    }
  }

  mobileMenuBtn?.addEventListener('click', () => {
    const isOpen = !mobileNav.classList.contains('open');
    setMenuState(isOpen);
  });

  document.querySelectorAll('.mobile-nav-link').forEach(link => {
    link.addEventListener('click', () => {
      setMenuState(false);
    });
  });

  /* ============================================================
     CUSTOM CURSOR
  ============================================================ */
  const cursor = document.querySelector('.cursor');
  const cursorAura = document.querySelector('.cursor-aura');
  let mouseX = 0, mouseY = 0, auraX = 0, auraY = 0;

  if (cursor && cursorAura && !reduceMotion) {
    document.addEventListener('mousemove', (e) => {
      mouseX = e.clientX;
      mouseY = e.clientY;
      cursor.style.left = mouseX - 5 + 'px';
      cursor.style.top = mouseY - 5 + 'px';
    });

    function animateAura() {
      auraX += (mouseX - auraX - 20) * 0.1;
      auraY += (mouseY - auraY - 20) * 0.1;
      cursorAura.style.left = auraX + 'px';
      cursorAura.style.top = auraY + 'px';
      requestAnimationFrame(animateAura);
    }
    animateAura();

    document.querySelectorAll('a, button, .project-card, .service-card, .skill-chip, .workflow-tool, .hero-profile-frame').forEach(el => {
      el.addEventListener('mouseenter', () => {
        cursor.style.transform = 'scale(2.5)';
        cursorAura.style.width = '70px';
        cursorAura.style.height = '70px';
      });
      el.addEventListener('mouseleave', () => {
        cursor.style.transform = 'scale(1)';
        cursorAura.style.width = '40px';
        cursorAura.style.height = '40px';
      });
    });
  }

  /* ============================================================
     MAGNETIC BUTTONS
  ============================================================ */
  const magneticEls = document.querySelectorAll('.btn-primary, .btn-whatsapp, .contact-cta-link');
  if (!reduceMotion) {
    magneticEls.forEach(btn => {
      btn.addEventListener('mousemove', e => {
        const rect = btn.getBoundingClientRect();
        const x = e.clientX - rect.left - rect.width / 2;
        const y = e.clientY - rect.top - rect.height / 2;
        btn.style.transform = `translate(${x * 0.2}px, ${y * 0.2}px)`;
      });
      btn.addEventListener('mouseleave', () => {
        btn.style.transform = '';
      });
    });
  }

  /* ============================================================
     INTERACTIVE TILT CARDS
  ============================================================ */
  const canTilt = !reduceMotion && window.matchMedia('(hover: hover)').matches;

  function bindTiltCards(scope = document) {
    if (!canTilt) return;
    scope.querySelectorAll('.tilt-card').forEach((card) => {
      if (card.dataset.tiltBound === '1') return;
      card.dataset.tiltBound = '1';

      const maxRotate = parseFloat(card.dataset.tiltMax || '8');

      card.addEventListener('mousemove', (e) => {
        const rect = card.getBoundingClientRect();
        const px = (e.clientX - rect.left) / rect.width;
        const py = (e.clientY - rect.top) / rect.height;
        const rotateY = (px - 0.5) * (maxRotate * 2);
        const rotateX = (0.5 - py) * (maxRotate * 2);
        card.style.setProperty('--tilt-x', `${rotateX.toFixed(2)}deg`);
        card.style.setProperty('--tilt-y', `${rotateY.toFixed(2)}deg`);
      });

      card.addEventListener('mouseleave', () => {
        card.style.setProperty('--tilt-x', '0deg');
        card.style.setProperty('--tilt-y', '0deg');
      });
    });
  }

  bindTiltCards();

  /* ============================================================
     BACKGROUND CANVAS (animated gradient orbs)
  ============================================================ */
  const canvas = document.getElementById('bg-canvas');
  if (canvas && !reduceMotion) {
    const ctx = canvas.getContext('2d');
    let w, h;

    function resize() {
      w = canvas.width = window.innerWidth;
      h = canvas.height = window.innerHeight;
    }
    resize();
    window.addEventListener('resize', resize);

    const orbs = [
      { x: 0.15, y: 0.3, r: 0.35, color: [0, 229, 255], speed: 0.0003 },
      { x: 0.8, y: 0.2, r: 0.28, color: [180, 0, 255], speed: 0.0004 },
      { x: 0.5, y: 0.7, r: 0.22, color: [255, 31, 110], speed: 0.0002 }
    ];

    let t = 0;
    function drawBg() {
      t++;
      if (w > 560) {
        ctx.clearRect(0, 0, w, h);
        orbs.forEach((orb, i) => {
          const ox = (orb.x + Math.sin(t * orb.speed + i * 2) * 0.12) * w;
          const oy = (orb.y + Math.cos(t * orb.speed * 1.3 + i) * 0.1) * h;
          const radius = orb.r * Math.min(w, h);
          const grad = ctx.createRadialGradient(ox, oy, 0, ox, oy, radius);
          const [r, g, b] = orb.color;
          grad.addColorStop(0, `rgba(${r},${g},${b},0.09)`);
          grad.addColorStop(1, `rgba(${r},${g},${b},0)`);
          ctx.fillStyle = grad;
          ctx.fillRect(0, 0, w, h);
        });
      }
      requestAnimationFrame(drawBg);
    }
    drawBg();
  } else if (canvas) {
    canvas.style.display = 'none';
  }

  /* ============================================================
     WELCOME SCREEN
  ============================================================ */
  const welcomeScreen = document.getElementById('welcomeScreen');
  const welcomeVideoStage = document.getElementById('welcomeVideoStage');
  const welcomeVideo = document.getElementById('welcomeVideo');
  let welcomeClosed = false;

  if (welcomeScreen) {
    document.body.classList.add('menu-open');

    // Smoothly releases the cinematic overlay without shifting the page underneath.
    const closeWelcome = () => {
      if (welcomeClosed) return;
      welcomeClosed = true;
      welcomeScreen.classList.add('hidden');
      setTimeout(() => {
        document.body.classList.remove('menu-open');
        welcomeScreen.style.display = 'none';
      }, reduceMotion ? 50 : 800);
    };

    let welcomeTimer = window.setTimeout(closeWelcome, reduceMotion ? 500 : 4000);

    if (welcomeVideo) {
      welcomeVideo.play().catch(() => {});
      welcomeVideo.addEventListener('loadedmetadata', () => {
        window.clearTimeout(welcomeTimer);
        const duration = Number.isFinite(welcomeVideo.duration) ? welcomeVideo.duration * 1000 : 3600;
        welcomeTimer = window.setTimeout(closeWelcome, Math.min(Math.max(duration + 180, 2200), 4000));
      }, { once: true });
      welcomeVideo.addEventListener('ended', closeWelcome, { once: true });
    }

    if (welcomeVideoStage && !reduceMotion) {
      // Tiny camera parallax, eased with requestAnimationFrame for a premium feel.
      let targetX = 0, targetY = 0, currentX = 0, currentY = 0;
      welcomeScreen.addEventListener('mousemove', (e) => {
        targetX = (e.clientX / window.innerWidth - 0.5) * 10;
        targetY = (e.clientY / window.innerHeight - 0.5) * 8;
      });

      function animateWelcomeCamera() {
        if (welcomeClosed) return;
        currentX += (targetX - currentX) * 0.08;
        currentY += (targetY - currentY) * 0.08;
        welcomeVideoStage.style.setProperty('--px', `${currentX.toFixed(2)}px`);
        welcomeVideoStage.style.setProperty('--py', `${currentY.toFixed(2)}px`);
        requestAnimationFrame(animateWelcomeCamera);
      }
      animateWelcomeCamera();
    }

    welcomeScreen.addEventListener('click', () => {
      window.clearTimeout(welcomeTimer);
      closeWelcome();
    });

    window.addEventListener('keydown', (e) => {
      if (!welcomeScreen || welcomeClosed) return;
      if (e.key === 'Enter' || e.key === 'Escape') {
        e.preventDefault();
        window.clearTimeout(welcomeTimer);
        closeWelcome();
      }
    });
  }

  /* ============================================================
     NAVBAR SCROLL
  ============================================================ */
  const navbar = document.getElementById('navbar');
  window.addEventListener('scroll', () => {
    navbar?.classList.toggle('scrolled', window.scrollY > 60);
  }, { passive: true });

  /* ============================================================
     SCROLL PROGRESS BAR
  ============================================================ */
  const scrollProgressBar = document.getElementById('scrollProgressBar');
  function updateScrollProgress() {
    if (!scrollProgressBar) return;
    const doc = document.documentElement;
    const maxScroll = doc.scrollHeight - window.innerHeight;
    const percent = maxScroll > 0 ? (window.scrollY / maxScroll) * 100 : 0;
    scrollProgressBar.style.width = `${Math.min(100, Math.max(0, percent))}%`;
  }
  updateScrollProgress();
  window.addEventListener('scroll', updateScrollProgress, { passive: true });
  window.addEventListener('resize', updateScrollProgress);

  /* ============================================================
     SCROLL REVEAL
  ============================================================ */
  const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) entry.target.classList.add('visible');
    });
  }, { threshold: 0.1 });

  document.querySelectorAll('.reveal').forEach(el => revealObserver.observe(el));

  /* ============================================================
     COUNTER ANIMATION
  ============================================================ */
  function animateCounter(el) {
    const target = parseInt(el.dataset.count);
    const duration = 1800;
    const start = performance.now();
    function update(now) {
      const elapsed = now - start;
      const progress = Math.min(elapsed / duration, 1);
      const ease = 1 - Math.pow(1 - progress, 4);
      el.textContent = Math.floor(ease * target).toLocaleString();
      if (progress < 1) requestAnimationFrame(update);
    }
    requestAnimationFrame(update);
  }

  const counterObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting && !entry.target.dataset.counted) {
        entry.target.dataset.counted = true;
        animateCounter(entry.target);
      }
    });
  }, { threshold: 0.35 });

  document.querySelectorAll('[data-count]').forEach(el => counterObserver.observe(el));

  /* ============================================================
     GALLERY: FETCH FOLDERS
  ============================================================ */
  const projectsGrid = document.getElementById('projectsGrid');
  const galleryStats = document.getElementById('galleryStats');
  let currentImages = [];
  let currentIndex = 0;
  let activeFolderName = '';
  const watermarkEnabled = document.body.dataset.watermarkEnabled === '1';
  const watermarkText = document.body.dataset.watermarkText || 'Yousef Sala7';
  const watermarkImage = document.body.dataset.watermarkImage || '';
  const watermarkPosition = document.body.dataset.watermarkPosition || 'bottom-right';
  const projectLayouts = ['layout-feature', 'layout-wide', 'layout-tall', 'layout-standard', 'layout-wide', 'layout-tall', 'layout-standard'];
  const projectCategories = ['Product Visual', 'Brand Story', 'Photography', 'Campaign Design', 'Motion'];

  // Project card metadata is generated in one place so the gallery is easy to scale.
  function getProjectMeta(folder, index, imageCount) {
    const category = projectCategories[index % projectCategories.length];
    const descriptions = {
      'Product Visual': 'Photoreal product work with cinematic lighting.',
      'Brand Story': 'A polished visual system shaped for impact.',
      'Photography': 'Sharp coverage with editorial composition.',
      'Campaign Design': 'Bold creative direction for digital launches.',
      'Motion': 'Dynamic frames built around timing and flow.'
    };
    const tagsByCategory = {
      'Product Visual': ['3D', 'Lighting', 'Detail'],
      'Brand Story': ['Identity', 'Art Direction'],
      'Photography': ['Event', 'Editorial'],
      'Campaign Design': ['Social', 'Launch'],
      'Motion': ['UI Motion', 'Digital']
    };

    return {
      category,
      description: descriptions[category] || 'Selected work from the visual archive.',
      tags: tagsByCategory[category] || ['Visual', 'Story'],
      status: imageCount > 1 ? 'Series' : 'Single'
    };
  }

  function createEl(tag, className, text = '') {
    const el = document.createElement(tag);
    if (className) el.className = className;
    if (text) el.textContent = text;
    return el;
  }

  // Gives the fullscreen viewer a subtle shared-origin transition from the clicked card.
  function setModalOrigin(card) {
    const lb = document.getElementById('premiumLightbox');
    if (!lb || !card) return;
    const rect = card.getBoundingClientRect();
    lb.style.setProperty('--origin-x', `${rect.left + rect.width / 2}px`);
    lb.style.setProperty('--origin-y', `${rect.top + rect.height / 2}px`);
  }

  function createWatermark(extraClass = '') {
    if (!watermarkEnabled || (!watermarkImage && !watermarkText.trim())) return null;
    const mark = document.createElement('span');
    mark.className = `project-watermark watermark-pos-${watermarkPosition} ${extraClass}`.trim();
    if (watermarkImage) {
      mark.classList.add('has-image');
      const img = document.createElement('img');
      img.src = watermarkImage;
      img.alt = '';
      img.decoding = 'async';
      mark.appendChild(img);
    } else {
      mark.textContent = watermarkText;
    }
    mark.setAttribute('aria-hidden', 'true');
    return mark;
  }

  function formatShotLabel(imagePath, index, total) {
    const fileName = String(imagePath || '').split('/').pop().split('\\').pop();
    const base = fileName.replace(/\.[^/.]+$/, '').replace(/^\d+_/, '').replace(/[_-]+/g, ' ').trim();
    const normalized = base ? base.replace(/\s+/g, ' ').slice(0, 46) : `Shot ${index + 1}`;
    const serial = `Shot ${String(index + 1).padStart(2, '0')} of ${String(total).padStart(2, '0')}`;
    return `${serial} - ${normalized}`;
  }

  function renderFolders(folders) {
    if (!projectsGrid) return;
    if (!folders.length) {
      projectsGrid.innerHTML = '<div class="grid-empty"><p>No projects yet. Add some via the admin panel.</p></div>';
      return;
    }

    projectsGrid.innerHTML = '';
    const totalShots = folders.reduce((sum, folder) => sum + parseInt(folder.image_count || 0, 10), 0);
    if (galleryStats) {
      galleryStats.innerHTML = `<span>${folders.length.toLocaleString()} Projects</span><span>${totalShots.toLocaleString()} Shots</span>`;
    }

    folders.forEach((folder, idx) => {
      const card = document.createElement('div');
      card.className = 'project-card tilt-card';
      card.dataset.tiltMax = '5';
      card.classList.add(projectLayouts[idx % projectLayouts.length]);
      if (idx === 0) card.classList.add('is-featured');
      card.style.animationDelay = `${idx * 0.08}s`;
      card.setAttribute('aria-label', `Open ${folder.name} project`);
      card.setAttribute('role', 'button');
      card.setAttribute('tabindex', '0');

      const img = document.createElement('img');
      img.src = folder.cover_image || 'https://placehold.co/1200x900/0d0d18/44445a?text=Project';
      img.alt = `${folder.name} cover`;
      img.loading = 'lazy';
      img.decoding = 'async';

      const overlay = document.createElement('div');
      overlay.className = 'project-overlay';

      const overlayInner = document.createElement('div');
      overlayInner.className = 'project-overlay-inner';

      const imageCount = parseInt(folder.image_count || 0, 10);
      const metaInfo = getProjectMeta(folder, idx, imageCount);

      const kicker = createEl('div', 'project-kicker', metaInfo.category);

      const title = createEl('div', 'project-title', folder.name);

      const desc = createEl('p', 'project-description', metaInfo.description);

      const tags = createEl('div', 'project-tags');
      metaInfo.tags.forEach(tag => tags.appendChild(createEl('span', '', tag)));

      const meta = createEl('div', 'project-meta');

      const count = createEl('span', 'project-count');
      count.textContent = `${imageCount.toLocaleString()} Shots`;

      const openCta = createEl('span', 'project-open', 'View Case');

      meta.appendChild(count);
      meta.appendChild(openCta);

      const badge = createEl('span', 'project-index', `#${String(idx + 1).padStart(2, '0')}`);

      const status = createEl('span', 'project-status', metaInfo.status);

      const arrow = document.createElement('div');
      arrow.className = 'project-arrow';
      arrow.innerHTML = '&rarr;';
      const watermark = createWatermark();

      overlayInner.appendChild(kicker);
      overlayInner.appendChild(title);
      overlayInner.appendChild(desc);
      overlayInner.appendChild(tags);
      overlayInner.appendChild(meta);
      overlay.appendChild(overlayInner);

      card.appendChild(img);
      if (watermark) card.appendChild(watermark);
      card.appendChild(badge);
      card.appendChild(status);
      card.appendChild(overlay);
      card.appendChild(arrow);

      card.addEventListener('pointermove', (e) => {
        const rect = card.getBoundingClientRect();
        card.style.setProperty('--mx', `${((e.clientX - rect.left) / rect.width) * 100}%`);
        card.style.setProperty('--my', `${((e.clientY - rect.top) / rect.height) * 100}%`);
      });
      card.addEventListener('click', () => {
        setModalOrigin(card);
        openFolder(folder.id, folder.name);
      });
      card.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          setModalOrigin(card);
          openFolder(folder.id, folder.name);
        }
      });
      projectsGrid.appendChild(card);

      // Observe for reveal
      setTimeout(() => revealObserver.observe(card), 10);
    });

    bindTiltCards(projectsGrid);
  }

  fetch('api.php?action=folders')
    .then(r => r.json())
    .then(data => { if (data.success) renderFolders(data.folders); })
    .catch(console.error);

  /* ============================================================
     PREMIUM CINEMATIC LIGHTBOX
  ============================================================ */
  const plb            = document.getElementById('premiumLightbox');
  const plbStage       = document.getElementById('plbStage');
  const plbMediaWrap   = document.getElementById('plbMediaWrap');
  const plbImg         = document.getElementById('plbImg');
  const plbSkeleton    = document.getElementById('plbSkeleton');
  const plbProgress    = document.getElementById('plbProgress');
  const plbCounter     = document.getElementById('plbCounter');
  const plbTitle       = document.getElementById('plbProjectTitle');
  const plbCaption     = document.getElementById('plbCaption');
  const plbThumbstrip  = document.getElementById('plbThumbstrip');
  const plbThumbWrap   = document.getElementById('plbThumbWrap');
  const plbInfoPanel   = document.getElementById('plbInfoPanel');
  const plbInfoTitle   = document.getElementById('plbInfoTitle');
  const plbInfoDesc    = document.getElementById('plbInfoDesc');
  const plbInfoTags    = document.getElementById('plbInfoTags');

  // ── State ─────────────────────────────────────────────────────
  let plbZoom = 1, plbPanX = 0, plbPanY = 0;
  let plbDragging = false, plbDragStartX = 0, plbDragStartY = 0;
  let plbDragOriginX = 0, plbDragOriginY = 0;
  let plbIdleTimer = null, plbInfoOpen = false, plbIsOpen = false;
  let plbSwipeStartX = 0, plbSwipeStartY = 0;
  const ZOOM_MIN = 1, ZOOM_MAX = 5, ZOOM_STEP = 0.4, IDLE_DELAY = 3200;

  // ── Open ──────────────────────────────────────────────────────
  function openFolder(folderId, folderName) {
    activeFolderName = folderName;
    fetch(`api.php?action=images&folder_id=${folderId}`)
      .then(r => r.json())
      .then(data => {
        if (data.success && data.images?.length) {
          currentImages = data.images;
          currentIndex  = 0;

          // Populate info panel
          const meta = getProjectMeta({ name: folderName }, 0, data.images.length);
          if (plbInfoTitle) plbInfoTitle.textContent = folderName;
          if (plbInfoDesc)  plbInfoDesc.textContent  = meta.description;
          if (plbInfoTags) {
            plbInfoTags.innerHTML = '';
            meta.tags.forEach(t => {
              const s = document.createElement('span');
              s.textContent = t;
              plbInfoTags.appendChild(s);
            });
          }

          plbRenderThumbs();
          plbNavigateTo(0, null, true);
          plb.classList.add('plb-open');
          document.body.classList.add('menu-open');
          plbIsOpen = true;
          plbResetIdle();
        } else {
          alert('This folder is empty.');
        }
      });
  }

  // ── Close ─────────────────────────────────────────────────────
  function plbClose() {
    if (!plbIsOpen) return;
    plb.classList.remove('plb-open');
    document.body.classList.remove('menu-open');
    plbIsOpen = false;
    plbClearIdle();
    plbSetInfo(false);
    setTimeout(() => {
      if (plbThumbstrip) plbThumbstrip.innerHTML = '';
      plbResetZoom(false);
    }, 420);
  }

  // ── Navigate ──────────────────────────────────────────────────
  function plbNavigateTo(idx, dir = null, initial = false) {
    if (!currentImages.length) return;
    currentIndex = ((idx % currentImages.length) + currentImages.length) % currentImages.length;
    plbResetZoom(false);
    plbLoadImage(currentImages[currentIndex], dir, initial);
    plbUpdateUI();
    plbUpdateThumbs();
    plbResetIdle();
  }

  // ── Load image with skeleton + slide animation ────────────────
  function plbLoadImage(imageObj, dir, initial) {
    const src = imageObj.file_name;
    plbSkeleton.style.display = '';
    plbImg.style.display = 'none';
    plbMediaWrap.classList.remove(
      'plb-entering', 'plb-slide-out-left', 'plb-slide-out-right',
      'plb-slide-in-left', 'plb-slide-in-right'
    );

    const doLoad = (afterClass) => {
      const tmp = new Image();
      tmp.onload = tmp.onerror = () => {
        plbImg.src = src;
        plbImg.alt = `${activeFolderName} shot ${currentIndex + 1}`;
        plbImg.style.display = '';
        plbSkeleton.style.display = 'none';
        if (afterClass) {
          plbMediaWrap.classList.remove(
            'plb-slide-out-left', 'plb-slide-out-right'
          );
          void plbMediaWrap.offsetWidth;
          plbMediaWrap.classList.add(afterClass);
        } else if (initial) {
          void plbMediaWrap.offsetWidth;
          plbMediaWrap.classList.add('plb-entering');
        }
      };
      tmp.src = src;
    };

    if (!initial && dir === 'next') {
      plbMediaWrap.classList.add('plb-slide-out-left');
      setTimeout(() => doLoad('plb-slide-in-left'), 280);
    } else if (!initial && dir === 'prev') {
      plbMediaWrap.classList.add('plb-slide-out-right');
      setTimeout(() => doLoad('plb-slide-in-right'), 280);
    } else {
      doLoad(null);
    }
  }

  // ── UI update (counter / caption / progress / nav) ────────────
  function plbUpdateUI() {
    const total = currentImages.length;
    const n     = currentIndex + 1;
    if (plbCounter) plbCounter.textContent = `${n} / ${total}`;
    if (plbTitle)   plbTitle.textContent   = activeFolderName;
    if (plbCaption) plbCaption.textContent = formatShotLabel(
      currentImages[currentIndex]?.file_name, currentIndex, total
    );
    if (plbProgress) plbProgress.style.width = `${(n / total) * 100}%`;
    const showNav = total > 1;
    document.getElementById('plbPrev').style.display = showNav ? '' : 'none';
    document.getElementById('plbNext').style.display = showNav ? '' : 'none';
  }

  // ── Thumbnails ────────────────────────────────────────────────
  function plbRenderThumbs() {
    if (!plbThumbstrip) return;
    plbThumbstrip.innerHTML = '';
    if (currentImages.length <= 1) {
      if (plbThumbWrap) plbThumbWrap.style.display = 'none';
      return;
    }
    if (plbThumbWrap) plbThumbWrap.style.display = '';
    currentImages.forEach((img, i) => {
      const t  = document.createElement('div');
      const im = document.createElement('img');
      t.className = 'plb-thumb' + (i === currentIndex ? ' plb-thumb-active' : '');
      t.setAttribute('role', 'button');
      t.setAttribute('tabindex', '0');
      t.setAttribute('aria-label', `Shot ${i + 1}`);
      im.src = img.file_name; im.alt = ''; im.loading = 'lazy'; im.decoding = 'async';
      t.appendChild(im);
      t.addEventListener('click', () => {
        if (currentIndex !== i) plbNavigateTo(i, i > currentIndex ? 'next' : 'prev');
      });
      t.addEventListener('keydown', e => {
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); t.click(); }
      });
      plbThumbstrip.appendChild(t);
    });
  }

  function plbUpdateThumbs() {
    plbThumbstrip?.querySelectorAll('.plb-thumb').forEach((t, i) => {
      const active = i === currentIndex;
      t.classList.toggle('plb-thumb-active', active);
      if (active) t.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
    });
  }

  // ── Info panel ────────────────────────────────────────────────
  function plbSetInfo(open) {
    plbInfoOpen = open;
    plbInfoPanel?.classList.toggle('plb-panel-open', open);
    plb?.classList.toggle('plb-panel-visible', open);
    document.getElementById('plbInfoToggle')?.classList.toggle('plb-active', open);
  }

  // ── Idle UI ───────────────────────────────────────────────────
  function plbResetIdle() {
    plbClearIdle();
    plb.classList.remove('plb-idle');
    plbIdleTimer = setTimeout(() => {
      if (!plbInfoOpen) plb.classList.add('plb-idle');
    }, IDLE_DELAY);
  }
  function plbClearIdle() {
    if (plbIdleTimer) { clearTimeout(plbIdleTimer); plbIdleTimer = null; }
  }

  plb?.addEventListener('mousemove',    plbResetIdle);
  plb?.addEventListener('pointerdown',  plbResetIdle);
  plb?.addEventListener('touchstart',   plbResetIdle, { passive: true });

  // ── Zoom / Pan ────────────────────────────────────────────────
  function plbApplyTransform(animate = false) {
    plbMediaWrap.style.transition = animate
      ? 'transform 0.3s cubic-bezier(0.22,1,0.36,1)' : '';
    plbMediaWrap.style.transform =
      `scale(${plbZoom}) translate(${plbPanX}px, ${plbPanY}px)`;
    plbStage?.setAttribute('data-zoom', plbZoom > 1 ? 'in' : 'out');
  }

  function plbResetZoom(animate = true) {
    plbZoom = 1; plbPanX = 0; plbPanY = 0;
    plbApplyTransform(animate);
  }

  function plbClampPan() {
    if (plbZoom <= 1) { plbPanX = 0; plbPanY = 0; return; }
    const mX = ((plbZoom - 1) / plbZoom) * (plbStage.offsetWidth  / 2);
    const mY = ((plbZoom - 1) / plbZoom) * (plbStage.offsetHeight / 2);
    plbPanX = Math.max(-mX, Math.min(mX, plbPanX));
    plbPanY = Math.max(-mY, Math.min(mY, plbPanY));
  }

  // Mouse wheel zoom
  plbStage?.addEventListener('wheel', e => {
    e.preventDefault();
    plbZoom = Math.max(ZOOM_MIN, Math.min(ZOOM_MAX,
      plbZoom + (e.deltaY < 0 ? ZOOM_STEP : -ZOOM_STEP)
    ));
    if (plbZoom <= 1) { plbPanX = 0; plbPanY = 0; }
    plbClampPan(); plbApplyTransform(); plbResetIdle();
  }, { passive: false });

  // Double-click zoom toggle
  plbStage?.addEventListener('dblclick', () => {
    if (plbZoom > 1) { plbResetZoom(true); }
    else { plbZoom = 2.5; plbApplyTransform(true); }
    plbResetIdle();
  });

  // Drag / pan
  plbStage?.addEventListener('pointerdown', e => {
    if (e.button !== 0) return;
    plbDragging = true;
    plbDragStartX = e.clientX; plbDragStartY = e.clientY;
    plbDragOriginX = plbPanX;  plbDragOriginY = plbPanY;
    plbStage.classList.add('plb-dragging');
    plbStage.setPointerCapture(e.pointerId);
  });
  plbStage?.addEventListener('pointermove', e => {
    if (!plbDragging || plbZoom <= 1) return;
    plbPanX = plbDragOriginX + (e.clientX - plbDragStartX) / plbZoom;
    plbPanY = plbDragOriginY + (e.clientY - plbDragStartY) / plbZoom;
    plbClampPan(); plbApplyTransform();
  });
  plbStage?.addEventListener('pointerup',     () => { plbDragging = false; plbStage.classList.remove('plb-dragging'); });
  plbStage?.addEventListener('pointercancel', () => { plbDragging = false; plbStage.classList.remove('plb-dragging'); });

  // Zoom buttons
  document.getElementById('plbZoomIn')?.addEventListener('click', () => {
    plbZoom = Math.min(ZOOM_MAX, plbZoom + ZOOM_STEP);
    plbClampPan(); plbApplyTransform(true); plbResetIdle();
  });
  document.getElementById('plbZoomOut')?.addEventListener('click', () => {
    plbZoom = Math.max(ZOOM_MIN, plbZoom - ZOOM_STEP);
    if (plbZoom <= 1) { plbPanX = 0; plbPanY = 0; }
    plbClampPan(); plbApplyTransform(true); plbResetIdle();
  });
  document.getElementById('plbZoomReset')?.addEventListener('click', () => {
    plbResetZoom(true); plbResetIdle();
  });

  // ── Touch swipe ───────────────────────────────────────────────
  plb?.addEventListener('touchstart', e => {
    plbSwipeStartX = e.touches[0].clientX;
    plbSwipeStartY = e.touches[0].clientY;
    plbStage?.classList.add('plb-touch');
  }, { passive: true });
  plb?.addEventListener('touchend', e => {
    if (plbZoom > 1) return;
    const dx = plbSwipeStartX - e.changedTouches[0].clientX;
    const dy = plbSwipeStartY - e.changedTouches[0].clientY;
    if (Math.abs(dx) > 50 && Math.abs(dx) > Math.abs(dy)) {
      if (dx > 0) plbNavigateTo(currentIndex + 1, 'next');
      else        plbNavigateTo(currentIndex - 1, 'prev');
    }
  }, { passive: true });

  // ── Navigation buttons ────────────────────────────────────────
  document.getElementById('plbNext')?.addEventListener('click', e => {
    e.stopPropagation();
    plbNavigateTo(currentIndex + 1, 'next');
  });
  document.getElementById('plbPrev')?.addEventListener('click', e => {
    e.stopPropagation();
    plbNavigateTo(currentIndex - 1, 'prev');
  });

  // ── Close button + backdrop click ─────────────────────────────
  document.getElementById('plbClose')?.addEventListener('click', plbClose);
  plb?.addEventListener('click', e => {
    if (e.target === plb || e.target === plbStage) plbClose();
  });

  // ── Info toggle ───────────────────────────────────────────────
  document.getElementById('plbInfoToggle')?.addEventListener('click', () => {
    plbSetInfo(!plbInfoOpen); plbResetIdle();
  });

  // ── Fullscreen ────────────────────────────────────────────────
  document.getElementById('plbFullscreen')?.addEventListener('click', () => {
    if (!document.fullscreenElement) plb?.requestFullscreen?.().catch(() => {});
    else document.exitFullscreen?.().catch(() => {});
    plbResetIdle();
  });

  // ── Keyboard ──────────────────────────────────────────────────
  window.addEventListener('keydown', e => {
    if (!plbIsOpen) return;
    switch (e.key) {
      case 'Escape':     e.preventDefault(); plbClose(); break;
      case 'ArrowRight': e.preventDefault(); plbNavigateTo(currentIndex + 1, 'next'); break;
      case 'ArrowLeft':  e.preventDefault(); plbNavigateTo(currentIndex - 1, 'prev'); break;
      case '+': case '=':
        plbZoom = Math.min(ZOOM_MAX, plbZoom + ZOOM_STEP);
        plbClampPan(); plbApplyTransform(true); break;
      case '-':
        plbZoom = Math.max(ZOOM_MIN, plbZoom - ZOOM_STEP);
        if (plbZoom <= 1) { plbPanX = 0; plbPanY = 0; }
        plbClampPan(); plbApplyTransform(true); break;
      case '0': plbResetZoom(true); break;
      case 'i': case 'I': plbSetInfo(!plbInfoOpen); plbResetIdle(); break;
      case 'f': case 'F':
        if (!document.fullscreenElement) plb?.requestFullscreen?.().catch(() => {});
        else document.exitFullscreen?.().catch(() => {});
        break;
    }
  });

  /* ============================================================
     CONTACT FORM
  ============================================================ */
  const contactForm = document.getElementById('contactForm');
  const formStatus = document.getElementById('formStatus');

  contactForm?.addEventListener('submit', function (e) {
    e.preventDefault();
    const btn = this.querySelector('button[type=submit]');
    const btnSpan = btn.querySelector('span');
    if (btnSpan) btnSpan.textContent = 'Sending...';
    btn.disabled = true;

    fetch('send_feedback.php', { method: 'POST', body: new FormData(this) })
      .then(r => {
        formStatus.style.display = 'block';
        if (r.ok) {
          formStatus.style.background = 'rgba(0,229,255,0.08)';
          formStatus.style.color = 'var(--neon-cyan)';
          formStatus.style.border = '1px solid rgba(0,229,255,0.2)';
          formStatus.textContent = 'Message sent successfully. I will get back to you soon.';
          contactForm.reset();
        } else {
          formStatus.style.background = 'rgba(255,31,110,0.08)';
          formStatus.style.color = 'var(--neon-pink)';
          formStatus.style.border = '1px solid rgba(255,31,110,0.2)';
          formStatus.textContent = 'Failed to send. Please try again.';
        }
        if (btnSpan) btnSpan.textContent = 'Send Message';
        btn.disabled = false;
        setTimeout(() => { formStatus.style.display = 'none'; }, 5000);
      })
      .catch(() => {
        formStatus.style.display = 'block';
        formStatus.textContent = 'Connection error. Please email directly.';
        if (btnSpan) btnSpan.textContent = 'Send Message';
        btn.disabled = false;
      });
  });

});
