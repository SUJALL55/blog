<?php
require_once '../includes/functions.php';

// Redirect if not logged in or not admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../index.php');
}

// Handle user status change (activate/deactivate)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = clean($_GET['id']);
    $action = clean($_GET['action']);
    
    if ($action == 'activate') {
        $sql = "UPDATE users SET status = 1 WHERE id = '$id'";
        $conn->query($sql);
        redirect('manage-users.php?success=activated');
    } elseif ($action == 'deactivate') {
        // Don't deactivate your own account
        if ($id != $_SESSION['user_id']) {
            $sql = "UPDATE users SET status = 0 WHERE id = '$id'";
            $conn->query($sql);
            redirect('manage-users.php?success=deactivated');
        } else {
            redirect('manage-users.php?error=self_deactivate');
        }
    } elseif ($action == 'make_admin') {
        $sql = "UPDATE users SET is_admin = 1 WHERE id = '$id'";
        $conn->query($sql);
        redirect('manage-users.php?success=admin_granted');
    } elseif ($action == 'remove_admin') {
        // Don't remove your own admin privileges
        if ($id != $_SESSION['user_id']) {
            $sql = "UPDATE users SET is_admin = 0 WHERE id = '$id'";
            $conn->query($sql);
            redirect('manage-users.php?success=admin_removed');
        } else {
            redirect('manage-users.php?error=self_admin_remove');
        }
    }
}

// Get all users
$users = getUsers();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin Dashboard</title>
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
                    <a href="index.php" class="list-group-item list-group-item-action">Dashboard</a>
                    <a href="dashboard.php" class="list-group-item list-group-item-action">Advanced Dashboard</a>
                    <a href="manage-posts.php" class="list-group-item list-group-item-action">Manage Posts</a>
                    <a href="manage-users.php" class="list-group-item list-group-item-action active">Manage Users</a>
                </div>
            </div>
            <div class="col-md-9">
                <h2>Manage Users</h2>
                
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success">
                        <?php if ($_GET['success'] == 'activated'): ?>
                            User activated successfully.
                        <?php elseif ($_GET['success'] == 'deactivated'): ?>
                            User deactivated successfully.
                        <?php elseif ($_GET['success'] == 'admin_granted'): ?>
                            Admin privileges granted successfully.
                        <?php elseif ($_GET['success'] == 'admin_removed'): ?>
                            Admin privileges removed successfully.
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger">
                        <?php if ($_GET['error'] == 'self_deactivate'): ?>
                            You cannot deactivate your own account.
                        <?php elseif ($_GET['error'] == 'self_admin_remove'): ?>
                            You cannot remove your own admin privileges.
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-body">
                        <?php if (empty($users)): ?>
                            <p class="text-muted">No users found.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th>Status</th>
                                            <th>Joined</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($users as $user): ?>
                                            <tr>
                                                <td><?php echo $user['id']; ?></td>
                                                <td><?php echo $user['username']; ?></td>
                                                <td><?php echo $user['email']; ?></td>
                                                <td>
                                                    <?php if ($user['is_admin']): ?>
                                                        <span class="badge badge-primary">Admin</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-secondary">User</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (isset($user['status']) && $user['status'] == 0): ?>
                                                        <span class="badge badge-danger">Inactive</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-success">Active</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                                <td>
                                                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                                        <?php if (isset($user['status']) && $user['status'] == 0): ?>
                                                            <a href="manage-users.php?action=activate&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-success">Activate</a>
                                                        <?php else: ?>
                                                            <a href="manage-users.php?action=deactivate&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-warning">Deactivate</a>
                                                        <?php endif; ?>
                                                        
                                                        <?php if ($user['is_admin']): ?>
                                                            <a href="manage-users.php?action=remove_admin&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-danger">Remove Admin</a>
                                                        <?php else: ?>
                                                            <a href="manage-users.php?action=make_admin&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-primary">Make Admin</a>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">Current User</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
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