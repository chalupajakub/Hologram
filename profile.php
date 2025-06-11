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
    
    $checkStmt = $conn->prepare("SELECT 1 FROM followers WHERE follower_id = ? AND followed_id = ?");
    $checkStmt->bind_param("ii", $_SESSION['user_id'], $user['id']);
    $checkStmt->execute();
    $checkStmt->store_result();
    
    $nesleduje = $checkStmt->num_rows === 0;
    
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
    $pocetSledovanych = $row['pocet'];
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <title>HOLOGRAM | Profil</title>
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

        /* Updated Navigation Styles for Perfect Centering */
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

        /* Modern Search Icon */
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

        /* User Actions */
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
            max-width: 1000px;
            height: 100%;
            margin-top: 100px;
            margin-inline: auto;
            padding-bottom: 100px;
            padding: 0 20px;
            box-sizing: border-box;
        }

        .profil {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 2rem;
        }

        .hlavicka {
            display: flex;
            align-items: center;
            gap: 50px;
            width: 100%;
            max-width: 800px;
            margin-bottom: 70px;
        }

        .hlavicka img {
            width: 200px;
            height: 200px;
            object-fit: cover;
            border-radius: 50%;
            border: 5px solid rgba(0, 193, 219, 0.3);
        }

        .udaje {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .udaje h3 {
            font-size: 2rem;
            margin: 0;
        }

        .udaje p {
            margin: 0;
            opacity: 0.8;
        }

        .staty {
            display: flex;
            gap: 20px;
            margin-top: 15px;
        }

        .staty p {
            font-weight: 600;
            cursor: pointer;
            transition: color 0.2s;
        }

        .staty p:hover {
            color: rgb(0, 193, 219);
        }

        .prispevky {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            width: 100%;
            max-width: 900px;
        }

        .prispevky img {
            width: 100%;
            aspect-ratio: 1/1;
            object-fit: cover;
            border-radius: 5px;
            transition: transform 0.3s ease;
            cursor: pointer;
        }

        .prispevky img:hover {
            transform: scale(1.02);
            box-shadow: 0 0 20px rgba(0, 166, 255, 0.3);
        }

        button, .button-like {
            padding: 10px 20px;
            font-family: "Be Vietnam Pro", sans-serif;
            font-size: 1rem;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            border: none;
            background: linear-gradient(20deg, rgb(0, 153, 255), rgb(0, 193, 219));
            color: white;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        button:hover, .button-like:hover {
            filter: brightness(1.2);
            box-shadow: 0 0 20px rgb(0, 166, 255);
        }
        
        .btn-secondary {
            background: transparent;
            border: 1px solid white;
            transition: all 0.2s ease;
        }
        
        .btn-secondary:hover {
            background: white;
            color: black;
            text-decoration: none;
            box-shadow: none;
        }

        .off {
            display: none !important;
        }
        
        .nahled-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            z-index: 1000;
            background: rgba(0, 0, 0, 0.95);
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(10px);
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        .nahled-container.show {
            opacity: 1;
            visibility: visible;
        }

        .nahled {
            position: relative;
            max-width: 90%;
            max-height: 90%;
            width: auto;
            height: auto;
            display: flex;
            background: rgba(0, 0, 0, 0.7);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 0 50px rgba(0, 166, 255, 0.3);
            transform: scale(0.8);
            transition: transform 0.4s ease;
        }

        .nahled-container.show .nahled {
            transform: scale(1);
        }

        .nahled-image {
            max-width: 60%;
            width: auto;
            height: auto;
            max-height: 80vh;
            object-fit: contain;
        }

        .nahled-content {
            flex: 1;
            padding: 30px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-width: 300px;
            max-width: 400px;
        }

        .nahled-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .nahled-header img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(0, 193, 219, 0.5);
        }

        .nahled-user-info h4 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .nahled-user-info p {
            margin: 0;
            opacity: 0.7;
            font-size: 0.9rem;
        }

        .nahled-description {
            flex-grow: 1;
            margin: 20px 0;
        }

        .nahled-description p {
            margin: 0;
            line-height: 1.6;
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.9);
        }

        .nahled-actions {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .close-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(0, 0, 0, 0.7);
            border: none;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            transition: background 0.2s;
            z-index: 1001;
        }

        .close-btn:hover {
            background: rgba(255, 0, 0, 0.7);
        }

        .delete-btn {
            background: linear-gradient(135deg, #ff4757, #ff3742);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .delete-btn:hover {
            filter: brightness(1.1);
            box-shadow: 0 0 15px rgba(255, 71, 87, 0.5);
        }

        /* Followers/Following Modal Styles */
        .follow-modal-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            z-index: 1000;
            background: rgba(0, 0, 0, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(10px);
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        .follow-modal-container.show {
            opacity: 1;
            visibility: visible;
        }

        .follow-modal {
            position: relative;
            width: 100%;
            max-width: 400px;
            max-height: 70vh;
            background: rgba(30, 30, 30, 0.95);
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 0 30px rgba(0, 166, 255, 0.2);
            overflow: hidden;
        }

        .follow-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .follow-modal-header h3 {
            margin: 0;
            font-size: 1.3rem;
        }

        .follow-modal-content {
            overflow-y: auto;
            max-height: 60vh;
            padding-right: 10px;
        }

        .follow-user {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 12px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            transition: background 0.2s;
        }

        .follow-user:hover {
            background: rgba(255, 255, 255, 0.03);
        }

        .follow-user img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(0, 193, 219, 0.3);
        }

        .follow-user-info {
            flex-grow: 1;
        }

        .follow-user-info h4 {
            margin: 0;
            font-size: 1rem;
        }

        .follow-user-info p {
            margin: 0;
            opacity: 0.7;
            font-size: 0.9rem;
        }

        .follow-user-action {
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
        }

        .follow-btn {
            background: linear-gradient(20deg, rgb(0, 153, 255), rgb(0, 193, 219));
            color: white;
        }

        .follow-btn:hover {
            filter: brightness(1.1);
            box-shadow: 0 0 10px rgba(0, 166, 255, 0.3);
        }

        .following-btn {
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
        }

        .following-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.5);
        }

        .profile-link {
            color: white;
            text-decoration: none;
            transition: color 0.2s;
        }

        .profile-link:hover {
            color: rgb(0, 193, 219);
            text-decoration: underline;
        }

        .empty-message {
            text-align: center;
            padding: 30px 0;
            opacity: 0.7;
        }
        
        /* Comments styles */
