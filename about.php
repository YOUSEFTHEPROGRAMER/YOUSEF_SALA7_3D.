<?php
/**
 * About Me Page - Professional Full-Stack Implementation
 * Enhanced with modern UI/UX, advanced animations, and scalable architecture
 * 
 * @author Yousef Sala7
 * @version 2.0.0
 */

declare(strict_types=1);

// ─── Bootstrap & Configuration ─────────────────────────────────
require 'db.php';
session_start();

// ─── Data Fetching Layer ───────────────────────────────────────
class AboutPageData {
    private PDO $pdo;
    private array $about;
    private array $settings;
    private array $specialties;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->loadData();
        $this->processSpecialties();
    }
    
    private function loadData(): void {
        try {
            $this->about = $this->pdo->query("SELECT * FROM about_settings WHERE id=1")->fetch() ?: [];
            $this->settings = $this->pdo->query("SELECT * FROM site_settings WHERE id=1")->fetch() ?: [];
            
            // Log visit asynchronously
            $this->trackVisit();
        } catch (PDOException $e) {
            error_log("About page data loading failed: " . $e->getMessage());
            $this->about = [];
            $this->settings = [];
        }
    }
    
    private function processSpecialties(): void {
        $raw = $this->about['specialties'] ?? '';
        $this->specialties = $raw ? array_filter(array_map('trim', explode(',', $raw))) : [];
    }
    
    private function trackVisit(): void {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO visits (ip_address, user_agent, page_url, visited_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([
                $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN',
                $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN',
                '/about.php'
            ]);
        } catch (PDOException $e) {
            // Silent fail for analytics
        }
    }
    
    // ─── Getters with Fallbacks ────────────────────────────────
    public function getProfileImage(): string {
        $img = $this->about['profile_image'] ?? '';
        return !empty($img) ? htmlspecialchars($img) : 'https://placehold.co/700x900/0d0d18/7777aa?text=Yousef+Salah';
    }
    
    public function getHeroImage(): string {
        $img = $this->about['hero_image'] ?? '';
        return !empty($img) ? htmlspecialchars($img) : '';
    }
    
    public function getWatermarkText(): string {
        return htmlspecialchars($this->settings['watermark_text'] ?? 'Yousef Sala7');
    }
    
    public function getWatermarkImage(): string {
        return htmlspecialchars($this->settings['watermark_image'] ?? '');
    }
    
    public function getWatermarkPosition(): string {
        $pos = $this->settings['watermark_position'] ?? 'bottom-right';
        return in_array($pos, ['top-left', 'top-right', 'bottom-left', 'bottom-right', 'center']) ? $pos : 'bottom-right';
    }
    
    public function getWatermarkOpacity(): string {
        return htmlspecialchars($this->settings['watermark_opacity'] ?? '0.62');
    }
    
    public function isWatermarkEnabled(): bool {
        return !empty($this->settings['watermark_enabled']);
    }
    
    public function getAbout(string $key, string $default = ''): string {
        return htmlspecialchars($this->about[$key] ?? $default);
    }
    
    public function getSpecialties(): array {
        return $this->specialties;
    }
    
    public function getYearsExperience(): int {
        $years = $this->about['years_experience'] ?? '3';
        return is_numeric($years) ? (int)$years : 3;
    }
    
    public function getLocation(): string {
        return $this->about['location'] ?? 'Egypt';
    }
    
    public function getPageTitle(): string {
        $title = $this->about['about_title'] ?? 'About Me';
        return htmlspecialchars($title) . ' | Yousef Salah';
    }
    
    public function getPageDescription(): string {
        return htmlspecialchars($this->about['short_intro'] ?? '3D Artist | VFX Compositor | Visual Storyteller.');
    }
    
    public function getHeroTagline(): string {
        return htmlspecialchars($this->about['hero_tagline'] ?? '3D Artist | VFX Compositor | Visual Storyteller');
    }
}

// ─── Initialize Data Layer ─────────────────────────────────────
$data = new AboutPageData($pdo);

// ─── SEO Meta ──────────────────────────────────────────────────
$pageTitle = $data->getPageTitle();
$pageDescription = $data->getPageDescription();
$profileImage = $data->getProfileImage();
$watermarkEnabled = $data->isWatermarkEnabled() ? '1' : '0';
$watermarkText = $data->getWatermarkText();
$watermarkImage = $data->getWatermarkImage();
$watermarkPosition = $data->getWatermarkPosition();
$watermarkOpacity = $data->getWatermarkOpacity();
$specialties = $data->getSpecialties();
$yearsExp = $data->getYearsExperience();
$location = $data->getLocation();
$heroTagline = $data->getHeroTagline();
$heroImage = $data->getHeroImage();

// ─── Calculate Skill Levels (Deterministic based on hash) ──────
$skillLevels = [];
foreach ($specialties as $sp) {
    $hash = crc32($sp);
    $skillLevels[] = 72 + ($hash % 23);
}

// ─── Timeline Data ─────────────────────────────────────────────
$timeline = [
    [
        'year' => '2016 – Present',
        'title' => 'Graphic Designer',
        'description' => 'Started my journey in design, focusing on visual identities and creative campaigns with a strong eye for aesthetics.',
        'icon' => '🎨'
    ],
    [
        'year' => '2019 – Present',
        'title' => '3D & VFX Artist',
        'description' => 'Expanded into the world of cinematic 3D and VFX compositing, delivering product visualizations and motion-driven storytelling.',
        'icon' => '🎬'
    ],
    [
        'year' => '2025',
        'title' => 'Campaign Designer (Initiatives)',
        'description' => 'Designed visuals for the International AI Forum and Code Camp initiative, blending brand identity with modern aesthetics.',
        'icon' => '🚀'
    ],
    [
        'year' => '2026 – Present',
        'title' => 'Photographer & Media Volunteer',
        'description' => 'Covered initiatives, events, and community stories. Joined the media team at Ana Motatawe3 and contributed to the Digital Initiative.',
        'icon' => '📸'
    ]
];

// ─── Tech Stack Data ───────────────────────────────────────────
$techStack = [
    ['name' => 'Cinema 4D', 'level' => 92, 'category' => '3D'],
    ['name' => 'After Effects', 'level' => 88, 'category' => 'VFX'],
    ['name' => 'Photoshop', 'level' => 95, 'category' => 'Design'],
    ['name' => 'Illustrator', 'level' => 85, 'category' => 'Design'],
    ['name' => 'Substance Painter', 'level' => 78, 'category' => '3D'],
    ['name' => 'DaVinci Resolve', 'level' => 82, 'category' => 'Post'],
    ['name' => 'Blender', 'level' => 70, 'category' => '3D'],
    ['name' => 'Houdini', 'level' => 55, 'category' => 'VFX'],
];

