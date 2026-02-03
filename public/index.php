<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db.php';

// Fetch initial data (can be replaced by Ajax load, but good for SEO/first paint)
try {
    // Fetch unique genres for the filter
    $stmtGenres = $pdo->query("SELECT * FROM genres ORDER BY name");
    $genres = $stmtGenres->fetchAll();

    // Fetch movies (limit to recent 20 for initial load)
    $stmtMovies = $pdo->query("SELECT * FROM movies ORDER BY created_at DESC LIMIT 20");
    $movies = $stmtMovies->fetchAll();

} catch (PDOException $e) {
    die("Error loading data: " . $e->getMessage());
}
?>

<div class="search-container">
    <div class="search-box">
        <input type="text" id="search-input" class="form-input" placeholder="Search by title...">
    </div>
    <div class="filter-group">
        <select id="genre-select" class="form-input">
            <option value="">All Genres</option>
            <?php foreach ($genres as $genre): ?>
                <option value="<?= h($genre['id']) ?>"><?= h($genre['name']) ?></option>
            <?php endforeach; ?>
        </select>
        
        <input type="number" id="year-input" class="form-input" placeholder="Year" min="1900" max="2100">
        
        <select id="rating-select" class="form-input">
            <option value="">Min Rating</option>
            <option value="9">★ 9+</option>
            <option value="8">★ 8+</option>
            <option value="7">★ 7+</option>
            <option value="6">★ 6+</option>
            <option value="5">★ 5+</option>
        </select>
    </div>
</div>

<!-- Hidden indicator for JS to know if admin is logged in -->
<input type="hidden" id="is-admin" value="<?= is_admin_logged_in() ? '1' : '0' ?>">

<div id="movie-grid" class="movie-grid">
    <?php foreach ($movies as $movie): ?>
        <a href="movie.php?id=<?= $movie['id'] ?>" class="movie-card-link">
            <div class="movie-card">
                <?php if (!empty($movie['poster_url'])): ?>
                    <img src="<?= h($movie['poster_url']) ?>" alt="<?= h($movie['title']) ?>" class="movie-poster">
                <?php endif; ?>
                <div class="movie-info">
                    <h3 class="movie-title">
                        <?= h($movie['title']) ?>
                    </h3>
                    <div class="movie-meta">
                        <span>
                            <?= h($movie['release_year']) ?>
                        </span>
                        <span class="rating">★
                            <?= h($movie['rating']) ?>
                        </span>
                    </div>
                    <?php if (is_admin_logged_in()): ?>
                        <div class="actions" style="margin-top: 10px;">
                            <object><a href="edit.php?id=<?= $movie['id'] ?>" class="btn btn-primary"
                                    style="font-size: 0.8rem; height: auto; display: inline-block;">Edit</a></object>
                            <object><a href="delete.php?id=<?= $movie['id'] ?>" class="btn btn-danger"
                                    style="font-size: 0.8rem; height: auto; display: inline-block; margin-left: 5px;"
                                    onclick="event.stopPropagation(); return confirm('Are you sure?');">Delete</a></object>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </a>
    <?php endforeach; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>