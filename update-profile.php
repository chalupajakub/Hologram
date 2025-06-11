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
    $username = trim($_POST['username']);
    $name = trim($_POST['name']);
    $bio = trim($_POST['bio']);
    
    if (empty($username)) {
        $error = "Uživatelské jméno nesmí být prázdné.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
        $error = "Uživatelské jméno může obsahovat pouze písmena, čísla a podtržítka (3-20 znaků).";
    }
    
    if (empty($name)) {
        $error = "Jméno nesmí být prázdné.";
    } elseif (strlen($name) > 50) {
        $error = "Jméno je příliš dlouhé.";
    }
    
    if (strlen($bio) > 500) {
        $error = "Bio je příliš dlouhé (maximálně 500 znaků).";
    }
    
    if (!isset($error)) {
        $check_stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $check_stmt->bind_param("si", $username, $user_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $error = "Uživatelské jméno '$username' je již zabrané. Zvolte prosím jiné.";
        } else {
            $pfp_path = $user['pfp']; 
            
            if (!empty($_FILES['pfp']['name'])) {
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
                $max_size = 2 * 1024 * 1024;
                
                if (in_array($_FILES['pfp']['type'], $allowed_types) && $_FILES['pfp']['size'] <= $max_size) {
                    $target_dir = "uploads/";
                    $safe_filename = preg_replace('/[^a-zA-Z0-9_-]/', '', $_SESSION['username']);
                    $extension = pathinfo($_FILES['pfp']['name'], PATHINFO_EXTENSION);
                    $target_file = $target_dir . $safe_filename . "_pfp." . $extension;
                    
                    if (move_uploaded_file($_FILES["pfp"]["tmp_name"], $target_file)) {
                        $pfp_path = $target_file;
                    } else {
                        $error = "Chyba při nahrávání obrázku.";
                    }
                } else {
                    $error = "Nepodporovaný typ souboru nebo příliš velký soubor (max. 2MB).";
                }
            }
            
            if (!isset($error)) {
                $update_stmt = $conn->prepare("UPDATE users SET username = ?, name = ?, pfp = ?, bio = ? WHERE id = ?");
                $update_stmt->bind_param("ssssi", $username, $name, $pfp_path, $bio, $user_id);
                
                if ($update_stmt->execute()) {
                    if ($_SESSION['username'] !== $username) {
                        $_SESSION['username'] = $username;
                    }
                    
                    header("Location: profile.php?user=" . urlencode($username));
                    exit;
                } else {
                    $error = "Chyba při aktualizaci profilu.";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <title>HOLOGRAM | Upravit profil</title>
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
            max-width: 600px;
            height: 100%;
            margin: 100px auto 40px;
            padding: 0 20px;
            box-sizing: border-box;
        }

        h2 {
            text-align: center;
            font-size: 1.8rem;
            margin-bottom: 25px;
            font-weight: 600;
            background: linear-gradient(20deg, rgb(0, 153, 255), rgb(0, 193, 219));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        form {
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(50px);
            padding: 30px;
            border-radius: 15px;
            display: flex;
            flex-direction: column;
            gap: 20px;
            box-shadow: 0 0 30px rgba(0, 166, 255, 0.2);
        }

        label {
            font-weight: 500;
            margin-bottom: 5px;
            display: block;
        }

        input[type="text"],
        textarea {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            background: rgb(30, 30, 30);
            color: white;
            font-family: inherit;
            font-size: 1rem;
            outline: none;
            box-sizing: border-box;
        }

        input[type="text"]:focus,
        textarea:focus {
            border-color: rgba(0, 193, 219, 0.5);
        }

        textarea {
            resize: vertical;
            min-height: 120px;
        }

        small {
            display: block;
            margin-top: 5px;
            font-size: 0.8rem;
            opacity: 0.7;
        }

        .pfp-container {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 15px;
        }

        .pfp-preview {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid rgba(0, 193, 219, 0.3);
        }

        .file-upload-wrapper {
            position: relative;
        }

        input[type="file"] {
            display: none;
        }

        .file-upload-label {
            display: inline-block;
            padding: 8px 15px;
            background: rgba(0, 153, 255, 0.1);
            border: 1px solid rgba(0, 193, 219, 0.5);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .file-upload-label:hover {
            background: rgba(0, 153, 255, 0.2);
        }

        button[type="submit"] {
            background: linear-gradient(20deg, rgb(0, 153, 255), rgb(0, 193, 219));
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-family: inherit;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-top: 10px;
        }

        button[type="submit"]:hover {
            filter: brightness(1.1);
            box-shadow: 0 0 15px rgba(0, 166, 255, 0.5);
        }

        .error-message {
            background: rgba(200, 0, 0, 0.2);
            color: #ff4d4d;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 500;
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
        }

        @media (max-width: 600px) {
            .pfp-container {
                flex-direction: column;
                align-items: center;
                gap: 15px;
            }
            
            form {
                padding: 20px;
            }
            
            main {
                padding: 0 15px;
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
            
            h2 {
                font-size: 1.5rem;
            }
            
            form {
                padding: 15px;
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
                        <a href="index.php">Home</a>
                        <a href="post.php">Post</a>
                        <a href="profile.php?user=<?php echo $_SESSION['username']?>">Profile</a>
                        <a href="#">Messages</a>
                    </div>
                </div>
                
                
                <div class="right-nav">
                    <div class="nav-divider"></div>
                    
                    <a href="search.php" class="search-link" title="Search">
                        <i></i>
                    </a>
                    
                    <div class="user-actions">
                        <a href="update-profile.php" id="checked">
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
        <h2>Upravit profil</h2>
        
        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <div>
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'); ?>" required minlength="3" maxlength="20" pattern="[a-zA-Z0-9_]+">
                <small>Pouze písmena, čísla a podtržítka (3-20 znaků)</small>
            </div>

            <div>
                <label for="name">Jméno:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8'); ?>" required maxlength="50">
            </div>

            <div>
                <label>Profilový obrázek:</label>
                <div class="pfp-container">
                    <?php if ($user['pfp']): ?>
                        <img src="<?php echo htmlspecialchars($user['pfp'], ENT_QUOTES, 'UTF-8'); ?>" alt="Profilový obrázek" class="pfp-preview">
                    <?php endif; ?>
                    <div class="file-upload-wrapper">
                        <label for="pfp" class="file-upload-label">Vybrat obrázek</label>
                        <input type="file" id="pfp" name="pfp" accept="image/jpeg,image/png">
                        <small>Max. 2MB (JPEG, PNG)</small>
                    </div>
                </div>
            </div>

            <div>
                <label for="bio">Bio:</label>
                <textarea id="bio" name="bio" maxlength="500"><?php echo htmlspecialchars($user['bio'], ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>

            <button type="submit">Uložit změny</button>
        </form>
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

        // Preview selected profile picture
        document.getElementById('pfp').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(event) {
                    const preview = document.querySelector('.pfp-preview');
                    if (!preview) {
                        const container = document.querySelector('.pfp-container');
                        const newPreview = document.createElement('img');
                        newPreview.className = 'pfp-preview';
                        container.insertBefore(newPreview, container.firstChild);
                        newPreview.src = event.target.result;
                    } else {
                        preview.src = event.target.result;
                    }
                }
                
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>
