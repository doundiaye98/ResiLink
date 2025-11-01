<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['error' => 'Non autorisé']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$post_id = $data['post_id'] ?? 0;

if (!$post_id) {
    echo json_encode(['error' => 'ID invalide']);
    exit;
}

$pdo = getDBConnection();

// Vérifier si l'utilisateur a déjà liké
$stmt = $pdo->prepare("SELECT id FROM likes WHERE post_id = ? AND user_id = ?");
$stmt->execute([$post_id, $_SESSION['user_id']]);
$existing = $stmt->fetch();

if ($existing) {
    // Retirer le like
    $stmt = $pdo->prepare("DELETE FROM likes WHERE post_id = ? AND user_id = ?");
    $stmt->execute([$post_id, $_SESSION['user_id']]);
    $liked = false;
} else {
    // Ajouter le like
    $stmt = $pdo->prepare("INSERT INTO likes (post_id, user_id) VALUES (?, ?)");
    $stmt->execute([$post_id, $_SESSION['user_id']]);
    $liked = true;
    
    // Notifier l'auteur du post
    $stmt = $pdo->prepare("SELECT user_id FROM posts WHERE id = ?");
    $stmt->execute([$post_id]);
    $post = $stmt->fetch();
    
    if ($post && $post['user_id'] != $_SESSION['user_id']) {
        $stmt = $pdo->prepare("INSERT INTO notifications (user_id, type, message, link) VALUES (?, 'like', ?, ?)");
        $message = $_SESSION['full_name'] . " a aimé votre post";
        $link = "post_detail.php?id=$post_id";
        $stmt->execute([$post['user_id'], $message, $link]);
    }
}

// Compter les likes
$stmt = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE post_id = ?");
$stmt->execute([$post_id]);
$count = $stmt->fetchColumn();

echo json_encode(['liked' => $liked, 'count' => (int)$count]);
?>

