<?php
include 'connect.php';
header('Content-Type: application/json');

if (!isset($_GET['user_id'])) {
    die(json_encode([]));
}

$userId = $_GET['user_id'];

$query = "SELECT users.id, users.username, users.name, users.pfp 
          FROM followers 
          JOIN users ON followers.follower_id = users.id 
          WHERE followers.followed_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$followers = [];
while ($row = $result->fetch_assoc()) {
    $followers[] = $row;
}

echo json_encode($followers);
?>
