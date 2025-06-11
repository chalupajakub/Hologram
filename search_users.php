<?php
include 'connect.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Get search query
$query = isset($_POST['query']) ? trim($_POST['query']) : '';

if (empty($query)) {
    echo json_encode([]);
    exit;
}

// Prepare search query with LIKE for partial matches
$searchTerm = '%' . $query . '%';

// Search for users (exclude current user)
$stmt = $conn->prepare("SELECT username, pfp FROM users WHERE username LIKE ? AND id != ? ORDER BY username ASC LIMIT 10");
$stmt->bind_param("si", $searchTerm, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = [
        'username' => $row['username'],
        'pfp' => $row['pfp'] ?: 'default-avatar.png' // fallback if no profile picture
    ];
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($users);
?>
