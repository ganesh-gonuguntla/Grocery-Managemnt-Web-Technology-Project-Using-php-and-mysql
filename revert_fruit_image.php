<?php
include 'config.php';

// Revert fruits category image to original
$query = "UPDATE categories SET image_url = 'images/fruits.jpg' WHERE category_name = 'Fruits'";
if (mysqli_query($conn, $query)) {
    echo "Successfully reverted fruits category image to original!";
} else {
    echo "Error reverting image: " . mysqli_error($conn);
}
?> 