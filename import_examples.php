<?php
// Script pour importer des exemples de données complètes dans ResiLink
// À supprimer après utilisation pour des raisons de sécurité
require_once 'config/database.php';

echo "=== Import des exemples de données ResiLink ===\n\n";

try {
    $pdo = getDBConnection();
    
    // Vérifier si les données existent déjà
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE username != 'admin'");
    $existing_users = $stmt->fetchColumn();
    
    if ($existing_users > 0) {
        echo "⚠ Des utilisateurs existent déjà. Souhaitez-vous continuer ? (o/n) : ";
        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);
        fclose($handle);
        if (trim($line) !== 'o') {
            echo "Import annulé.\n";
            exit;
        }
    }
    
    // Hash de mot de passe pour tous les utilisateurs de test
    $password_hash = password_hash('admin123', PASSWORD_DEFAULT);
    
    // Créer les utilisateurs
    echo "Création des utilisateurs...\n";
    $users = [
        ['marie_dupont', 'marie.dupont@example.com', 'Marie Dupont', 'A12', '0612345678'],
        ['pierre_martin', 'pierre.martin@example.com', 'Pierre Martin', 'B05', '0623456789'],
        ['sophie_bernard', 'sophie.bernard@example.com', 'Sophie Bernard', 'C20', '0634567890'],
        ['lucas_roux', 'lucas.roux@example.com', 'Lucas Roux', 'D08', '0645678901'],
        ['emma_leclerc', 'emma.leclerc@example.com', 'Emma Leclerc', 'E15', '0656789012']
    ];
    
    $user_ids = [];
    
    foreach ($users as $user) {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$user[0]]);
            $existing = $stmt->fetchColumn();
            
            if ($existing) {
                $user_ids[] = $existing;
                continue;
            }
            
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, full_name, apartment_number, phone, role) VALUES (?, ?, ?, ?, ?, ?, 'user')");
            $stmt->execute([$user[0], $user[1], $password_hash, $user[2], $user[3], $user[4]]);
            $user_ids[] = $pdo->lastInsertId();
        } catch (PDOException $e) {
            // Ignorer les erreurs
        }
    }
    
    echo "✓ " . count($user_ids) . " utilisateurs créés\n";
    
    if (count($user_ids) < 2) {
        echo "❌ Pas assez d'utilisateurs. Import annulé.\n";
        exit;
    }
    
    // Créer les posts
    echo "Création des posts...\n";
    $posts = [
        [$user_ids[0], 'Bienvenue dans la résidence !', 'Bonjour à tous ! Je suis nouveau dans la résidence et je suis ravi de vous rejoindre.', 'general', null, null, null],
        [$user_ids[1], 'Réunion du conseil', 'Réunion prévue le 15 novembre à 19h dans la salle commune.', 'general', null, null, null],
        [$user_ids[0], 'Machine à laver à vendre', 'Machine à laver Whirlpool 8kg, 2 ans d\'usage, excellent état.', 'sale', null, 250.00, null],
        [$user_ids[2], 'Canapé convertible', 'Canapé 3 places gris clair, confortable, avec housses lavables.', 'sale', null, 350.00, null],
        [$user_ids[3], 'Vélo électrique', 'Autonomie 50km, 1 an d\'usage, batterie excellente.', 'sale', null, 800.00, null],
        [$user_ids[1], 'Fête de Noël', 'Proposition d\'organiser une fête le 20 décembre à 19h.', 'event', '2024-12-20 19:00:00', null, 'Salle commune'],
        [$user_ids[3], 'Petit déjeuner partagé', 'Dimanche prochain à 9h dans le jardin commun.', 'event', '2024-11-10 09:00:00', null, 'Jardin'],
        [$user_ids[0], 'Nettoyage collectif', 'Matinée de nettoyage samedi 25 novembre.', 'event', '2024-11-25 09:00:00', null, 'Espaces communs'],
        [$user_ids[2], 'Lampe cassée', 'Lampe dans l\'ascenseur cassée depuis 3 jours.', 'issue', null, null, null],
        [$user_ids[0], 'Chaudière bruyante', 'Chaudière B05 très bruyante, surtout la nuit.', 'issue', null, null, null],
        [$user_ids[1], 'Porte qui grince', 'Porte d\'entrée grince beaucoup.', 'issue', null, null, null],
        [$user_ids[3], 'Ascenseur en panne', 'Ascenseur bloqué au 3ème étage.', 'issue', null, null, null]
    ];
    
    $stmt = $pdo->prepare("INSERT INTO posts (user_id, title, content, type, event_date, price, location) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $post_ids = [];
    
    foreach ($posts as $post) {
        try {
            $stmt->execute($post);
            $post_ids[] = $pdo->lastInsertId();
        } catch (PDOException $e) {
            // Ignorer les doublons
        }
    }
    
    echo "✓ " . count($post_ids) . " posts créés\n";
    
    // Créer les commentaires
    echo "Création des commentaires...\n";
    $comments = [
        [$post_ids[0], $user_ids[1], 'Bienvenue ! 😊'],
        [$post_ids[0], $user_ids[2], 'Ravi de te rencontrer !'],
        [$post_ids[0], $user_ids[3], 'Super de t\'avoir parmi nous !'],
        [$post_ids[2], $user_ids[0], 'Très intéressé !'],
        [$post_ids[3], $user_ids[0], 'Ça m\'intéresse aussi'],
        [$post_ids[5], $user_ids[2], 'Je serai présent !'],
        [$post_ids[5], $user_ids[3], 'Je viens avec mon café !'],
        [$post_ids[8], $user_ids[0], 'Merci de signaler'],
        [$post_ids[8], $user_ids[1], 'C\'est dangereux le soir !'],
        [$post_ids[9], $user_ids[2], 'Merci pour le signalement']
    ];
    
    $stmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)");
    $comment_count = 0;
    
    foreach ($comments as $comment) {
        try {
            $stmt->execute($comment);
            $comment_count++;
        } catch (PDOException $e) {
            // Ignorer les doublons
        }
    }
    
    echo "✓ $comment_count commentaires créés\n";
    
    // Créer les likes
    echo "Création des likes...\n";
    $likes = [
        [$post_ids[0], $user_ids[1]], [$post_ids[0], $user_ids[2]], [$post_ids[0], $user_ids[3]],
        [$post_ids[1], $user_ids[0]], [$post_ids[1], $user_ids[2]],
        [$post_ids[2], $user_ids[1]], [$post_ids[2], $user_ids[3]],
        [$post_ids[3], $user_ids[0]],
        [$post_ids[4], $user_ids[1]],
        [$post_ids[5], $user_ids[0]], [$post_ids[5], $user_ids[1]], [$post_ids[5], $user_ids[2]],
        [$post_ids[6], $user_ids[1]], [$post_ids[6], $user_ids[2]],
        [$post_ids[8], $user_ids[1]],
        [$post_ids[9], $user_ids[0]],
        [$post_ids[10], $user_ids[0]], [$post_ids[10], $user_ids[1]], [$post_ids[10], $user_ids[2]]
    ];
    
    $stmt = $pdo->prepare("INSERT INTO likes (post_id, user_id) VALUES (?, ?)");
    $like_count = 0;
    
    foreach ($likes as $like) {
        try {
            $stmt->execute($like);
            $like_count++;
        } catch (PDOException $e) {
            // Ignorer les doublons
        }
    }
    
    echo "✓ $like_count likes créés\n";
    
    echo "\n✅ Import terminé avec succès !\n\n";
    echo "Comptes de test disponibles (mot de passe: admin123) :\n";
    echo "- marie_dupont\n";
    echo "- pierre_martin\n";
    echo "- sophie_bernard\n";
    echo "- lucas_roux\n";
    echo "- emma_leclerc\n";
    echo "- admin (administrateur)\n";
    echo "\n⚠️  Supprimez ce fichier pour des raisons de sécurité.\n";
    
} catch (PDOException $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
}
?>

