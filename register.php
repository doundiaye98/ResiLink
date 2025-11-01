<?php
require_once 'includes/header.php';

if (isLoggedIn()) {
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    $full_name = $_POST['full_name'] ?? '';
    $apartment = $_POST['apartment_number'] ?? '';
    $phone = $_POST['phone'] ?? '';
    
    $errors = [];
    
    if (empty($username) || empty($email) || empty($password)) {
        $errors[] = "Tous les champs obligatoires doivent être remplis.";
    }
    
    if ($password !== $password_confirm) {
        $errors[] = "Les mots de passe ne correspondent pas.";
    }
    
    if (strlen($password) < 6) {
        $errors[] = "Le mot de passe doit contenir au moins 6 caractères.";
    }
    
    if (empty($errors)) {
        $pdo = getDBConnection();
        
        // Vérifier si l'username ou l'email existe déjà
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        
        if ($stmt->fetch()) {
            $errors[] = "Ce nom d'utilisateur ou email est déjà utilisé.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, full_name, apartment_number, phone) VALUES (?, ?, ?, ?, ?, ?)");
            
            if ($stmt->execute([$username, $email, $hashed_password, $full_name, $apartment, $phone])) {
                $_SESSION['success'] = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
                redirect('login.php');
            } else {
                $errors[] = "Erreur lors de l'inscription.";
            }
        }
    }
}
?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0"><i class="bi bi-person-plus"></i> Créer un compte</h3>
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
                
                <form method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label">Nom d'utilisateur *</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe *</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password_confirm" class="form-label">Confirmer le mot de passe *</label>
                        <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Nom complet</label>
                        <input type="text" class="form-control" id="full_name" name="full_name">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="apartment_number" class="form-label">Numéro d'appartement</label>
                            <input type="text" class="form-control" id="apartment_number" name="apartment_number">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Téléphone</label>
                            <input type="tel" class="form-control" id="phone" name="phone">
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-check-circle"></i> S'inscrire
                    </button>
                </form>
                
                <hr>
                <p class="text-center mb-0">
                    Déjà un compte ? <a href="login.php">Se connecter</a>
                </p>
            </div>
        </div>
    </div>
</div>
<?php require_once 'includes/footer.php'; ?>

