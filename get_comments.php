<?php
include 'connect.php';
session_start();

if (!isset($_GET['post_id'])) {
    echo json_encode(['success' => false, 'error' => 'Missing post_id parameter']);
    exit;
}

$post_id = (int)$_GET['post_id'];

try {
    $stmt = $conn->prepare("
        SELECT c.*, u.username, u.pfp 
        FROM comments c 
        JOIN users u ON c.user_id = u.user_id
        WHERE c.post_id = ?
        ORDER BY c.created_at DESC
    ");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $comments = [];
    while ($row = $result->fetch_assoc()) {
        // Format the date for display
        $date = new DateTime($row['created_at']);
        $formattedDate = $date->format('j. n. Y H:i');
        
        $comments[] = [
            'username' => htmlspecialchars($row['username']),
            'pfp' => $row['pfp'] ? htmlspecialchars($row['pfp']) : 'default_pfp.jpg',
            'content' => htmlspecialchars($row['content']),
            'date' => $formattedDate
        ];
    }
    
    echo json_encode([
        'success' => true,
        'comments' => $comments
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
