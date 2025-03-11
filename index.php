<?php

session_start();

if(isset($_POST['submit'])){
    $content = $_POST['content'];
    include_once 'db.php';

    $SQL = "INSERT INTO comments (user_id, content) VALUES (?, ?)";
    $stmt = $conn->prepare($SQL);
    $stmt->bind_param("is", $_SESSION['user_id'], $content);
    $stmt->execute();
}

?>