.comments-container {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s ease;
}

.comments-container.active {
    opacity: 1;
    pointer-events: all;
}

.comments-box {
    background: white;
    border-radius: 10px;
    width: 90%;
    max-width: 500px;
    max-height: 80vh;
    display: flex;
    flex-direction: column;
}

.comments-header {
    padding: 15px;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.comments-header h4 {
    margin: 0;
}

.close-comments {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    padding: 0;
    line-height: 1;
}

.comments-list {
    padding: 15px;
    overflow-y: auto;
    flex-grow: 1;
}

.no-comments {
    text-align: center;
    color: #999;
    padding: 20px 0;
}

.comment {
    display: flex;
    margin-bottom: 15px;
}

.comment-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    margin-right: 10px;
    object-fit: cover;
}

.comment-content {
    flex-grow: 1;
}

.comment-user {
    font-weight: bold;
    margin-bottom: 5px;
}

.comment-user a {
    color: inherit;
    text-decoration: none;
}

.comment-text {
    margin-bottom: 5px;
}

.comment-date {
    font-size: 12px;
    color: #999;
}

.comments-form {
    padding: 15px;
    border-top: 1px solid #eee;
}

.comments-form textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    resize: none;
    margin-bottom: 10px;
    min-height: 60px;
}

.comments-form button {
    background: #1a73e8;
    color: white;
    border: none;
    padding: 8px 15px;
    border-radius: 5px;
    cursor: pointer;
}

.comments-form button:hover {
    background: #0d5bba;
}

/* Stats styles */
.stats {
    display: flex;
    gap: 15px;
    padding: 10px 0;
    border-top: 1px solid #eee;
    border-bottom: 1px solid #eee;
    margin: 10px 0;
}

.like-btn, .comment-btn {
    display: flex;
    align-items: center;
    gap: 5px;
    text-decoration: none;
    color: inherit;
    transition: all 0.2s ease;
}

.like-btn:hover, .comment-btn:hover {
    opacity: 0.8;
}

