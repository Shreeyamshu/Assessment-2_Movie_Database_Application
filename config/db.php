<?php
// config/db.php

// $host = 'localhost';
// $dbname = 'NP03CS4A240052';
// $user = 'NP03CS4A240052'; 
// $pass = 'tycTT2gFtN';     

$host = 'localhost';
$dbname = 'movie_db';
$user = 'root';
$pass = '';


try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // In production, log this error instead of showing it
    die("Database Connection Failed: " . $e->getMessage());
}
?>