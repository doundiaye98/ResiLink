<?php
require_once 'includes/header.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$pdo = getDBConnection();

// Récupérer les infos de l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Mettre à jour le profil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $apartment = $_POST['apartment_number'] ?? '';
    $phone = $_POST['phone'] ?? '';
    
    // Traitement de l'avatar
    $avatar_name = $user['avatar'];
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $avatar_name = uniqid() . '.' . $ext;
            $upload_path = __DIR__ . '/assets/uploads/avatars/' . $avatar_name;
            
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $upload_path)) {
                // Supprimer l'ancien avatar si ce n'est pas le défaut
                if ($user['avatar'] !== 'default.png') {
                    @unlink(__DIR__ . '/assets/uploads/avatars/' . $user['avatar']);
                }
            }
        }
    }
    
    // Changer le mot de passe si fourni
    $password_update = "";
    $params = [$full_name, $email, $apartment, $phone, $avatar_name, $_SESSION['user_id']];
    
    if (!empty($_POST['new_password'])) {
        $new_password = $_POST['new_password'];
        $password_confirm = $_POST['password_confirm'] ?? '';
        
        if ($new_password === $password_confirm && strlen($new_password) >= 6) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $password_update = ", password = ?";
            array_splice($params, 5, 0, $hashed_password);
        }
    }
    
    $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, apartment_number = ?, phone = ?, avatar = ?$password_update WHERE id = ?");
    
    if ($stmt->execute($params)) {
        $_SESSION['full_name'] = $full_name;
        $_SESSION['avatar'] = $avatar_name;
        $_SESSION['success'] = "Profil mis à jour avec succès !";
        redirect('profile.php');
    }
}

// Compter les posts et commentaires de l'utilisateur
$stmt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$post_count = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM comments WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$comment_count = $stmt->fetchColumn();
?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0"><i class="bi bi-person-circle"></i> Mon profil</h3>
            </div>
            <div class="card-body">
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success">
                        <?= htmlspecialchars($_SESSION['success']) ?>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="text-center mb-4">
                        <?php if (!empty($user['avatar'])): ?>
                            <img src="assets/uploads/avatars/<?= htmlspecialchars($user['avatar']) ?>" 
                                 class="avatar-lg rounded-circle mb-2" alt="Avatar" id="avatarPreview">
                        <?php else: ?>
                            <div class="avatar-lg rounded-circle mb-2 bg-secondary d-flex align-items-center justify-content-center mx-auto" id="avatarPreview">
                                <i class="bi bi-person-fill text-white" style="font-size: 3rem;"></i>
                            </div>
                        <?php endif; ?>
                        <div>
                            <input type="file" class="form-control d-none" id="avatar" name="avatar" 
                                   accept="image/*" onchange="previewAvatar(this)">
                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                    onclick="document.getElementById('avatar').click()">
                                <i class="bi bi-camera"></i> Changer la photo
                            </button>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="username" class="form-label">Nom d'utilisateur</label>
                            <input type="text" class="form-control" id="username" 
                                   value="<?= htmlspecialchars($user['username']) ?>" disabled>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?= htmlspecialchars($user['email']) ?>" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Nom complet</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" 
                               value="<?= htmlspecialchars($user['full_name']) ?>">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="apartment_number" class="form-label">Numéro d'appartement</label>
                            <input type="text" class="form-control" id="apartment_number" name="apartment_number" 
                                   value="<?= htmlspecialchars($user['apartment_number']) ?>">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Téléphone</label>
                            <input type="tel" class="form-control" id="phone" name="phone" 
                                   value="<?= htmlspecialchars($user['phone']) ?>">
                        </div>
                    </div>
                    
                    <hr>
                    
                    <h5>Changer le mot de passe (optionnel)</h5>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Nouveau mot de passe</label>
                        <input type="password" class="form-control" id="new_password" name="new_password">
                    </div>
                    
                    <div class="mb-3">
                        <label for="password_confirm" class="form-label">Confirmer le nouveau mot de passe</label>
                        <input type="password" class="form-control" id="password_confirm" name="password_confirm">
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Enregistrer les modifications
                        </button>
                        <a href="index.php" class="btn btn-secondary">Retour</a>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-body">
                <h5><i class="bi bi-bar-chart"></i> Statistiques</h5>
                <div class="row text-center">
                    <div class="col-6">
                        <h3 class="text-primary"><?= $post_count ?></h3>
                        <p class="text-muted mb-0">Publications</p>
                    </div>
                    <div class="col-6">
                        <h3 class="text-success"><?= $comment_count ?></h3>
                        <p class="text-muted mb-0">Commentaires</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('avatarPreview');
            if (preview.tagName === 'IMG') {
                preview.src = e.target.result;
            } else {
                // Remplacer le div par une image
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'avatar-lg rounded-circle mb-2';
                img.alt = 'Avatar';
                img.id = 'avatarPreview';
                preview.parentNode.replaceChild(img, preview);
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>

