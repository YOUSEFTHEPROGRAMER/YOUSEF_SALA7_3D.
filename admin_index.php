<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require 'db.php';

// ── كلمة المرور ─────────────────────────────────────────────────────────────
$admin_password = 'password123'; // غيرها لكلمة سر قوية

if (!isset($_SESSION['is_admin'])) {
    $login_error = '';
    
    if (isset($_POST['password'])) {
        if ($_POST['password'] === $admin_password) {
            session_regenerate_id(true);
            $_SESSION['is_admin'] = true;
            $_SESSION['just_logged_in'] = true;
            header('Location: admin_index.php?tab=dashboard');
            exit;
        } else {
            $login_error = '<div class="login-error">⛔ Incorrect password! Please try again.</div>';
        }
    }
    
    // صفحة تسجيل الدخول
    echo '<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin — Yousef Sala7 3D</title>
  <link rel="stylesheet" href="admin_style.css">
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Outfit:wght@300;400;500;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="login-page">
  <div class="login-card">
    <div class="login-logo">YS3D</div>
    <div class="login-sub">Admin Dashboard</div>
    ' . $login_error . '
    <form method="POST">
      <input type="password" name="password" class="input-field" placeholder="Enter admin password" required autofocus>
      <button type="submit" class="submit-btn">Access Dashboard</button>
    </form>
  </div>
</div>
</body>
</html>';
    exit;
}

// ── Logout ─────────────────────────────────────────────────────────────────
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

// ── Welcome Screen Check ──────────────────────────────────────────────────
$show_welcome = false;
if (isset($_SESSION['just_logged_in']) && $_SESSION['just_logged_in'] === true) {
    $show_welcome = true;
    unset($_SESSION['just_logged_in']);
}

$msg = '';
$tab = $_GET['tab'] ?? 'dashboard';

// ── Create folder ─────────────────────────────────────────────────────────────
if (isset($_POST['create_folder'])) {
    $name = trim($_POST['folder_name']);
    if (!empty($name)) {
        $pdo->prepare("INSERT INTO folders (name) VALUES (:n)")->execute([':n' => $name]);
        $msg = "<p class='success'>✓ Folder \"" . htmlspecialchars($name) . "\" created.</p>";
    }
}

// ── Rename folder ─────────────────────────────────────────────────────────────
if (isset($_POST['rename_folder'])) {
    $fid  = intval($_POST['folder_id']);
    $name = trim($_POST['new_name']);
    if ($fid && !empty($name)) {
        $pdo->prepare("UPDATE folders SET name=:n WHERE id=:id")->execute([':n' => $name, ':id' => $fid]);
        $msg = "<p class='success'>✓ Folder renamed successfully.</p>";
    }
}

// ── Delete folder ─────────────────────────────────────────────────────────────
if (isset($_POST['delete_folder'])) {
    $fid = intval($_POST['folder_id']);
    if ($fid) {
        $imgs = $pdo->prepare("SELECT file_name FROM images WHERE folder_id=:fid");
        $imgs->execute([':fid' => $fid]);

        while ($row = $imgs->fetch(PDO::FETCH_ASSOC)) {
            $file_path = __DIR__ . '/' . $row['file_name'];
            if (file_exists($file_path)) {
                @unlink($file_path);
            }
        }

        $pdo->prepare("DELETE FROM images WHERE folder_id=:fid")->execute([':fid' => $fid]);
        $pdo->prepare("DELETE FROM folders WHERE id=:fid")->execute([':fid' => $fid]);
        $msg = "<p class='success'>✓ Folder and all its images deleted.</p>";
    }
}

