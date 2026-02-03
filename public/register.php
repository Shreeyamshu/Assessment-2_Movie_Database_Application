<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($username) || empty($password)) {
        $error = "Please fill in all fields.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        if ($stmt->fetch()) {
            $error = "Username already taken.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, role) VALUES (:username, :hash, 'user')");
            if ($stmt->execute(['username' => $username, 'hash' => $hash])) {
                $success = "Account created successfully! You can now <a href='login.php'>login</a>.";
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}
?>

<div style="max-width: 400px; margin: 4rem auto;">
    <div class="movie-card" style="padding: 2rem;">
        <h2 style="text-align: center; margin-bottom: 2rem;">Create Account</h2>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <?= h($error) ?>
            </div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success">
                <?= $success ?>
            </div>
        <?php endif; ?>

        <?php if (!$success): ?>
            <form method="POST" action="">
                <div style="margin-bottom: 1rem;">
                    <label>Username</label>
                    <input type="text" name="username" class="form-input" required>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label>Password</label>
                    <input type="password" name="password" class="form-input" required>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" class="form-input" required>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">Register</button>
            </form>
        <?php endif; ?>

        <p style="text-align: center; margin-top: 1rem; color: var(--text-muted);">
            Already have an account? <a href="login.php" style="color: var(--primary);">Login here</a>
        </p>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>