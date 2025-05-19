<?php 
    include 'connect.php';
    session_start();
    
    if(empty($_SESSION['username'])){
        header("Location: login.php");
        exit;
    }
    
    $username;
    
    if(isset($_GET['user'])){
        $username = $_GET['user'];
    }

    $userstmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $userstmt->bind_param("s", $username);
    $userstmt->execute();
    $userResult = $userstmt->get_result();
    $user = $userResult->fetch_assoc();
    
    if (!isset($username) || empty($user['id'])) {
        die("Uzivatel neexistuje.");
    }
    
    $stmt = $conn->prepare("SELECT COUNT(*) AS pocet FROM posts WHERE user_id = ?");
    $stmt->bind_param("i", $user['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $pocetPrispevku = $row['pocet'];
    
    $stmt = $conn->prepare("SELECT COUNT(*) AS pocet FROM followers WHERE followed_id = ?");
    $stmt->bind_param("i", $user['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $pocetFolloweru = $row['pocet'];
    
    $stmt = $conn->prepare("SELECT COUNT(*) AS pocet FROM followers WHERE follower_id = ?");
    $stmt->bind_param("i", $user['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $pocetSledovanych = $row['pocet']
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <title>HOLOGRAM | Profil</title>
</head>
<body>
    <header>
        <a href="index.php" class="logo">Hologram</a>
        
        <nav>
            <div>
                <a href="index.php">Home</a>
                <a href="profile.php" id="checked">Profile</a>
                <a href="#">Messages</a>
            </div>
        </nav>
        
        <div>
            <a href="update-profile.php">Update profile</a>
            <a href="logout.php" style="color: rgb(200, 100, 100)">Log out</a>
        </div>
    </header>

<main>
    <div class="profil">
    <div class="hlavicka">
      <img src=<?php echo $user['pfp']?> alt="Profilová fotka">
      <div class="udaje">
        <h3><?php echo $user['name']?></h3>
        <p><?php echo $user['username']?></p>
        <p><?php echo $user['bio']?></p>
        
        <div class="staty">
            <p>Příspěvky: <?php echo $pocetPrispevku ?></p>
            <p>Sledující: <?php echo $pocetFolloweru ?></p>
            <p>Sleduje: <?php echo $pocetSledovanych ?></p>
        </div>
        
        <a href="follow.php?id=<?php echo $user['id']?>"><?php if($username != $_SESSION['username']){ echo 'Sledovat';}?></a>
      </div>
    </div>

    <div class="prispevky">
        <?php
            $stmt = $conn->prepare("SELECT * FROM posts WHERE user_id = ? ORDER BY date DESC");
            $stmt->bind_param("i", $user['id']);
            $stmt->execute();

            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                echo "Žádné příspěvky.";
            } else {
                while ($obrazek = $result->fetch_assoc()) {
                    echo '<img src="' . htmlspecialchars($obrazek['image']) . '" alt="obrazek"><br><br>';
                }
            }

            $stmt->close();
        ?>
    </div>

  </div>
</main>
  

</body>
</html>