// ── Upload image ──────────────────────────────────────────────────────────────
if (isset($_FILES['image']) && isset($_POST['folder_id'])) {
    $folder_id  = intval($_POST['folder_id']);
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

    $file_name   = preg_replace("/[^a-zA-Z0-9.-]/", "_", basename($_FILES["image"]["name"]));
    $target_file = $target_dir . time() . '_' . $file_name;

    if (getimagesize($_FILES["image"]["tmp_name"]) !== false) {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $pdo->prepare("INSERT INTO images (file_name, folder_id) VALUES (:f, :fid)")
                ->execute([':f' => $target_file, ':fid' => $folder_id]);
            $msg = "<p class='success'>✓ Image uploaded successfully.</p>";
        } else {
            $msg = "<p class='error'>✗ Upload failed. Check directory permissions.</p>";
        }
    } else {
        $msg = "<p class='error'>✗ File is not a valid image.</p>";
    }
}

// ── Delete image ──────────────────────────────────────────────────────────────
if (isset($_POST['delete_image'])) {
    $iid = intval($_POST['image_id']);
    if ($iid) {
        $stmt = $pdo->prepare("SELECT file_name FROM images WHERE id=:id");
        $stmt->execute([':id' => $iid]);
        $img = $stmt->fetch();
        
        if ($img) {
            $file_path = __DIR__ . '/' . $img['file_name'];
            if (file_exists($file_path)) {
                @unlink($file_path);
            }
            $pdo->prepare("DELETE FROM images WHERE id=:id")->execute([':id' => $iid]);
            $msg = "<p class='success'>✓ Image deleted.</p>";
        }
    }
}

// ── Delete message ────────────────────────────────────────────────────────────
if (isset($_POST['delete_message'])) {
    $mid = intval($_POST['message_id']);
    if ($mid) {
        $pdo->prepare("DELETE FROM messages WHERE id=:id")->execute([':id' => $mid]);
        $msg = "<p class='success'>✓ Message deleted.</p>";
    }
}

// ── Update Watermark ──────────────────────────────────────────────────────────
if (isset($_POST['save_watermark'])) {
    $watermark_text = trim($_POST['watermark_text'] ?? '');
    $allowed_positions = ['top-left', 'top-right', 'center', 'bottom-left', 'bottom-right'];
    $watermark_position = $_POST['watermark_position'] ?? 'bottom-right';
    if (!in_array($watermark_position, $allowed_positions, true)) {
        $watermark_position = 'bottom-right';
    }
    $watermark_opacity = max(0.15, min(1, (float)($_POST['watermark_opacity'] ?? 0.62)));
    $watermark_enabled = isset($_POST['watermark_enabled']) ? 1 : 0;

    $pdo->prepare("UPDATE site_settings SET
        watermark_text=:watermark_text,
        watermark_position=:watermark_position,
        watermark_opacity=:watermark_opacity,
        watermark_enabled=:watermark_enabled,
        updated_at=CURRENT_TIMESTAMP
        WHERE id=1")->execute([
            ':watermark_text' => $watermark_text,
            ':watermark_position' => $watermark_position,
            ':watermark_opacity' => $watermark_opacity,
            ':watermark_enabled' => $watermark_enabled
        ]);

    $msg = "<p class='success'>Watermark settings updated.</p>";
}

if (isset($_FILES['watermark_image']) && isset($_POST['upload_watermark_image'])) {
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

    $file_name = preg_replace("/[^a-zA-Z0-9.-]/", "_", basename($_FILES["watermark_image"]["name"]));
    $target_file = $target_dir . 'watermark_' . time() . '_' . $file_name;

    if (!empty($_FILES["watermark_image"]["tmp_name"]) && getimagesize($_FILES["watermark_image"]["tmp_name"]) !== false) {
        if (move_uploaded_file($_FILES["watermark_image"]["tmp_name"], $target_file)) {
            $current = $pdo->query("SELECT watermark_image FROM site_settings WHERE id=1")->fetchColumn();
            if (!empty($current) && file_exists(__DIR__ . '/' . $current)) {
                @unlink(__DIR__ . '/' . $current);
            }
            $pdo->prepare("UPDATE site_settings SET watermark_image=:img, watermark_enabled=1, updated_at=CURRENT_TIMESTAMP WHERE id=1")
                ->execute([':img' => $target_file]);
            $msg = "<p class='success'>Watermark image updated.</p>";
        } else {
            $msg = "<p class='error'>Failed to upload watermark image.</p>";
        }
    } else {
        $msg = "<p class='error'>Invalid watermark image.</p>";
    }
}

