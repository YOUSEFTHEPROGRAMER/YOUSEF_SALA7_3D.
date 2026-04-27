<?php
// send_feedback.php
// Handles contact form submissions + sends real email

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name    = strip_tags(trim($_POST["name"]    ?? ''));
    $email   = filter_var(trim($_POST["email"]   ?? ''), FILTER_SANITIZE_EMAIL);
    $message = strip_tags(trim($_POST["message"] ?? ''));

    if (empty($name) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo "Please fill out all fields correctly.";
        exit;
    }

    // ── Save to file ──────────────────────────────────────────────────────────
    $feedback_file = __DIR__ . '/feedback_messages.txt';
    $entry = "========================================\n";
    $entry .= "Date:    " . date('Y-m-d H:i:s') . "\n";
    $entry .= "Name:    $name\n";
    $entry .= "Email:   $email\n";
    $entry .= "Message:\n$message\n\n";
    file_put_contents($feedback_file, $entry, FILE_APPEND);

    // ── Also save to SQLite (messages table) ─────────────────────────────────
    try {
        require_once __DIR__ . '/db.php';
        $pdo->exec("CREATE TABLE IF NOT EXISTS messages (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT,
            email TEXT,
            message TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        $stmt = $pdo->prepare("INSERT INTO messages (name, email, message) VALUES (:n, :e, :m)");
        $stmt->execute([':n' => $name, ':e' => $email, ':m' => $message]);
    } catch (Exception $e) {
        // Non-fatal — file save already succeeded
    }

    // ── Send email ────────────────────────────────────────────────────────────
    $to      = 'yooooosef.tv@gmail.com';
    $subject = "New Portfolio Message from $name";

    $body  = "You received a new message from your portfolio website.\n\n";
    $body .= "Name:    $name\n";
    $body .= "Email:   $email\n";
    $body .= "Date:    " . date('Y-m-d H:i:s') . "\n";
    $body .= "─────────────────────────────────────\n";
    $body .= "Message:\n$message\n\n";
    $body .= "─────────────────────────────────────\n";
    $body .= "Reply directly to: $email\n";

    $headers  = "From: portfolio@yousef-sala7.com\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    @mail($to, $subject, $body, $headers);

    http_response_code(200);
    echo "Message received.";
} else {
    http_response_code(403);
    echo "Forbidden";
}
?>