// ─── Services Data ─────────────────────────────────────────────
$services = [
    [
        'title' => '3D Art & Rendering',
        'description' => 'Hyper-realistic product renders, character art, and environment designs.',
        'icon' => '<svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>',
        'color' => '#00e5ff'
    ],
    [
        'title' => 'VFX Compositing',
        'description' => 'Cinematic visual effects, motion tracking, and green screen integration.',
        'icon' => '<svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10"/><path d="M2 12h20"/></svg>',
        'color' => '#7b61ff'
    ],
    [
        'title' => 'Motion Design',
        'description' => 'Animated content for social media, branding, and storytelling campaigns.',
        'icon' => '<svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="2" width="20" height="20" rx="2.18"/><path d="M7 2v20M17 2v20M2 12h20M2 7h5M2 17h5M17 17h5M17 7h5"/></svg>',
        'color' => '#b400ff'
    ],
    [
        'title' => 'Photo Editing',
        'description' => 'Professional retouching, color grading, and composite manipulation.',
        'icon' => '<svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>',
        'color' => '#ff1f6e'
    ],
];

// ─── Achievements Data ─────────────────────────────────────────
$achievements = [
    ['number' => $yearsExp + 2, 'label' => 'Years of Experience', 'icon' => '⏳'],
    ['number' => 150, 'label' => 'Projects Completed', 'icon' => '✅'],
    ['number' => 60, 'label' => 'Happy Clients', 'icon' => '😊'],
    ['number' => 12, 'label' => 'Event Coverages', 'icon' => '📷'],
];

// ─── Testimonials Data ─────────────────────────────────────────
$testimonials = [
    [
        'text' => 'Yousef delivered exceptional 3D work that exceeded our expectations. His attention to detail is remarkable.',
        'author' => 'Ahmed M.',
        'role' => 'Creative Director',
        'rating' => 5
    ],
    [
        'text' => 'Working with Yousef was a game-changer for our campaign. The visuals he created were stunning.',
        'author' => 'Sara K.',
        'role' => 'Marketing Lead',
        'rating' => 5
    ],
    [
        'text' => 'Professional, timely, and incredibly talented. His compositing skills brought our vision to life.',
        'author' => 'Mohamed R.',
        'role' => 'Film Producer',
        'rating' => 5
    ],
];

// ─── Process Timeline ──────────────────────────────────────────
$workflow = [
    [
        'step' => '01',
        'title' => 'Discovery',
        'description' => 'Understanding your vision, goals, and requirements through deep consultation.',
        'duration' => '1-2 Days'
    ],
    [
        'step' => '02',
        'title' => 'Concept & Planning',
        'description' => 'Creating moodboards, sketches, and technical plans for your project.',
        'duration' => '2-3 Days'
    ],
    [
        'step' => '03',
        'title' => 'Production',
        'description' => 'Building 3D assets, compositing, and bringing concepts to life.',
        'duration' => '5-10 Days'
    ],
    [
        'step' => '04',
        'title' => 'Refinement',
        'description' => 'Iterative feedback loops to polish every detail to perfection.',
        'duration' => '2-3 Days'
    ],
    [
        'step' => '05',
        'title' => 'Delivery',
        'description' => 'Final exports in required formats with full documentation.',
        'duration' => '1 Day'
    ],
];

