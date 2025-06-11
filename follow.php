<?php
session_start();
include "connect.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Check if username parameter is provided
if (!isset($_GET['user'])) {
    $_SESSION['error'] = "Uživatel nebyl specifikován.";
    header("Location: index.php");
    exit;
}

$currentUserId = (int)$_SESSION['user_id'];
$targetUsername = $_GET['user'];

// Get the target user's ID
$userStmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$userStmt->bind_param("s", $targetUsername);
$userStmt->execute();
$userResult = $userStmt->get_result();

if ($userResult->num_rows === 0) {
    $_SESSION['error'] = "Uživatel neexistuje.";
    header("Location: index.php");
    exit;
}

$targetUser = $userResult->fetch_assoc();
$targetUserId = (int)$targetUser['id'];
$userStmt->close();

// Check if trying to follow self
if ($currentUserId === $targetUserId) {
    $_SESSION['error'] = "Nemůžete sledovat sami sebe.";
    header("Location: profile.php?user=" . urlencode($targetUsername));
    exit;
}

// Check current follow status
$checkStmt = $conn->prepare("SELECT 1 FROM followers WHERE follower_id = ? AND followed_id = ?");
$checkStmt->bind_param("ii", $currentUserId, $targetUserId);
$checkStmt->execute();
$checkStmt->store_result();

$isFollowing = $checkStmt->num_rows > 0;
$checkStmt->close();

// Toggle follow status
if ($isFollowing) {
    // Unfollow
    $deleteStmt = $conn->prepare("DELETE FROM followers WHERE follower_id = ? AND followed_id = ?");
    $deleteStmt->bind_param("ii", $currentUserId, $targetUserId);
    $success = $deleteStmt->execute();
    $deleteStmt->close();
    
    if ($success) {
        $_SESSION['success'] = "Přestali jste sledovat uživatele @$targetUsername";
    } else {
        $_SESSION['error'] = "Nepodařilo se přestat sledovat uživatele.";
    }
} else {
    // Follow
    $insertStmt = $conn->prepare("INSERT INTO followers (follower_id, followed_id) VALUES (?, ?)");
    $insertStmt->bind_param("ii", $currentUserId, $targetUserId);
    $success = $insertStmt->execute();
    $insertStmt->close();
    
    if ($success) {
        $_SESSION['success'] = "Nyní sledujete uživatele @$targetUsername";
    } else {
        $_SESSION['error'] = "Nepodařilo se sledovat uživatele.";
    }
}

// Redirect back to the previous page
$redirectUrl = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "profile.php?user=" . urlencode($targetUsername);
header("Location: $redirectUrl");
exit;
?>
