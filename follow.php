<?php 
session_start();
include "connect.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Neplatné ID.");
}

$id = (int)$_SESSION['user_id'];
$follow_id = (int)$_GET['id'];


$checkStmt = $conn->prepare("SELECT 1 FROM followers WHERE follower_id = ? AND followed_id = ?");
$checkStmt->bind_param("ii", $id, $follow_id);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows === 0) {
    $insertStmt = $conn->prepare("INSERT INTO followers (follower_id, followed_id) VALUES (?, ?)");
    $insertStmt->bind_param("ii", $id, $follow_id);
    $insertStmt->execute();
    $insertStmt->close();
}
else{
    $deleteStmt = $conn->prepare("DELETE FROM followers WHERE follower_id = ? AND followed_id = ?");
    $deleteStmt->bind_param("ii", $id, $follow_id);
    $deleteStmt->execute();
    $deleteStmt->close();
}

$checkStmt->close();

header("Location: profile.php?id=$follow_id");
exit;
?>
