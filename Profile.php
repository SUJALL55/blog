<?php
require_once 'includes/header.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

$user = getUser($_SESSION['user_id']);
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $username = clean(trim($_POST['username']));
    $email = clean(trim($_POST['email']));
    $current_password = clean(trim($_POST['current_password']));
    $new_password = clean(trim($_POST['new_password']));
    $confirm_password = clean(trim($_POST['confirm_password']));

    $changePassword = false;

    // Check for duplicate username or email
    if ($username !== $user['username'] || $email !== $user['email']) {
        $stmt = $conn->prepare("SELECT username, email FROM users WHERE (username = ? OR email = ?) AND id != ?");
        $stmt->bind_param("ssi", $username, $email, $user['id']);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            if ($row['username'] === $username) {
                $errors[] = "Username already exists";
            }
            if ($row['email'] === $email) {
                $errors[] = "Email already exists";
            }
        }
        $stmt->close();
    }

    // Handle password change
    if (!empty($current_password) || !empty($new_password) || !empty($confirm_password)) {
        $changePassword = true;

        if (empty($current_password)) {
            $errors[] = "Please enter your current password to change it.";
        } elseif (!password_verify($current_password, $user['password'])) {
            $errors[] = "Current password is incorrect.";
        }

        if (empty($new_password)) {
            $errors[] = "New password is required.";
        } elseif (strlen($new_password) < 6) {
            $errors[] = "New password must be at least 6 characters long.";
        }

        if ($new_password !== $confirm_password) {
            $errors[] = "New passwords do not match.";
        }
    }

    // If no errors, update user
    if (empty($errors)) {
        if ($changePassword) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?");
            $stmt->bind_param("sssi", $username, $email, $hashed_password, $user['id']);
        } else {
            $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
            $stmt->bind_param("ssi", $username, $email, $user['id']);
        }

        if ($stmt->execute()) {
            $_SESSION['username'] = $username;
            $user = getUser($_SESSION['user_id']);
            $success = "Profile updated successfully!";
        } else {
            $errors[] = "Failed to update profile. Please try again.";
        }

        $stmt->close();
    }
}

// Fetch user posts
$stmt = $conn->prepare("SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user['id']);
$stmt->execute();
$result = $stmt->get_result();
$user_posts = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!-- HTML Output -->
<div class="row">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header"><h4>Profile Information</h4></div>
            <div class="card-body">
                <h5><?php echo htmlspecialchars($user['username']); ?></h5>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                <p><strong>Joined:</strong> <?php echo date('F d, Y', strtotime($user['created_at'])); ?></p>
                <p><strong>Role:</strong> <?php echo $user['is_admin'] ? 'Administrator' : 'User'; ?></p>
                <p><strong>Posts:</strong> <?php echo count($user_posts); ?></p>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header"><h4>Edit Profile</h4></div>
            <div class="card-body">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $e) echo "<li>" . htmlspecialchars($e) . "</li>"; ?></ul></div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>

                <form method="post">
                    <div class="form-group">
                        <label>Username</label>
                        <input name="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>

                    <h5 class="mt-4">Change Password</h5>
                    <div class="form-group">
                        <label>Current Password</label>
                        <input type="password" name="current_password" class="form-control">
                        <small class="form-text text-muted">Leave blank if you don’t want to change your password.</small>
                    </div>
                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" name="new_password" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Confirm New Password</label>
                        <input type="password" name="confirm_password" class="form-control">
                    </div>

                    <button name="update_profile" class="btn btn-primary">Update Profile</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h4>My Posts</h4></div>
            <div class="card-body">
                <?php if (empty($user_posts)): ?>
                    <div class="alert alert-info">You haven't created any posts yet.</div>
                <?php else: ?>
                    <div class="list-group">
                        <?php foreach ($user_posts as $post): ?>
                            <a href="post.php?id=<?php echo $post['id']; ?>" class="list-group-item list-group-item-action">
                                <div class="d-flex justify-content-between">
                                    <h5 class="mb-1"><?php echo htmlspecialchars($post['title']); ?></h5>
                                    <small><?php echo date('M d, Y', strtotime($post['created_at'])); ?></small>
                                </div>
                                <p class="mb-1"><?php echo htmlspecialchars(substr(strip_tags($post['content']), 0, 100)); ?>...</p>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
