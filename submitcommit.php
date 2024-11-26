<?php
session_start();
include('db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];
    $post_id = $_POST['post_id'];
    $content = $_POST['content'];

    // Check if fields are empty  
    if (empty($user_id) || empty($post_id) || empty($content)) {
        die("User ID, Post ID, and content cannot be empty.");
    }

    // Prepare and bind  
    $stmt = $conn->prepare("INSERT INTO Comments (post_id, user_id, content) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $post_id, $user_id, $content);

    if ($stmt->execute()) {
        header("Location: home.php"); // Redirect back to the home page  
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
