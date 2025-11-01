<?php
require_once 'includes/header.php';

$pdo = getDBConnection();

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Filtre par type
$filter = $_GET['filter'] ?? 'all';

$where = "1=1";
$params = [];

if ($filter !== 'all') {
    $where .= " AND p.type = ?";
    $params[] = $filter;
}

// Récupérer les posts
$sql = "SELECT p.*, u.username, u.full_name, u.avatar,
        (SELECT COUNT(*) FROM likes l WHERE l.post_id = p.id) as like_count,
        (SELECT COUNT(*) FROM comments c WHERE c.post_id = p.id) as comment_count,
        (SELECT COUNT(*) FROM likes l WHERE l.post_id = p.id AND l.user_id = ?) as user_liked
        FROM posts p
        JOIN users u ON p.user_id = u.id
        WHERE $where
        ORDER BY p.created_at DESC
        LIMIT $per_page OFFSET $offset";

$params_total = array_filter($params);
$total_sql = "SELECT COUNT(*) FROM posts p WHERE $where";
$stmt = $pdo->prepare($total_sql);
if (!empty($params_total)) {
    $stmt->execute($params_total);
} else {
    $stmt->execute();
}
$total_posts = $stmt->fetchColumn();

// Récupérer les posts
$params_with_user = array_merge([$_SESSION['user_id'] ?? 0], $params);
$stmt = $pdo->prepare($sql);
$stmt->execute($params_with_user);
$posts = $stmt->fetchAll();
?>
<div class="row">
    <div class="col-md-3">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-funnel"></i> Filtres</h5>
            </div>
            <div class="list-group list-group-flush">
                <a href="?filter=all" class="list-group-item list-group-item-action <?= $filter === 'all' ? 'active' : '' ?>">
                    <i class="bi bi-collection"></i> Tous les posts
                </a>
                <a href="?filter=general" class="list-group-item list-group-item-action <?= $filter === 'general' ? 'active' : '' ?>">
                    <i class="bi bi-chat"></i> Général
                </a>
                <a href="?filter=sale" class="list-group-item list-group-item-action <?= $filter === 'sale' ? 'active' : '' ?>">
                    <i class="bi bi-tag"></i> Vente
                </a>
                <a href="?filter=event" class="list-group-item list-group-item-action <?= $filter === 'event' ? 'active' : '' ?>">
                    <i class="bi bi-calendar-event"></i> Événements
                </a>
                <a href="?filter=issue" class="list-group-item list-group-item-action <?= $filter === 'issue' ? 'active' : '' ?>">
                    <i class="bi bi-exclamation-triangle"></i> Problèmes
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <?php if (isLoggedIn()): ?>
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Que voulez-vous partager ?</h5>
                    <a href="create_post.php" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Créer un nouveau post
                    </a>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if (empty($posts)): ?>
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-inbox fs-1 text-muted"></i>
                    <p class="text-muted mt-3">Aucun post pour le moment.</p>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($posts as $post): ?>
                <div class="card mb-3 shadow-sm post-card">
                    <div class="card-body">
                        <div class="d-flex align-items-start mb-2">
                            <?php if (!empty($post['avatar'])): ?>
                                <img src="assets/uploads/avatars/<?= htmlspecialchars($post['avatar']) ?>" 
                                     class="avatar-sm me-2" alt="Avatar">
                            <?php else: ?>
                                <div class="avatar-sm me-2 bg-secondary rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-person-fill text-white"></i>
                                </div>
                            <?php endif; ?>
                            <div>
                                <h6 class="mb-0">
                                    <?= htmlspecialchars($post['full_name'] ?: $post['username']) ?>
                                    <?php if ($post['status'] !== 'active'): ?>
                                        <span class="badge bg-secondary"><?= ucfirst($post['status']) ?></span>
                                    <?php endif; ?>
                                </h6>
                                <small class="text-muted">
                                    <?= date('d/m/Y H:i', strtotime($post['created_at'])) ?>
                                </small>
                            </div>
                            <?php if ($post['type'] !== 'general'): ?>
                                <span class="badge badge-type bg-info ms-auto">
                                    <?php
                                    $types = [
                                        'sale' => 'Vente',
                                        'event' => 'Événement',
                                        'issue' => 'Problème'
                                    ];
                                    echo $types[$post['type']] ?? $post['type'];
                                    ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($post['title']): ?>
                            <h5 class="card-title"><?= htmlspecialchars($post['title']) ?></h5>
                        <?php endif; ?>
                        
                        <p class="card-text"><?= nl2br(htmlspecialchars($post['content'])) ?></p>
                        
                        <?php if ($post['image']): ?>
                            <img src="assets/uploads/posts/<?= htmlspecialchars($post['image']) ?>" 
                                 class="img-fluid rounded mb-2" alt="Image du post">
                        <?php endif; ?>
                        
                        <?php if ($post['type'] === 'sale' && $post['price']): ?>
                            <div class="alert alert-info mb-2">
                                <i class="bi bi-currency-euro"></i> Prix: <?= number_format($post['price'], 2, ',', ' ') ?> €
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($post['type'] === 'event' && $post['event_date']): ?>
                            <div class="alert alert-warning mb-2">
                                <i class="bi bi-calendar"></i> Date: <?= date('d/m/Y H:i', strtotime($post['event_date'])) ?>
                                <?php if ($post['location']): ?>
                                    <br><i class="bi bi-geo-alt"></i> Lieu: <?= htmlspecialchars($post['location']) ?>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <?php if (isLoggedIn()): ?>
                                    <button class="btn btn-sm btn-outline-danger me-2" 
                                            onclick="toggleLike(<?= $post['id'] ?>)" 
                                            id="likeBtn<?= $post['id'] ?>">
                                        <i class="bi bi-heart<?= $post['user_liked'] ? '-fill' : '' ?>"></i> 
                                        <span id="likeCount<?= $post['id'] ?>"><?= $post['like_count'] ?></span>
                                    </button>
                                <?php else: ?>
                                    <span class="btn btn-sm btn-outline-secondary me-2">
                                        <i class="bi bi-heart"></i> <?= $post['like_count'] ?>
                                    </span>
                                <?php endif; ?>
                                
                                <a href="post_detail.php?id=<?= $post['id'] ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-chat"></i> <?= $post['comment_count'] ?> commentaires
                                </a>
                            </div>
                            
                            <?php if (isLoggedIn() && ($post['user_id'] == $_SESSION['user_id'] || isAdmin())): ?>
                                <a href="edit_post.php?id=<?= $post['id'] ?>" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <!-- Pagination -->
            <?php
            $total_pages = ceil($total_posts / $per_page);
            if ($total_pages > 1):
            ?>
                <nav>
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $page - 1 ?>&filter=<?= $filter ?>">Précédent</a>
                            </li>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>&filter=<?= $filter ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $page + 1 ?>&filter=<?= $filter ?>">Suivant</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    
    <div class="col-md-3">
        <?php if (isLoggedIn()): ?>
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-person-circle"></i> Mon profil</h5>
                </div>
                <div class="card-body text-center">
                    <?php if (!empty($_SESSION['avatar'])): ?>
                        <img src="assets/uploads/avatars/<?= htmlspecialchars($_SESSION['avatar']) ?>" 
                             class="avatar-lg mb-2 rounded-circle" alt="Avatar">
                    <?php else: ?>
                        <div class="avatar-lg rounded-circle mb-2 bg-secondary d-flex align-items-center justify-content-center mx-auto">
                            <i class="bi bi-person-fill text-white" style="font-size: 3rem;"></i>
                        </div>
                    <?php endif; ?>
                    <h6><?= htmlspecialchars($_SESSION['full_name'] ?: $_SESSION['username']) ?></h6>
                    <a href="profile.php" class="btn btn-sm btn-primary">
                        <i class="bi bi-pencil"></i> Modifier
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="card">
                <div class="card-body text-center">
                    <i class="bi bi-people fs-1 text-primary"></i>
                    <h5>Rejoignez ResiLink !</h5>
                    <p>Connectez-vous avec vos voisins et échangez ensemble.</p>
                    <a href="register.php" class="btn btn-primary">
                        <i class="bi bi-person-plus"></i> S'inscrire
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if (isLoggedIn()): ?>
<script>
function toggleLike(postId) {
    fetch('api/toggle_like.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({post_id: postId})
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('likeBtn' + postId).innerHTML = 
            '<i class="bi bi-heart' + (data.liked ? '-fill' : '') + '"></i> ' + data.count;
        document.getElementById('likeCount' + postId).textContent = data.count;
    });
}
</script>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>

