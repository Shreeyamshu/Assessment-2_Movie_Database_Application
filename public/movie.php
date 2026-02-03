<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header("Location: index.php");
    exit;
}

try {
    // Fetch Movie
    $stmt = $pdo->prepare("SELECT * FROM movies WHERE id = ?");
    $stmt->execute([$id]);
    $movie = $stmt->fetch();

    if (!$movie) {
        die("Movie not found.");
    }

    // Fetch Genres
    $stmtGenres = $pdo->prepare("
        SELECT g.name 
        FROM genres g 
        JOIN movie_genres mg ON g.id = mg.genre_id 
        WHERE mg.movie_id = ?
    ");
    $stmtGenres->execute([$id]);
    $genres = $stmtGenres->fetchAll(PDO::FETCH_COLUMN);

    // Fetch Cast
    $stmtCast = $pdo->prepare("
        SELECT c.name, mc.role 
        FROM cast_members c 
        JOIN movie_cast mc ON c.id = mc.cast_id 
        WHERE mc.movie_id = ?
    ");
    $stmtCast->execute([$id]);
    $cast = $stmtCast->fetchAll();

} catch (PDOException $e) {
    die("Error loading movie: " . $e->getMessage());
}
?>

<div class="movie-details-container">
    <div class="movie-hero">
        <div class="movie-hero-poster">
            <?php if (!empty($movie['poster_url'])): ?>
                <img src="<?= h($movie['poster_url']) ?>" alt="<?= h($movie['title']) ?>">
            <?php endif; ?>
        </div>
        <div class="movie-hero-info">
            <h1 class="movie-detail-title">
                <?= h($movie['title']) ?>
            </h1>
            <div class="movie-detail-meta">
                <span class="detail-year">
                    <?= h($movie['release_year']) ?>
                </span>
                <span class="detail-rating">★
                    <?= h($movie['rating']) ?> / 10
                </span>
            </div>

            <?php if (!empty($genres)): ?>
                <div class="genre-tags">
                    <?php foreach ($genres as $genre): ?>
                        <span class="genre-tag">
                            <?= h($genre) ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="movie-detail-actions">
                <button class="btn btn-primary btn-large">WATCH NOW</button>
                <?php if (is_admin_logged_in()): ?>
                    <a href="edit.php?id=<?= $movie['id'] ?>" class="btn btn-accent btn-large">Edit Movie</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="movie-content">
        <div class="detail-section">
            <h3>Description</h3>
            <p class="movie-description">
                <?= nl2br(h($movie['description'])) ?: 'No description available for this movie.' ?>
            </p>
        </div>

        <?php if (!empty($cast)): ?>
            <div class="detail-section">
                <h3>Cast</h3>
                <div class="cast-grid">
                    <?php foreach ($cast as $member): ?>
                        <div class="cast-card">
                            <span class="cast-name">
                                <?= h($member['name']) ?>
                            </span>
                            <span class="cast-role">
                                <?= h($member['role']) ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>