<?php
// Script d'installation ResiLink
// À supprimer après la première installation

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation - ResiLink</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0"><i class="bi bi-gear"></i> Installation ResiLink</h3>
                    </div>
                    <div class="card-body">
                        <?php
                        $errors = [];
                        $success = [];
                        
                        // Vérifier les extensions PHP
                        if (!extension_loaded('pdo')) {
                            $errors[] = "Extension PDO manquante";
                        } else {
                            $success[] = "Extension PDO installée";
                        }
                        
                        if (!extension_loaded('pdo_mysql')) {
                            $errors[] = "Extension PDO MySQL manquante";
                        } else {
                            $success[] = "Extension PDO MySQL installée";
                        }
                        
                        // Vérifier les dossiers d'upload
                        $upload_dirs = [
                            'assets/uploads/posts' => 'Posts',
                            'assets/uploads/avatars' => 'Avatars'
                        ];
                        
                        foreach ($upload_dirs as $dir => $name) {
                            if (!is_dir($dir)) {
                                if (!mkdir($dir, 0755, true)) {
                                    $errors[] = "Impossible de créer le dossier : $dir";
                                } else {
                                    $success[] = "Dossier créé : $dir";
                                }
                            } else {
                                $success[] = "Dossier existe : $dir";
                            }
                            
                            if (!is_writable($dir)) {
                                $errors[] = "Le dossier $dir n'est pas accessible en écriture";
                            } else {
                                $success[] = "Dossier accessible en écriture : $dir";
                            }
                        }
                        
                        // Vérifier la connexion à la base de données
                        define('DB_HOST', 'localhost');
                        define('DB_USER', 'root');
                        define('DB_PASS', 'root');
                        
                        try {
                            $pdo = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
                            $success[] = "Connexion MySQL réussie";
                            
                            // Vérifier si la base existe
                            $stmt = $pdo->query("SHOW DATABASES LIKE 'resilink'");
                            if ($stmt->rowCount() > 0) {
                                $success[] = "Base de données 'resilink' existe déjà";
                            } else {
                                $errors[] = "La base de données 'resilink' n'existe pas. Veuillez exécuter le script database/schema.sql";
                            }
                        } catch (PDOException $e) {
                            $errors[] = "Erreur de connexion MySQL : " . $e->getMessage();
                        }
                        
                        // Afficher les résultats
                        if (!empty($success)) {
                            echo '<div class="alert alert-success"><strong>✓ Succès :</strong><ul class="mb-0">';
                            foreach ($success as $msg) {
                                echo '<li>' . htmlspecialchars($msg) . '</li>';
                            }
                            echo '</ul></div>';
                        }
                        
                        if (!empty($errors)) {
                            echo '<div class="alert alert-danger"><strong>✗ Erreurs :</strong><ul class="mb-0">';
                            foreach ($errors as $msg) {
                                echo '<li>' . htmlspecialchars($msg) . '</li>';
                            }
                            echo '</ul></div>';
                        }
                        
                        if (empty($errors)) {
                            echo '<div class="alert alert-success">';
                            echo '<h5><i class="bi bi-check-circle"></i> Installation terminée !</h5>';
                            echo '<p>Vous pouvez maintenant accéder à <a href="index.php">ResiLink</a></p>';
                            echo '<p class="mb-0"><strong>Important :</strong> Supprimez ce fichier install.php pour des raisons de sécurité.</p>';
                            echo '</div>';
                        }
                        ?>
                        
                        <hr>
                        
                        <h5>Prochaines étapes :</h5>
                        <ol>
                            <li>Exécuter le script <code>database/schema.sql</code> dans phpMyAdmin</li>
                            <li>Vérifier que les dossiers d'upload sont accessibles</li>
                            <li>Se connecter avec le compte admin : <code>admin / admin123</code></li>
                            <li>Supprimer ce fichier <code>install.php</code></li>
                        </ol>
                        
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6>Informations de connexion par défaut :</h6>
                                <ul class="mb-0">
                                    <li><strong>Utilisateur admin :</strong> admin</li>
                                    <li><strong>Mot de passe :</strong> admin123</li>
                                    <li><strong>Email :</strong> admin@resilink.fr</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

