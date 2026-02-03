<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db.php';

require_admin();

$error = '';
$success = '';

// Fetch all genres for the form
$stmtGenres = $pdo->query("SELECT * FROM genres ORDER BY name");
$allGenres = $stmtGenres->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $year = (int) $_POST['year'];
    $rating = (float) $_POST['rating'];
    $description = trim($_POST['description']);
    $poster_url = trim($_POST['poster_url']);
    $genre_ids = $_POST['genres'] ?? []; // Array of genre IDs
    $cast_names = trim($_POST['cast']); // Comma separated names

    if (!$title || !$year || !$rating) {
        $error = "Title, Year, and Rating are required.";
    } else {
        try {
            $pdo->beginTransaction();

            // Insert Movie
            $stmt = $pdo->prepare("INSERT INTO movies (title, release_year, rating, description, poster_url) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$title, $year, $rating, $description, $poster_url]);
            $movieId = $pdo->lastInsertId();

            // Insert Genres
            if (!empty($genre_ids)) {
                $genreStmt = $pdo->prepare("INSERT INTO movie_genres (movie_id, genre_id) VALUES (?, ?)");
                foreach ($genre_ids as $gid) {
                    $genreStmt->execute([$movieId, $gid]);
                }
            }

            // Handle Cast Members
            if (!empty($cast_names)) {
                $castList = array_map('trim', explode(',', $cast_names));
                $checkCast = $pdo->prepare("SELECT id FROM cast_members WHERE name = ?");
                $insertCast = $pdo->prepare("INSERT INTO cast_members (name) VALUES (?)");
                $linkCast = $pdo->prepare("INSERT INTO movie_cast (movie_id, cast_id, role) VALUES (?, ?, ?)");

                foreach ($castList as $name) {
                    if (empty($name))
                        continue;

                    // Check if cast exists
                    $checkCast->execute([$name]);
                    $castId = $checkCast->fetchColumn();

                    if (!$castId) {
                        $insertCast->execute([$name]);
                        $castId = $pdo->lastInsertId();
                    }

                    // Link cast member
                    $linkCast->execute([$movieId, $castId, 'Actor']);
                }
            }

            $pdo->commit();
            $success = "Movie added successfully!";
            // Reset form fields if needed, or redirect
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Error adding movie: " . $e->getMessage();
        }
    }
}
?>

<h2 style="margin-bottom: 2rem;">Add New Movie</h2>

<?php if ($error): ?>
    <div class="alert alert-error">
        <?= h($error) ?>
    </div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="alert alert-success">
        <?= h($success) ?>
    </div>
<?php endif; ?>

<div class="movie-card" style="padding: 2rem; max-width: 800px; margin: 0 auto;">
    <form method="POST" action="">
        <div style="margin-bottom: 1rem;">
            <label>Title</label>
            <input type="text" name="title" class="form-input" required>
        </div>

        <div style="display: flex; gap: 1rem; margin-bottom: 1rem;">
            <div style="flex: 1;">
                <label>Year</label>
                <input type="number" name="year" class="form-input" required min="1900" max="2100">
            </div>
            <div style="flex: 1;">
                <label>Rating (0-10)</label>
                <input type="number" name="rating" class="form-input" required min="0" max="10" step="0.1">
            </div>
        </div>

        <div style="margin-bottom: 1rem;">
            <label>Poster URL (Image Link)</label>
            <input type="url" name="poster_url" class="form-input" placeholder="https://example.com/poster.jpg">
        </div>

        <div style="margin-bottom: 1rem;">
            <label>Description</label>
            <textarea name="description" class="form-input" rows="5"></textarea>
        </div>

        <div style="margin-bottom: 1rem;">
            <label>Genres</label>
            <div
                style="display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 10px; margin-top: 5px;">
                <?php foreach ($allGenres as $g): ?>
                    <label style="display: flex; align-items: center; gap: 5px; cursor: pointer;">
                        <input type="checkbox" name="genres[]" value="<?= $g['id'] ?>">
                        <?= h($g['name']) ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <div style="margin-bottom: 1rem;">
            <label>Cast (Comma separated names)</label>
            <input type="text" name="cast" class="form-input" placeholder="Leonardo DiCaprio, Brad Pitt...">
        </div>

        <button type="submit" class="btn btn-primary">Save Movie</button>
        <a href="index.php" class="btn btn-danger" style="text-decoration: none; margin-left: 10px;">Cancel</a>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>