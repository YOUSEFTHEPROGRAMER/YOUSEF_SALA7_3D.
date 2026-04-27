<?php
require 'db.php';

try {
    // Visits table
    $pdo->exec("CREATE TABLE IF NOT EXISTS visits (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        ip_address TEXT,
        date_visited TEXT,
        visit_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Folders table
    $pdo->exec("CREATE TABLE IF NOT EXISTS folders (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL
    )");

    // Images table
    $pdo->exec("CREATE TABLE IF NOT EXISTS images (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        file_name TEXT NOT NULL,
        folder_id INTEGER,
        FOREIGN KEY (folder_id) REFERENCES folders(id) ON DELETE CASCADE
    )");

    // Add some sample data if folders is empty
    $count = $pdo->query("SELECT COUNT(*) FROM folders")->fetchColumn();
    if ($count == 0) {
        $pdo->exec("INSERT INTO folders (name) VALUES ('Sample Project 1')");
        $pdo->exec("INSERT INTO folders (name) VALUES ('Sample Project 2')");
    }

    echo "<h3>Database initialized successfully.</h3>";
    echo "<p><a href='index.php'>Go to Homepage</a> | <a href='admin_index.php'>Go to Admin</a></p>";
} catch (PDOException $e) {
    echo "Error setting up database: " . $e->getMessage();
}
?>
