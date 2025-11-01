<?php
// Script complet pour configurer ResiLink avec tous les exemples
// À supprimer après utilisation pour des raisons de sécurité
require_once 'config/database.php';

echo "=== Configuration complète de ResiLink ===\n\n";

try {
    $pdo = getDBConnection();
    
    // 1. Créer les utilisateurs de test
    echo "1. Création des utilisateurs...\n";
    $password_hash = password_hash('admin123', PASSWORD_DEFAULT);
    
    $users = [
        ['marie_dupont', 'marie.dupont@example.com', 'Marie Dupont', 'A12', '0612345678'],
        ['pierre_martin', 'pierre.martin@example.com', 'Pierre Martin', 'B05', '0623456789'],
        ['sophie_bernard', 'sophie.bernard@example.com', 'Sophie Bernard', 'C20', '0634567890'],
        ['lucas_roux', 'lucas.roux@example.com', 'Lucas Roux', 'D08', '0645678901'],
        ['emma_leclerc', 'emma.leclerc@example.com', 'Emma Leclerc', 'E15', '0656789012']
    ];
    
    $user_ids = [];
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, full_name, apartment_number, phone, role) VALUES (?, ?, ?, ?, ?, ?, 'user')");
    
    foreach ($users as $user) {
        try {
            $stmt->execute([$user[0], $user[1], $password_hash, $user[2], $user[3], $user[4]]);
            $user_ids[] = $pdo->lastInsertId();
        } catch (PDOException $e) {
            // Récupérer l'ID existant
            $stmt2 = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt2->execute([$user[0]]);
            $user_ids[] = $stmt2->fetchColumn();
        }
    }
    
    echo "✓ " . count($user_ids) . " utilisateurs prêts\n";
    
    // 2. Créer les posts
    echo "\n2. Création des posts...\n";
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
    
    // 3. Ajouter les avatars
    echo "\n3. Ajout des avatars...\n";
    $stmt_all_users = $pdo->query("SELECT id, username, full_name FROM users");
    $all_users = $stmt_all_users->fetchAll();
    $avatar_count = 0;
    
    foreach ($all_users as $user) {
        $avatar_filename = uniqid() . '.jpg';
        $avatar_path = __DIR__ . '/assets/uploads/avatars/' . $avatar_filename;
        
        if ($user['username'] === 'admin') {
            $avatar_url = 'https://ui-avatars.com/api/?name=Admin&background=dc3545&color=fff&size=200&bold=true';
        } else {
            $img_number = rand(1, 70);
            $avatar_url = "https://i.pravatar.cc/200?img=$img_number";
        }
        
        $image_data = @file_get_contents($avatar_url);
        if ($image_data !== false && file_put_contents($avatar_path, $image_data) !== false) {
            $stmt = $pdo->prepare("UPDATE users SET avatar = ? WHERE id = ?");
            $stmt->execute([$avatar_filename, $user['id']]);
            $avatar_count++;
        }
    }
    
    echo "✓ $avatar_count avatars ajoutés\n";
    
    // 4. Ajouter des images aux posts
    echo "\n4. Ajout d'images aux posts...\n";
    $stmt_posts = $pdo->query("SELECT id, type FROM posts WHERE image IS NULL ORDER BY id LIMIT 8");
    $posts_to_image = $stmt_posts->fetchAll();
    $image_count = 0;
    
    foreach ($posts_to_image as $post) {
        $img_number = rand(1, 1000);
        $image_url = "https://picsum.photos/800/600?random=$img_number";
        
        $image_data = @file_get_contents($image_url);
        if ($image_data !== false) {
            $image_filename = uniqid() . '.jpg';
            $image_path = __DIR__ . '/assets/uploads/posts/' . $image_filename;
            
            if (file_put_contents($image_path, $image_data) !== false) {
                $stmt = $pdo->prepare("UPDATE posts SET image = ? WHERE id = ?");
                $stmt->execute([$image_filename, $post['id']]);
                $image_count++;
            }
        }
    }
    
    echo "✓ $image_count images ajoutées\n";
    
    // 5. Ajouter commentaires et likes
    echo "\n5. Ajout des interactions...\n";
    $comments = [
        [$post_ids[0], $user_ids[1], 'Bienvenue ! 😊'], [$post_ids[0], $user_ids[2], 'Ravi de te rencontrer !'],
        [$post_ids[2], $user_ids[0], 'Très intéressé !'], [$post_ids[5], $user_ids[2], 'Je serai présent !'],
    ];
    
    $stmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)");
    $comment_count = 0;
    foreach ($comments as $comment) {
        try {
            $stmt->execute($comment);
            $comment_count++;
        } catch (PDOException $e) {}
    }
    
    $likes = [
        [$post_ids[0], $user_ids[1]], [$post_ids[0], $user_ids[2]],
        [$post_ids[5], $user_ids[0]], [$post_ids[5], $user_ids[1]],
    ];
    
    $stmt = $pdo->prepare("INSERT INTO likes (post_id, user_id) VALUES (?, ?)");
    $like_count = 0;
    foreach ($likes as $like) {
        try {
            $stmt->execute($like);
            $like_count++;
        } catch (PDOException $e) {}
    }
    
    echo "✓ $comment_count commentaires et $like_count likes ajoutés\n";
    
    echo "\n✅ Configuration terminée avec succès !\n\n";
    echo "Comptes de test (mot de passe: admin123) :\n";
    echo "- marie_dupont, pierre_martin, sophie_bernard, lucas_roux, emma_leclerc\n";
    echo "- admin (administrateur)\n\n";
    echo "Accédez à http://localhost/app/ pour voir le résultat !\n\n";
    echo "⚠️  Supprimez ce fichier pour des raisons de sécurité.\n";
    
} catch (PDOException $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
}
?>

