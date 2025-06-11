<?php
session_start();
include 'connect.php';

function toggleLike($conn, $user_id, $post_id) {
    // Check if user already liked the post
    $check = $conn->prepare("SELECT 1 FROM likes WHERE liked_by = ? AND liked_post = ?");
    $check->bind_param("ii", $user_id, $post_id);
    $check->execute();
    $check->store_result();
    
    if ($check->num_rows > 0) {
        // Unlike the post
        $delete = $conn->prepare("DELETE FROM likes WHERE liked_by = ? AND liked_post = ?");
        $delete->bind_param("ii", $user_id, $post_id);
        $delete->execute();
        $delete->close();
        $liked = false;
    } else {
        // Like the post
        $insert = $conn->prepare("INSERT INTO likes (liked_by, liked_post) VALUES (?, ?)");
        $insert->bind_param("ii", $user_id, $post_id);
        $insert->execute();
        $insert->close();
        $liked = true;
    }
    $check->close();
    
    // Get updated like count
    $count = $conn->prepare("SELECT COUNT(*) as like_count FROM likes WHERE liked_post = ?");
    $count->bind_param("i", $post_id);
    $count->execute();
    $result = $count->get_result();
    $like_count = $result->fetch_assoc()['like_count'];
    $count->close();
    
    return [
        'liked' => $liked,
        'like_count' => $like_count
    ];
}

if(empty($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$post_id = isset($_GET['id']) && is_numeric($_GET['id']) ? $_GET['id'] : null;

if (!$user_id || !$post_id) {
    die("Neplatný požadavek.");
}

$result = toggleLike($conn, $user_id, $post_id);

// Return JSON response for potential AJAX implementation
header('Content-Type: application/json');
echo json_encode($result);
exit;
