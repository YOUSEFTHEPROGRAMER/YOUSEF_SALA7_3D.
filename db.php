<?php
// db.php
// A simple SQLite setup so that no external database server is required.

$db_file = __DIR__ . '/portfolio.db';

try {
    $pdo = new PDO('sqlite:' . $db_file);
    // Enable exceptions on errors
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Make sure we fetch associative arrays by default
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Connection failed: " . $e->getMessage());
}

// About me editable content
$pdo->exec("CREATE TABLE IF NOT EXISTS about_settings (
    id INTEGER PRIMARY KEY CHECK (id = 1),
    profile_image TEXT,
    hero_tagline TEXT,
    about_title TEXT,
    short_intro TEXT,
    bio_1 TEXT,
    bio_2 TEXT,
    bio_3 TEXT,
    location TEXT,
    years_experience TEXT,
    specialties TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$exists = $pdo->query("SELECT COUNT(*) FROM about_settings WHERE id = 1")->fetchColumn();
if ((int)$exists === 0) {
    $stmt = $pdo->prepare("INSERT INTO about_settings
        (id, profile_image, hero_tagline, about_title, short_intro, bio_1, bio_2, bio_3, location, years_experience, specialties)
        VALUES
        (1, '', '3D Artist • VFX Compositor • Visual Storyteller', 'About Me',
         'I craft premium visual experiences that blend art with technical precision.',
         'I am Yousef Sala7, a visual artist focused on cinematic 3D, compositing, and motion-driven storytelling.',
         'My process combines design thinking, technical workflows, and strong attention to detail from concept to final delivery.',
         'I care about creating visuals that do not just look good, but communicate emotion, clarity, and brand value.',
         'Egypt', '3+', 'VFX Compositing, 3D Visualization, Motion Graphics, UI/UX')"
    );
    $stmt->execute();
}

// Optional hero image for about page
try {
    $pdo->exec("ALTER TABLE about_settings ADD COLUMN hero_image TEXT");
} catch (PDOException $e) {
    // Column already exists in most cases.
}

// Global site controls
$pdo->exec("CREATE TABLE IF NOT EXISTS site_settings (
    id INTEGER PRIMARY KEY CHECK (id = 1),
    watermark_text TEXT DEFAULT 'Yousef Sala7',
    watermark_image TEXT DEFAULT '',
    watermark_position TEXT DEFAULT 'bottom-right',
    watermark_opacity REAL DEFAULT 0.62,
    watermark_enabled INTEGER DEFAULT 1,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$settingsExists = $pdo->query("SELECT COUNT(*) FROM site_settings WHERE id = 1")->fetchColumn();
if ((int)$settingsExists === 0) {
    $stmt = $pdo->prepare("INSERT INTO site_settings
        (id, watermark_text, watermark_position, watermark_opacity, watermark_enabled)
        VALUES (1, 'Yousef Sala7 | Photographer & Visual Artist', 'bottom-right', 0.62, 1)"
    );
    $stmt->execute();
}

try {
    $pdo->exec("ALTER TABLE site_settings ADD COLUMN watermark_image TEXT DEFAULT ''");
} catch (PDOException $e) {
    // Column already exists in most cases.
}
?>
