<?php
require_once 'includes/header.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

if (!isset($_GET['id'])) {
    redirect('index.php');
}

$pdo = getDBConnection();
$post_id = (int)$_GET['id'];

// Récupérer le post
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$post_id]);
$post = $stmt->fetch();

if (!$post) {
    redirect('index.php');
}

// Vérifier les permissions
if ($post['user_id'] != $_SESSION['user_id'] && !isAdmin()) {
    redirect('index.php');
}

// Traiter la mise à jour
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $price = $_POST['price'] ?? null;
    $event_date = $_POST['event_date'] ?? null;
    $location = $_POST['location'] ?? '';
    
    $errors = [];
    
    if (empty($content)) {
        $errors[] = "Le contenu du post est requis.";
    }
    
    // Traitement de l'image
    $image_name = $post['image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        
        if (!in_array($ext, $allowed)) {
            $errors[] = "Format d'image non supporté.";
        } else {
            $image_name = uniqid() . '.' . $ext;
            $upload_path = __DIR__ . '/assets/uploads/posts/' . $image_name;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                // Supprimer l'ancienne image
                if ($post['image'] && file_exists(__DIR__ . '/assets/uploads/posts/' . $post['image'])) {
                    @unlink(__DIR__ . '/assets/uploads/posts/' . $post['image']);
                }
            }
        }
    }
    
    if (empty($errors)) {
        $price = $price ? filter_var($price, FILTER_VALIDATE_FLOAT) : null;
        
        $stmt = $pdo->prepare("UPDATE posts SET title = ?, content = ?, image = ?, price = ?, event_date = ?, location = ? WHERE id = ?");
        
        if ($stmt->execute([$title, $content, $image_name, $price, $event_date, $location, $post_id])) {
            $_SESSION['success'] = "Post mis à jour avec succès !";
            redirect('post_detail.php?id=' . $post_id);
        }
    }
}
?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-warning">
                <h3 class="mb-0"><i class="bi bi-pencil"></i> Modifier le post</h3>
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
                        <label for="type" class="form-label">Type de publication</label>
                        <select class="form-select" id="type" name="type" disabled>
                            <option value="general" <?= $post['type'] === 'general' ? 'selected' : '' ?>>Général</option>
                            <option value="sale" <?= $post['type'] === 'sale' ? 'selected' : '' ?>>Vente</option>
                            <option value="event" <?= $post['type'] === 'event' ? 'selected' : '' ?>>Événement</option>
                            <option value="issue" <?= $post['type'] === 'issue' ? 'selected' : '' ?>>Problème</option>
                        </select>
                        <small class="text-muted">Le type ne peut pas être modifié</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Titre</label>
                        <input type="text" class="form-control" id="title" name="title" 
                               value="<?= htmlspecialchars($post['title']) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="content" class="form-label">Contenu *</label>
                        <textarea class="form-control" id="content" name="content" rows="5" required><?= htmlspecialchars($post['content']) ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="image" class="form-label">Image</label>
                        <?php if ($post['image']): ?>
                            <div class="mb-2">
                                <img src="assets/uploads/posts/<?= htmlspecialchars($post['image']) ?>" 
                                     class="img-thumbnail" style="max-width: 200px;" alt="Image actuelle">
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                    </div>
                    
                    <?php if ($post['type'] === 'sale'): ?>
                        <div class="mb-3">
                            <label for="price" class="form-label">Prix (€)</label>
                            <input type="number" step="0.01" class="form-control" id="price" name="price" 
                                   value="<?= $post['price'] ?>">
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($post['type'] === 'event'): ?>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="event_date" class="form-label">Date et heure</label>
                                <input type="datetime-local" class="form-control" id="event_date" name="event_date" 
                                       value="<?= $post['event_date'] ? date('Y-m-d\TH:i', strtotime($post['event_date'])) : '' ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="location" class="form-label">Lieu</label>
                                <input type="text" class="form-control" id="location" name="location" 
                                       value="<?= htmlspecialchars($post['location']) ?>">
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Enregistrer
                    </button>
                    <a href="post_detail.php?id=<?= $post_id ?>" class="btn btn-secondary">Annuler</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

