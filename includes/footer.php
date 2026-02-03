</div> <!-- End Container -->

<footer class="footer">
    <div class="footer-content">
        <div class="footer-section">
            <h3 class="footer-logo">Movies<span>Center</span></h3>
            <p>Your ultimate destination for discovering and managing your favorite movies.</p>
        </div>

        <div class="footer-section">
            <h4>Quick Links</h4>
            <ul class="footer-links">
                <li><a href="index.php">Home</a></li>
                <?php if (isset($_SESSION['user_id']) && is_admin_logged_in()): ?>
                    <li><a href="add.php">Add Movie</a></li>
                <?php endif; ?>
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                <?php else: ?>
                    <li><a href="logout.php">Logout</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <div class="footer-bottom">
        <p>&copy; <?= date('Y') ?> MoviesCenter | Final Assessment 2026</p>
    </div>
</footer>

<script src="assets/js/main.js?v=<?= time() ?>"></script>
</body>

</html>