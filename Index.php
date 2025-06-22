<?php
require_once 'includes/header.php';

// Get all posts with like and comment counts
$posts = getPosts();
?>

<h1 class="mb-4">Latest Blog Posts</h1>

<div class="row">
    <?php if (empty($posts)): ?>
        <div class="col-12">
            <div class="alert alert-info">No posts found. Be the first to create a post!</div>
        </div>
    <?php else: ?>
        <?php foreach ($posts as $post): ?>
            <div class="col-md-4">
                <div class="card">
                    <?php if (!empty($post['image'])): ?>
                        <img src="<?php echo $post['image']; ?>" class="card-img-top" alt="<?php echo $post['title']; ?>">
                    <?php else: ?>
                        <img src="assets/uploads/default-post.jpg" class="card-img-top" alt="Default Image">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $post['title']; ?></h5>
                        <p class="card-text"><?php echo substr(strip_tags($post['content']), 0, 100); ?>...</p>
                        
                        <div class="post-meta">
                            <span>By <?php echo $post['username']; ?></span>
                            <span><?php echo date('M d, Y', strtotime($post['created_at'])); ?></span>
                        </div>
                        
                        <div class="interaction-buttons">
                            <button class="btn-like <?php echo isPostLikedByUser($post['id']) ? 'active' : ''; ?>" data-post-id="<?php echo $post['id']; ?>">
                                <i class="far fa-heart"></i>
                                <span class="likes-count"><?php echo getPostLikes($post['id']); ?></span>
                            </button>
                            <button class="btn-comment">
                                <i class="far fa-comment"></i>
                                <span class="comments-count"><?php echo getCommentsCount($post['id']); ?></span>
                            </button>
                        </div>
                        
                        <a href="post.php?id=<?php echo $post['id']; ?>" class="btn btn-primary mt-3">Read More</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>