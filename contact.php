<?php require_once 'includes/header.php'; ?>

<div class="container mt-5">
    <div class="card shadow-lg">
        <div class="card-body">
            <h2 class="mb-4">Contact Us</h2>
            <p>If you have any questions, feedback, or just want to say hello, feel free to contact us using the form below.</p>

            <?php
            $errors = [];
            $success = "";

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $name = clean($_POST['name']);
                $email = clean($_POST['email']);
                $subject = clean($_POST['subject']);
                $message = clean($_POST['message']);

                // Basic validation
                if (empty($name) || empty($email) || empty($subject) || empty($message)) {
                    $errors[] = "All fields are required.";
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Invalid email format.";
                }

                // Dummy success logic (can be replaced with mail or DB logic)
                if (empty($errors)) {
                    $success = "Your message has been received. We'll get back to you soon!";
                }
            }
            ?>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <form method="post" action="">
                <div class="form-group">
                    <label for="name">Your Name</label>
                    <input name="name" type="text" class="form-control" required value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="email">Your Email</label>
                    <input name="email" type="email" class="form-control" required value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input name="subject" type="text" class="form-control" required value="<?php echo isset($subject) ? htmlspecialchars($subject) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea name="message" class="form-control" rows="5" required><?php echo isset($message) ? htmlspecialchars($message) : ''; ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Send Message</button>
            </form>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
