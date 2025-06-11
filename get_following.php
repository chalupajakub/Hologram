<?php
include 'connect.php';
header('Content-Type: application/json');

if (!isset($_GET['user_id'])) {
    die(json_encode([]));
}

$userId = $_GET['user_id'];

$query = "SELECT users.id, users.username, users.name, users.pfp 
          FROM followers 
          JOIN users ON followers.followed_id = users.id 
          WHERE followers.follower_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$following = [];
while ($row = $result->fetch_assoc()) {
    $following[] = $row;
}

echo json_encode($following);
?>
