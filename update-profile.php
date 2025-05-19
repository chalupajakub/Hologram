<?php
session_start();
require 'connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT username, name, pfp, bio FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $name = $_POST['name'];
    $bio = $_POST['bio'];
    
    $kontrola = "SELECT id FROM users WHERE username = '$username'";
    $vysledek = mysqli_query($conn, $kontrola);
    $vysl = $vysledek->fetch_assoc();
    
    

    if (mysqli_num_rows($vysledek) > 0 && $_SESSION['user_id'] != $vysl['id']) {
        echo "<p style='text-align: center'>Uživatelské jméno '$jmeno' je již zabrané. Zvolte prosím jiné.</p>";
    }
    else{
        if (!empty($_FILES['pfp']['name'])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . $_SESSION['username']. "_pfp.jpg";

        if (move_uploaded_file($_FILES["pfp"]["tmp_name"], $target_file)) {
            $pfp_path = $target_file;
        } else {
            echo "Chyba při nahrávání obrázku.";
            $pfp_path = $user['pfp'];
        }
    } else {
        $pfp_path = $user['pfp'];
    }

    $stmt = $conn->prepare("UPDATE users SET username = ?, name = ?, pfp = ?, bio = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $username, $name, $pfp_path, $bio, $user_id);
    $stmt->execute();

    echo "Profil byl aktualizován.";
    
    header("Location: index.php");
    exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <title>Úprava profilu</title>
</head>
<body>
    <h2>Upravit profil</h2>
    <form method="POST" enctype="multipart/form-data">
        <label>Username:</label><br>
        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>">

        <label>Jméno:</label><br>
        <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>">

        <label>Profilový obrázek:</label>
        <?php if ($user['pfp']): ?>
            <img src="<?= htmlspecialchars($user['pfp']) ?>" alt="pfp" width="100"><br>
        <?php endif; ?>
        <input type="file" name="pfp">

        <label>Bio:</label><br>
        <textarea name="bio" rows="5" cols="40"><?= htmlspecialchars($user['bio']) ?></textarea>

        <button type="submit">Uložit změny</button>
    </form>
</body>
</html>
