<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION['user_id'];
    $popis = mysqli_real_escape_string($conn, $_POST['obsah']);


    if (!empty($_FILES['obrazek']['name'])) {
        $povolenetypy = ['jpg', 'jpeg', 'png', 'gif'];
        $nazev = $_FILES['obrazek']['name'];
        $tmp_name = $_FILES['obrazek']['tmp_name'];
        $ext = strtolower(pathinfo($nazev, PATHINFO_EXTENSION));

        if (!in_array($ext, $povolenetypy)) {
            die("Nepovolený typ souboru.");
        }

        $unikatni_nazev = uniqid('img_', true) . "." . $ext;
        $cilova_cesta = 'uploads/' . $unikatni_nazev;

        if (move_uploaded_file($tmp_name, $cilova_cesta)) {
            $sql = "INSERT INTO posts (user_id, content, image) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iss", $user_id, $popis, $cilova_cesta);

            if ($stmt->execute()) {
                echo "Příspěvek byl úspěšně přidán.";
            } else {
                echo "Chyba při ukládání do databáze: " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "Nepodařilo se nahrát soubor.";
        }
    } else {
        echo "Soubor nebyl nahrán nebo nastala chyba.";
        echo $_FILES['obrazek']['error'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <title>HOLOGRAM | Post</title>
</head>
<body>
    <h2>Přidat nový příspěvek</h2>

    <form action="#" method="post" enctype="multipart/form-data">
        <label for="obrazek">Add image</label>
        <input type="file" id="obrazek" name="obrazek" required>
        
        <label for="obsah">Description</label>
        <textarea id="obsah" name="obsah" rows="10" required></textarea>
    
        <input type="submit" value="Confirm"></input>
    </form>
</body>
</html>

