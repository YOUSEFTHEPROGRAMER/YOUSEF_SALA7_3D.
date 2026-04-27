<?php
require 'db.php';
require 'track.php';
$about = $pdo->query("SELECT * FROM about_settings WHERE id=1")->fetch();
$settings = $pdo->query("SELECT * FROM site_settings WHERE id=1")->fetch();
$specialties = array_filter(array_map('trim', explode(',', $about['specialties'] ?? '')));
$profileImage = !empty($about['profile_image']) ? $about['profile_image'] : 'https://placehold.co/700x900/0d0d18/7777aa?text=Your+Photo';
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Yousef Sala7 3D - Visual Portfolio</title>
  <meta name="description" content="High-end VFX, 3D visualization, and immersive digital experiences by Yousef Sala7.">
  <link rel="stylesheet" href="style.css?v=<?= time() ?>">
  <link rel="stylesheet" href="lightbox.css?v=<?= time() ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link
    href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Outfit:wght@300;400;500;600;700&family=Space+Mono:ital,wght@0,400;0,700;1,400&display=swap"
    rel="stylesheet">
  <style>
    /* â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
       FLOATING 3D/DESIGN TOOL ICONS
    â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• */
    .tools-float-layer {
      position: fixed;
      inset: 0;
      pointer-events: none;
      z-index: 0;
      overflow: hidden;
    }

    .tool-icon {
      position: absolute;
      width: 62px;
      height: 62px;
      border-radius: 16px;
      display: flex;
      align-items: center;
      justify-content: center;
      opacity: 0;
      animation: floatToolUp linear infinite;
      backdrop-filter: blur(6px);
      border: 1px solid;
      box-shadow: 0 8px 32px rgba(0, 0, 0, .5);
      user-select: none;
    }

    .tool-icon img {
      width: 34px;
      height: 34px;
      object-fit: contain;
      display: block;
      filter: drop-shadow(0 8px 14px rgba(0, 0, 0, .28));
    }

    /* Unified Frosted Glass Tools */
    .tool-icon {
      background: rgba(255, 255, 255, 0.02);
      border-color: rgba(255, 255, 255, 0.1);
      color: rgba(255, 255, 255, 0.6);
      backdrop-filter: blur(8px);
      transition: color 0.3s, transform 0.35s var(--ease), box-shadow 0.35s var(--ease);
    }

    .tool-icon:hover {
      color: var(--neon-cyan);
      border-color: var(--neon-cyan);
      box-shadow: 0 0 24px rgba(42, 130, 255, 0.24);
      transform: translateY(-6px) scale(1.08);
    }

    .tool-blender {
      animation-duration: 26s;
      animation-delay: 0s;
      left: 6%;
      bottom: -80px;
    }

    .tool-ps {
      animation-duration: 22s;
      animation-delay: -5s;
      left: 18%;
      bottom: -80px;
    }

    .tool-ae {
      animation-duration: 30s;
      animation-delay: -12s;
      left: 38%;
      bottom: -80px;
    }

    .tool-c4d {
      animation-duration: 24s;
      animation-delay: -18s;
      left: 55%;
      bottom: -80px;
    }

    .tool-ai {
      animation-duration: 28s;
      animation-delay: -8s;
      left: 70%;
      bottom: -80px;
    }

    .tool-max {
      animation-duration: 32s;
      animation-delay: -22s;
      left: 85%;
      bottom: -80px;
    }

    .tool-pr {
      animation-duration: 20s;
      animation-delay: -15s;
      left: 45%;
      bottom: -80px;
    }

    @keyframes floatToolUp {
      0% {
        transform: translateY(0) rotate(0deg) scale(.6);
        opacity: 0;
      }

      5% {
        opacity: .4;
      }

      50% {
        transform: translateY(-110vh) rotate(180deg) scale(1);
        opacity: .2;
      }

      95% {
        opacity: .3;
      }

      100% {
        transform: translateY(-120vh) rotate(360deg) scale(.6);
        opacity: 0;
      }
    }

    /* â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
       FLOATING PILL NAVBAR
    â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• */


    .nav-logo {
      padding: 0 20px !important;
    }

    .nav-links {
      gap: 4px !important;
    }

    .nav-links a {
      font-size: 13px !important;
      padding: 7px 14px !important;
      border-radius: 100px !important;
    }

    .nav-whatsapp {
      margin-inline-end: 12px;
    }

    /* â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
       HERO SKILLS BELT
    â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• */
    .hero-skills-belt {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      margin-top: 28px;
      animation: fadeUp .8s .75s both;
    }

    .hero-skills-belt .skill-chip {
      display: flex;
      align-items: center;
      gap: 7px;
      padding: 7px 14px;
      background: var(--glass);
      border: 1px solid var(--glass-border);
      border-radius: 100px;
      font-family: 'Space Mono', monospace;
      font-size: .75rem;
      font-weight: 600;
      color: var(--text-2);
      backdrop-filter: blur(8px);
      transition: all .25s var(--ease);
      cursor: default;
    }

    .hero-skills-belt .skill-chip:hover {
      border-color: var(--accent);
      color: var(--accent);
      background: rgba(0, 229, 255, 0.05);
      transform: translateY(-2px);
      box-shadow: 0 4px 16px rgba(0, 229, 255, 0.15);
    }

    .hero-skills-belt .chip-icon {
      width: 20px;
      height: 20px;
      border-radius: 5px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: .7rem;
      font-weight: 900;
      overflow: hidden;
    }

    .chip-logo {
      width: 14px;
      height: 14px;
      object-fit: contain;
      display: block;
    }

    .chip-blender {
      background: #3d1500;
      color: currentColor;
    }

    .chip-ps {
      background: #001e36;
      color: currentColor;
    }

    .chip-ae {
      background: #0d0038;
      color: currentColor;
    }

    .chip-c4d {
      background: #001520;
      color: currentColor;
    }

    .chip-ai {
      background: #2d0e00;
      color: currentColor;
    }

    .chip-pr {
      background: #120023;
      color: currentColor;
    }

    .chip-max {
      background: #001a00;
      color: currentColor;
    }

    /* â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
       AMBIENT ORBS (stacks above canvas)
    â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• */
    .ambient-orbs {
      position: fixed;
      inset: 0;
      z-index: 0;
      pointer-events: none;
      overflow: hidden;
    }

    .a-orb {
      position: absolute;
      border-radius: 50%;
      filter: blur(120px);
      opacity: 0.07;
      animation: orbDrift 28s ease-in-out infinite alternate;
    }

    [data-theme="light"] .a-orb {
      opacity: 0.04;
    }

    .a-orb-1 {
      width: 55vw;
      height: 55vw;
      background: var(--neon-cyan);
      top: -15vw;
      right: -10vw;
    }

    .a-orb-2 {
      width: 38vw;
      height: 38vw;
      background: var(--neon-violet);
      bottom: -8vw;
      left: -8vw;
      animation-delay: -9s;
    }

    .a-orb-3 {
      width: 30vw;
      height: 30vw;
      background: var(--neon-pink);
      top: 45%;
      left: 35%;
      animation-delay: -16s;
      opacity: 0.04;
    }

    @keyframes orbDrift {
      0% {
        transform: translate(0, 0) scale(1);
      }

      100% {
        transform: translate(50px, 35px) scale(1.1);
      }
    }

    @keyframes fadeUp {
      from {
        opacity: 0;
        transform: translateY(22px);
      }

      to {
        opacity: 1;
        transform: none;
      }
    }
  </style>
