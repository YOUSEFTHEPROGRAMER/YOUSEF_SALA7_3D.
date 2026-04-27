<?php
// track.php
// Tracks unique daily visits

if (!isset($pdo)) {
    require 'db.php';
}

$ip_address = $_SERVER['REMOTE_ADDR'];
$date_visited = date('Y-m-d');

// Check if this IP has already visited today
$stmt = $pdo->prepare("SELECT id FROM visits WHERE ip_address = :ip AND date_visited = :date");
$stmt->execute([':ip' => $ip_address, ':date' => $date_visited]);

if ($stmt->rowCount() == 0) {
    // Unique visit for today
    $insert = $pdo->prepare("INSERT INTO visits (ip_address, date_visited) VALUES (:ip, :date)");
    $insert->execute([':ip' => $ip_address, ':date' => $date_visited]);
}
?>
