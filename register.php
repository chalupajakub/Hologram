<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <title>HOLOGRAM | Registrace</title>
</head>
<body>
    <h2>Registrace</h2>

    <form action="#" method="post">
        <input type="text" name="username" placeholder="Uživatelské jméno" autocomplete="off">
        <input type="password" name="password" placeholder="Heslo">
        <input type="email" name="email" placeholder="Email" autocomplete="off">
        <label>Registrací souhlasíte se <a href="https://www.youtube.com/watch?v=4HkO5P7gZuI" style="color: aqua">smluvními podmínkami</a></label>
        <input type="submit" value="Zaregistrovat" class="btn-primary">
    </form>
    <p style="text-align: center; opacity: 0.7"><a href="login.php">Již mám účet</a></p>
</body>
</html>

<?php
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $jmeno = mysqli_real_escape_string($conn, $_POST["username"]);
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $heslo = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $kontrola = "SELECT id FROM users WHERE username = '$jmeno'";
    $vysledek = mysqli_query($conn, $kontrola);

    if (mysqli_num_rows($vysledek) > 0) {
        echo "<p style='text-align: center'>Uživatelské jméno '$jmeno' je již zabrané. Zvolte prosím jiné.</p>";
    } else {
        $sql = "INSERT INTO users (username, email, password, name) VALUES ('$jmeno', '$email', '$heslo', '$jmeno')";
        
        if (mysqli_query($conn, $sql)) {
            echo "<p style='text-align: center'>Registrace byla úspěšná.</p>";
        } else {
            echo "<p style='text-align: center'>Chyba: " . mysqli_error($conn). "</p>";
        }
    }
}
?>
