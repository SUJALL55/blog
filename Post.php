<?php
require_once 'includes/header.php';

// Check if post ID is provided
if (!isset($_GET['id'])) {
    redirect('index.php');
}

// Get post details
$post = getPost($_GET['id']);

// If post doesn't exist, redirect to home
if (!$post) {
    redirect('index.php');
}

// Handle comment submission
if (isset($_POST['submit_comment']) && isLoggedIn()) {
    $comment = clean($_POST['comment']);
    $post_id = clean($_GET['id']);
    $user_id = $_SESSION['user_id'];
    
    if (!empty($comment)) {
        $sql = "INSERT INTO comments (post_id, user_id, comment) VALUES ('$post_id', '$user_id', '$comment')";
        if ($conn->query($sql) === TRUE) {
            // Refresh the page to show the new comment
            redirect("post.php?id=$post_id&success=1");
        } else {
            $error = "Error: " . $conn->error;
        }
    } else {
        $error = "Comment cannot be empty";
    }
}

// Get comments for this post
$comments = getComments($post['id']);
?>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">Comment added successfully!</div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="card mb-4">
            <?php if (!empty($post['image'])): ?>
                <img src="<?php echo $post['image']; ?>" class="card-img-top" alt="<?php echo $post['title']; ?>">
            <?php endif; ?>
            <div class="card-body">
                <h1 class="card-title"><?php echo $post['title']; ?></h1>
                <div class="post-meta mb-3">
                    <span>By <?php echo $post['username']; ?></span> | 
                    <span><?php echo date('F d, Y', strtotime($post['created_at'])); ?></span>
                </div>
                <div class="post-content">
                    <?php echo nl2br($post['content']); ?>
                </div>
                
                <?php if (isLoggedIn() && ($_SESSION['user_id'] == $post['user_id'] || isAdmin())): ?>
                    <div class="mt-4">
                        <a href="edit-post.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                        <a href="delete-post.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-danger delete-link" data-confirm-message="Are you sure you want to delete this post?">Delete</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Comments Section -->
        <div class="card mb-4">
            <div class="card-header">
                <h4>Comments (<?php echo count($comments); ?>)</h4>
            </div>
            <div class="card-body">
                <?php if (isLoggedIn()): ?>
                    <form method="post" action="">
                        <div class="form-group">
                            <label for="comment">Add a Comment</label>
                            <textarea class="form-control" id="comment" name="comment" rows="3" required></textarea>
                        </div>
                        <button type="submit" name="submit_comment" class="btn btn-primary">Submit</button>
                    </form>
                    <hr>
                <?php else: ?>
                    <div class="alert alert-info">Please <a href="login.php">login</a> to leave a comment.</div>
                <?php endif; ?>
                
                <?php if (empty($comments)): ?>
                    <div class="alert alert-info">No comments yet. Be the first to comment!</div>
                <?php else: ?>
                    <?php foreach ($comments as $comment): ?>
                        <div class="comment">
                            <div class="comment-meta mb-2">
                                <strong><?php echo $comment['username']; ?></strong> | 
                                <span><?php echo date('M d, Y g:i A', strtotime($comment['created_at'])); ?></span>
                            </div>
                            <div class="comment-content">
                                <?php echo nl2br($comment['comment']); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>