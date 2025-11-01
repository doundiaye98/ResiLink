<?php
require_once 'includes/header.php';

if (!isAdmin()) {
    redirect('index.php');
}

$pdo = getDBConnection();

// Actions de modération
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $post_id = $_POST['post_id'] ?? 0;
    
    switch ($_POST['action']) {
        case 'delete':
            $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
            $stmt->execute([$post_id]);
            $_SESSION['success'] = "Post supprimé avec succès.";
            break;
            
        case 'resolve':
            $stmt = $pdo->prepare("UPDATE posts SET status = 'resolved' WHERE id = ?");
            $stmt->execute([$post_id]);
            $_SESSION['success'] = "Problème marqué comme résolu.";
            break;
            
        case 'close':
            $stmt = $pdo->prepare("UPDATE posts SET status = 'closed' WHERE id = ?");
            $stmt->execute([$post_id]);
            $_SESSION['success'] = "Post fermé.";
            break;
    }
    redirect('admin.php');
}

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

// Récupérer tous les posts
$sql = "SELECT p.*, u.username, u.full_name,
        (SELECT COUNT(*) FROM likes l WHERE l.post_id = p.id) as like_count,
        (SELECT COUNT(*) FROM comments c WHERE c.post_id = p.id) as comment_count
        FROM posts p
        JOIN users u ON p.user_id = u.id
        ORDER BY p.created_at DESC
        LIMIT $per_page OFFSET $offset";

$stmt = $pdo->prepare("SELECT COUNT(*) FROM posts");
$stmt->execute();
$total_posts = $stmt->fetchColumn();

$stmt = $pdo->prepare($sql);
$stmt->execute();
$posts = $stmt->fetchAll();

// Statistiques
$stmt = $pdo->prepare("SELECT COUNT(*) FROM users");
$stmt->execute();
$total_users = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE type = 'issue'");
$stmt->execute();
$total_issues = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE type = 'sale'");
$stmt->execute();
$total_sales = $stmt->fetchColumn();
?>
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_SESSION['success']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header bg-danger text-white">
                <h3 class="mb-0"><i class="bi bi-shield-check"></i> Panneau de modération</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h2><?= $total_users ?></h2>
                                <p class="mb-0">Utilisateurs</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                <h2><?= $total_posts ?></h2>
                                <p class="mb-0">Publications</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body text-center">
                                <h2><?= $total_issues ?></h2>
                                <p class="mb-0">Problèmes</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h2><?= $total_sales ?></h2>
                                <p class="mb-0">Ventes</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-list-ul"></i> Toutes les publications</h5>
            </div>
            <div class="card-body">
                <?php if (empty($posts)): ?>
                    <p class="text-center text-muted">Aucune publication.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Auteur</th>
                                    <th>Type</th>
                                    <th>Contenu</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($posts as $post): ?>
                                    <tr>
                                        <td>
                                            <strong><?= htmlspecialchars($post['full_name'] ?: $post['username']) ?></strong>
                                            <br>
                                            <small class="text-muted">
                                                <i class="bi bi-heart"></i> <?= $post['like_count'] ?>
                                                <i class="bi bi-chat ms-2"></i> <?= $post['comment_count'] ?>
                                            </small>
                                        </td>
                                        <td>
                                            <?php
                                            $types = [
                                                'general' => ['label' => 'Général', 'color' => 'secondary'],
                                                'sale' => ['label' => 'Vente', 'color' => 'info'],
                                                'event' => ['label' => 'Événement', 'color' => 'warning'],
                                                'issue' => ['label' => 'Problème', 'color' => 'danger']
                                            ];
                                            $type_info = $types[$post['type']] ?? ['label' => $post['type'], 'color' => 'secondary'];
                                            ?>
                                            <span class="badge bg-<?= $type_info['color'] ?>">
                                                <?= $type_info['label'] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($post['title']): ?>
                                                <strong><?= htmlspecialchars($post['title']) ?></strong><br>
                                            <?php endif; ?>
                                            <?= htmlspecialchars(mb_substr($post['content'], 0, 100)) ?>...
                                        </td>
                                        <td>
                                            <?= date('d/m/Y H:i', strtotime($post['created_at'])) ?>
                                        </td>
                                        <td>
                                            <?php
                                            $status_colors = [
                                                'active' => 'success',
                                                'resolved' => 'info',
                                                'closed' => 'secondary'
                                            ];
                                            ?>
                                            <span class="badge bg-<?= $status_colors[$post['status']] ?? 'secondary' ?>">
                                                <?= ucfirst($post['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="post_detail.php?id=<?= $post['id'] ?>" 
                                                   class="btn btn-outline-primary">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <?php if ($post['type'] === 'issue' && $post['status'] === 'active'): ?>
                                                    <form method="POST" style="display:inline;" 
                                                          onsubmit="return confirm('Marquer comme résolu ?');">
                                                        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                                        <input type="hidden" name="action" value="resolve">
                                                        <button type="submit" class="btn btn-outline-success">
                                                            <i class="bi bi-check-circle"></i>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                                <?php if ($post['status'] === 'active'): ?>
                                                    <form method="POST" style="display:inline;" 
                                                          onsubmit="return confirm('Fermer ce post ?');">
                                                        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                                        <input type="hidden" name="action" value="close">
                                                        <button type="submit" class="btn btn-outline-warning">
                                                            <i class="bi bi-lock"></i>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                                <form method="POST" style="display:inline;" 
                                                      onsubmit="return confirm('Supprimer définitivement ce post ?');">
                                                    <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                                    <input type="hidden" name="action" value="delete">
                                                    <button type="submit" class="btn btn-outline-danger">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <?php
                    $total_pages = ceil($total_posts / $per_page);
                    if ($total_pages > 1):
                    ?>
                        <nav>
                            <ul class="pagination justify-content-center">
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?= $page - 1 ?>">Précédent</a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php if ($page < $total_pages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?= $page + 1 ?>">Suivant</a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

