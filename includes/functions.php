<?php
// includes/functions.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Escape HTML special characters
function h($string)
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Check if user is logged in as admin
function is_admin_logged_in()
{
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Require admin login, otherwise redirect
function require_admin()
{
    if (!is_admin_logged_in()) {
        header("Location: login.php");
        exit;
    }
}

// Format rating as stars
function render_stars($rating)
{
    $stars = round($rating / 2);
    $out = '';
    for ($i = 0; $i < 5; $i++) {
        if ($i < $stars) {
            $out .= '★';
        } else {
            $out .= '☆';
        }
    }
    return $out;
}
?>