// ── Update About Me content ───────────────────────────────────────────────────
if (isset($_POST['save_about'])) {
    $hero_tagline     = trim($_POST['hero_tagline'] ?? '');
    $about_title      = trim($_POST['about_title'] ?? '');
    $short_intro      = trim($_POST['short_intro'] ?? '');
    $bio_1            = trim($_POST['bio_1'] ?? '');
    $bio_2            = trim($_POST['bio_2'] ?? '');
    $bio_3            = trim($_POST['bio_3'] ?? '');
    $location         = trim($_POST['location'] ?? '');
    $years_experience = trim($_POST['years_experience'] ?? '');
    $specialties      = trim($_POST['specialties'] ?? '');

    $pdo->prepare("UPDATE about_settings SET
        hero_tagline=:hero_tagline,
        about_title=:about_title,
        short_intro=:short_intro,
        bio_1=:bio_1,
        bio_2=:bio_2,
        bio_3=:bio_3,
        location=:location,
        years_experience=:years_experience,
        specialties=:specialties,
        updated_at=CURRENT_TIMESTAMP
        WHERE id=1")->execute([
            ':hero_tagline' => $hero_tagline,
            ':about_title' => $about_title,
            ':short_intro' => $short_intro,
            ':bio_1' => $bio_1,
            ':bio_2' => $bio_2,
            ':bio_3' => $bio_3,
            ':location' => $location,
            ':years_experience' => $years_experience,
            ':specialties' => $specialties
        ]);

    $msg = "<p class='success'>✓ About Me content updated.</p>";
}

// ── Upload profile image ──────────────────────────────────────────────────────
if (isset($_FILES['profile_image']) && isset($_POST['upload_profile_image'])) {
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

    $file_name   = preg_replace("/[^a-zA-Z0-9.-]/", "_", basename($_FILES["profile_image"]["name"]));
    $target_file = $target_dir . 'profile_' . time() . '_' . $file_name;

    if (!empty($_FILES["profile_image"]["tmp_name"]) && getimagesize($_FILES["profile_image"]["tmp_name"]) !== false) {
        if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
            $current = $pdo->query("SELECT profile_image FROM about_settings WHERE id=1")->fetchColumn();
            if (!empty($current) && file_exists(__DIR__ . '/' . $current)) {
                @unlink(__DIR__ . '/' . $current);
            }
            $pdo->prepare("UPDATE about_settings SET profile_image=:img, updated_at=CURRENT_TIMESTAMP WHERE id=1")
                ->execute([':img' => $target_file]);
            $msg = "<p class='success'>✓ Profile image updated.</p>";
        } else {
            $msg = "<p class='error'>✗ Failed to upload profile image.</p>";
        }
    } else {
        $msg = "<p class='error'>✗ Invalid image file.</p>";
    }
}

// ── Upload hero image ─────────────────────────────────────────────────────────
if (isset($_FILES['hero_image']) && isset($_POST['upload_hero_image'])) {
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

    $file_name   = preg_replace("/[^a-zA-Z0-9.-]/", "_", basename($_FILES["hero_image"]["name"]));
    $target_file = $target_dir . 'hero_' . time() . '_' . $file_name;

    if (!empty($_FILES["hero_image"]["tmp_name"]) && getimagesize($_FILES["hero_image"]["tmp_name"]) !== false) {
        if (move_uploaded_file($_FILES["hero_image"]["tmp_name"], $target_file)) {
            $current = $pdo->query("SELECT hero_image FROM about_settings WHERE id=1")->fetchColumn();
            if (!empty($current) && file_exists(__DIR__ . '/' . $current)) {
                @unlink(__DIR__ . '/' . $current);
            }
            $pdo->prepare("UPDATE about_settings SET hero_image=:img, updated_at=CURRENT_TIMESTAMP WHERE id=1")
                ->execute([':img' => $target_file]);
            $msg = "<p class='success'>✓ Hero image updated.</p>";
        } else {
            $msg = "<p class='error'>✗ Failed to upload hero image.</p>";
        }
    } else {
        $msg = "<p class='error'>✗ Invalid hero image file.</p>";
    }
}

