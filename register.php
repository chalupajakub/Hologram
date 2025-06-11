<?php
include 'connect.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $canPost = false;
    

    if (empty($username)) {
        echo "Uživatelské jméno je povinné.";
    } elseif (!preg_match('/^[a-z0-9._]+$/', $username)) {
        echo "Uživatelské jméno může obsahovat pouze malá písmena, čísla, tečky a podtržítka.";
    } elseif (strlen($username) < 3 || strlen($username) > 20) {
        echo "Uživatelské jméno musí mít 3-20 znaků.";
    }
    

    if (empty($email)) {
        echo "Email je povinný.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Neplatný formát emailu.";
    } elseif (strlen($email) > 100) {
        echo "Email je příliš dlouhý.";
    }

    if (empty($password)) {
        echo "Heslo je povinné.";
    } elseif (strlen($password) < 8) {
        echo "Heslo musí mít alespoň 8 znaků.";
    }
    
        $check_stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $check_stmt->bind_param("s", $username);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            echo "Uživatelské jméno '$username' je již zabrané. Zvolte prosím jiné.";
        } else {
            $email_check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $email_check_stmt->bind_param("s", $email);
            $email_check_stmt->execute();
            $email_check_result = $email_check_stmt->get_result();
            
            if ($email_check_result->num_rows > 0) {
                echo "Tento email je již registrován.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $defaultpfp = "uploads/pfp.jpg";
                
                $canPost = 0;
                $insert_stmt = $conn->prepare("INSERT INTO users (username, email, password, name, pfp, canPost) VALUES (?, ?, ?, ?, ?, ?)");
                $insert_stmt->bind_param("sssssi", $username, $email, $hashed_password, $username, $defaultpfp, $canPost);
                
                if ($insert_stmt->execute()) {
                    $success = "Registrace byla úspěšná. Nyní se můžete přihlásit.";
                    header("Refresh: 3; URL=login.php");
                } else {
                    echo "Chyba při registraci: " . $conn->error;
                }
            }
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
    <title>HOLOGRAM | Registrace</title>
</head>
<body>
    <h2>Registrace</h2>
    
        <form action="#" method="post">
            <input type="text" name="username" placeholder="Uživatelské jméno" autocomplete="off" 
                   pattern="[a-z0-9._]+" title="Pouze malá písmena, čísla, tečky a podtržítka" required>
            
            <input type="password" name="password" placeholder="Heslo" minlength="8" required>
            
            <input type="email" name="email" placeholder="Email" autocomplete="off"
                   maxlength="100" required>
            
            <label>Registrací souhlasíte se <a href="smluvnipodminky.html" style="color: aqua">smluvními podmínkami</a></label>
            <input type="submit" value="Zaregistrovat" class="btn-primary">
        </form>
    
    <p style="text-align: center; opacity: 0.7"><a href="login.php">Již mám účet</a></p>
</body>
</html>
