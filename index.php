<?php
    include 'connect.php';
    session_start();

    if (!isset($_SESSION['user_id'])) {
    
        header("Location: login.php");
        exit;
    }
    
    $id = $_SESSION['user_id'];
    
    $stmt = $conn->prepare
    ("SELECT 
    posts.image, 
    posts.date, 
    users.username, 
    posts.content, 
    users.pfp,
    users.id
    FROM posts
    JOIN followers ON posts.user_id = followers.followed_id
    JOIN users ON posts.user_id = users.id
    WHERE followers.follower_id = ?
    ORDER BY posts.date DESC
    LIMIT 15");
    
    $stmt->bind_param("i", $id);
    $stmt->execute();
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
    <title>HOLOGRAM | Home</title>
</head>
<body>
    <header>
        <a href="index.php" class="logo">Hologram</a>
        
        <nav>
            <div>
                <a href="#" id="checked">Home</a>
                <a href="post.php">Post</a>
                <a href="profile.php?id=<?php echo $id?>">Profile</a>
                <a href="#">Messages</a>
            </div>
        </nav>
        
        <div>
            <a href="update-profile.php">Update profile</a>
            <a href="logout.php" style="color: rgb(200, 100, 100)">Log out</a>
        </div>
    </header>

    

    <main>
        <h3>Nejnovější příspěvky od lidí co sledujete</h3>
        <?php 
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                ?>
                <div class="post">
            <div class="header">
                <img src="<?php echo htmlspecialchars($row['pfp']);?>" alt="pfp">
                <a href="profile.php?id=<?php echo htmlspecialchars($row['id']);?>"><?php echo htmlspecialchars($row['username']);?></a>
            </div>
            <div class="content">
                <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="post">
            </div>
            <div class="footer">
                <p>
                    <?php echo htmlspecialchars($row['content']); ?>
                </p>
            </div>
        </div>
        <?php
            }
        ?>
        
    </main>
</body>
</html>
