<?php
require_once '../includes/functions.php';

// Redirect if not logged in or not admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../index.php');
}

// Get counts for dashboard
$sql = "SELECT COUNT(*) as post_count FROM posts";
$result = $conn->query($sql);
$post_count = $result->fetch_assoc()['post_count'];

$sql = "SELECT COUNT(*) as user_count FROM users";
$result = $conn->query($sql);
$user_count = $result->fetch_assoc()['user_count'];

$sql = "SELECT COUNT(*) as comment_count FROM comments";
$result = $conn->query($sql);
$comment_count = $result->fetch_assoc()['comment_count'];

// Get recent posts
$sql = "SELECT p.*, u.username FROM posts p JOIN users u ON p.user_id = u.id ORDER BY p.created_at DESC LIMIT 5";
$result = $conn->query($sql);
$recent_posts = [];
while ($row = $result->fetch_assoc()) {
    $recent_posts[] = $row;
}

// Get recent users
$sql = "SELECT * FROM users ORDER BY created_at DESC LIMIT 5";
$result = $conn->query($sql);
$recent_users = [];
while ($row = $result->fetch_assoc()) {
    $recent_users[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Blog Website</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Admin Dashboard</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">View Site</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-3">
                <div class="list-group">
                    <a href="index.php" class="list-group-item list-group-item-action active">Dashboard</a>
                    <a href="manage-posts.php" class="list-group-item list-group-item-action">Manage Posts</a>
                    <a href="manage-users.php" class="list-group-item list-group-item-action">Manage Users</a>
                </div>
            </div>
            <div class="col-md-9">
                <h2>Dashboard</h2>
                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="card text-white bg-primary mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Total Posts</h5>
                                <p class="card-text display-4"><?php echo $post_count; ?></p>
                                <a href="manage-posts.php" class="text-white">Manage Posts →</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-success mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Total Users</h5>
                                <p class="card-text display-4"><?php echo $user_count; ?></p>
                                <a href="manage-users.php" class="text-white">Manage Users →</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-info mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Total Comments</h5>
                                <p class="card-text display-4"><?php echo $comment_count; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Recent Posts</h5>
                            </div>
                            <div class="card-body">
                                <?php if (empty($recent_posts)): ?>
                                    <p class="text-muted">No posts found.</p>
                                <?php else: ?>
                                    <div class="list-group">
                                        <?php foreach ($recent_posts as $post): ?>
                                            <a href="../post.php?id=<?php echo $post['id']; ?>" class="list-group-item list-group-item-action">
                                                <div class="d-flex w-100 justify-content-between">
                                                    <h6 class="mb-1"><?php echo $post['title']; ?></h6>
                                                    <small><?php echo date('M d', strtotime($post['created_at'])); ?></small>
                                                </div>
                                                <small>By <?php echo $post['username']; ?></small>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Recent Users</h5>
                            </div>
                            <div class="card-body">
                                <?php if (empty($recent_users)): ?>
                                    <p class="text-muted">No users found.</p>
                                <?php else: ?>
                                    <div class="list-group">
                                        <?php foreach ($recent_users as $user): ?>
                                            <div class="list-group-item">
                                                <div class="d-flex w-100 justify-content-between">
                                                    <h6 class="mb-1"><?php echo $user['username']; ?></h6>
                                                    <small><?php echo date('M d', strtotime($user['created_at'])); ?></small>
                                                </div>
                                                <small><?php echo $user['email']; ?></small>
                                                <?php if ($user['is_admin']): ?>
                                                    <span class="badge badge-primary">Admin</span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <div class="container">
            <p class="m-0">© <?php echo date('Y'); ?> Blog Website. All rights reserved.</p>
        </div>
    </footer>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="../assets/js/script.js"></script>
</body>
</html>