<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
$is_logged_in = isset($_SESSION['name']) && !empty($_SESSION['name']);

// Return JSON response
header('Content-Type: application/json');
echo json_encode([
    'logged_in' => $is_logged_in,
    'user_name' => $is_logged_in ? $_SESSION['name'] : null
]);
?>
