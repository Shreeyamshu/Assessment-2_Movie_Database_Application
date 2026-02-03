<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db.php';

require_admin();

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: index.php");
    exit;
}

$error = '';
$success = '';

// Fetch Movie
$stmt = $pdo->prepare("SELECT * FROM movies WHERE id = ?");
$stmt->execute([$id]);
$movie = $stmt->fetch();

if (!$movie) {
    echo "Movie not found.";
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

// Fetch Linked Genres
$stmtG = $pdo->prepare("SELECT genre_id FROM movie_genres WHERE movie_id = ?");
$stmtG->execute([$id]);
$currentGenres = $stmtG->fetchAll(PDO::FETCH_COLUMN);

// Fetch Linked Cast
$stmtC = $pdo->prepare("SELECT c.name FROM cast_members c JOIN movie_cast mc ON c.id = mc.cast_id WHERE mc.movie_id = ?");
$stmtC->execute([$id]);
$currentCast = $stmtC->fetchAll(PDO::FETCH_COLUMN);
$currentCastStr = implode(', ', $currentCast);

// Fetch all genres for the form
$stmtGenres = $pdo->query("SELECT * FROM genres ORDER BY name");
$allGenres = $stmtGenres->fetchAll();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $year = (int) $_POST['year'];
    $rating = (float) $_POST['rating'];
    $description = trim($_POST['description']);
    $poster_url = trim($_POST['poster_url']);
    $genre_ids = $_POST['genres'] ?? [];
    $cast_names = trim($_POST['cast']);

    if (!$title || !$year || !$rating) {
        $error = "Title, Year, and Rating are required.";
    } else {
        try {
            $pdo->beginTransaction();

            // Update Movie
            $updateStmt = $pdo->prepare("UPDATE movies SET title=?, release_year=?, rating=?, description=?, poster_url=? WHERE id=?");
            $updateStmt->execute([$title, $year, $rating, $description, $poster_url, $id]);

            // Update Genres
            $pdo->prepare("DELETE FROM movie_genres WHERE movie_id = ?")->execute([$id]);
            if (!empty($genre_ids)) {
                $genreStmt = $pdo->prepare("INSERT INTO movie_genres (movie_id, genre_id) VALUES (?, ?)");
                foreach ($genre_ids as $gid) {
                    $genreStmt->execute([$id, $gid]);
                }
            }

            // Update Cast
            $pdo->prepare("DELETE FROM movie_cast WHERE movie_id = ?")->execute([$id]);

            if (!empty($cast_names)) {
                $castList = array_map('trim', explode(',', $cast_names));
                $checkCast = $pdo->prepare("SELECT id FROM cast_members WHERE name = ?");
                $insertCast = $pdo->prepare("INSERT INTO cast_members (name) VALUES (?)");
                $linkCast = $pdo->prepare("INSERT INTO movie_cast (movie_id, cast_id, role) VALUES (?, ?, ?)");

                foreach ($castList as $name) {
                    if (empty($name))
                        continue;

                    $checkCast->execute([$name]);
                    $castId = $checkCast->fetchColumn();

                    if (!$castId) {
                        $insertCast->execute([$name]);
                        $castId = $pdo->lastInsertId();
                    }

                    $linkCast->execute([$id, $castId, 'Actor']);
                }
            }

            $pdo->commit();
            $success = "Movie updated successfully! <a href='index.php'>Go Back</a>";

            // Refresh data for the form
            $movie['title'] = $title;
            $movie['release_year'] = $year;
            $movie['rating'] = $rating;
            $movie['description'] = $description;
            $movie['poster_url'] = $poster_url;
            $currentGenres = $genre_ids;
            $currentCastStr = $cast_names; // Approximation

        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Error updating movie: " . $e->getMessage();
        }
    }
}
?>

<h2 style="margin-bottom: 2rem;">Edit Movie:
    <?= h($movie['title']) ?>
</h2>

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

<div class="movie-card" style="padding: 2rem; max-width: 800px; margin: 0 auto;">
    <form method="POST" action="">
        <div style="margin-bottom: 1rem;">
            <label>Title</label>
            <input type="text" name="title" class="form-input" value="<?= h($movie['title']) ?>" required>
        </div>

        <div style="display: flex; gap: 1rem; margin-bottom: 1rem;">
            <div style="flex: 1;">
                <label>Year</label>
                <input type="number" name="year" class="form-input" value="<?= h($movie['release_year']) ?>" required>
            </div>
            <div style="flex: 1;">
                <label>Rating (0-10)</label>
                <input type="number" name="rating" class="form-input" value="<?= h($movie['rating']) ?>" required
                    step="0.1">
            </div>
        </div>

        <div style="margin-bottom: 1rem;">
            <label>Poster URL</label>
            <input type="url" name="poster_url" class="form-input" value="<?= h($movie['poster_url']) ?>">
        </div>

        <div style="margin-bottom: 1rem;">
            <label>Description</label>
            <textarea name="description" class="form-input" rows="5"><?= h($movie['description']) ?></textarea>
        </div>

        <div style="margin-bottom: 1rem;">
            <label>Genres</label>
            <div
                style="display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 10px; margin-top: 5px;">
                <?php foreach ($allGenres as $g): ?>
                    <label style="display: flex; align-items: center; gap: 5px; cursor: pointer;">
                        <input type="checkbox" name="genres[]" value="<?= $g['id'] ?>" <?= in_array($g['id'], $currentGenres) ? 'checked' : '' ?>>
                        <?= h($g['name']) ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <div style="margin-bottom: 1rem;">
            <label>Cast (Comma separated names)</label>
            <input type="text" name="cast" class="form-input" value="<?= h($currentCastStr) ?>">
        </div>

        <button type="submit" class="btn btn-primary">Update Movie</button>
        <a href="index.php" class="btn btn-danger" style="text-decoration: none; margin-left: 10px;">Cancel</a>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>