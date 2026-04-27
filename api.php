<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE, PUT");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}
require 'db.php';

$action = $_GET['action'] ?? '';

if ($action === 'folders') {
    $sql = "
        SELECT
            f.*,
            (
                SELECT i.file_name
                FROM images i
                WHERE i.folder_id = f.id
                ORDER BY i.id DESC
                LIMIT 1
            ) AS cover_image,
            (
                SELECT COUNT(*)
                FROM images i2
                WHERE i2.folder_id = f.id
            ) AS image_count
        FROM folders f
        ORDER BY f.id DESC
    ";
    $folders = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'folders' => $folders]);
    exit;
} elseif ($action === 'images') {
    $folder_id = intval($_GET['folder_id'] ?? 0);
    $stmt = $pdo->prepare("SELECT * FROM images WHERE folder_id = :fid ORDER BY id DESC");
    $stmt->execute([':fid' => $folder_id]);
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'images' => $images]);
    exit;
} elseif ($action === 'about') {
    $about = $pdo->query("SELECT * FROM about_settings WHERE id=1")->fetch(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'about' => $about]);
    exit;
}

echo json_encode(['success' => false, 'error' => 'Invalid action']);
?>