// ── Ensure messages table exists ──────────────────────────────────────────────
$pdo->exec("CREATE TABLE IF NOT EXISTS messages (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT,
    email TEXT,
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// ── Stats ─────────────────────────────────────────────────────────────────────
$total_views    = $pdo->query("SELECT COUNT(*) FROM visits")->fetchColumn();
$total_images   = $pdo->query("SELECT COUNT(*) FROM images")->fetchColumn();
$total_folders  = $pdo->query("SELECT COUNT(*) FROM folders")->fetchColumn();
$total_messages = $pdo->query("SELECT COUNT(*) FROM messages")->fetchColumn();
$folders        = $pdo->query("SELECT * FROM folders ORDER BY id DESC")->fetchAll();

// Images per folder + جلب أول صورة كغلاف
$images_by_folder = [];
$folder_thumbnails = [];
foreach ($folders as $f) {
    $stmt = $pdo->prepare("SELECT * FROM images WHERE folder_id=:fid ORDER BY id ASC LIMIT 1");
    $stmt->execute([':fid' => $f['id']]);
    $folder_thumbnails[$f['id']] = $stmt->fetch();

    $stmt_all = $pdo->prepare("SELECT * FROM images WHERE folder_id=:fid ORDER BY id DESC");
    $stmt_all->execute([':fid' => $f['id']]);
    $images_by_folder[$f['id']] = $stmt_all->fetchAll();
}

// Messages
$messages = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC")->fetchAll();
$about = $pdo->query("SELECT * FROM about_settings WHERE id=1")->fetch();
$settings = $pdo->query("SELECT * FROM site_settings WHERE id=1")->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard — Yousef Sala7 3D</title>
  <link rel="stylesheet" href="admin_style.css">
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Outfit:wght@300;400;500;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>

<?php if ($show_welcome): ?>
<!-- ══════════════════════════════════════════
   WELCOME SCREEN
══════════════════════════════════════════ -->
<div class="welcome-overlay">
  <div class="welcome-content">
    <div class="welcome-icon">
      <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
        <polyline points="22 4 12 14.01 9 11.01"/>
      </svg>
    </div>
    <h1 class="welcome-title">Welcome Back</h1>
    <p class="welcome-subtitle">Dashboard Ready</p>
  </div>
</div>
<?php endif; ?>

<div class="admin-layout">

  <!-- Sidebar -->
  <aside class="sidebar">
    <div class="sidebar-logo">YS<span>3D</span></div>

    <div>
      <div class="sidebar-label">Navigation</div>
      <ul class="sidebar-nav">
        <li><a href="admin_index.php?tab=dashboard" class="<?= $tab==='dashboard' ? 'active':'' ?>">
          <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
          Dashboard
        </a></li>
        <li><a href="admin_index.php?tab=folders" class="<?= $tab==='folders' ? 'active':'' ?>">
          <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/></svg>
          Projects
        </a></li>
        <li><a href="admin_index.php?tab=upload" class="<?= $tab==='upload' ? 'active':'' ?>">
          <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
          Upload
        </a></li>
        <li><a href="admin_index.php?tab=messages" class="<?= $tab==='messages' ? 'active':'' ?>">
          <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
          Messages <span class="badge"><?= $total_messages ?></span>
        </a></li>
        <li><a href="admin_index.php?tab=watermark" class="<?= $tab==='watermark' ? 'active':'' ?>">
          <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 17h16M8 7v10m8-10v10M6 4h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2z"/></svg>
          Watermark
        </a></li>
        <li><a href="admin_index.php?tab=about" class="<?= $tab==='about' ? 'active':'' ?>">
          <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14h.01M9 9h6m-6 4h6m-6 4h3M7 3h10a2 2 0 012 2v14l-3-2-3 2-3-2-3 2V5a2 2 0 012-2z"/></svg>
          About Me
        </a></li>
        <li><a href="index.php" target="_blank">
          <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
          View Portfolio
        </a></li>
      </ul>
    </div>

    <a href="admin_index.php?logout=true" class="sidebar-logout">
      <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
      Logout
    </a>
  </aside>

  <!-- Main -->
  <main class="admin-main">

    <?php if ($msg): ?><div class="msg-bar"><?= $msg ?></div><?php endif; ?>

    <?php if ($tab === 'dashboard'): ?>
    <!-- ── DASHBOARD ──────────────────────────────────────────── -->
    <div class="page-header">
      <h1 class="page-title">Dashboard</h1>
      <p class="page-sub">Welcome back, Yousef.</p>
    </div>
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-card-label">Unique Visitors</div>
        <div class="stat-card-number"><?= number_format((int)$total_views) ?></div>
      </div>
      <div class="stat-card">
        <div class="stat-card-label">Projects</div>
        <div class="stat-card-number"><?= $total_folders ?></div>
      </div>
      <div class="stat-card">
        <div class="stat-card-label">Gallery Images</div>
        <div class="stat-card-number"><?= $total_images ?></div>
      </div>
      <div class="stat-card">
        <div class="stat-card-label">Messages</div>
        <div class="stat-card-number"><?= $total_messages ?></div>
      </div>
    </div>

    <div class="panels-grid">
      <div class="panel">
        <h2 class="panel-title">New Project Folder</h2>
        <p class="panel-sub">Create a new folder to organize your artwork.</p>
        <form method="POST">
          <input type="text" name="folder_name" class="input-field" placeholder="e.g. Rolex 3D Render" required>
          <button type="submit" name="create_folder" class="submit-btn">Create Folder</button>
        </form>
      </div>
      <div class="panel">
        <h2 class="panel-title">Quick Upload</h2>
        <p class="panel-sub">Add images to a project folder.</p>
        <form method="POST" enctype="multipart/form-data">
          <select name="folder_id" class="input-field" required>
            <option value="" disabled selected>Select folder…</option>
            <?php foreach ($folders as $f): ?>
              <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['name']) ?></option>
            <?php endforeach; ?>
          </select>
          <input type="file" name="image" accept="image/*" class="input-field" required>
          <button type="submit" class="submit-btn">Upload Image</button>
        </form>
      </div>
    </div>

    <?php elseif ($tab === 'folders'): ?>
    <!-- ── PROJECTS ───────────────────────────────────────────── -->
    <div class="page-header">
      <h1 class="page-title">Projects</h1>
      <p class="page-sub">Manage your project folders and images.</p>
    </div>

    <?php if (empty($folders)): ?>
      <div class="empty-state">
        <p>No projects yet. <a href="admin_index.php?tab=dashboard">Create one.</a></p>
      </div>
    <?php else: ?>
    <?php foreach ($folders as $f): ?>
    <div class="folder-card">
      <div class="folder-header">
        <div class="folder-title-area">
          <?php
            $thumb = $folder_thumbnails[$f['id']] ?? null;
            if ($thumb && file_exists(__DIR__ . '/' . $thumb['file_name'])):
          ?>
            <img src="<?= htmlspecialchars($thumb['file_name']) ?>" alt="Cover" class="folder-cover">
          <?php else: ?>
            <div class="folder-cover" style="background: #2a2a3c;"></div>
          <?php endif; ?>
          <div>
            <div class="folder-name"><?= htmlspecialchars($f['name']) ?></div>
            <div class="folder-count"><?= count($images_by_folder[$f['id']]) ?> image(s)</div>
          </div>
        </div>
        <div class="folder-actions">
          <button class="btn-sm" onclick="toggleRename(<?= $f['id'] ?>)">Rename</button>
          <form method="POST" style="display:inline" onsubmit="return confirm('Delete this folder and ALL its images?\nThis action cannot be undone.')">
            <input type="hidden" name="folder_id" value="<?= $f['id'] ?>">
            <button type="submit" name="delete_folder" class="btn-sm btn-danger">Delete Folder</button>
          </form>
        </div>
      </div>

      <div class="rename-form" id="rename-<?= $f['id'] ?>" style="display:none; margin-top: 20px;">
        <form method="POST" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
          <input type="hidden" name="folder_id" value="<?= $f['id'] ?>">
          <input type="text" name="new_name" class="input-field" style="margin:0;flex:1;min-width:200px" value="<?= htmlspecialchars($f['name']) ?>" required>
          <button type="submit" name="rename_folder" class="submit-btn" style="width:auto;padding:12px 24px">Save</button>
        </form>
      </div>

      <?php if (!empty($images_by_folder[$f['id']])): ?>
      <div class="images-grid">
        <?php foreach ($images_by_folder[$f['id']] as $img): ?>
        <div class="image-item">
          <img src="<?= htmlspecialchars($img['file_name']) ?>" alt="Image" loading="lazy">
          <div class="image-overlay">
            <form method="POST" onsubmit="return confirm('Delete this image?')">
              <input type="hidden" name="image_id" value="<?= $img['id'] ?>">
              <button type="submit" name="delete_image" class="img-delete-btn">✕</button>
            </form>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php else: ?>
        <p class="no-images">No images yet. <a href="admin_index.php?tab=upload">Upload some.</a></p>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>

    <?php elseif ($tab === 'upload'): ?>
    <!-- ── UPLOAD ──────────────────────────────────────────────── -->
    <div class="page-header">
      <h1 class="page-title">Upload Artwork</h1>
      <p class="page-sub">Add new images to your portfolio.</p>
    </div>
    <div class="panels-grid">
      <div class="panel">
        <h2 class="panel-title">New Project Folder</h2>
        <form method="POST">
          <input type="text" name="folder_name" class="input-field" placeholder="e.g. Rolex 3D Render" required>
          <button type="submit" name="create_folder" class="submit-btn">Create Folder</button>
        </form>
      </div>
      <div class="panel">
        <h2 class="panel-title">Upload Image</h2>
        <form method="POST" enctype="multipart/form-data">
          <select name="folder_id" class="input-field" required>
            <option value="" disabled selected>Select folder…</option>
            <?php foreach ($folders as $f): ?>
              <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['name']) ?></option>
            <?php endforeach; ?>
          </select>
          <input type="file" name="image" accept="image/*" class="input-field" required>
          <button type="submit" class="submit-btn">Upload Image</button>
        </form>
      </div>
    </div>

    <?php elseif ($tab === 'messages'): ?>
    <!-- ── MESSAGES ───────────────────────────────────────────── -->
    <div class="page-header">
      <h1 class="page-title">Messages</h1>
      <p class="page-sub"><?= $total_messages ?> message(s) received.</p>
    </div>

    <?php if (empty($messages)): ?>
      <div class="empty-state"><p>No messages yet.</p></div>
    <?php else: ?>
    <div class="messages-list">
      <?php foreach ($messages as $m): ?>
      <div class="message-card">
        <div class="message-header">
          <div>
            <div class="message-name"><?= htmlspecialchars($m['name']) ?></div>
            <a href="mailto:<?= htmlspecialchars($m['email']) ?>" class="message-email"><?= htmlspecialchars($m['email']) ?></a>
          </div>
          <div class="message-meta">
            <span class="message-date"><?= date('M j, Y — H:i', strtotime($m['created_at'])) ?></span>
            <form method="POST" style="display:inline" onsubmit="return confirm('Delete this message?')">
              <input type="hidden" name="message_id" value="<?= $m['id'] ?>">
              <button type="submit" name="delete_message" class="btn-sm btn-danger">Delete</button>
            </form>
          </div>
        </div>
        <div class="message-body"><?= nl2br(htmlspecialchars($m['message'])) ?></div>
        <a href="mailto:<?= htmlspecialchars($m['email']) ?>?subject=Re: Your message&body=Hi <?= urlencode($m['name']) ?>," class="reply-btn">↩ Reply via Email</a>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php elseif ($tab === 'watermark'): ?>
    <!-- ── WATERMARK ──────────────────────────────────────────── -->
    <div class="page-header">
      <h1 class="page-title">Watermark</h1>
      <p class="page-sub">Control the fixed mark shown on every project cover and lightbox image.</p>
    </div>
    <div class="panels-grid">
      <div class="panel">
        <h2 class="panel-title">Watermark Settings</h2>
        <p class="panel-sub">Upload your logo or signature image, then choose where it sits on every project.</p>
        <?php if (!empty($settings['watermark_image'])): ?>
          <div class="watermark-admin-current">
            <img src="<?= htmlspecialchars($settings['watermark_image']) ?>" alt="Current watermark">
          </div>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
          <label class="field-label">Watermark Image</label>
          <input type="file" name="watermark_image" accept="image/*" class="input-field" required>
          <button type="submit" name="upload_watermark_image" class="submit-btn">Upload Watermark Image</button>
        </form>
        <hr style="border:0;border-top:1px solid rgba(255,255,255,0.1);margin:18px 0;">
        <form method="POST">
          <label class="field-label">Fallback Text</label>
          <input type="text" name="watermark_text" class="input-field" value="<?= htmlspecialchars($settings['watermark_text'] ?? '') ?>" placeholder="Shown only if no image is uploaded">
          <label class="field-label">Position</label>
          <select name="watermark_position" class="input-field">
            <?php
              $positions = ['bottom-right' => 'Bottom Right', 'bottom-left' => 'Bottom Left', 'top-right' => 'Top Right', 'top-left' => 'Top Left', 'center' => 'Center'];
              foreach ($positions as $value => $label):
            ?>
              <option value="<?= $value ?>" <?= (($settings['watermark_position'] ?? 'bottom-right') === $value) ? 'selected' : '' ?>><?= $label ?></option>
            <?php endforeach; ?>
          </select>
          <label class="field-label">Opacity</label>
          <input type="range" name="watermark_opacity" class="range-field" min="0.15" max="1" step="0.05" value="<?= htmlspecialchars($settings['watermark_opacity'] ?? '0.62') ?>">
          <label class="check-field">
            <input type="checkbox" name="watermark_enabled" <?= !empty($settings['watermark_enabled']) ? 'checked' : '' ?>>
            <span>Show watermark on public projects</span>
          </label>
          <button type="submit" name="save_watermark" class="submit-btn">Save Watermark</button>
        </form>
      </div>
      <div class="panel">
        <h2 class="panel-title">Preview</h2>
        <div class="watermark-preview watermark-pos-<?= htmlspecialchars($settings['watermark_position'] ?? 'bottom-right') ?>" style="--wm-opacity: <?= htmlspecialchars($settings['watermark_opacity'] ?? '0.62') ?>;">
          <div class="watermark-preview-img"></div>
          <?php if (!empty($settings['watermark_enabled'])): ?>
            <span class="watermark-preview-mark <?= !empty($settings['watermark_image']) ? 'has-image' : '' ?>">
              <?php if (!empty($settings['watermark_image'])): ?>
                <img src="<?= htmlspecialchars($settings['watermark_image']) ?>" alt="">
              <?php else: ?>
                <?= htmlspecialchars($settings['watermark_text'] ?? 'Yousef Sala7') ?>
              <?php endif; ?>
            </span>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <?php elseif ($tab === 'about'): ?>
    <!-- ── ABOUT ME ───────────────────────────────────────────── -->
    <div class="page-header">
      <h1 class="page-title">About Me Settings</h1>
      <p class="page-sub">Control your biography content and personal photo from one place.</p>
    </div>
    <div class="panels-grid">
      <div class="panel">
        <h2 class="panel-title">Profile Image</h2>
        <p class="panel-sub">Upload your personal image shown in Hero and About sections.</p>
        <?php if (!empty($about['profile_image'])): ?>
          <img src="<?= htmlspecialchars($about['profile_image']) ?>" alt="Profile" class="about-admin-preview">
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
          <input type="file" name="profile_image" accept="image/*" class="input-field" required>
          <button type="submit" name="upload_profile_image" class="submit-btn">Upload Profile Image</button>
        </form>
        <hr style="border:0;border-top:1px solid rgba(255,255,255,0.1);margin:18px 0;">
        <h2 class="panel-title">About Hero Image</h2>
        <p class="panel-sub">Upload the large background image used at the top of About page.</p>
        <?php if (!empty($about['hero_image'])): ?>
          <img src="<?= htmlspecialchars($about['hero_image']) ?>" alt="Hero" class="about-admin-preview" style="width:100%;height:180px;border-radius:14px;">
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
          <input type="file" name="hero_image" accept="image/*" class="input-field" required>
          <button type="submit" name="upload_hero_image" class="submit-btn">Upload Hero Image</button>
        </form>
      </div>
      <div class="panel">
        <h2 class="panel-title">About Me Content</h2>
        <p class="panel-sub">Edit the public text that appears on your portfolio.</p>
        <form method="POST">
          <label class="field-label">Hero Tagline</label>
          <input type="text" name="hero_tagline" class="input-field" value="<?= htmlspecialchars($about['hero_tagline'] ?? '') ?>">
          <label class="field-label">Section Title</label>
          <input type="text" name="about_title" class="input-field" value="<?= htmlspecialchars($about['about_title'] ?? '') ?>">
          <label class="field-label">Short Intro</label>
          <textarea name="short_intro" class="input-field input-area" rows="3"><?= htmlspecialchars($about['short_intro'] ?? '') ?></textarea>
          <label class="field-label">Bio Paragraph 1</label>
          <textarea name="bio_1" class="input-field input-area" rows="4"><?= htmlspecialchars($about['bio_1'] ?? '') ?></textarea>
          <label class="field-label">Bio Paragraph 2</label>
          <textarea name="bio_2" class="input-field input-area" rows="4"><?= htmlspecialchars($about['bio_2'] ?? '') ?></textarea>
          <label class="field-label">Bio Paragraph 3</label>
          <textarea name="bio_3" class="input-field input-area" rows="4"><?= htmlspecialchars($about['bio_3'] ?? '') ?></textarea>
          <label class="field-label">Location</label>
          <input type="text" name="location" class="input-field" value="<?= htmlspecialchars($about['location'] ?? '') ?>">
          <label class="field-label">Years of Experience</label>
          <input type="text" name="years_experience" class="input-field" value="<?= htmlspecialchars($about['years_experience'] ?? '') ?>">
          <label class="field-label">Specialties (comma separated)</label>
          <input type="text" name="specialties" class="input-field" value="<?= htmlspecialchars($about['specialties'] ?? '') ?>">
          <button type="submit" name="save_about" class="submit-btn">Save About Content</button>
        </form>
      </div>
    </div>
    <?php endif; ?>

  </main>
</div>

<script>
function toggleRename(id) {
  const el = document.getElementById('rename-' + id);
  if (el) {
   el.style.display = el.style.display === 'none' ? 'block' : 'none';
  }
}
</script>

</body>
</html>