// ─── FAQ Data ──────────────────────────────────────────────────
$faqs = [
    [
        'question' => 'What software do you use?',
        'answer' => 'I primarily work with Cinema 4D, After Effects, Photoshop, and Substance Painter. For specific projects, I also use Blender, Houdini, and DaVinci Resolve.'
    ],
    [
        'question' => 'How long does a typical 3D project take?',
        'answer' => 'Timeline varies by complexity. Simple product renders can take 2-3 days, while complex scenes or animations may take 1-3 weeks.'
    ],
    [
        'question' => 'Do you work with international clients?',
        'answer' => 'Absolutely! I work remotely with clients worldwide and adapt to different time zones for smooth communication.'
    ],
    [
        'question' => 'What file formats do you deliver?',
        'answer' => 'I deliver in industry-standard formats: PNG, JPEG, TIFF for stills; MP4, MOV, EXR sequences for video; and PSD/AI for design work.'
    ],
    [
        'question' => 'Can I request revisions?',
        'answer' => 'Yes! I include 2-3 revision rounds in every project to ensure you are completely satisfied with the final result.'
    ],
];
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $pageTitle ?></title>
  <meta name="description" content="<?= $pageDescription ?>">
  <meta name="author" content="Yousef Salah">
  
  <!-- Open Graph -->
  <meta property="og:title" content="<?= $pageTitle ?>">
  <meta property="og:description" content="<?= $pageDescription ?>">
  <meta property="og:image" content="<?= $profileImage ?>">
  <meta property="og:type" content="profile">
  <meta property="og:url" content="https://yousefsalah.com/about">
  
  <!-- Twitter Card -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?= $pageTitle ?>">
  <meta name="twitter:description" content="<?= $pageDescription ?>">
  <meta name="twitter:image" content="<?= $profileImage ?>">
  
  <!-- Preconnect for Performance -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  
  <!-- Stylesheets -->
  <link rel="stylesheet" href="style.css?v=<?= time() ?>">
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Outfit:wght@300;400;500;600;700;800&family=Space+Mono:ital,wght@0,400;0,700;1,400&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  
  <!-- AOS (Animate on Scroll) -->
  <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
  
  <style>
    /* ═══════════════════════════════════════════════════════════
       CSS CUSTOM PROPERTIES
       ═══════════════════════════════════════════════════════════ */
    :root {
      --ab-bg: #06060C;
      --ab-surface: #0d0d1a;
      --ab-surface-2: #131325;
      --ab-surface-3: #1a1a30;
      --ab-glass: rgba(255,255,255,0.03);
      --ab-glass-hover: rgba(255,255,255,0.06);
      --ab-border: rgba(255,255,255,0.06);
      --ab-border-hover: rgba(255,255,255,0.12);
      --ab-text-primary: #f0f0fa;
      --ab-text-secondary: #9999bb;
      --ab-text-muted: #555577;
      --ab-accent: #00e5ff;
      --ab-accent-2: #7b61ff;
      --ab-accent-3: #b400ff;
      --ab-gradient: linear-gradient(135deg, var(--ab-accent), var(--ab-accent-2), var(--ab-accent-3));
      --ab-gradient-subtle: linear-gradient(135deg, rgba(0,229,255,0.1), rgba(123,97,255,0.1));
      --ab-shadow-sm: 0 2px 8px rgba(0,0,0,0.2);
      --ab-shadow-md: 0 8px 32px rgba(0,0,0,0.3);
      --ab-shadow-lg: 0 16px 48px rgba(0,0,0,0.4);
      --ab-shadow-glow: 0 0 40px rgba(0,229,255,0.1);
      --ab-radius-sm: 10px;
      --ab-radius-md: 16px;
      --ab-radius-lg: 24px;
      --ab-radius-xl: 32px;
      --ab-transition: 0.4s cubic-bezier(0.16, 1, 0.3, 1);
      --ab-transition-fast: 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* ═══════════════════════════════════════════════════════════
       BASE STYLES
       ═══════════════════════════════════════════════════════════ */
    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      background: var(--ab-bg);
      color: var(--ab-text-primary);
      font-family: 'Outfit', 'Inter', sans-serif;
      line-height: 1.6;
      overflow-x: hidden;
      -webkit-font-smoothing: antialiased;
    }

    /* Ambient Background */
    body::before {
      content: '';
      position: fixed;
      inset: 0;
      background: 
        radial-gradient(circle at 20% 30%, rgba(0,229,255,0.04) 0%, transparent 50%),
        radial-gradient(circle at 80% 70%, rgba(180,0,255,0.04) 0%, transparent 50%),
        radial-gradient(circle at 50% 50%, rgba(123,97,255,0.02) 0%, transparent 60%);
      pointer-events: none;
      z-index: 0;
    }

    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-track { background: var(--ab-bg); }
    ::-webkit-scrollbar-thumb { background: var(--ab-text-muted); border-radius: 3px; }
    ::-webkit-scrollbar-thumb:hover { background: var(--ab-text-secondary); }

    /* ═══════════════════════════════════════════════════════════
       LOADING SCREEN
       ═══════════════════════════════════════════════════════════ */
    .page-loader {
      position: fixed;
      inset: 0;
      z-index: 10000;
      background: var(--ab-bg);
      display: flex;
      align-items: center;
      justify-content: center;
      transition: opacity 0.6s ease, visibility 0.6s ease;
    }

    .page-loader.hidden {
      opacity: 0;
      visibility: hidden;
      pointer-events: none;
    }

    .loader-content {
      text-align: center;
    }

    .loader-logo {
      font-family: 'Bebas Neue', sans-serif;
      font-size: 64px;
      letter-spacing: 0.1em;
      background: var(--ab-gradient);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      margin-bottom: 16px;
      animation: loaderPulse 1.5s ease-in-out infinite;
    }

    .loader-progress {
      width: 200px;
      height: 3px;
      background: rgba(255,255,255,0.1);
      border-radius: 2px;
      overflow: hidden;
      margin: 0 auto;
    }

    .loader-bar {
      height: 100%;
      background: var(--ab-gradient);
      border-radius: 2px;
      animation: loaderProgress 1.2s ease-in-out infinite;
    }

    @keyframes loaderPulse {
      0%, 100% { opacity: 0.5; transform: scale(0.98); }
      50% { opacity: 1; transform: scale(1); }
    }

    @keyframes loaderProgress {
      0% { width: 0%; margin-left: 0; }
      50% { width: 60%; margin-left: 20%; }
      100% { width: 0%; margin-left: 100%; }
    }

    /* ═══════════════════════════════════════════════════════════
       HERO SECTION
       ═══════════════════════════════════════════════════════════ */
    .hero-section {
      position: relative;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
      margin: 80px auto 0;
      max-width: 1400px;
      border-radius: var(--ab-radius-xl);
    }

    .hero-canvas {
      position: absolute;
      inset: 0;
      z-index: 1;
    }

    .hero-bg-image {
      position: absolute;
      inset: 0;
      z-index: 0;
      background-size: cover;
      background-position: center;
      filter: brightness(0.5) saturate(1.2);
      transform: scale(1.1);
      transition: transform 0.3s ease-out;
    }

    .hero-overlay-gradient {
      position: absolute;
      inset: 0;
      z-index: 2;
      background: linear-gradient(
        to bottom,
        rgba(6,6,12,0.4) 0%,
        rgba(6,6,12,0.1) 40%,
        rgba(6,6,12,0.1) 60%,
        rgba(6,6,12,0.95) 100%
      );
      pointer-events: none;
    }

    .hero-noise {
      position: absolute;
      inset: 0;
      z-index: 3;
      opacity: 0.04;
      background-image: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIzMDAiIGhlaWdodD0iMzAwIj48ZmlsdGVyIGlkPSJmIj48ZmVUdXJidWxlbmNlIHR5cGU9ImZyYWN0YWxOb2lzZSIgYmFzZUZyZXF1ZW5jeT0iLjc0IiBudW1PY3RhdmVzPSIzIiAvPjwvZmlsdGVyPjxyZWN0IHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbHRlcj0idXJsKCNmKSIgb3BhY2l0eT0iMC41Ii8+PC9zdmc+');
      pointer-events: none;
    }

    .hero-content {
      position: relative;
      z-index: 5;
      text-align: center;
      padding: 2rem;
      max-width: 800px;
    }

    .hero-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 8px 20px;
      background: var(--ab-glass);
      border: 1px solid var(--ab-border);
      border-radius: 100px;
      font-family: 'Space Mono', monospace;
      font-size: 0.75rem;
      letter-spacing: 0.15em;
      text-transform: uppercase;
      color: var(--ab-text-secondary);
      margin-bottom: 32px;
      backdrop-filter: blur(10px);
      animation: fadeInUp 0.8s ease 0.2s both;
    }

    .hero-badge-dot {
      width: 8px;
      height: 8px;
      background: #25d366;
      border-radius: 50%;
      animation: pulse-dot 2s ease-in-out infinite;
    }

    @keyframes pulse-dot {
      0%, 100% { box-shadow: 0 0 0 0 rgba(37,211,102,0.4); }
      50% { box-shadow: 0 0 0 8px rgba(37,211,102,0); }
    }

    .hero-title {
      font-family: 'Bebas Neue', sans-serif;
      font-size: clamp(3rem, 8vw, 7rem);
      line-height: 0.95;
      letter-spacing: 0.02em;
      margin-bottom: 24px;
      animation: fadeInUp 0.8s ease 0.4s both;
    }

    .hero-title-line {
      display: block;
    }

    .hero-title-accent {
      background: var(--ab-gradient);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .hero-subtitle {
      font-family: 'Space Mono', monospace;
      font-size: clamp(0.9rem, 2vw, 1.1rem);
      color: var(--ab-text-secondary);
      letter-spacing: 0.08em;
      margin-bottom: 40px;
      animation: fadeInUp 0.8s ease 0.6s both;
    }

    .hero-actions {
      display: flex;
      gap: 16px;
      justify-content: center;
      flex-wrap: wrap;
      animation: fadeInUp 0.8s ease 0.8s both;
    }

    .btn-primary {
      padding: 16px 36px;
      background: var(--ab-gradient);
      color: #fff;
      font-family: 'Bebas Neue', sans-serif;
      font-size: 1.2rem;
      letter-spacing: 0.1em;
      border: none;
      border-radius: 100px;
      cursor: pointer;
      text-decoration: none;
      transition: all var(--ab-transition-fast);
      position: relative;
      overflow: hidden;
    }

    .btn-primary:hover {
      transform: translateY(-3px);
      box-shadow: 0 12px 40px rgba(0,229,255,0.3);
    }

    .btn-outline {
      padding: 16px 36px;
      background: transparent;
      color: var(--ab-text-primary);
      font-family: 'Bebas Neue', sans-serif;
      font-size: 1.2rem;
      letter-spacing: 0.1em;
      border: 1px solid var(--ab-border);
      border-radius: 100px;
      cursor: pointer;
      text-decoration: none;
      transition: all var(--ab-transition-fast);
      backdrop-filter: blur(10px);
    }

    .btn-outline:hover {
      border-color: var(--ab-accent);
      background: rgba(0,229,255,0.1);
      transform: translateY(-3px);
    }

    .scroll-indicator {
      position: absolute;
      bottom: 40px;
      left: 50%;
      transform: translateX(-50%);
      z-index: 5;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 8px;
      color: var(--ab-text-muted);
      font-family: 'Space Mono', monospace;
      font-size: 0.65rem;
      letter-spacing: 0.2em;
      text-transform: uppercase;
      animation: bounceDown 2s ease-in-out infinite;
    }

    .scroll-indicator-line {
      width: 1px;
      height: 40px;
      background: linear-gradient(to bottom, var(--ab-text-muted), transparent);
    }

    @keyframes bounceDown {
      0%, 100% { transform: translateX(-50%) translateY(0); }
      50% { transform: translateX(-50%) translateY(10px); }
    }

    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(30px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* ═══════════════════════════════════════════════════════════
       SECTIONS
       ═══════════════════════════════════════════════════════════ */
    .section {
      padding: 100px 24px;
      max-width: 1200px;
      margin: 0 auto;
      position: relative;
      z-index: 1;
    }

    .section-label {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      font-family: 'Space Mono', monospace;
      font-size: 0.75rem;
      letter-spacing: 0.2em;
      text-transform: uppercase;
      color: var(--ab-accent);
      margin-bottom: 20px;
      padding: 8px 18px;
      background: var(--ab-glass);
      border: 1px solid var(--ab-border);
      border-radius: 100px;
      backdrop-filter: blur(10px);
    }

    .section-label::before {
      content: '';
      width: 6px;
      height: 6px;
      background: var(--ab-accent);
      border-radius: 50%;
      box-shadow: 0 0 10px var(--ab-accent);
    }

    .section-title {
      font-family: 'Bebas Neue', sans-serif;
      font-size: clamp(2.5rem, 5vw, 4.5rem);
      line-height: 1.05;
      letter-spacing: 0.02em;
      margin-bottom: 20px;
    }

    .section-subtitle {
      font-size: 1.1rem;
      color: var(--ab-text-secondary);
      max-width: 600px;
      line-height: 1.7;
      margin-bottom: 50px;
    }

    /* ═══════════════════════════════════════════════════════════
       ABOUT GRID
       ═══════════════════════════════════════════════════════════ */
    .about-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 80px;
      align-items: start;
    }

    @media (max-width: 900px) {
      .about-grid { grid-template-columns: 1fr; gap: 60px; }
    }

    .about-text-block p {
      font-size: 1.05rem;
      color: var(--ab-text-secondary);
      line-height: 1.85;
      margin-bottom: 20px;
    }

    .about-text-block p.lead {
      font-size: 1.3rem;
      font-weight: 500;
      color: var(--ab-text-primary);
      margin-bottom: 28px;
    }

    /* ═══════════════════════════════════════════════════════════
       VISUAL CARD
       ═══════════════════════════════════════════════════════════ */
    .visual-card {
      position: sticky;
      top: 120px;
    }

    .visual-card-wrap {
      position: relative;
      border-radius: var(--ab-radius-lg);
      overflow: hidden;
      box-shadow: var(--ab-shadow-lg);
      border: 1px solid var(--ab-border);
      transition: transform var(--ab-transition);
    }

    .visual-card-wrap:hover {
      transform: translateY(-8px);
    }

    .visual-card-img {
      width: 100%;
      height: auto;
      display: block;
      transition: transform 0.8s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .visual-card-wrap:hover .visual-card-img {
      transform: scale(1.06);
    }

    .visual-glow {
      position: absolute;
      inset: -1px;
      background: var(--ab-gradient);
      border-radius: var(--ab-radius-lg);
      opacity: 0;
      transition: opacity var(--ab-transition);
      z-index: -1;
      filter: blur(20px);
    }

    .visual-card-wrap:hover .visual-glow {
      opacity: 0.3;
    }

    /* Info Chips */
    .info-chips {
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
      margin-top: 28px;
      justify-content: center;
    }

    .info-chip {
      padding: 10px 20px;
      background: var(--ab-glass);
      border: 1px solid var(--ab-border);
      border-radius: 100px;
      font-family: 'Space Mono', monospace;
      font-size: 0.8rem;
      letter-spacing: 0.06em;
      color: var(--ab-text-secondary);
      display: flex;
      align-items: center;
      gap: 8px;
      backdrop-filter: blur(10px);
      transition: all var(--ab-transition-fast);
    }

    .info-chip:hover {
      border-color: var(--ab-accent);
      background: rgba(0,229,255,0.08);
      color: var(--ab-text-primary);
      transform: translateY(-3px);
    }

    /* ═══════════════════════════════════════════════════════════
       TIMELINE
       ═══════════════════════════════════════════════════════════ */
    .timeline {
      display: flex;
      flex-direction: column;
      gap: 20px;
      margin-top: 40px;
    }

    .timeline-item {
      position: relative;
      padding: 32px;
      background: var(--ab-glass);
      border: 1px solid var(--ab-border);
      border-radius: var(--ab-radius-lg);
      backdrop-filter: blur(10px);
      transition: all var(--ab-transition);
      overflow: hidden;
    }

    .timeline-item::before {
      content: '';
      position: absolute;
      left: 0;
      top: 0;
      bottom: 0;
      width: 3px;
      background: var(--ab-gradient);
      opacity: 0;
      transition: opacity var(--ab-transition);
    }

    .timeline-item:hover {
      border-color: var(--ab-accent);
      transform: translateX(8px);
      box-shadow: var(--ab-shadow-glow);
    }

    .timeline-item:hover::before {
      opacity: 1;
    }

    .timeline-header {
      display: flex;
      align-items: center;
      gap: 16px;
      margin-bottom: 12px;
    }

    .timeline-icon {
      font-size: 1.8rem;
    }

    .timeline-year {
      font-family: 'Space Mono', monospace;
      font-size: 0.8rem;
      color: var(--ab-accent);
      letter-spacing: 0.1em;
    }

    .timeline-title {
      font-size: 1.2rem;
      font-weight: 600;
      margin-bottom: 8px;
    }

    .timeline-desc {
      font-size: 0.95rem;
      color: var(--ab-text-secondary);
      line-height: 1.7;
    }

    /* ═══════════════════════════════════════════════════════════
       STATS CARDS
       ═══════════════════════════════════════════════════════════ */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 24px;
      margin-top: 60px;
    }

    @media (max-width: 768px) {
      .stats-grid { grid-template-columns: repeat(2, 1fr); }
    }

    @media (max-width: 480px) {
      .stats-grid { grid-template-columns: 1fr; }
    }

    .stat-card {
      padding: 40px 24px;
      text-align: center;
      background: var(--ab-glass);
      border: 1px solid var(--ab-border);
      border-radius: var(--ab-radius-lg);
      backdrop-filter: blur(10px);
      transition: all var(--ab-transition);
      position: relative;
      overflow: hidden;
    }

    .stat-card::after {
      content: '';
      position: absolute;
      inset: 0;
      background: var(--ab-gradient);
      opacity: 0;
      transition: opacity var(--ab-transition);
      z-index: -1;
    }

    .stat-card:hover {
      transform: translateY(-8px);
      border-color: var(--ab-accent);
      box-shadow: var(--ab-shadow-lg);
    }

    .stat-card:hover::after {
      opacity: 0.05;
    }

    .stat-icon {
      font-size: 2.5rem;
      margin-bottom: 16px;
    }

    .stat-number {
      font-family: 'Bebas Neue', sans-serif;
      font-size: 3.5rem;
      line-height: 1;
      background: var(--ab-gradient);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .stat-label {
      font-size: 0.85rem;
      color: var(--ab-text-secondary);
      margin-top: 8px;
      letter-spacing: 0.04em;
    }

    /* ═══════════════════════════════════════════════════════════
       SKILLS SECTION
       ═══════════════════════════════════════════════════════════ */
    .skills-section-inner {
      background: var(--ab-glass);
      border: 1px solid var(--ab-border);
      border-radius: var(--ab-radius-xl);
      padding: 60px;
      backdrop-filter: blur(10px);
      position: relative;
      overflow: hidden;
    }

    .skills-section-inner::before {
      content: '';
      position: absolute;
      top: -100px;
      right: -100px;
      width: 400px;
      height: 400px;
      background: radial-gradient(circle, rgba(123,97,255,0.08) 0%, transparent 70%);
      pointer-events: none;
    }

    .skills-categories {
      display: flex;
      gap: 12px;
      flex-wrap: wrap;
      margin-bottom: 40px;
    }

    .skill-category-btn {
      padding: 10px 24px;
      background: transparent;
      border: 1px solid var(--ab-border);
      border-radius: 100px;
      color: var(--ab-text-secondary);
      font-family: 'Space Mono', monospace;
      font-size: 0.8rem;
      letter-spacing: 0.08em;
      cursor: pointer;
      transition: all var(--ab-transition-fast);
    }

    .skill-category-btn.active,
    .skill-category-btn:hover {
      background: var(--ab-accent);
      border-color: var(--ab-accent);
      color: #000;
    }

    .skills-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 30px;
    }

    .skill-item {
      margin-bottom: 8px;
    }

    .skill-info {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 12px;
    }

    .skill-name {
      font-weight: 500;
      font-size: 0.95rem;
    }

    .skill-percent {
      font-family: 'Space Mono', monospace;
      font-size: 0.85rem;
      color: var(--ab-accent);
    }

    .skill-bar {
      height: 8px;
      background: rgba(255,255,255,0.06);
      border-radius: 4px;
      overflow: hidden;
    }

    .skill-fill {
      height: 100%;
      background: var(--ab-gradient);
      border-radius: 4px;
      width: 0;
      transition: width 2s cubic-bezier(0.16, 1, 0.3, 1);
      box-shadow: 0 0 20px rgba(0,229,255,0.3);
    }

    /* ═══════════════════════════════════════════════════════════
       SERVICES CARDS
       ═══════════════════════════════════════════════════════════ */
    .services-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 24px;
    }

    .service-card {
      padding: 40px 32px;
      background: var(--ab-glass);
      border: 1px solid var(--ab-border);
      border-radius: var(--ab-radius-lg);
      backdrop-filter: blur(10px);
      transition: all var(--ab-transition);
      position: relative;
    }

    .service-card:hover {
      transform: translateY(-8px);
      border-color: var(--ab-border-hover);
      box-shadow: var(--ab-shadow-lg);
    }

    .service-icon {
      width: 64px;
      height: 64px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: var(--ab-radius-md);
      margin-bottom: 24px;
      background: var(--ab-glass-hover);
      border: 1px solid var(--ab-border);
      color: var(--ab-accent);
    }

    .service-title {
      font-size: 1.3rem;
      font-weight: 600;
      margin-bottom: 12px;
    }

    .service-desc {
      font-size: 0.95rem;
      color: var(--ab-text-secondary);
      line-height: 1.7;
    }

    /* ═══════════════════════════════════════════════════════════
       WORKFLOW
       ═══════════════════════════════════════════════════════════ */
    .workflow-track {
      display: flex;
      gap: 0;
      position: relative;
      overflow-x: auto;
      padding: 20px 0;
      scroll-snap-type: x mandatory;
    }

    .workflow-track::before {
      content: '';
      position: absolute;
      top: 50%;
      left: 0;
      right: 0;
      height: 2px;
      background: var(--ab-border);
      z-index: 0;
    }

    .workflow-step {
      min-width: 220px;
      padding: 40px 24px;
      text-align: center;
      scroll-snap-align: center;
      position: relative;
      z-index: 1;
      background: var(--ab-bg);
      border: 1px solid var(--ab-border);
      border-radius: var(--ab-radius-lg);
      margin: 0 12px;
      transition: all var(--ab-transition);
    }

    .workflow-step:hover {
      border-color: var(--ab-accent);
      transform: translateY(-8px);
    }

    .workflow-step-number {
      font-family: 'Bebas Neue', sans-serif;
      font-size: 3rem;
      background: var(--ab-gradient);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      margin-bottom: 8px;
    }

    .workflow-step-title {
      font-weight: 600;
      margin-bottom: 8px;
    }

    .workflow-step-desc {
      font-size: 0.85rem;
      color: var(--ab-text-secondary);
      margin-bottom: 12px;
    }

    .workflow-step-duration {
      display: inline-block;
      padding: 4px 14px;
      background: rgba(0,229,255,0.1);
      border: 1px solid rgba(0,229,255,0.2);
      border-radius: 100px;
      font-family: 'Space Mono', monospace;
      font-size: 0.7rem;
      color: var(--ab-accent);
      letter-spacing: 0.08em;
    }

    /* ═══════════════════════════════════════════════════════════
       TESTIMONIALS
       ═══════════════════════════════════════════════════════════ */
    .testimonials-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
      gap: 24px;
    }

    .testimonial-card {
      padding: 36px;
      background: var(--ab-glass);
      border: 1px solid var(--ab-border);
      border-radius: var(--ab-radius-lg);
      backdrop-filter: blur(10px);
      transition: all var(--ab-transition);
    }

    .testimonial-card:hover {
      border-color: var(--ab-border-hover);
      transform: translateY(-6px);
    }

    .testimonial-stars {
      color: #ffb800;
      margin-bottom: 16px;
      font-size: 1.2rem;
    }

    .testimonial-text {
      font-size: 1.05rem;
      line-height: 1.8;
      color: var(--ab-text-secondary);
      font-style: italic;
      margin-bottom: 20px;
    }

    .testimonial-author {
      font-weight: 600;
    }

    .testimonial-role {
      font-size: 0.85rem;
      color: var(--ab-text-muted);
    }

    /* ═══════════════════════════════════════════════════════════
       FAQ ACCORDION
       ═══════════════════════════════════════════════════════════ */
    .faq-list {
      display: flex;
      flex-direction: column;
      gap: 12px;
      max-width: 800px;
    }

    .faq-item {
      border: 1px solid var(--ab-border);
      border-radius: var(--ab-radius-md);
      overflow: hidden;
      transition: all var(--ab-transition-fast);
    }

    .faq-question {
      width: 100%;
      padding: 24px;
      background: var(--ab-glass);
      border: none;
      color: var(--ab-text-primary);
      font-family: 'Outfit', sans-serif;
      font-size: 1.05rem;
      font-weight: 500;
      text-align: left;
      cursor: pointer;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 16px;
      transition: all var(--ab-transition-fast);
    }

    .faq-question:hover {
      background: var(--ab-glass-hover);
    }

    .faq-icon {
      flex-shrink: 0;
      transition: transform var(--ab-transition-fast);
      font-size: 1.2rem;
      color: var(--ab-accent);
    }

    .faq-item.open .faq-icon {
      transform: rotate(45deg);
      color: var(--ab-accent-3);
    }

    .faq-answer {
      max-height: 0;
      overflow: hidden;
      transition: max-height 0.4s ease, padding 0.4s ease;
    }

    .faq-item.open .faq-answer {
      max-height: 300px;
      padding: 0 24px 24px;
    }

    .faq-answer p {
      color: var(--ab-text-secondary);
      line-height: 1.8;
      font-size: 0.95rem;
    }

    /* ═══════════════════════════════════════════════════════════
       CTA SECTION
       ═══════════════════════════════════════════════════════════ */
    .cta-section {
      text-align: center;
      padding: 100px 24px;
      position: relative;
    }

    .cta-card {
      max-width: 700px;
      margin: 0 auto;
      padding: 64px;
      background: var(--ab-glass);
      border: 1px solid var(--ab-border);
      border-radius: var(--ab-radius-xl);
      backdrop-filter: blur(10px);
      position: relative;
      overflow: hidden;
    }

    .cta-card::before {
      content: '';
      position: absolute;
      inset: 0;
      background: var(--ab-gradient);
      opacity: 0.03;
    }

    .cta-title {
      font-family: 'Bebas Neue', sans-serif;
      font-size: clamp(2.5rem, 5vw, 3.5rem);
      margin-bottom: 16px;
    }

    .cta-text {
      color: var(--ab-text-secondary);
      margin-bottom: 32px;
      font-size: 1.1rem;
    }

    /* ═══════════════════════════════════════════════════════════
       FOOTER
       ═══════════════════════════════════════════════════════════ */
    .site-footer {
      padding: 40px 24px;
      border-top: 1px solid var(--ab-border);
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 16px;
      max-width: 1200px;
      margin: 0 auto;
    }

    .footer-logo-text {
      font-family: 'Bebas Neue', sans-serif;
      font-size: 1.5rem;
      letter-spacing: 0.1em;
    }

    .footer-copy {
      color: var(--ab-text-muted);
      font-size: 0.85rem;
    }

    .footer-links {
      display: flex;
      gap: 24px;
    }

    .footer-links a {
      color: var(--ab-text-secondary);
      text-decoration: none;
      font-size: 0.9rem;
      transition: color var(--ab-transition-fast);
    }

    .footer-links a:hover {
      color: var(--ab-accent);
    }

    /* ═══════════════════════════════════════════════════════════
       RESPONSIVE
       ═══════════════════════════════════════════════════════════ */
    @media (max-width: 768px) {
      .section { padding: 60px 16px; }
      .hero-section { margin-top: 60px; border-radius: 0; }
      .skills-section-inner { padding: 32px 20px; }
      .skills-grid { grid-template-columns: 1fr; }
      .cta-card { padding: 40px 24px; }
      .workflow-track { flex-direction: column; gap: 16px; }
      .workflow-track::before { display: none; }
      .workflow-step { min-width: 100%; margin: 0; }
      .site-footer { flex-direction: column; text-align: center; }
    }
  </style>
