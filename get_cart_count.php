<?php
include 'config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode(['count' => 0]);
    exit();
}

// Get cart count
$query = "SELECT SUM(quantity) as count FROM cart WHERE user_id = {$_SESSION['user_id']}";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

echo json_encode(['count' => (int)$row['count']]);
?> 