.comment-btn svg {
    stroke: currentColor;
}

        /* Responsive styles with better main-links centering */
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

            .hlavicka {
                flex-direction: column;
                text-align: center;
                gap: 30px;
            }

            .staty {
                justify-content: center;
            }

            .prispevky {
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
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

            .hlavicka img {
                width: 150px;
                height: 150px;
            }

            .udaje h3 {
                font-size: 1.5rem;
            }

            .staty {
                flex-direction: column;
                gap: 10px;
            }

            .prispevky {
                grid-template-columns: 1fr;
            }

            .nahled-content {
                padding: 15px;
            }

            .nahled-header {
                gap: 10px;
            }

            .nahled-header img {
                width: 40px;
                height: 40px;
            }
            
            .comments{
                border: 1px solid white;
                width: 100%;
                height: 100%;
            }
        }
    </style>
<body>
    <header>
        <a href="index.php" class="logo">Hologram</a>
        
        <div class="nav-container">
            <nav>
                <div class="links-container">
                    <div class="main-links">
                        <a href="index.php">Home</a>
                        <a href="post.php">Post</a>
                        <a href="profile.php?user=<?php echo $_SESSION['username']?>" id="checked">Profile</a>
                        <a href="#">Messages</a>
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
        <div class="profil">
            <div class="hlavicka">
                <img src="<?php echo $user['pfp']?>" alt="Profilová fotka">
                <div class="udaje">
                    <h3><?php echo htmlspecialchars($user['name'])?></h3>
                    <p>@<?php echo htmlspecialchars($user['username'])?></p>
                    <p><?php echo htmlspecialchars($user['bio'])?></p>
                    
                    <div class="staty">
                        <p>Příspěvky: <?php echo $pocetPrispevku ?></p>
                        <p onclick="showFollowers('<?php echo $user['id']?>')">Sledující: <?php echo $pocetFolloweru ?></p>
                        <p onclick="showFollowing('<?php echo $user['id']?>')">Sleduje: <?php echo $pocetSledovanych ?></p>
                    </div>
                    
                    <?php if($username != $_SESSION['username']): ?>
                        <a href="follow.php?user=<?php echo $user['username']?>" class="button-like <?php if(!$nesleduje) echo "btn-secondary"; ?>"><?php if($nesleduje) echo "Sledovat"; else echo "Zrušit sledování"; ?></a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="prispevky">
                <?php
                    $stmt = $conn->prepare("SELECT posts.*, 
                                           (SELECT COUNT(*) FROM likes WHERE liked_post = posts.id) AS like_count,
                                           EXISTS(SELECT 1 FROM likes WHERE liked_post = posts.id AND user_id = ?) AS liked
                                           FROM posts 
                                           WHERE user_id = ? 
                                           ORDER BY date DESC");
                    $stmt->bind_param("ii", $user['id'], $user['id']);
                    $stmt->execute();

                    $result = $stmt->get_result();
                    
                    $post;

                    if ($result->num_rows === 0) {
                        echo "<p style='grid-column: 1 / -1; text-align: center;'>Žádné příspěvky.</p>";
                    } else {
                        while ($post = $result->fetch_assoc()) {
                                echo '<img src="' . htmlspecialchars($post['image']) . '" 
                                alt="obrazek" 
                                data-post-id="' . $post['id'] . '" 
                                data-content="' . htmlspecialchars($post['content']) . '" 
                                data-date="' . $post['date'] . '"
                                data-liked="' . ($post['liked'] ? '1' : '0') . '"
                                data-like-count="' . $post['like_count'] . '">';
                        }
                    }

                    $stmt->close();
                ?>
            </div>
        </div>
    </main>
    
    <div class="nahled-container off">
        <?php $post = $result->fetch_assoc(); ?>
        
        <div class="nahled">
            <button class="close-btn" onclick="ClosePreview()">&times;</button>
            <img src="" alt="nahled" id="nahled-image" class="nahled-image">
            <div class="nahled-content">
                <div class="nahled-header">
                    <img src="<?php echo $user['pfp']?>" alt="Profilová fotka">
                    <div class="nahled-user-info">
                        <h4><?php echo htmlspecialchars($user['name'])?></h4>
                        <p>@<?php echo htmlspecialchars($user['username'])?></p>
                    </div>
                </div>
                
                <div class="nahled-description">
                    <p id="nahled-content"></p>
                </div>
                
                <div class="comments">
                    
                </div>
                
                <div class="stats">
    <a id="like-button" href="#" class="like-btn" data-post-id="">
        <svg class="heart-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 
                     2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 
                     4.5 2.09C13.09 3.81 14.76 3 
                     16.5 3 19.58 3 22 5.42 
                     22 8.5c0 3.78-3.4 6.86-8.55 
                     11.54L12 21.35z"/>
        </svg>
        <span class="like-count">0</span>
    </a>
    
    <a href="#" class="comment-btn" id="comment-button" data-post-id="">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
        </svg>
        <span class="comment-count">0</span>
    </a>
</div>

                
                <div class="nahled-actions">
                    <?php if($user['username'] == $_SESSION['username']): ?>
                        <button class="delete-btn" onclick="DeletePost()">Smazat příspěvek</button>
                    <?php endif; ?>
                </div>
                
                <div class="nahled-date">
                    <small id="nahled-date" style="opacity: 0.6;"></small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Followers/Following Modal -->
    <div class="follow-modal-container off">
        <div class="follow-modal">
            <div class="follow-modal-header">
                <h3 id="follow-modal-title">Sledující</h3>
                <button class="close-btn" onclick="CloseFollowModal()">&times;</button>
            </div>
            <div class="follow-modal-content" id="follow-modal-content">
                <!-- Users will be loaded here -->
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
    const likeBtn = document.getElementById('like-button');

    if (likeBtn) {
        likeBtn.addEventListener('click', async function (e) {
            e.preventDefault();

            const postId = this.getAttribute('data-post-id');
            const url = this.getAttribute('href');

            try {
                const response = await fetch(url);
                const data = await response.json();

                // Update srdíčko a počet
                const heartIcon = this.querySelector('.heart-icon');
                const likeCount = this.querySelector('.like-count');

                heartIcon.setAttribute('fill', data.liked ? 'red' : 'none');
                likeCount.textContent = data.like_count;

                // Animace
                this.classList.add('liked-animation');
                setTimeout(() => {
                    this.classList.remove('liked-animation');
                }, 500);
            } catch (error) {
                console.error('Chyba při lajkování:', error);
                window.location.href = url; // fallback
            }
        });
    }
});
    
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

        // Enhanced post preview functionality
        var currentPostId = null;
        var postGrid = document.querySelector(".prispevky");
        var previewContainer = document.querySelector(".nahled-container");
        var previewImage = document.querySelector("#nahled-image");
        var previewContent = document.querySelector("#nahled-content");
        var previewDate = document.querySelector("#nahled-date");

        postGrid.addEventListener("click", (e) => {
            if (e.target.tagName === 'IMG') {
                const imageSrc = e.target.src;
                const postContent = e.target.getAttribute('data-content');
                const postDate = e.target.getAttribute('data-date');
                const postId = e.target.getAttribute('data-post-id');
                
                const isLiked = e.target.getAttribute('data-liked') === '1';
                const likeCount = e.target.getAttribute('data-like-count');

                const heartIcon = document.querySelector('#like-button .heart-icon');
                const likeCountSpan = document.querySelector('#like-button .like-count');
                
                heartIcon.setAttribute('fill', isLiked ? 'red' : 'none');
                likeCountSpan.textContent = likeCount;

                
                // Nastavit href pro tlačítko like podle postId
                const likeButton = document.getElementById('like-button');
                    if (likeButton) {
                        likeButton.href = `like.php?id=${postId}`;
                }

                
                currentPostId = postId;
                
                // Set the image
                previewImage.src = imageSrc;
                
                // Set the content
                previewContent.textContent = postContent || 'Bez popisu';
                
                // Format and set the date
                const date = new Date(postDate);
                const formattedDate = date.toLocaleDateString('cs-CZ', {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
                previewDate.textContent = formattedDate;
                
                // Show the preview
                previewContainer.classList.remove("off");
                previewContainer.classList.add("show");
                document.body.style.overflow = 'hidden';
            }
        });
        
        // Close preview when clicking on the background
        previewContainer.addEventListener("click", (e) => {
            if (e.target === previewContainer) {
                ClosePreview();
            }
        });

        // Close preview with escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && previewContainer.classList.contains('show')) {
                ClosePreview();
            }
        });
        
        function ClosePreview() {
            previewContainer.classList.remove("show");
            previewContainer.classList.add("off");
            document.body.style.overflow = '';
            currentPostId = null;
        }
        
        function DeletePost() {
            if (currentPostId && confirm("Opravdu chcete smazat tento příspěvek?")) {
                
                window.open("delete.php?id=" + currentPostId);
                /*fetch('delete.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `post_id=${currentPostId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const imageToDelete = document.querySelector(`img[data-post-id="${currentPostId}"]`);
                        if (imageToDelete) {
                            imageToDelete.remove();
                        }
                        ClosePreview();
                    } else {
                        alert("Chyba při mazání příspěvku: " + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert("Došlo k chybě při mazání příspěvku");
                });*/
            }
        }
        
        // Followers/Following Modal Functions
        const followModal = document.querySelector('.follow-modal-container');
        const followModalTitle = document.getElementById('follow-modal-title');
        const followModalContent = document.getElementById('follow-modal-content');
        
        async function showFollowers(userId) {
            try {
                const response = await fetch(`get_followers.php?user_id=${userId}`);
                const data = await response.json();
                
                followModalTitle.textContent = 'Sledující';
                await renderUsersList(data, userId, 'followers');
                followModal.classList.remove('off');
                followModal.classList.add('show');
                document.body.style.overflow = 'hidden';
            } catch (error) {
                console.error('Error:', error);
                alert('Nepodařilo se načíst seznam sledujících');
            }
        }
        
        async function showFollowing(userId) {
            try {
                const response = await fetch(`get_following.php?user_id=${userId}`);
                const data = await response.json();
                
                followModalTitle.textContent = 'Sleduje';
                await renderUsersList(data, userId, 'following');
                followModal.classList.remove('off');
                followModal.classList.add('show');
                document.body.style.overflow = 'hidden';
            } catch (error) {
                console.error('Error:', error);
                alert('Nepodařilo se načíst seznam sledovaných');
            }
        }
        
        async function renderUsersList(users, profileUserId, type) {
            if (users.length === 0) {
                followModalContent.innerHTML = '<p class="empty-message">Žádní uživatelé</p>';
                return;
            }
            
            // Get current user's following list
            const currentUserId = <?php echo $_SESSION['user_id'] ?? 0; ?>;
            let followingList = [];
            
            if (currentUserId > 0) {
                try {
                    const response = await fetch(`get_following.php?user_id=${currentUserId}`);
                    followingList = await response.json();
                } catch (error) {
                    console.error('Error fetching following list:', error);
                }
            }
            
            let html = '';
            users.forEach(user => {
                const isFollowing = followingList.some(u => u.id == user.id);
                const isCurrentUser = user.id == currentUserId;
                
                html += `
                    <div class="follow-user">
                        <a href="profile.php?user=${user.username}" class="profile-link">
                            <img src="${user.pfp}" alt="Profilová fotka">
                        </a>
                        <div class="follow-user-info">
                            <a href="profile.php?user=${user.username}" class="profile-link">
                                <h4>${user.name}</h4>
                                <p>@${user.username}</p>
                            </a>
                        </div>
                        ${!isCurrentUser && type === 'followers' ? 
                            `<button class="follow-user-action ${isFollowing ? 'following-btn' : 'follow-btn'}" 
                                onclick="toggleFollow(${user.id}, this, ${profileUserId})">
                                ${isFollowing ? 'Sledováno' : 'Sledovat'}
                            </button>` : ''
                        }
                    </div>
                `;
            });
            
            followModalContent.innerHTML = html;
        }
        
        async function toggleFollow(userId, button, profileUserId) {
            const isFollowing = button.classList.contains('following-btn');
            
            try {
                const response = await fetch('follow.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `user_id=${userId}&action=${isFollowing ? 'unfollow' : 'follow'}`
                });
                
                const data = await response.json();
                
                if (data.success) {
                    if (isFollowing) {
                        button.classList.remove('following-btn');
                        button.classList.add('follow-btn');
                        button.textContent = 'Sledovat';
                    } else {
                        button.classList.remove('follow-btn');
                        button.classList.add('following-btn');
                        button.textContent = 'Sledováno';
                    }
                    
                    // Update the follower count if we're on the user's profile
                    if (profileUserId == <?php echo $user['id'] ?? 0; ?>) {
                        updateFollowerCount();
                    }
                } else {
                    alert(data.message || 'Došlo k chybě');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Došlo k chybě při provádění akce');
            }
        }
        
        function updateFollowerCount() {
            // This would update the follower count on the page without refresh
            // You would need to implement this based on your specific needs
            location.reload(); // Simple reload for now
        }
        
        function CloseFollowModal() {
            followModal.classList.remove('show');
            followModal.classList.add('off');
            document.body.style.overflow = '';
        }
        
        // Close follow modal when clicking on the background
        followModal.addEventListener('click', (e) => {
            if (e.target === followModal) {
                CloseFollowModal();
            }
        });
        
        // Close follow modal with escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && followModal.classList.contains('show')) {
                CloseFollowModal();
            }
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
    </script>
</body>
