<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/functions.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MoviesCenter - Movie Database</title>
    <link rel="stylesheet" href="assets/css/style.css?v=1.8">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>

<body>

    <nav class="navbar">
        <a href="index.php" class="logo">Movies<span>Center</span></a>

        <button class="menu-toggle" id="menu-toggle">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <div class="nav-container" id="nav-menu">
            <?php if (isset($_SESSION['user_id'])): ?>
                <span class="welcome-text">Welcome, <?= h($_SESSION['username'] ?? 'User') ?></span>
            <?php endif; ?>

            <div class="nav-links">
                <a href="index.php" class="btn btn-primary">Home</a>
                <?php if (is_admin_logged_in()): ?>
                    <a href="add.php" class="btn btn-primary">Add Movie</a>
                <?php endif; ?>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="logout.php" class="btn btn-danger">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-primary">Login</a>
                    <a href="register.php" class="btn btn-primary">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <div class="container">