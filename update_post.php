<?php
session_start();
require 'db.php'; // Include the database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $post_id = $_POST['post_id'];
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id']; // Ensure the user is editing their own post

    // Fetch current image path
    $stmt = $conn->prepare("SELECT image_path FROM images WHERE post_id = ?");
    $stmt->bind_param("i", $post_id); // Bind the integer parameter
    $stmt->execute();
    $result = $stmt->get_result();
    $oldImage = $result->fetch_assoc()['image_path']; // Fetch the image path

    // Check if a new image is uploaded
    if (!empty($_FILES["image"]["name"])) {
        $image_name = time() . "_" . $_FILES["image"]["name"];
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($image_name);

        // Move the uploaded file
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $new_image_path = $target_file;

            // Delete the old image if it exists
            if (!empty($oldImage) && file_exists($oldImage)) {
                unlink($oldImage);
            }

            // Update the image path in the database
            $stmt = $conn->prepare("UPDATE images SET image_path = ? WHERE post_id = ?");
            $stmt->bind_param("si", $new_image_path, $post_id); // Bind parameters for the update
            $stmt->execute();
        }
    } else {
        $new_image_path = $oldImage; // Keep the old image if no new one uploaded
    }

    // Update the post content in the database
    $stmt = $conn->prepare("UPDATE posts SET content = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("sii", $content, $post_id, $user_id); // Bind parameters for the update
    $stmt->execute();

    header("Location: home.php"); // Redirect back to posts page
    exit();
}
?>