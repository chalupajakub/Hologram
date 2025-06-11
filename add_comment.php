<?php
include 'connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id'], $_POST['comment'])) {
    $post_id = intval($_POST['post_id']);
    $user_id = $_SESSION['user_id'];
    $comment = trim($_POST['comment']);
    
    if (strlen($comment) > 0 && strlen($comment) <= 500) {
        $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $post_id, $user_id, $comment);
        $stmt->execute();
        
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid comment length']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
?>
