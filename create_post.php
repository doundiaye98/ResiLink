<?php
require_once 'includes/header.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $type = $_POST['type'] ?? 'general';
    $price = $_POST['price'] ?? null;
    $event_date = $_POST['event_date'] ?? null;
    $location = $_POST['location'] ?? '';
    
    $errors = [];
    
    if (empty($content)) {
        $errors[] = "Le contenu du post est requis.";
    }
    
    // Traitement de l'image
    $image_name = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        
        if (!in_array($ext, $allowed)) {
            $errors[] = "Format d'image non supporté (jpg, png, gif uniquement).";
        } else {
            $image_name = uniqid() . '.' . $ext;
            $upload_path = __DIR__ . '/assets/uploads/posts/' . $image_name;
            
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $errors[] = "Erreur lors de l'upload de l'image.";
            }
        }
    }
    
    if (empty($errors)) {
        $pdo = getDBConnection();
        
        // Nettoyer les données
        $price = $price ? filter_var($price, FILTER_VALIDATE_FLOAT) : null;
        
        $stmt = $pdo->prepare("INSERT INTO posts (user_id, title, content, type, image, price, event_date, location) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
        if ($stmt->execute([$_SESSION['user_id'], $title, $content, $type, $image_name, $price, $event_date, $location])) {
            // Créer une notification pour les autres utilisateurs
            $post_id = $pdo->lastInsertId();
            
            // Notifier tous les utilisateurs sauf l'auteur
            $stmt = $pdo->prepare("INSERT INTO notifications (user_id, type, message, link) 
                                   SELECT id, 'new_post', ?, ? FROM users WHERE id != ?");
            $notif_message = "Nouveau post de " . $_SESSION['full_name'];
            $notif_link = "post_detail.php?id=$post_id";
            $stmt->execute([$notif_message, $notif_link, $_SESSION['user_id']]);
            
            $_SESSION['success'] = "Post créé avec succès !";
            redirect('index.php');
        } else {
            $errors[] = "Erreur lors de la création du post.";
        }
    }
}
?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0"><i class="bi bi-plus-circle"></i> Créer un nouveau post</h3>
            </div>
            <div class="card-body">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="type" class="form-label">Type de publication *</label>
                        <select class="form-select" id="type" name="type" required>
                            <option value="general">Général</option>
                            <option value="sale">Vente</option>
                            <option value="event">Événement</option>
                            <option value="issue">Problème/Signalement</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Titre (optionnel)</label>
                        <input type="text" class="form-control" id="title" name="title">
                    </div>
                    
                    <div class="mb-3">
                        <label for="content" class="form-label">Contenu *</label>
                        <textarea class="form-control" id="content" name="content" rows="5" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="image" class="form-label">Image (optionnel)</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                    </div>
                    
                    <!-- Champs spécifiques selon le type -->
                    <div id="saleFields" class="mb-3" style="display:none;">
                        <label for="price" class="form-label">Prix (€)</label>
                        <input type="number" step="0.01" class="form-control" id="price" name="price">
                    </div>
                    
                    <div id="eventFields" class="mb-3" style="display:none;">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="event_date" class="form-label">Date et heure</label>
                                <input type="datetime-local" class="form-control" id="event_date" name="event_date">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="location" class="form-label">Lieu</label>
                                <input type="text" class="form-control" id="location" name="location">
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Publier
                    </button>
                    <a href="index.php" class="btn btn-secondary">Annuler</a>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('type').addEventListener('change', function() {
    const type = this.value;
    document.getElementById('saleFields').style.display = type === 'sale' ? 'block' : 'none';
    document.getElementById('eventFields').style.display = type === 'event' ? 'block' : 'none';
});
</script>

<?php require_once 'includes/footer.php'; ?>

