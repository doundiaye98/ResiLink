<?php
require_once 'includes/header.php';

if (!isset($_GET['id'])) {
    redirect('index.php');
}

$pdo = getDBConnection();
$post_id = (int)$_GET['id'];

// Récupérer le post
$stmt = $pdo->prepare("SELECT p.*, u.username, u.full_name, u.avatar,
                       (SELECT COUNT(*) FROM likes l WHERE l.post_id = p.id) as like_count,
                       (SELECT COUNT(*) FROM comments c WHERE c.post_id = p.id) as comment_count,
                       (SELECT COUNT(*) FROM likes l WHERE l.post_id = p.id AND l.user_id = ?) as user_liked
                       FROM posts p
                       JOIN users u ON p.user_id = u.id
                       WHERE p.id = ?");
$stmt->execute([$_SESSION['user_id'] ?? 0, $post_id]);
$post = $stmt->fetch();

if (!$post) {
    redirect('index.php');
}

// Récupérer les commentaires
$stmt = $pdo->prepare("SELECT c.*, u.username, u.full_name, u.avatar
                       FROM comments c
                       JOIN users u ON c.user_id = u.id
                       WHERE c.post_id = ?
                       ORDER BY c.created_at ASC");
$stmt->execute([$post_id]);
$comments = $stmt->fetchAll();

// Ajouter un commentaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isLoggedIn() && isset($_POST['comment'])) {
    $content = trim($_POST['comment']);
    
    if (!empty($content)) {
        $stmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)");
        
        if ($stmt->execute([$post_id, $_SESSION['user_id'], $content])) {
            // Notifier l'auteur du post
            if ($post['user_id'] != $_SESSION['user_id']) {
                $stmt = $pdo->prepare("INSERT INTO notifications (user_id, type, message, link) VALUES (?, 'comment', ?, ?)");
                $message = $_SESSION['full_name'] . " a commenté votre post";
                $link = "post_detail.php?id=$post_id";
                $stmt->execute([$post['user_id'], $message, $link]);
            }
            
            $_SESSION['success'] = "Commentaire ajouté !";
            redirect("post_detail.php?id=$post_id");
        }
    }
}
?>
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_SESSION['success']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <!-- Post principal -->
        <div class="card shadow-sm mb-3 post-card">
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
                        <h6 class="mb-0"><?= htmlspecialchars($post['full_name'] ?: $post['username']) ?></h6>
                        <small class="text-muted">
                            <?= date('d/m/Y H:i', strtotime($post['created_at'])) ?>
                        </small>
                    </div>
                    <?php if ($post['type'] !== 'general'): ?>
                        <span class="badge badge-type bg-info ms-auto">
                            <?php
                            $types = ['sale' => 'Vente', 'event' => 'Événement', 'issue' => 'Problème'];
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
                    
                    <span class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-chat"></i> <?= count($comments) ?> commentaires
                    </span>
                    
                    <?php if (isLoggedIn() && ($post['user_id'] == $_SESSION['user_id'] || isAdmin())): ?>
                        <a href="edit_post.php?id=<?= $post['id'] ?>" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-pencil"></i> Modifier
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Formulaire de commentaire -->
        <?php if (isLoggedIn()): ?>
            <div class="card mb-3">
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-2">
                            <textarea class="form-control" name="comment" rows="3" 
                                      placeholder="Écrivez un commentaire..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-send"></i> Commenter
                        </button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Liste des commentaires -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-chat-left-text"></i> Commentaires (<?= count($comments) ?>)</h5>
            </div>
            <div class="card-body">
                <?php if (empty($comments)): ?>
                    <p class="text-muted text-center mb-0">Aucun commentaire pour le moment.</p>
                <?php else: ?>
                    <?php foreach ($comments as $comment): ?>
                        <div class="d-flex mb-3 border-bottom pb-3">
                            <?php if (!empty($comment['avatar'])): ?>
                                <img src="assets/uploads/avatars/<?= htmlspecialchars($comment['avatar']) ?>" 
                                     class="avatar-sm me-2" alt="Avatar">
                            <?php else: ?>
                                <div class="avatar-sm me-2 bg-secondary rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-person-fill text-white"></i>
                                </div>
                            <?php endif; ?>
                            <div class="flex-grow-1">
                                <h6 class="mb-0"><?= htmlspecialchars($comment['full_name'] ?: $comment['username']) ?></h6>
                                <small class="text-muted"><?= date('d/m/Y H:i', strtotime($comment['created_at'])) ?></small>
                                <p class="mb-0 mt-1"><?= nl2br(htmlspecialchars($comment['content'])) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
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

