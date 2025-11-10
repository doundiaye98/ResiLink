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

// Statistiques pour le hero
$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$resolved_issues = $pdo->query("SELECT COUNT(*) FROM posts WHERE type = 'issue' AND status = 'resolved'")->fetchColumn();
$event_count = $pdo->query("SELECT COUNT(*) FROM posts WHERE type = 'event'")->fetchColumn();
?>

<section class="hero-panel mb-5">
    <div class="row align-items-center g-4 hero-row">
        <div class="col-xl-7">
            <span class="hero-pill d-inline-flex align-items-center mb-3">
                <i class="bi bi-stars me-2"></i>
                Communauté résidentielle premium
            </span>
            <h1 class="display-5 fw-semibold text-white mb-3">
                Faites rayonner votre résidence auprès des recruteurs
            </h1>
            <p class="lead text-white-50 mb-4">
                ResiLink met en valeur la vie de quartier, la résolution rapide des incidents et l’organisation d’événements inspirants. Un environnement élégant et professionnel pour démontrer votre dynamisme collectif.
            </p>
            <div class="d-flex flex-wrap gap-3">
                <?php if (isLoggedIn()): ?>
                    <a href="create_post.php" class="btn btn-lg btn-light text-primary shadow-sm fw-semibold">
                        <i class="bi bi-pencil-square me-2"></i> Partager une réussite
                    </a>
                    <a href="profile.php" class="btn btn-lg btn-outline-light fw-semibold">
                        <i class="bi bi-person-badge me-2"></i> Booster mon profil
                    </a>
                <?php else: ?>
                    <a href="register.php" class="btn btn-lg btn-light text-primary shadow-sm fw-semibold">
                        <i class="bi bi-people-fill me-2"></i> Rejoindre ResiLink
                    </a>
                    <a href="login.php" class="btn btn-lg btn-outline-light fw-semibold">
                        <i class="bi bi-door-open me-2"></i> Se connecter
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-xl-5">
            <div class="hero-summary shadow-lg">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="metric-card">
                            <div class="metric-icon bg-gradient-primary">
                                <i class="bi bi-people"></i>
                            </div>
                            <div>
                                <span class="metric-value"><?= number_format($total_users, 0, ',', ' ') ?></span>
                                <span class="metric-label">Résidents actifs</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="metric-card">
                            <div class="metric-icon bg-gradient-success">
                                <i class="bi bi-shield-check"></i>
                            </div>
                            <div>
                                <span class="metric-value"><?= number_format($resolved_issues, 0, ',', ' ') ?></span>
                                <span class="metric-label">Incidents résolus</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="metric-card">
                            <div class="metric-icon bg-gradient-warning">
                                <i class="bi bi-calendar-event"></i>
                            </div>
                            <div>
                                <span class="metric-value"><?= number_format($event_count, 0, ',', ' ') ?></span>
                                <span class="metric-label">Événements prévus</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="hero-quote mt-4">
                    <div class="quote-icon">
                        <i class="bi bi-chat-quote"></i>
                    </div>
                    <p class="mb-0 text-white-50">
                        « Une vitrine moderne qui reflète la cohésion de notre résidence et rassure les recruteurs. »
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="row g-4 align-items-start res-layout">
    <aside class="col-lg-3 order-2 order-lg-1">
        <div class="d-lg-none mb-3">
            <button class="btn btn-outline-primary w-100 res-collapse-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#filtersPanel" aria-expanded="true" aria-controls="filtersPanel">
                <span class="d-flex align-items-center gap-2">
                    <i class="bi bi-funnel"></i>
                    <span>Filtres</span>
                </span>
                <i class="bi bi-chevron-down"></i>
            </button>
        </div>
        <div class="collapse show d-lg-block" id="filtersPanel">
            <div class="card res-sticky">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-funnel"></i> Filtrer les posts</h5>
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
    </aside>
    
    <section class="col-lg-6 order-1 order-lg-2">
        <?php if (isLoggedIn()): ?>
            <div class="card res-highlight-card mb-4">
                <div class="card-body d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
                    <div>
                        <h5 class="card-title mb-1">Que voulez-vous mettre en lumière ?</h5>
                        <p class="mb-0 text-muted">Partagez vos réussites, événements et initiatives pour dynamiser votre communauté.</p>
                    </div>
                    <a href="create_post.php" class="btn btn-primary btn-lg">
                        <i class="bi bi-plus-circle me-2"></i> Nouveau post
                    </a>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if (empty($posts)): ?>
            <div class="card res-empty-card text-center">
                <div class="card-body py-5">
                    <i class="bi bi-inbox fs-1 text-muted"></i>
                    <p class="text-muted mt-3">Aucun post pour le moment. Soyez le premier à partager une actualité&nbsp;!</p>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($posts as $post): ?>
                <article class="card mb-4 shadow-sm res-post-card">
                    <div class="card-body">
                        <div class="d-flex flex-wrap flex-sm-nowrap align-items-start gap-3 mb-3">
                            <?php if (!empty($post['avatar'])): ?>
                                <img src="assets/uploads/avatars/<?= htmlspecialchars($post['avatar']) ?>" class="avatar-sm flex-shrink-0" alt="Avatar">
                            <?php else: ?>
                                <div class="avatar-sm flex-shrink-0 bg-secondary rounded-circle d-flex align-items-center justify-content-center text-white">
                                    <i class="bi bi-person-fill"></i>
                                </div>
                            <?php endif; ?>
                            <div class="me-auto">
                                <h6 class="mb-1">
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
                                <span class="badge badge-type align-self-start">
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
                </article>
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
    </section>
    
    <div class="col-lg-3 order-1 order-lg-3">
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

