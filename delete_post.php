<?php
session_start();
require 'db.php'; // Include database connection

// Check if the user is logged in and the 'id' parameter is set
if (isset($_SESSION['user_id']) && isset($_GET['id'])) {
    $post_id = $_GET['id'];

    // Disable foreign key checks temporarily
    $conn->query("SET FOREIGN_KEY_CHECKS = 0");

    // Fetch the image path of the post to be deleted
    $stmt = $conn->prepare("SELECT image_path FROM images WHERE post_id = ?");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $image = $result->fetch_assoc();

    // If the image exists, delete the image file from the server
    if ($image && !empty($image['image_path']) && file_exists($image['image_path'])) {
        unlink($image['image_path']); // Delete the file
    }

    // Delete the related comments first
    $stmt = $conn->prepare("DELETE FROM comments WHERE post_id = ?");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();

    // Delete the image record from the database
    $stmt = $conn->prepare("DELETE FROM images WHERE post_id = ?");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();

    // Delete the post from the database
    $stmt = $conn->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $post_id, $_SESSION['user_id']);
    $stmt->execute();

    // Re-enable foreign key checks
    $conn->query("SET FOREIGN_KEY_CHECKS = 1");

    // Redirect back to the homepage or post list
    header("Location: home.php");
    exit();
} else {
    // Redirect to home page if the user is not logged in or the 'id' is missing
    header("Location: home.php");
    exit();
}
?>