</head>

<body data-watermark-enabled="<?= $watermarkEnabled ?>"
  data-watermark-text="<?= $watermarkText ?>"
  data-watermark-image="<?= $watermarkImage ?>"
  data-watermark-position="<?= $watermarkPosition ?>"
  style="--wm-opacity: <?= $watermarkOpacity ?>;">

  <!-- ─── Loading Screen ────────────────────────────────────── -->
  <div class="page-loader" id="pageLoader">
    <div class="loader-content">
      <div class="loader-logo">YS3D</div>
      <div class="loader-progress">
        <div class="loader-bar"></div>
      </div>
    </div>
  </div>

  <!-- ─── Custom Cursor ──────────────────────────────────────── -->
  <div class="cursor"></div>
  <div class="cursor-aura"></div>

  <!-- ─── Ambient Effects ───────────────────────────────────── -->
  <div class="ambient-orbs" aria-hidden="true">
    <div class="a-orb a-orb-1"></div>
    <div class="a-orb a-orb-2"></div>
    <div class="a-orb a-orb-3"></div>
  </div>

  <!-- ─── Theme Toggle ──────────────────────────────────────── -->
  <button class="theme-toggle" id="themeToggle" aria-label="Toggle theme">
    <svg class="icon-sun" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
      <circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/>
      <line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/>
      <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/>
      <line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/>
      <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
    </svg>
    <svg class="icon-moon" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
      <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
    </svg>
  </button>

  <!-- ─── Navigation ────────────────────────────────────────── -->
  <nav id="navbar">
    <a href="index.php" class="nav-logo">YS<span>3D</span></a>
    <ul class="nav-links">
      <li><a href="index.php">Home</a></li>
      <li><a href="about.php" style="color: var(--accent);">About Me</a></li>
      <li><a href="index.php#services">Services</a></li>
      <li><a href="index.php#gallery">Projects</a></li>
      <li><a href="index.php#contact">Contact</a></li>
    </ul>
    <a href="https://wa.me/201061006991" target="_blank" class="nav-whatsapp">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="margin-right:8px;">
        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347"/>
      </svg>
      WhatsApp
    </a>
  </nav>

  <!-- ─── Mobile Menu ───────────────────────────────────────── -->
  <button class="mobile-menu-btn" id="mobileMenuBtn"><span></span><span></span><span></span></button>
  <div class="mobile-nav" id="mobileNav">
    <a href="index.php" class="mobile-nav-link">Home</a>
    <a href="about.php" class="mobile-nav-link" style="color:var(--accent);">About Me</a>
    <a href="index.php#services" class="mobile-nav-link">Services</a>
    <a href="index.php#gallery" class="mobile-nav-link">Projects</a>
    <a href="index.php#contact" class="mobile-nav-link">Contact</a>
  </div>

  <!-- ═══════════════════════════════════════════════════════════
       HERO SECTION
       ═══════════════════════════════════════════════════════════ -->
  <section class="hero-section" id="hero">
    <?php if ($heroImage): ?>
      <div class="hero-bg-image" style="background-image:url('<?= htmlspecialchars($heroImage) ?>');" data-parallax="0.3"></div>
    <?php else: ?>
      <div class="hero-bg-image" style="background:linear-gradient(135deg, #0d0d1a, #1a1a30);"></div>
    <?php endif; ?>
    <div class="hero-overlay-gradient"></div>
    <div class="hero-noise"></div>
    
    <div class="hero-content">
      <div class="hero-badge">
        <span class="hero-badge-dot"></span> Available for Projects
      </div>
      <h1 class="hero-title">
        <span class="hero-title-line">Behind</span>
        <span class="hero-title-line hero-title-accent">the Vision</span>
      </h1>
      <p class="hero-subtitle"><?= $heroTagline ?></p>
      <div class="hero-actions">
        <a href="#about" class="btn-primary">Explore My Story</a>
        <a href="index.php#contact" class="btn-outline">Get in Touch</a>
      </div>
    </div>
    
    <div class="scroll-indicator">
      <span>Scroll</span>
      <div class="scroll-indicator-line"></div>
    </div>
  </section>

  <!-- ═══════════════════════════════════════════════════════════
       ABOUT SECTION
       ═══════════════════════════════════════════════════════════ -->
  <section class="section" id="about">
    <div class="about-grid">
      <!-- Left Column -->
      <div class="about-text-block" data-aos="fade-up" data-aos-duration="800">
        <div class="section-label">My Story</div>
        <h2 class="section-title">Crafting Visuals<br>with Purpose</h2>
        <p class="lead"><?= $data->getAbout('short_intro', 'I craft premium visual experiences that blend art with technical precision.') ?></p>
        <p><?= $data->getAbout('bio_1', 'I am Yousef Sala7, a visual artist focused on cinematic 3D, compositing, and motion-driven storytelling.') ?></p>
        <p><?= $data->getAbout('bio_2', 'My process combines design thinking, technical workflows, and strong attention to detail from concept to final delivery.') ?></p>
        <p><?= $data->getAbout('bio_3', 'I care about creating visuals that do not just look good, but communicate emotion, clarity, and brand value.') ?></p>
        
        <!-- Timeline -->
        <div class="timeline">
          <?php foreach ($timeline as $item): ?>
            <div class="timeline-item" data-aos="fade-right" data-aos-duration="600">
              <div class="timeline-header">
                <span class="timeline-icon"><?= $item['icon'] ?></span>
                <span class="timeline-year"><?= $item['year'] ?></span>
              </div>
              <div class="timeline-title"><?= $item['title'] ?></div>
              <div class="timeline-desc"><?= $item['description'] ?></div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
      
      <!-- Right Column -->
      <div class="visual-card" data-aos="fade-left" data-aos-duration="800">
        <div class="visual-card-wrap">
          <div class="visual-glow"></div>
          <img src="<?= $profileImage ?>" alt="Yousef Salah - 3D Artist" class="visual-card-img" loading="lazy">
        </div>
        
        <div class="info-chips">
          <div class="info-chip">📍 <?= htmlspecialchars($location) ?></div>
          <div class="info-chip">⏳ <?= $yearsExp ?>+ Years</div>
          <div class="info-chip">🎓 Self-Taught Artist</div>
        </div>
      </div>
    </div>
  </section>

  <!-- ═══════════════════════════════════════════════════════════
       ACHIEVEMENTS STATS
       ═══════════════════════════════════════════════════════════ -->
  <section class="section">
    <div class="stats-grid">
      <?php foreach ($achievements as $index => $ach): ?>
        <div class="stat-card" data-aos="zoom-in" data-aos-duration="600" data-aos-delay="<?= $index * 100 ?>">
          <div class="stat-icon"><?= $ach['icon'] ?></div>
          <div class="stat-number"><span class="counter" data-target="<?= $ach['number'] ?>">0</span>+</div>
          <div class="stat-label"><?= $ach['label'] ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- ═══════════════════════════════════════════════════════════
       TECH STACK / SKILLS
       ═══════════════════════════════════════════════════════════ -->
  <section class="section">
    <div class="skills-section-inner" data-aos="fade-up" data-aos-duration="1000">
      <div class="section-label">Technical Skills</div>
      <h2 class="section-title">Tools & Expertise</h2>
      <p class="section-subtitle">Software mastery developed through years of hands-on project experience.</p>
      
      <div class="skills-categories" id="skillCategories">
        <button class="skill-category-btn active" data-category="all">All</button>
        <button class="skill-category-btn" data-category="3D">3D</button>
        <button class="skill-category-btn" data-category="Design">Design</button>
        <button class="skill-category-btn" data-category="VFX">VFX</button>
        <button class="skill-category-btn" data-category="Post">Post-Production</button>
      </div>
      
      <div class="skills-grid" id="skillsGrid">
        <?php foreach ($techStack as $tech): ?>
          <div class="skill-item" data-category="<?= $tech['category'] ?>">
            <div class="skill-info">
              <span class="skill-name"><?= $tech['name'] ?></span>
              <span class="skill-percent"><?= $tech['level'] ?>%</span>
            </div>
            <div class="skill-bar">
              <div class="skill-fill" data-width="<?= $tech['level'] ?>%" style="width:0;"></div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- ═══════════════════════════════════════════════════════════
       SERVICES
       ═══════════════════════════════════════════════════════════ -->
  <section class="section">
    <div class="section-label">What I Do</div>
    <h2 class="section-title">Services & Expertise</h2>
    <p class="section-subtitle">Delivering premium visual solutions tailored to your project needs.</p>
    
    <div class="services-grid">
      <?php foreach ($services as $index => $service): ?>
        <div class="service-card" data-aos="fade-up" data-aos-duration="600" data-aos-delay="<?= $index * 100 ?>">
          <div class="service-icon" style="color:<?= $service['color'] ?>;"><?= $service['icon'] ?></div>
          <div class="service-title"><?= $service['title'] ?></div>
          <div class="service-desc"><?= $service['description'] ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- ═══════════════════════════════════════════════════════════
       WORKFLOW
       ═══════════════════════════════════════════════════════════ -->
  <section class="section">
    <div class="section-label">How I Work</div>
    <h2 class="section-title">Project Workflow</h2>
    <p class="section-subtitle">A structured process ensuring quality, consistency, and on-time delivery.</p>
    
    <div class="workflow-track">
      <?php foreach ($workflow as $step): ?>
        <div class="workflow-step" data-aos="fade-up" data-aos-duration="600">
          <div class="workflow-step-number"><?= $step['step'] ?></div>
          <div class="workflow-step-title"><?= $step['title'] ?></div>
          <div class="workflow-step-desc"><?= $step['description'] ?></div>
          <span class="workflow-step-duration"><?= $step['duration'] ?></span>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- ═══════════════════════════════════════════════════════════
       TESTIMONIALS
       ═══════════════════════════════════════════════════════════ -->
  <section class="section">
    <div class="section-label">Client Feedback</div>
    <h2 class="section-title">What They Say</h2>
    <p class="section-subtitle">Trusted by creative professionals and brands for impactful visual solutions.</p>
    
    <div class="testimonials-grid">
      <?php foreach ($testimonials as $index => $test): ?>
        <div class="testimonial-card" data-aos="fade-up" data-aos-duration="600" data-aos-delay="<?= $index * 100 ?>">
          <div class="testimonial-stars"><?= str_repeat('★', $test['rating']) ?></div>
          <div class="testimonial-text">"<?= $test['text'] ?>"</div>
          <div class="testimonial-author"><?= $test['author'] ?></div>
          <div class="testimonial-role"><?= $test['role'] ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- ═══════════════════════════════════════════════════════════
       FAQ
       ═══════════════════════════════════════════════════════════ -->
  <section class="section">
    <div class="section-label">FAQ</div>
    <h2 class="section-title">Frequently Asked</h2>
    <p class="section-subtitle">Quick answers to common questions about my process and services.</p>
    
    <div class="faq-list">
      <?php foreach ($faqs as $index => $faq): ?>
        <div class="faq-item" data-aos="fade-up" data-aos-duration="400" data-aos-delay="<?= $index * 50 ?>">
          <button class="faq-question" onclick="toggleFaq(this)">
            <?= $faq['question'] ?>
            <span class="faq-icon">+</span>
          </button>
          <div class="faq-answer">
            <p><?= $faq['answer'] ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- ═══════════════════════════════════════════════════════════
       CTA
       ═══════════════════════════════════════════════════════════ -->
  <section class="cta-section">
    <div class="cta-card" data-aos="fade-up" data-aos-duration="800">
      <h2 class="cta-title">Let's Create Together</h2>
      <p class="cta-text">Have a project in mind? I'd love to hear about it. Reach out and let's bring your vision to life.</p>
      <a href="index.php#contact" class="btn-primary">Start a Project</a>
    </div>
  </section>

  <!-- ═══════════════════════════════════════════════════════════
       FOOTER
       ═══════════════════════════════════════════════════════════ -->
  <footer class="site-footer">
    <span class="footer-logo-text">Yousef Salah <span style="background:var(--ab-gradient);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">3D</span></span>
    <span class="footer-copy">&copy; <?= date('Y') ?> — Crafted with precision</span>
    <div class="footer-links">
      <a href="admin_index.php">Admin Panel</a>
      <a href="mailto:yooooosef.tv@gmail.com">Email</a>
      <a href="https://wa.me/201061006991" target="_blank">WhatsApp</a>
    </div>
  </footer>

  <!-- ─── Back to Top ───────────────────────────────────────── -->
  <button class="back-to-top" id="backToTop" aria-label="Back to top">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M18 15l-6-6-6 6"/>
    </svg>
  </button>

  <!-- ─── WhatsApp Float ────────────────────────────────────── -->
  <a href="https://wa.me/201061006991" target="_blank" class="whatsapp-float" aria-label="Chat on WhatsApp">
    <svg width="26" height="26" viewBox="0 0 24 24" fill="currentColor">
      <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347"/>
    </svg>
  </a>

  <!-- ─── Scripts ───────────────────────────────────────────── -->
  <script src="script.js"></script>
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  
  <script>
    // ═══════════════════════════════════════════════════════════
    // INITIALIZATION
    // ═══════════════════════════════════════════════════════════
    document.addEventListener('DOMContentLoaded', () => {
      // Hide loader
      const loader = document.getElementById('pageLoader');
      if (loader) {
        setTimeout(() => loader.classList.add('hidden'), 800);
      }
      
      // Initialize AOS
      if (typeof AOS !== 'undefined') {
        AOS.init({
          duration: 800,
          once: true,
          offset: 50,
          easing: 'ease-out-cubic'
        });
      }
      
      // Initialize skills animation
      animateSkillsOnScroll();
      
      // Initialize counters
      animateCountersOnScroll();
      
      // Initialize parallax
      initParallax();
      
      // Initialize back to top
      initBackToTop();
      
      // Initialize skill filters
      initSkillFilters();
      
      // Initialize navbar scroll
      initNavbar();
      
      // Initialize FAQ
      initFaq();
    });
    
    // ═══════════════════════════════════════════════════════════
    // SKILLS ANIMATION
    // ═══════════════════════════════════════════════════════════
    function animateSkillsOnScroll() {
      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            const fills = entry.target.querySelectorAll('.skill-fill');
            fills.forEach(fill => {
              fill.style.width = fill.dataset.width;
            });
            observer.unobserve(entry.target);
          }
        });
      }, { threshold: 0.2 });
      
      document.querySelectorAll('.skills-section-inner').forEach(s => observer.observe(s));
    }
    
    // ═══════════════════════════════════════════════════════════
    // COUNTERS ANIMATION
    // ═══════════════════════════════════════════════════════════
    function animateCountersOnScroll() {
      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            const counters = entry.target.querySelectorAll('.counter');
            counters.forEach(counter => {
              const target = +counter.dataset.target;
              const duration = 2000;
              const start = 0;
              const increment = target / (duration / 16);
              let current = start;
              
              const update = () => {
                current += increment;
                if (current < target) {
                  counter.textContent = Math.ceil(current);
                  requestAnimationFrame(update);
                } else {
                  counter.textContent = target;
                }
              };
              update();
            });
            observer.unobserve(entry.target);
          }
        });
      }, { threshold: 0.5 });
      
      document.querySelectorAll('.stat-card').forEach(card => observer.observe(card));
    }
    
    // ═══════════════════════════════════════════════════════════
    // PARALLAX
    // ═══════════════════════════════════════════════════════════
    function initParallax() {
      const parallaxElements = document.querySelectorAll('[data-parallax]');
      
      window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;
        
        parallaxElements.forEach(el => {
          const rate = parseFloat(el.dataset.parallax) || 0.3;
          const offset = scrolled * rate;
          el.style.transform = `translateY(${offset}px) scale(1.1)`;
        });
      }, { passive: true });
    }
    
    // ═══════════════════════════════════════════════════════════
    // BACK TO TOP
    // ═══════════════════════════════════════════════════════════
    function initBackToTop() {
      const btn = document.getElementById('backToTop');
      if (!btn) return;
      
      window.addEventListener('scroll', () => {
        btn.classList.toggle('visible', window.scrollY > 600);
      }, { passive: true });
      
      btn.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
      });
    }
    
    // ═══════════════════════════════════════════════════════════
    // SKILL FILTERS
    // ═══════════════════════════════════════════════════════════
    function initSkillFilters() {
      const buttons = document.querySelectorAll('.skill-category-btn');
      const items = document.querySelectorAll('#skillsGrid .skill-item');
      
      buttons.forEach(btn => {
        btn.addEventListener('click', () => {
          const category = btn.dataset.category;
          
          buttons.forEach(b => b.classList.remove('active'));
          btn.classList.add('active');
          
          items.forEach(item => {
            if (category === 'all' || item.dataset.category === category) {
              item.style.display = 'block';
              setTimeout(() => {
                item.style.opacity = '1';
                item.style.transform = 'translateY(0)';
              }, 50);
            } else {
              item.style.opacity = '0';
              item.style.transform = 'translateY(10px)';
              setTimeout(() => {
                item.style.display = 'none';
              }, 300);
            }
          });
          
          // Re-animate visible skill bars
          setTimeout(() => {
            document.querySelectorAll('#skillsGrid .skill-item[style*="display: block"] .skill-fill, #skillsGrid .skill-item:not([style*="display: none"]) .skill-fill').forEach(fill => {
              fill.style.width = '0';
              requestAnimationFrame(() => {
                fill.style.width = fill.dataset.width;
              });
            });
          }, 350);
        });
      });
      
      // Initial animation
      setTimeout(() => {
        document.querySelectorAll('.skill-fill').forEach(fill => {
          fill.style.width = fill.dataset.width;
        });
      }, 500);
    }
    
    // ═══════════════════════════════════════════════════════════
    // NAVBAR
    // ═══════════════════════════════════════════════════════════
    function initNavbar() {
      const navbar = document.getElementById('navbar');
      if (!navbar) return;
      
      window.addEventListener('scroll', () => {
        navbar.classList.toggle('scrolled', window.scrollY > 60);
      }, { passive: true });
    }
    
    // ═══════════════════════════════════════════════════════════
    // FAQ TOGGLE
    // ═══════════════════════════════════════════════════════════
    function initFaq() {
      // FAQ toggle is handled via inline onclick
    }
    
    function toggleFaq(button) {
      const item = button.parentElement;
      const isOpen = item.classList.contains('open');
      
      // Close all other FAQs
      document.querySelectorAll('.faq-item.open').forEach(el => {
        if (el !== item) el.classList.remove('open');
      });
      
      // Toggle current
      item.classList.toggle('open', !isOpen);
    }
  </script>
</body>
</html>