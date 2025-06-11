<?php
include 'connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}



if (!isset($_GET['feed']) || ($_GET['feed'] !== "following" && $_GET['feed'] !== "all")) {
    header("Location: ?feed=following");
    exit;
}

$feed = $_GET['feed'];

$id = $_SESSION['user_id'];
$username = $_SESSION['username'];

if($feed == "following"){
    $stmt = $conn->prepare("SELECT 
    posts.id,
    posts.image, 
    posts.date, 
    users.username, 
    posts.content, 
    users.pfp,
    users.id AS user_id,
    COUNT(likes.liked_post) AS like_count,
    SUM(CASE WHEN likes.liked_by = ? THEN 1 ELSE 0 END) > 0 AS is_liked,
    (SELECT COUNT(*) FROM comments WHERE post_id = posts.id) AS comment_count
    FROM posts
    JOIN followers ON posts.user_id = followers.followed_id
    JOIN users ON posts.user_id = users.id
    LEFT JOIN likes ON posts.id = likes.liked_post
    WHERE followers.follower_id = ?
    GROUP BY posts.id
    ORDER BY posts.date DESC
    LIMIT 15");

$stmt->bind_param("ii", $id, $id);

}else if($feed == "all"){
    $stmt = $conn->prepare("SELECT 
    posts.id,
    posts.image, 
    posts.date, 
    users.username, 
    posts.content, 
    users.pfp,
    users.id AS user_id,
    COUNT(likes.liked_post) AS like_count,
    SUM(CASE WHEN likes.liked_by = ? THEN 1 ELSE 0 END) > 0 AS is_liked,
    (SELECT COUNT(*) FROM comments WHERE post_id = posts.id) AS comment_count
    FROM posts
    JOIN users ON posts.user_id = users.id
    LEFT JOIN likes ON posts.id = likes.liked_post
    GROUP BY posts.id
    ORDER BY posts.date DESC
    LIMIT 15");

$stmt->bind_param("i", $id);


}

$stmt->execute();
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <title>HOLOGRAM | Home</title>
    <style>
        body {
            font-family: "Be Vietnam Pro", sans-serif;
            padding: 0;
            margin: 0;
            min-height: 100vh;
            color: white;
            background: linear-gradient(45deg, rgb(0, 0, 0) 0%, rgb(15, 15, 15) 29%, rgb(35, 35, 35) 100%);
        }

        header {
            height: 80px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            background: rgba(0, 0, 0, 0.9);
            z-index: 100;
            padding: 0 20px;
            box-sizing: border-box;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.3);
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: transparent;
            background-clip: text;
            background-image: linear-gradient(20deg, rgb(0, 153, 255), rgb(0, 193, 219));
            text-decoration: none;
            flex-shrink: 0;
        }

        .nav-container {
            display: flex;
            flex-grow: 1;
            justify-content: center;
            position: absolute;
            left: 0;
            width: 100%;
        }

        nav {
            display: flex;
            align-items: center;
            justify-content: end;
            gap: 10px;
            width: 100%;
            position: relative;
        }
        
        .links-container{
            width: 100%;
            position: absolute;
        }

        .main-links {
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        .main-links a {
            color: white;
            font-weight: 500;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 20px;
            transition: all 0.3s ease;
            font-size: 0.95rem;
            position: relative;
            overflow: hidden;
        }

        .main-links a::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(20deg, rgba(0, 153, 255, 0.2), rgba(0, 193, 219, 0.2));
            transform: scaleX(0);
            transform-origin: right;
            transition: transform 0.3s ease;
            z-index: -1;
        }

        .main-links a:hover::before {
            transform: scaleX(1);
            transform-origin: left;
        }

        #checked {
            background: linear-gradient(20deg, rgb(0, 153, 255), rgb(0, 193, 219));
            color: white;
            box-shadow: 0 0 15px rgba(0, 166, 255, 0.5);
        }

        .right-nav {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-left: auto;
        }

        .nav-divider {
            width: 1px;
            height: 30px;
            background: white;
            margin: 0 10px;
        }

        .search-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
            transition: all 0.3s ease;
            position: relative;
        }

        .search-link:hover {
            background: rgba(0, 153, 255, 0.2);
            transform: scale(1.1);
        }

        .search-link i {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid white;
            border-radius: 50%;
            position: relative;
        }

        .search-link i::after {
            content: "";
            position: absolute;
            width: 6px;
            height: 2px;
            background: white;
            right: -5px;
            bottom: -3px;
            transform: rotate(45deg);
        }

        .user-actions {
            display: flex;
            gap: 8px;
            z-index: 10;
            margin-right: 30px;
        }

        .user-actions a {
            color: white;
            font-weight: 500;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 20px;
            transition: all 0.3s ease;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .user-actions a:first-child {
            background: rgba(0, 153, 255, 0.1);
        }

        .user-actions a:first-child:hover {
            background: rgba(0, 153, 255, 0.2);
        }

        .user-actions a:last-child {
            background: rgba(200, 100, 100, 0.1);
            color: rgb(255, 150, 150);
        }

        .user-actions a:last-child:hover {
            background: rgba(200, 100, 100, 0.2);
        }

        .menu-toggle {
            display: none;
            background: none;
            border: none;
            cursor: pointer;
            width: 30px;
            height: 30px;
            position: relative;
            z-index: 101;
        }

        .menu-toggle span {
            display: block;
            width: 100%;
            height: 2px;
            background: white;
            position: absolute;
            left: 0;
            transition: all 0.3s ease;
        }

        .menu-toggle span:nth-child(1) {
            top: 8px;
        }

        .menu-toggle span:nth-child(2) {
            top: 14px;
        }

        .menu-toggle span:nth-child(3) {
            top: 20px;
        }

        .menu-toggle.active span:nth-child(1) {
            transform: rotate(45deg);
            top: 14px;
        }

        .menu-toggle.active span:nth-child(2) {
            opacity: 0;
        }

        .menu-toggle.active span:nth-child(3) {
            transform: rotate(-45deg);
            top: 14px;
        }

        main {
            width: 100%;
            max-width: 600px;
            margin: 100px auto 40px;
            padding: 0 20px;
            box-sizing: border-box;
        }

        h3 {
            text-align: center;
            font-size: 1.5rem;
            margin-bottom: 25px;
            font-weight: 600;
            background: linear-gradient(20deg, rgb(0, 153, 255), rgb(0, 193, 219));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .post {
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(50px);
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .post-header {
            display: flex;
            align-items: center;
            padding: 15px;
            gap: 10px;
        }

        .post-header img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(0, 193, 219, 0.3);
        }

        .post-header a {
            text-decoration: none;
            color: white;
            font-weight: 500;
        }
        
        .post-header a:hover {
            text-decoration: underline;
        }

        .post-content {
            width: 100%;
        }

        .post-content img {
            width: 100%;
            max-height: 600px;
            object-fit: contain;
            display: block;
        }

        .post-footer {
            padding: 15px;
        }

        .post-footer p {
            margin: 0;
            line-height: 1.5;
        }

        .post-date {
            font-size: 0.8rem;
            opacity: 0.7;
            margin-top: 10px;
            display: block;
        }
        
        .stats {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 12px;
        }

        .like-btn, .comment-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            color: white;
            transition: all 0.3s ease;
            padding: 5px 10px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.05);
        }

        .like-btn:hover {
            background: rgba(255, 0, 0, 0.1);
            transform: scale(1.05);
        }
        
        .comment-btn:hover {
            background: rgba(0, 153, 255, 0.1);
            transform: scale(1.05);
        }

        .like-btn .heart-icon, .comment-btn .comment-icon {
            transition: all 0.3s ease;
        }

        .like-btn:hover .heart-icon, .comment-btn:hover .comment-icon {
            transform: scale(1.2);
        }

        .like-count, .comment-count {
            font-weight: 500;
            font-size: 0.9rem;
        }
        
        .liked-animation {
            transform: scale(1.1);
            box-shadow: 0 0 15px rgba(255, 0, 0, 0.3);
        }
        
        .comments-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        
        .comments-container.active {
            opacity: 1;
            visibility: visible;
        }
            
        .comments-box {
            width: 100%;
            max-width: 500px;
            max-height: 80vh;
            background: rgba(20, 20, 20, 0.9);
            border-radius: 15px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: 0 0 30px rgba(0,0,0,0.5);
        }
        
        .comments-header {
            padding: 15px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .comments-header h4 {
            margin: 0;
            font-size: 1.1rem;
        }
        
        .close-comments {
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 5px;
        }
        
        .comments-list {
            flex: 1;
            overflow-y: auto;
            padding: 15px;
        }

        .comment {
            display: flex;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        .comment-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid rgba(0, 193, 219, 0.3);
        }

        .comment-content {
            flex: 1;
        }
        
        .comment-user {
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 5px;
        }
        
        .comment-user a {
            color: white;
            text-decoration: none;
        }
        
        .comment-user a:hover {
            text-decoration: underline;
        }
        
        .comment-text {
            font-size: 0.9rem;
            line-height: 1.4;
            word-break: break-word;
        }
        
        .comment-date {
            font-size: 0.7rem;
            opacity: 0.6;
            margin-top: 5px;
            display: block;
        }
        
        .comments-form {
            padding: 15px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        
        .comments-form form {
            display: flex;
            gap: 10px;
        }
        
        .comments-form textarea {
            flex: 1;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 20px;
            padding: 10px 15px;
            color: white;
            font-family: inherit;
            resize: none;
            height: 40px;
            max-height: 100px;
            outline: none;
            transition: all 0.3s ease;
        }
        
        .comments-form textarea:focus {
            border-color: rgba(0, 193, 219, 0.5);
            background: rgba(0, 193, 219, 0.05);
        }
        
        .comments-form button {
            background: linear-gradient(20deg, rgb(0, 153, 255), rgb(0, 193, 219));
            border: none;
            color: white;
            padding: 0 20px;
            border-radius: 20px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .comments-form button:hover {
            transform: scale(1.05);
            box-shadow: 0 0 15px rgba(0, 193, 219, 0.3);
        }
        
        .no-comments {
            text-align: center;
            padding: 30px;
            opacity: 0.7;
            font-size: 0.9rem;
        }

        @media (max-width: 1200px) {
            .main-links {
                gap: 8px;
            }
            
            .main-links a {
                padding: 8px 12px;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 1000px) {
            .main-links {
                gap: 6px;
            }
            
            .main-links a {
                padding: 8px 10px;
                font-size: 0.85rem;
            }
            
            .user-actions {
                gap: 6px;
            }
            
            .user-actions a {
                padding: 8px 10px;
                font-size: 0.85rem;
            }
        }

        @media (max-width: 900px) {
            .nav-container {
                justify-content: flex-end;
            }
            
            .links-container{
                position: relative;
            }
            
            .main-links, .right-nav {
                display: none;
            }
            
            .menu-toggle {
                display: block;
            }
            
            nav {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100vh;
                background: rgba(0, 0, 0, 0.97);
                flex-direction: column;
                justify-content: center;
                align-items: center;
                padding: 40px 20px;
                transform: translateY(-100%);
                transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                z-index: 100;
                gap: 30px;
            }
            
            nav.active {
                transform: translateY(0);
            }
            
            .main-links {
                display: flex;
                position: static;
                transform: none;
                flex-direction: column;
                gap: 20px;
                width: 100%;
                max-width: 320px;
                align-items: center;
                padding: 0;
                margin: 0 auto;
            }
            
            .main-links a {
                width: 100%;
                text-align: center;
                padding: 16px 20px;
                font-size: 1.1rem;
                background: rgba(255, 255, 255, 0.05);
                border-radius: 12px;
                transition: all 0.3s ease;
                border: 1px solid rgba(255, 255, 255, 0.1);
            }
            
            .main-links a:hover {
                background: rgba(255, 255, 255, 0.1);
                transform: translateY(-2px);
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            }
            
            #checked {
                background: linear-gradient(20deg, rgb(0, 153, 255), rgb(0, 193, 219)) !important;
                box-shadow: 0 4px 20px rgba(0, 166, 255, 0.4);
                border: 1px solid rgba(0, 166, 255, 0.3);
            }
            
            #checked:hover {
                background: linear-gradient(20deg, rgb(0, 153, 255), rgb(0, 193, 219)) !important;
                transform: translateY(-2px);
                box-shadow: 0 6px 25px rgba(0, 166, 255, 0.5);
            }
            
            .right-nav {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 25px;
                width: 100%;
                max-width: 320px;
                margin: 0 auto;
            }
            
            .nav-divider {
                display: block;
                width: 60px;
                height: 1px;
                background: rgba(255, 255, 255, 0.2);
                margin: 0 auto;
            }
            
            .search-link {
                display: flex;
                width: 60px;
                height: 60px;
                background: rgba(255, 255, 255, 0.05);
                border-radius: 50%;
                border: 1px solid rgba(255, 255, 255, 0.1);
                transition: all 0.3s ease;
                margin: 0 auto;
            }
            
            .search-link:hover {
                background: rgba(0, 153, 255, 0.2);
                transform: scale(1.05);
                box-shadow: 0 4px 20px rgba(0, 166, 255, 0.3);
            }
            
            .search-link i {
                width: 22px;
                height: 22px;
                border-width: 2.5px;
            }
            
            .search-link i::after {
                width: 8px;
                height: 2.5px;
            }
            
            .user-actions {
                display: flex;
                flex-direction: column;
                width: 100%;
                max-width: 320px;
                gap: 15px;
                padding: 0;
                margin: 0 auto;
            }
            
            .user-actions a {
                width: 100%;
                text-align: center;
                padding: 16px 20px;
                font-size: 1.1rem;
                justify-content: center;
                margin: 0;
                border-radius: 12px;
                transition: all 0.3s ease;
                border: 1px solid rgba(255, 255, 255, 0.1);
            }
            
            .user-actions a:first-child {
                background: rgba(0, 153, 255, 0.1);
                border-color: rgba(0, 153, 255, 0.2);
            }
            
            .user-actions a:first-child:hover {
                background: rgba(0, 153, 255, 0.2);
                transform: translateY(-2px);
                box-shadow: 0 4px 20px rgba(0, 153, 255, 0.3);
            }
            
            .user-actions a:last-child {
                background: rgba(200, 100, 100, 0.1);
                color: rgb(255, 150, 150);
                border-color: rgba(200, 100, 100, 0.2);
            }
            
            .user-actions a:last-child:hover {
                background: rgba(200, 100, 100, 0.2);
                transform: translateY(-2px);
                box-shadow: 0 4px 20px rgba(200, 100, 100, 0.3);
            }
            
            main {
                margin-top: 90px;
            }
            
            .comments-box {
                max-width: 90%;
                max-height: 85vh;
            }
        }

        @media (max-width: 480px) {
            header {
                padding: 0 15px;
                height: 70px;
            }
            
            .logo {
                font-size: 1.6rem;
            }
            
            .main-links a,
            .user-actions a {
                max-width: 100%;
                padding: 12px;
            }
            
            .stats {
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <header>
        <a href="index.php" class="logo">Hologram</a>
        
        <div class="nav-container">
            <nav>
                <div class="links-container">
                    <div class="main-links">
                        <a href="#" id="checked">Home</a>
                        <a href="post.php">Post</a>
                        <a href="profile.php?user=<?php echo $username?>">Profile</a>
                        <a href="messages.php">Messages</a>
                    </div>
                </div>
                
                
                <div class="right-nav">
                    <div class="nav-divider"></div>
                    
                    <a href="search.php" class="search-link" title="Search">
                        <i></i>
                    </a>
                    
                    <div class="user-actions">
                        <a href="update-profile.php">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                            Update
                        </a>
                        <a href="logout.php">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                <polyline points="16 17 21 12 16 7"></polyline>
                                <line x1="21" y1="12" x2="9" y2="12"></line>
                            </svg>
                            Logout
                        </a>
                    </div>
                </div>
            </nav>
        </div>
        
        <button class="menu-toggle">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </header>

    <main>
        
        <a href="?feed=following">Lidé co sleduji</a>
        <a href="?feed=all">Doporučené příspěvky</a>

        <h3><?php if($feed == "following") echo "Nejnovější příspěvky od lidí co sledujete"; else echo "Nejnovější příspěvky od lidí";?></h3>
        <?php 
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                echo '<div style="text-align: center; padding: 30px; background: rgba(0,0,0,0.2); border-radius: 15px;">Zatím žádné příspěvky k zobrazení. Začněte sledovat více lidí!</div>';
            } else {
                while ($row = $result->fetch_assoc()) {
                    $post_date = date('d.m.Y H:i', strtotime($row['date']));
                    
                    // Get comments for this post
                    $comments_stmt = $conn->prepare("SELECT 
                        comments.id, 
                        comments.content, 
                        comments.date, 
                        users.username, 
                        users.pfp 
                        FROM comments 
                        JOIN users ON comments.user_id = users.id 
                        WHERE comments.post_id = ? 
                        ORDER BY comments.date DESC");
                    $comments_stmt->bind_param("i", $row['id']);
                    $comments_stmt->execute();
                    $comments_result = $comments_stmt->get_result();
                    $comments = $comments_result->fetch_all(MYSQLI_ASSOC);
        ?>
                <div class="post" id="<?php echo $row['id']?>">
                    <div class="post-header">
                        <img src="<?php echo htmlspecialchars($row['pfp']);?>" alt="Profilová fotka">
                        <a href="profile.php?user=<?php echo htmlspecialchars($row['username']);?>"><?php echo htmlspecialchars($row['username']);?></a>
                    </div>
                    <div class="post-content">
                        <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="Příspěvek">
                    </div>
                    <div class="post-footer">
                        <div class="stats">
    <a href="like.php?id=<?php echo $row['id']?>" class="like-btn">
        <svg class="heart-icon" width="24" height="24" viewBox="0 0 24 24" fill="<?php echo $row['is_liked'] ? 'red' : 'none'; ?>" stroke="currentColor" stroke-width="2">
            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
        </svg>
        <span class="like-count"><?php echo $row['like_count']; ?></span>
    </a>
    <a href="#" onclick="openComments(<?php echo $row['id'] ?>); return false;" class="comment-btn">
        <svg class="comment-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
        </svg>
        <span class="comment-count"><?php echo $row['comment_count']; ?></span>
    </a>
</div>
                        <p><?php echo htmlspecialchars($row['content']); ?></p>
                        <span class="post-date"><?php echo $post_date; ?></span>
                    </div>
                </div>
                
                <!-- Comments modal for this post -->
                <div class="comments-container" id="comments-<?php echo $row['id'] ?>">
                    <div class="comments-box">
                        <div class="comments-header">
                            <h4>Komentáře</h4>
                            <button class="close-comments" onclick="closeComments(<?php echo $row['id'] ?>)">×</button>
                        </div>
                        <div class="comments-list">
                            <?php if(empty($comments)): ?>
                                <div class="no-comments">Zatím žádné komentáře</div>
                            <?php else: ?>
                                <?php foreach($comments as $comment): ?>
                                    <div class="comment">
                                        <img src="<?php echo htmlspecialchars($comment['pfp']) ?>" alt="Profilová fotka" class="comment-avatar">
                                        <div class="comment-content">
                                            <div class="comment-user">
                                                <a href="profile.php?user=<?php echo htmlspecialchars($comment['username']) ?>"><?php echo htmlspecialchars($comment['username']) ?></a>
                                            </div>
                                            <div class="comment-text"><?php echo htmlspecialchars($comment['content']) ?></div>
                                            <span class="comment-date"><?php echo date('d.m.Y H:i', strtotime($comment['date'])) ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <div class="comments-form">
                            <form action="add_comment.php" method="post">
                                <input type="hidden" name="post_id" value="<?php echo $row['id'] ?>">
                                <textarea name="comment" placeholder="Napište komentář..." required></textarea>
                                <button type="submit">Odeslat</button>
                            </form>
                        </div>
                    </div>
                </div>
        <?php
                }
            }
        ?>
    </main>

    <script>
        // Mobile menu toggle with animation
        const menuToggle = document.querySelector('.menu-toggle');
        const nav = document.querySelector('nav');
        
        menuToggle.addEventListener('click', () => {
            menuToggle.classList.toggle('active');
            nav.classList.toggle('active');
            
            // Prevent scrolling when menu is open
            if (nav.classList.contains('active')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        });
        
        // Close menu when clicking on a link
        document.querySelectorAll('nav a').forEach(link => {
            link.addEventListener('click', () => {
                menuToggle.classList.remove('active');
                nav.classList.remove('active');
                document.body.style.overflow = '';
            });
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!nav.contains(e.target) && !menuToggle.contains(e.target) && nav.classList.contains('active')) {
                menuToggle.classList.remove('active');
                nav.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
        
        // AJAX implementation for smoother like toggling
        document.querySelectorAll('.like-btn').forEach(btn => {
            btn.addEventListener('click', async function(e) {
                e.preventDefault();
                const postId = this.closest('.post').id;
                
                try {
                    const response = await fetch(this.href);
                    const data = await response.json();
                    
                    // Update like button appearance
                    const heartIcon = this.querySelector('.heart-icon');
                    const likeCount = this.querySelector('.like-count');
                    
                    heartIcon.setAttribute('fill', data.liked ? 'red' : 'none');
                    likeCount.textContent = data.like_count;
                    
                    // Add animation
                    this.classList.add('liked-animation');
                    setTimeout(() => {
                        this.classList.remove('liked-animation');
                    }, 500);
                    
                } catch (error) {
                    console.error('Error:', error);
                    // Fallback to normal link behavior if AJAX fails
                    window.location.href = this.href;
                }
            });
        });
        
        // Comments functionality
        function openComments(postId) {
            event.preventDefault();
            const commentsContainer = document.getElementById(`comments-${postId}`);
            commentsContainer.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        
        function closeComments(postId) {
            const commentsContainer = document.getElementById(`comments-${postId}`);
            commentsContainer.classList.remove('active');
            document.body.style.overflow = '';
        }
        
        // Close comments when clicking outside
        document.querySelectorAll('.comments-container').forEach(container => {
            container.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.remove('active');
                    document.body.style.overflow = '';
                }
            });
        });
        
        // AJAX for adding comments
        document.querySelectorAll('.comments-form form').forEach(form => {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const postId = formData.get('post_id');
                const commentText = formData.get('comment');
                
                try {
                    const response = await fetch(this.action, {
                        method: 'POST',
                        body: formData
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        // Clear textarea
                        this.querySelector('textarea').value = '';
                        
                        // Add new comment to the list
                        const commentsList = this.closest('.comments-box').querySelector('.comments-list');
                        
                        // Remove "no comments" message if it exists
                        const noComments = commentsList.querySelector('.no-comments');
                        if (noComments) {
                            noComments.remove();
                        }
                        
                        // Create new comment element
                        const commentDiv = document.createElement('div');
                        commentDiv.className = 'comment';
                        commentDiv.innerHTML = `
                            <img src="<?php echo htmlspecialchars($_SESSION['pfp'] ?? 'default_pfp.jpg'); ?>" alt="Profilová fotka" class="comment-avatar">
                            <div class="comment-content">
                                <div class="comment-user">
                                    <a href="profile.php?user=<?php echo htmlspecialchars($username); ?>"><?php echo htmlspecialchars($username); ?></a>
                                </div>
                                <div class="comment-text">${commentText}</div>
                                <span class="comment-date">právě teď</span>
                            </div>
                        `;
                        
                        // Add to top of comments list
                        commentsList.insertBefore(commentDiv, commentsList.firstChild);
                        
                        // Update comment count
                        const commentBtn = document.querySelector(`#${postId} .comment-count`);
                        if (commentBtn) {
                            commentBtn.textContent = parseInt(commentBtn.textContent) + 1;
                        }
                    }
                } catch (error) {
                    console.error('Error:', error);
                }
            });
        });
    </script>
</body>
</html>
