<?php
require_once '../includes/functions.php';

// Redirect non-admin users
if (!isLoggedIn() || !isAdmin()) {
    redirect('../index.php');
}

// Fetch dashboard statistics
$post_count = $conn->query("SELECT COUNT(*) as count FROM posts")->fetch_assoc()['count'];
$user_count = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$comment_count = $conn->query("SELECT COUNT(*) as count FROM comments")->fetch_assoc()['count'];

// Fetch recent activity (latest 10 from posts or comments)
$sql = "
    SELECT 'post' AS type, p.id, p.title AS content, p.created_at, u.username
    FROM posts p
    JOIN users u ON p.user_id = u.id
    UNION
    SELECT 'comment' AS type, c.id, c.content, c.created_at, u.username
    FROM comments c
    JOIN users u ON c.user_id = u.id
    ORDER BY created_at DESC
    LIMIT 10
";
$result = $conn->query($sql);
$recent_activity = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Blog Website</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap & Custom CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">Admin Dashboard</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item"><a class="nav-link" href="../index.php">View Site</a></li>
                <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="container-fluid mt-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            <div class="list-group">
                <a href="index.php" class="list-group-item list-group-item-action">Dashboard</a>
                <a href="dashboard.php" class="list-group-item list-group-item-action active">Advanced Dashboard</a>
                <a href="manage-posts.php" class="list-group-item list-group-item-action">Manage Posts</a>
                <a href="manage-users.php" class="list-group-item list-group-item-action">Manage Users</a>
            </div>
        </div>

        <!-- Dashboard Info -->
        <div class="col-md-9">
            <h2>Advanced Dashboard</h2>
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card text-white bg-primary mb-3 shadow">
                        <div class="card-body">
                            <h5 class="card-title">Total Posts</h5>
                            <p class="card-text display-4"><?php echo $post_count; ?></p>
                            <a href="manage-posts.php" class="text-white">Manage Posts →</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-success mb-3 shadow">
                        <div class="card-body">
                            <h5 class="card-title">Total Users</h5>
                            <p class="card-text display-4"><?php echo $user_count; ?></p>
                            <a href="manage-users.php" class="text-white">Manage Users →</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-info mb-3 shadow">
                        <div class="card-body">
                            <h5 class="card-title">Total Comments</h5>
                            <p class="card-text display-4"><?php echo $comment_count; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="card mt-4">
                <div class="card-header"><h5>Recent Activity</h5></div>
                <div class="card-body">
                    <?php if (empty($recent_activity)): ?>
                        <p class="text-muted">No recent activity found.</p>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($recent_activity as $activity): ?>
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between">
                                        <h6 class="mb-1">
                                            <span class="badge badge-<?php echo $activity['type'] === 'post' ? 'primary' : 'info'; ?>">
                                                <?php echo ucfirst($activity['type']); ?>
                                            </span>
                                            <?php echo htmlspecialchars(substr($activity['content'], 0, 80)); ?>
                                        </h6>
                                        <small><?php echo date('M d, H:i', strtotime($activity['created_at'])); ?></small>
                                    </div>
                                    <small>By <?php echo htmlspecialchars($activity['username']); ?></small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="bg-dark text-white text-center py-3 mt-5">
    <div class="container">
        <p class="m-0">© <?php echo date('Y'); ?> Blog Website. All rights reserved.</p>
    </div>
</footer>

<!-- JS Scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/script.js"></script>
</body>
</html>

