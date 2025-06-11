<?php 
session_start();
include 'connect.php';

if (empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $post_id = (int)$_GET['id'];

    $check = $conn->prepare("SELECT user_id, image FROM posts WHERE id = ?");
    $check->bind_param("i", $post_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        if ($row['user_id'] == $user_id) {

            if (!empty($row['image'])) {
                $file_path = 'uploads/' . $row['image']; 
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }

            $stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
            $stmt->bind_param("i", $post_id);
            $stmt->execute();
        }
    }
}

header("Location: profile.php?user=" .$_SESSION['username']);
exit;
?>
