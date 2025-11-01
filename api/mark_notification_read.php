<?php
session_start();
require_once '../config/database.php';

if (!isLoggedIn()) {
    http_response_code(401);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$notif_id = $data['id'] ?? 0;

if (!$notif_id) {
    http_response_code(400);
    exit;
}

$pdo = getDBConnection();

$stmt = $pdo->prepare("UPDATE notifications SET is_read = TRUE WHERE id = ? AND user_id = ?");
$stmt->execute([$notif_id, $_SESSION['user_id']]);
?>