</head>

<body
  data-watermark-enabled="<?= !empty($settings['watermark_enabled']) ? '1' : '0' ?>"
  data-watermark-text="<?= htmlspecialchars($settings['watermark_text'] ?? 'Yousef Sala7') ?>"
  data-watermark-image="<?= htmlspecialchars($settings['watermark_image'] ?? '') ?>"
  data-watermark-position="<?= htmlspecialchars($settings['watermark_position'] ?? 'bottom-right') ?>"
  style="--wm-opacity: <?= htmlspecialchars($settings['watermark_opacity'] ?? '0.62') ?>;">
  <div class="scroll-progress" aria-hidden="true">
    <span id="scrollProgressBar"></span>
  </div>

  <!-- Custom Cursor -->
  <div class="cursor"></div>
  <div class="cursor-aura"></div>
  <div class="motion-backdrop" aria-hidden="true">
    <span class="motion-line motion-line-1"></span>
    <span class="motion-line motion-line-2"></span>
    <span class="motion-line motion-line-3"></span>
  </div>

  <!-- Ambient Orbs -->
  <div class="ambient-orbs" aria-hidden="true">
    <div class="a-orb a-orb-1"></div>
    <div class="a-orb a-orb-2"></div>
    <div class="a-orb a-orb-3"></div>
  </div>

  <!-- Floating 3D / Design Tool Icons -->
  <div class="tools-float-layer" aria-hidden="true">
    <div class="tool-icon tool-blender">
      <img src="https://img.icons8.com/color/96/blender-3d.png" alt="">
    </div>
    <div class="tool-icon tool-ps">
      <img src="https://img.icons8.com/color/96/adobe-photoshop.png" alt="">
    </div>
    <div class="tool-icon tool-ae">
      <img src="https://img.icons8.com/color/96/adobe-after-effects.png" alt="">
    </div>
    <div class="tool-icon tool-c4d">
      <img src="https://img.icons8.com/color/96/cinema-4d.png" alt="">
    </div>
    <div class="tool-icon tool-ai">
      <img src="https://img.icons8.com/color/96/adobe-illustrator.png" alt="">
    </div>
    <div class="tool-icon tool-pr">
      <img src="https://img.icons8.com/color/96/adobe-premiere-pro.png" alt="">
    </div>
    <div class="tool-icon tool-max">
      <img src="https://img.icons8.com/color/96/3ds-max.png" alt="">
    </div>

    <div class="tool-icon tool-blender" style="left:62%;animation-delay:-10s;animation-duration:29s;">
      <img src="https://img.icons8.com/color/96/blender-3d.png" alt="">
    </div>
    <div class="tool-icon tool-ps" style="left:30%;animation-delay:-20s;animation-duration:25s;">
      <img src="https://img.icons8.com/color/96/adobe-photoshop.png" alt="">
    </div>
    <div class="tool-icon tool-ae" style="left:78%;animation-delay:-6s;animation-duration:33s;">
      <img src="https://img.icons8.com/color/96/adobe-after-effects.png" alt="">
    </div>
    <div class="tool-icon tool-c4d" style="left:12%;animation-delay:-17s;animation-duration:22s;">
      <img src="https://img.icons8.com/color/96/cinema-4d.png" alt="">
    </div>
    <div class="tool-icon tool-pr" style="left:50%;animation-delay:-24s;animation-duration:27s;">
      <img src="https://img.icons8.com/color/96/adobe-premiere-pro.png" alt="">
    </div>
  </div>

  <!-- Background Canvas -->
  <canvas id="bg-canvas"></canvas>
  <div class="noise-overlay"></div>

  <!-- Theme Toggle -->
  <button class="theme-toggle" id="themeToggle" aria-label="Toggle theme" title="Toggle theme">
    <svg class="icon-sun" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
      <circle cx="12" cy="12" r="5" />
      <line x1="12" y1="1" x2="12" y2="3" />
      <line x1="12" y1="21" x2="12" y2="23" />
      <line x1="4.22" y1="4.22" x2="5.64" y2="5.64" />
      <line x1="18.36" y1="18.36" x2="19.78" y2="19.78" />
      <line x1="1" y1="12" x2="3" y2="12" />
      <line x1="21" y1="12" x2="23" y2="12" />
      <line x1="4.22" y1="19.78" x2="5.64" y2="18.36" />
      <line x1="18.36" y1="5.64" x2="19.78" y2="4.22" />
    </svg>
    <svg class="icon-moon" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"
      viewBox="0 0 24 24">
      <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" />
    </svg>
  </button>

  <!-- Welcome Screen -->
  <div id="welcomeScreen">
    <div class="welcome-vignette" aria-hidden="true"></div>
    <div class="welcome-sweep" aria-hidden="true"></div>
    <div class="welcome-grain" aria-hidden="true"></div>
    <div class="welcome-video-stage" id="welcomeVideoStage">
      <video id="welcomeVideo" class="welcome-video" autoplay muted playsinline preload="auto">
        <source src="uploads/welcome-logo-animation.mp4" type="video/mp4">
      </video>
    </div>
  </div>

  <!-- Navigation -->
  <nav id="navbar">
    <a href="#hero" class="nav-logo">YS<span>3D</span></a>
    <ul class="nav-links">
      <li><a href="#about">About</a></li>
      <li><a href="about.php">About Me</a></li>
      <li><a href="#services">Services</a></li>
      <li><a href="#gallery">Projects</a></li>
      <li><a href="#contact">Contact</a></li>
    </ul>
    <a href="https://wa.me/201061006991" target="_blank" class="nav-whatsapp">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
        <path
          d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
      </svg>
      WhatsApp
    </a>
  </nav>

  <!-- Mobile Menu -->
  <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Open navigation menu" aria-controls="mobileNav"
    aria-expanded="false">
    <span></span><span></span><span></span>
  </button>
  <div class="mobile-nav" id="mobileNav" role="dialog" aria-label="Mobile navigation">
    <a href="#about" class="mobile-nav-link">About</a>
    <a href="about.php" class="mobile-nav-link">About Me</a>
    <a href="#services" class="mobile-nav-link">Services</a>
    <a href="#gallery" class="mobile-nav-link">Projects</a>
    <a href="#contact" class="mobile-nav-link">Contact</a>
    <a href="https://wa.me/201061006991" target="_blank" class="mobile-nav-link whatsapp-link">WhatsApp</a>
  </div>

  <main>

    <!-- ========== HERO ========== -->
    <section id="hero">
      <div class="hero-inner">
        <div class="hero-eyebrow">
          <span class="hero-dot"></span>
          Available for Projects - 2026
        </div>
        <p class="hero-personal-tagline">
          <?= htmlspecialchars($about['hero_tagline'] ?? '3D Artist | VFX Compositor | Visual Storyteller') ?>
        </p>
        <h1 class="hero-title">
          <span class="hero-line">Shaping</span>
          <span class="hero-line glow-text">Reality</span>
          <span class="hero-line stroke-text">Through Art</span>
        </h1>
        <p class="hero-description">
          Premium VFX compositing, photorealistic 3D visualization, and immersive digital experiences crafted to make
          brands and stories unforgettable.
        </p>
        <div class="hero-cta">
          <a href="#gallery" class="btn-primary">
            <span>View Projects</span>
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7" />
            </svg>
          </a>
          <a href="about.php" class="btn-primary btn-about-me">
            <span>About Me</span>
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h4m0 0v4m0-4l-7 7-4 1 1-4 7-7z" />
            </svg>
          </a>
          <a href="https://wa.me/201061006991" target="_blank" class="btn-whatsapp">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
              <path
                d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
            </svg>
            Chat on WhatsApp
          </a>
        </div>

        <!-- 3D / Design Tools Skills Belt -->
        <div class="hero-skills-belt">
          <span class="skill-chip">
            <span class="chip-icon chip-blender">
              <img src="https://img.icons8.com/color/96/blender-3d.png" alt="" class="chip-logo">
            </span> Blender
          </span>
          <span class="skill-chip">
            <span class="chip-icon chip-ps">
              <img src="https://img.icons8.com/color/96/adobe-photoshop.png" alt="" class="chip-logo">
            </span> Photoshop
          </span>
          <span class="skill-chip">
            <span class="chip-icon chip-ae">
              <img src="https://img.icons8.com/color/96/adobe-after-effects.png" alt="" class="chip-logo">
            </span> After Effects
          </span>
          <span class="skill-chip">
            <span class="chip-icon chip-c4d">
              <img src="https://img.icons8.com/color/96/cinema-4d.png" alt="" class="chip-logo">
            </span> Cinema 4D
          </span>
          <span class="skill-chip">
            <span class="chip-icon chip-ai">
              <img src="https://img.icons8.com/color/96/adobe-illustrator.png" alt="" class="chip-logo">
            </span> Illustrator
          </span>
          <span class="skill-chip">
            <span class="chip-icon chip-pr">
              <img src="https://img.icons8.com/color/96/adobe-premiere-pro.png" alt="" class="chip-logo">
            </span> Premiere Pro
          </span>
          <span class="skill-chip">
            <span class="chip-icon chip-max">
              <img src="https://img.icons8.com/color/96/3ds-max.png" alt="" class="chip-logo">
            </span> 3ds Max
          </span>
        </div>
      </div>
      <div class="hero-profile reveal reveal-delay-1">
        <div class="hero-profile-frame tilt-card" data-tilt-max="5">
          <img src="<?= htmlspecialchars($profileImage) ?>" alt="Yousef profile photo" loading="lazy">
        </div>
      </div>
      <div class="scroll-hint">
        <div class="scroll-hint-line"></div>
        Scroll
      </div>
    </section>

    <section class="workflow-strip reveal">
      <div class="workflow-strip-inner">
        <p class="workflow-kicker">Production Pipeline</p>
        <div class="workflow-tools">
          <article class="workflow-tool tilt-card" data-tilt-max="6">
            <img src="https://img.icons8.com/color/96/blender-3d.png" alt="Blender logo" loading="lazy">
            <div>
              <h3>Blender</h3>
              <p>Modeling & lookdev</p>
            </div>
          </article>
          <article class="workflow-tool tilt-card" data-tilt-max="6">
            <img src="https://img.icons8.com/color/96/cinema-4d.png" alt="Cinema 4D logo" loading="lazy">
            <div>
              <h3>Cinema 4D</h3>
              <p>Motion and dynamics</p>
            </div>
          </article>
          <article class="workflow-tool tilt-card" data-tilt-max="6">
            <img src="https://img.icons8.com/color/96/adobe-after-effects.png" alt="After Effects logo" loading="lazy">
            <div>
              <h3>After Effects</h3>
              <p>Compositing polish</p>
            </div>
          </article>
          <article class="workflow-tool tilt-card" data-tilt-max="6">
            <img src="https://img.icons8.com/color/96/adobe-premiere-pro.png" alt="Premiere Pro logo" loading="lazy">
            <div>
              <h3>Premiere Pro</h3>
              <p>Final cut delivery</p>
            </div>
          </article>
        </div>
      </div>
    </section>

    <!-- ========== MARQUEE ========== -->
    <div class="marquee-band">
      <div class="marquee-inner">
        <?php for ($i = 0; $i < 3; $i++): ?>
          <span class="marquee-item">VFX Compositing</span>
          <span class="marquee-item">3D Visualization</span>
          <span class="marquee-item">Motion Graphics</span>
          <span class="marquee-item">UI/UX Design</span>
          <span class="marquee-item">Digital Art</span>
          <span class="marquee-item">Product Renders</span>
          <span class="marquee-item">Visual Effects</span>
          <span class="marquee-item">3D Modeling</span>
        <?php endfor; ?>
      </div>
    </div>

    <!-- ========== STATS ========== -->
    <div class="stats-row">
      <?php
      $views = $pdo->query("SELECT COUNT(*) as total FROM visits")->fetch()['total'];
      $imgs = $pdo->query("SELECT COUNT(*) as total FROM images")->fetch()['total'];
      $folders = $pdo->query("SELECT COUNT(*) as total FROM folders")->fetch()['total'];
      ?>
      <div class="stat-item">
        <div class="stat-number" data-count="<?= $views ?>">0</div>
        <div class="stat-label">Unique Visitors</div>
      </div>
      <div class="stat-item">
        <div class="stat-number" data-count="<?= $folders ?>">0</div>
        <div class="stat-label">Projects</div>
      </div>
      <div class="stat-item">
        <div class="stat-number" data-count="<?= $imgs ?>">0</div>
        <div class="stat-label">Artworks</div>
      </div>
      <div class="stat-item">
        <div class="stat-number" data-count="3">0</div>
        <div class="stat-label">Years Active</div>
      </div>
    </div>

    <!-- ========== ABOUT ========== -->
    <section id="about" style="position: relative;">
      <!-- Ambient Orbs -->
      <div class="ambient-orbs" aria-hidden="true" style="z-index: 0; pointer-events: none;">
        <div class="a-orb a-orb-1" style="opacity: 0.03;"></div>
        <div class="a-orb a-orb-2" style="opacity: 0.03;"></div>
      </div>
      
      <div class="about-grid" style="position: relative; z-index: 1;">
        <!-- LEFT COLUMN (60%) -->
        <div class="reveal">
          <div class="section-label">Who I Am</div>
          <h2 class="section-title">CRAFTING VISUALS<br>WITH PURPOSE</h2>
          <p class="about-lead">
            Crafting premium visual experiences that seamlessly blend art with technical precision.
          </p>
          <p class="about-text">
            <?= htmlspecialchars($about['bio_1'] ?? 'I am Yousef Sala7, a visual artist focused on cinematic 3D, compositing, and motion-driven storytelling.') ?>
          </p>
          <p class="about-text">
            <?= htmlspecialchars($about['bio_2'] ?? 'My workflow merges design thinking, technical workflows, and strong attention to detail from concept to final delivery.') ?>
          </p>
          <p class="about-text">
            <?= htmlspecialchars($about['bio_3'] ?? 'I focus on creating visuals that do not just look good, but communicate emotion, clarity, and brand value.') ?>
          </p>
          
          <div class="about-highlights-bento">
            <div class="bento-box reveal reveal-delay-1">
              <span class="bento-title">Photographer</span>
              <p class="bento-desc">Documenting events with a distinct perspective.</p>
            </div>
            <div class="bento-box reveal reveal-delay-2">
              <span class="bento-title">Campaign Design</span>
              <p class="bento-desc">Crafting visuals for major events.</p>
            </div>
            <div class="bento-box full-width reveal reveal-delay-3">
              <span class="bento-title">Media Volunteer</span>
              <p class="bento-desc">Actively contributing to the Digital Initiative and community stories.</p>
            </div>
          </div>
          
          <a href="#gallery" class="btn-primary" style="margin-top: 10px;">
            <span>View My Work</span>
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7" />
            </svg>
          </a>
        </div>
        
        <!-- RIGHT COLUMN (40% Sticky) -->
        <div class="about-visual reveal reveal-delay-2">
          <div class="about-visual-blob"></div>
          <div class="about-photo-wrap tilt-card" data-tilt-max="5" style="width: 100%; max-width: 420px; aspect-ratio: 4/5;">
            <img src="<?= htmlspecialchars($profileImage) ?>" alt="Yousef portrait" class="about-photo" loading="lazy">
          </div>
          
          <div class="about-meta-glass" style="width: 100%; max-width: 420px;">
            <div class="meta-item">
              <span class="meta-label">Location</span>
              <span class="meta-value"><?= htmlspecialchars($about['location'] ?? 'Egypt') ?></span>
            </div>
            <div class="meta-item">
              <span class="meta-label">Experience</span>
              <span class="meta-value"><?= htmlspecialchars($about['years_experience'] ?? '3+') ?> Years</span>
            </div>
          </div>
          
          <div class="skills-chip-cloud" style="width: 100%; max-width: 420px;">
            <?php foreach ($specialties as $sp): ?>
              <span class="skill-chip"><?= htmlspecialchars($sp) ?></span>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </section>

    <!-- ========== SERVICES ========== -->
    <section id="services">
      <div class="services-wrapper">
        <div class="reveal">
          <div class="section-label">What I Offer</div>
          <h2 class="section-title">Precision-Driven<br>Visual Solutions</h2>
        </div>
        <div class="bento-grid reveal reveal-delay-1">
          <!-- Main Large Feature -->
          <div class="bento-card large service-vfx tilt-card" data-tilt-max="4">
            <span class="bento-num">01</span>
            <span class="bento-orbit"></span>
            <div class="bento-icon">
              <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
              </svg>
            </div>
            <div>
              <h3>VFX & Compositing</h3>
              <p>High-end visual effects, camera tracking, and seamless compositing for film and digital media projects.
                Creating worlds that blur the line between imagination and reality.</p>
              <div class="bento-tags">
                <span>Tracking</span>
                <span>Compositing</span>
                <span>Cinematic</span>
              </div>
            </div>
          </div>

          <!-- Tall Side Feature -->
          <div class="bento-card col-span-4 tall service-3d tilt-card" data-tilt-max="4">
            <span class="bento-num">02</span>
            <span class="bento-orbit"></span>
            <div class="bento-icon">
              <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5" />
              </svg>
            </div>
            <div>
              <h3>3D Visualization</h3>
              <p style="font-size: 14px;">Photorealistic product rendering, environment design, and architectural
                visualization with flawless lighting.</p>
              <div class="bento-tags">
                <span>Product</span>
                <span>Lighting</span>
              </div>
            </div>
          </div>

          <!-- Bottom Wide Feature -->
          <div class="bento-card col-span-8 service-motion tilt-card" data-tilt-max="4">
            <span class="bento-num">03</span>
            <span class="bento-orbit"></span>
            <div class="bento-icon">
              <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
            </div>
            <div>
              <h3>UI/UX Design & Motion</h3>
              <p style="font-size: 14px;">Immersive web interfaces crafted around dynamic user journeys, with meticulous
                attention to motion and detail.</p>
              <div class="bento-tags">
                <span>Interfaces</span>
                <span>Motion</span>
                <span>Experience</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ========== GALLERY ========== -->
    <section id="gallery">
      <div class="gallery-header reveal">
        <div class="section-label">Portfolio</div>
        <h2 class="section-title">Explore Projects</h2>
        <p class="section-sub">Tap any project to explore the full visual story.</p>
      </div>
      <div class="gallery-toolbar reveal reveal-delay-1" aria-label="Portfolio display tools">
        <div class="gallery-mode">
          <span class="mode-dot"></span>
          Curated Visual Stories
        </div>
        <div class="gallery-stats" id="galleryStats">
          <span>Loading projects</span>
          <span>Live gallery</span>
        </div>
      </div>
      <div class="projects-grid" id="projectsGrid">
        <div class="project-skeleton"></div>
        <div class="project-skeleton wide"></div>
        <div class="project-skeleton tall"></div>
      </div>
    </section>

    <!-- ========== CONTACT ========== -->
    <section id="contact">
      <div class="contact-grid">
        <div class="reveal">
          <div class="section-label">Get In Touch</div>
          <h2 class="section-title">Let's Build<br>Something<br>Remarkable</h2>
          <p class="section-sub">Have a project in mind? I'm always open to discussing new creative work.</p>
          <div class="contact-details">
            <a href="mailto:yooooosef.tv@gmail.com" class="contact-detail">
              <div class="contact-detail-icon">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
              </div>
              yooooosef.tv@gmail.com
            </a>
            <a href="https://wa.me/201061006991" target="_blank" class="contact-detail">
              <div class="contact-detail-icon whatsapp-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                </svg>
              </div>
              +20 106 100 6991
            </a>
          </div>
        </div>
        <div class="reveal reveal-delay-2">
          <div class="contact-cta-banner" aria-label="Start a project banner">
            <div>
              <p class="contact-cta-kicker">Fast Response</p>
              <h3>Ready to launch your next visual project?</h3>
              <p>Share your idea and get a tailored creative direction with timeline and budget range.</p>
            </div>
            <a href="https://wa.me/201061006991" target="_blank" class="contact-cta-link"
              aria-label="Start project on WhatsApp">Start on WhatsApp</a>
          </div>
          <form class="contact-form" id="contactForm" action="send_feedback.php" method="POST">
            <div class="form-row">
              <div class="form-group">
                <label class="form-label" for="name">Name</label>
                <input type="text" id="name" name="name" class="form-input" placeholder="Your name" required>
              </div>
              <div class="form-group">
                <label class="form-label" for="email">Email</label>
                <input type="email" id="email" name="email" class="form-input" placeholder="your@email.com" required>
              </div>
            </div>
            <div class="form-group">
              <label class="form-label" for="message">Message</label>
              <textarea id="message" name="message" class="form-input" placeholder="Tell me about your project..."
                required></textarea>
            </div>
            <div id="formStatus"></div>
            <button type="submit" class="btn-submit" aria-label="Send contact message">
              <span>Send Message</span>
              <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z" />
              </svg>
            </button>
          </form>
        </div>
      </div>
    </section>

  </main>

  <!-- WhatsApp Float Button -->
  <a href="https://wa.me/201061006991" target="_blank" class="whatsapp-float" aria-label="Chat on WhatsApp">
    <svg width="26" height="26" viewBox="0 0 24 24" fill="currentColor">
      <path
        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
    </svg>
    <span class="whatsapp-float-tooltip">Chat with me</span>
  </a>

  <!-- Footer -->
  <footer>
    <span class="footer-logo">Yousef Sala7 <span>3D</span></span>
    <span class="footer-copy">&copy; <?php echo date("Y"); ?> - Crafted with precision</span>
    <div class="footer-links">
      <a href="admin_index.php" title="Admin Dashboard">Admin Panel</a>
      <a href="mailto:yooooosef.tv@gmail.com">Email</a>
      <a href="https://wa.me/201061006991" target="_blank">WhatsApp</a>
    </div>
  </footer>

  <!-- ═══════════════════════════════════════════════════════
       PREMIUM CINEMATIC LIGHTBOX
  ════════════════════════════════════════════════════════ -->
  <div id="premiumLightbox" role="dialog" aria-modal="true" aria-label="Project media viewer">

    <!-- Cinematic letter-box bands -->
    <div class="plb-band plb-band-top"    aria-hidden="true"></div>
    <div class="plb-band plb-band-bottom" aria-hidden="true"></div>

    <!-- Main grid layout -->
    <div class="plb-layout">

      <!-- ── TOP BAR ───────────────────────────────────────── -->
      <header class="plb-topbar">
        <div class="plb-topbar-left">
          <span class="plb-project-title" id="plbProjectTitle">Project</span>
          <span class="plb-counter" id="plbCounter">1 / 1</span>
        </div>
        <div class="plb-topbar-right">
          <button class="plb-btn" id="plbZoomOut" aria-label="Zoom out" title="Zoom out (−)">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
          </button>
          <button class="plb-btn" id="plbZoomIn" aria-label="Zoom in" title="Zoom in (+)">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
          </button>
          <button class="plb-btn" id="plbZoomReset" aria-label="Reset zoom" title="Reset zoom (0)">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
          </button>
          <button class="plb-btn" id="plbInfoToggle" aria-label="Project info" title="Project info (I)">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          </button>
          <button class="plb-btn" id="plbFullscreen" aria-label="Toggle fullscreen" title="Fullscreen (F)">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"/></svg>
          </button>
          <button class="plb-btn plb-btn-close" id="plbClose" aria-label="Close viewer (Esc)">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          </button>
        </div>
      </header>

      <!-- ── MEDIA STAGE ─────────────────────────────────────── -->
      <div class="plb-stage" id="plbStage" tabindex="-1">
        <button class="plb-nav plb-nav-prev" id="plbPrev" aria-label="Previous image">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        </button>
        <button class="plb-nav plb-nav-next" id="plbNext" aria-label="Next image">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
        </button>
        <!-- Media wrapper — zoom + pan target -->
        <div class="plb-media-wrap" id="plbMediaWrap">
          <div class="plb-img-skeleton" id="plbSkeleton"></div>
          <img class="plb-img" id="plbImg" src="" alt="" style="display:none;" draggable="false">
        </div>
      </div>

      <!-- ── BOTTOM BAR ─────────────────────────────────────── -->
      <footer class="plb-bottombar">
        <div class="plb-bottombar-left"></div>
        <span class="plb-caption" id="plbCaption"></span>
        <div class="plb-bottombar-right"></div>
      </footer>
    </div>

    <!-- Progress strip -->
    <div class="plb-progress-wrap" aria-hidden="true">
      <div class="plb-progress-bar" id="plbProgress"></div>
    </div>

    <!-- Thumbnail strip -->
    <div class="plb-thumbstrip-wrap" id="plbThumbWrap">
      <div class="plb-thumbstrip" id="plbThumbstrip"></div>
    </div>

    <!-- Info slide-in panel -->
    <aside class="plb-info-panel" id="plbInfoPanel" aria-label="Project information">
      <div>
        <div class="plb-info-label">Project</div>
        <div class="plb-info-title" id="plbInfoTitle">—</div>
      </div>
      <div>
        <div class="plb-info-label">Description</div>
        <div class="plb-info-desc" id="plbInfoDesc">—</div>
      </div>
      <div>
        <div class="plb-info-label">Tags</div>
        <div class="plb-info-tags" id="plbInfoTags"></div>
      </div>
    </aside>

  </div><!-- /#premiumLightbox -->

  <script src="script.js"></script>
</body>

</html>
