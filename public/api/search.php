<?php
require_once __DIR__ . '/../../config/db.php';

header('Content-Type: application/json');

$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$genreId = isset($_GET['genre']) ? trim($_GET['genre']) : '';
$year = isset($_GET['year']) ? trim($_GET['year']) : '';
$rating = isset($_GET['rating']) ? trim($_GET['rating']) : '';

$rating = isset($_GET['rating']) ? trim($_GET['rating']) : '';

try {
    $sql = "SELECT movies.* FROM movies";
    $params = [];
    $conditions = [];

    // Build query
    if ($genreId) {
        $sql .= " JOIN movie_genres ON movies.id = movie_genres.movie_id";
        $conditions[] = "movie_genres.genre_id = :genreId";
        $params['genreId'] = $genreId;
    }

    if ($query) {
        $conditions[] = "movies.title LIKE :query";
        $params['query'] = '%' . $query . '%';
    }

    if ($year) {
        $conditions[] = "movies.release_year = :year";
        $params['year'] = $year;
    }

    if ($rating) {
        $conditions[] = "movies.rating >= :rating";
        $params['rating'] = $rating;
    }

    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(' AND ', $conditions);
    }

    $sql .= " ORDER BY movies.created_at DESC LIMIT 50";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $movies = $stmt->fetchAll();

    echo json_encode($movies);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>