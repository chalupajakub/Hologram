<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <title>HOLOGRAM | Přihlášení</title>
</head>
<body>
    <h2>Přihlášení</h2>

    <form action="#" method="post">
        <input type="text" name="username" placeholder="Uživatelské jméno" autocomplete="off">
        <input type="password" name="password" placeholder="Heslo">
        <input type="submit" value="Přihlásit" class="btn-primary">
    </form>
    
    <p style="text-align: center; opacity: 0.7; margin-block: 0"><a href="register.php">Vytvořit účet</a></p>
</body>
</html>

<?php
session_start();
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $jmeno = mysqli_real_escape_string($conn, $_POST["username"]);
    $heslo = $_POST["password"];

    $sql = "SELECT * FROM users WHERE username = '$jmeno'";
    $vysledek = mysqli_query($conn, $sql);

    if (mysqli_num_rows($vysledek) === 1) {
        $uzivatel = mysqli_fetch_assoc($vysledek);

        if (password_verify($heslo, $uzivatel["password"])) {
            $_SESSION["user_id"] = $uzivatel["id"];
            $_SESSION["username"] = $uzivatel["username"];
            $_SESSION["name"] = $uzivatel["name"];
            $_SESSION["pfp"] = $uzivatel["pfp"];
            $_SESSION["bio"] = $uzivatel["bio"];
            echo "<p style='text-align: center'>Přihlášení bylo úspěšné.</p>";
            header("Location: index.php");
        } else {
            echo "<p style='text-align: center'>Nesprávné heslo.</p>";
        }
    } else {
        echo "<p style='text-align: center'>Uživatel s tímto jménem neexistuje.</p>";
    }
}
?>
