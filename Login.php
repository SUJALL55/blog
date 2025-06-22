<?php
require_once 'includes/header.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('index.php');
}

// Initialize variables
$errors = [];
$username = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = clean(trim($_POST['username']));
    $password = clean(trim($_POST['password']));

    // Validate inputs
    if (empty($username)) {
        $errors[] = "Username or email is required.";
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    }

    // If valid input, attempt login
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1");
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['is_admin'] = $user['is_admin'];

                redirect('index.php');
            } else {
                $errors[] = "Invalid password.";
            }
        } else {
            $errors[] = "Username or email not found.";
        }

        $stmt->close();
    }
}
?>

<!-- HTML Output -->
<div class="row">
    <div class="col-md-6 offset-md-3">
        <div class="card mt-5 shadow">
            <div class="card-header text-center">
                <h4>Login</h4>
            </div>
            <div class="card-body">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="post" novalidate>
                    <div class="form-group">
                        <label for="username">Username or Email</label>
                        <input type="text" name="username" id="username" class="form-control" value="<?php echo htmlspecialchars($username); ?>" required autofocus>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>
                    <button type="submit" name="login" class="btn btn-primary btn-block">Login</button>
                </form>

                <div class="mt-3 text-center">
                    <p>Don't have an account? <a href="register.php">Register</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
