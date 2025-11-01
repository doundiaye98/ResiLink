<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['error' => 'Non autorisé']);
    exit;
}

$pdo = getDBConnection();

// Récupérer les notifications non lues
$stmt = $pdo->prepare("SELECT id, type, message, link, is_read, created_at 
                       FROM notifications 
                       WHERE user_id = ? 
                       ORDER BY created_at DESC 
                       LIMIT 10");
$stmt->execute([$_SESSION['user_id']]);
$notifications = $stmt->fetchAll();

// Compter les non lues
$stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = FALSE");
$stmt->execute([$_SESSION['user_id']]);
$unread = $stmt->fetchColumn();

// Formater les dates
foreach ($notifications as &$notif) {
    $timestamp = strtotime($notif['created_at']);
    $diff = time() - $timestamp;
    
    if ($diff < 60) {
        $notif['time_ago'] = 'Il y a quelques secondes';
    } elseif ($diff < 3600) {
        $notif['time_ago'] = 'Il y a ' . floor($diff / 60) . ' min';
    } elseif ($diff < 86400) {
        $notif['time_ago'] = 'Il y a ' . floor($diff / 3600) . 'h';
    } else {
        $notif['time_ago'] = date('d/m/Y', $timestamp);
    }
}

echo json_encode([
    'notifications' => $notifications,
    'unread' => (int)$unread
]);
?>

