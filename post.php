<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];

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

        // Získání velikosti obrázku
        list($width, $height) = getimagesize($tmp_name);
        $max_width = 400;

        if ($width > $max_width) {
            // Vytvoření zmenšené kopie
            $new_width = $max_width;
            $new_height = floor($height * ($new_width / $width));

            switch ($ext) {
                case 'jpg':
                case 'jpeg':
                    $src_image = imagecreatefromjpeg($tmp_name);
                    break;
                case 'png':
                    $src_image = imagecreatefrompng($tmp_name);
                    break;
                case 'gif':
                    $src_image = imagecreatefromgif($tmp_name);
                    break;
                default:
                    die("Nepodporovaný formát.");
            }

            $resized_image = imagecreatetruecolor($new_width, $new_height);

            // Zachování průhlednosti
            if ($ext === 'png' || $ext === 'gif') {
                imagecolortransparent($resized_image, imagecolorallocatealpha($resized_image, 0, 0, 0, 127));
                imagealphablending($resized_image, false);
                imagesavealpha($resized_image, true);
            }

            imagecopyresampled($resized_image, $src_image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

            switch ($ext) {
                case 'jpg':
                case 'jpeg':
                    imagejpeg($resized_image, $cilova_cesta, 80); // komprese
                    break;
                case 'png':
                    imagepng($resized_image, $cilova_cesta, 6); // komprese PNG 0–9
                    break;
                case 'gif':
                    imagegif($resized_image, $cilova_cesta);
                    break;
            }

            imagedestroy($src_image);
            imagedestroy($resized_image);
        } else {
            // Pokud není třeba zmenšovat, jen ulož
            move_uploaded_file($tmp_name, $cilova_cesta);
        }

        $sql = "INSERT INTO posts (user_id, content, image) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $user_id, $popis, $cilova_cesta);

        if ($stmt->execute()) {
            $message = "Příspěvek byl úspěšně přidán.";
            echo '<script>
                document.getElementById("upload-form").reset();
                document.getElementById("image-preview").style.display = "none";
                document.getElementById("upload-container").innerHTML = \'<div class="upload-instructions"><span class="upload-icon">+</span><span>Klikněte pro nahrání obrázku</span></div><input type="file" id="obrazek" name="obrazek" accept="image/jpeg, image/png, image/gif" required>\';
                document.getElementById("obrazek").addEventListener("change", previewImage);
            </script>';
        } else {
            $error = "Chyba při ukládání do databáze: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $error = "Nepodařilo se nahrát soubor.";
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
    <title>HOLOGRAM | Nový příspěvek</title>
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
            padding: 25px;
            border-radius: 15px;
            display: flex;
            flex-direction: column;
            gap: 15px;
            box-shadow: 0 0 20px rgba(0, 166, 255, 0.2);
        }

        .file-upload-wrapper {
            position: relative;
            width: 100%;
            height: 200px;
            margin-bottom: 15px;
        }

        #upload-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 2px dashed rgba(255, 255, 255, 0.3);
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            transition: all 0.3s ease;
            overflow: hidden;
        }

        #upload-container:hover {
            border-color: rgba(0, 193, 219, 0.5);
            background: rgba(0, 153, 255, 0.05);
        }

        .upload-instructions {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            text-align: center;
            z-index: 1;
        }

        .upload-icon {
            font-size: 30px;
            color: rgba(0, 193, 219, 0.7);
        }

        input[type="file"] {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        #image-preview {
            display: none;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 10px;
            overflow: hidden;
            background-color: #1a1a1a;
        }

        #preview-image {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .preview-controls {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 2;
        }

        #remove-image {
            background: rgba(0, 0, 0, 0.7);
            border: none;
            color: white;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            transition: background 0.2s;
        }

        #remove-image:hover {
            background: rgba(255, 0, 0, 0.7);
        }

        textarea {
            background: rgb(30, 30, 30);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 12px;
            color: white;
            font-family: inherit;
            resize: vertical;
            min-height: 120px;
            font-size: 0.95rem;
            outline: none;
        }

        textarea:focus {
            border-color: rgba(0, 193, 219, 0.5);
        }

        input[type="submit"] {
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
            margin-top: 5px;
        }

        input[type="submit"]:hover {
            filter: brightness(1.1);
            box-shadow: 0 0 12px rgba(0, 166, 255, 0.5);
        }

        .message {
            text-align: center;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-weight: 500;
            font-size: 0.95rem;
        }

        .success {
            background: rgba(0, 200, 100, 0.2);
            color: #00c864;
        }

        .error {
            background: rgba(200, 0, 0, 0.2);
            color: #ff4d4d;
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
            .links-container{
                position: relative;
            }
            
            .nav-container {
                justify-content: flex-end;
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
                        <a href="post.php" id="checked">Post</a>
                        <a href="profile.php?user=<?php echo $username?>">Profile</a>
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
        <h2>Přidat nový příspěvek</h2>

        <?php if (isset($message)): ?>
            <div class="message success"><?php echo $message; ?></div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form id="upload-form" action="#" method="post" enctype="multipart/form-data">
            <div class="file-upload-wrapper">
                <div id="upload-container">
                    <div class="upload-instructions">
                        <span class="upload-icon">+</span>
                        <span>Klikněte pro nahrání obrázku</span>
                    </div>
                    <input type="file" id="obrazek" name="obrazek" accept="image/jpeg, image/png, image/gif" required>
                </div>
                
                <div id="image-preview">
                    <div class="preview-controls">
                        <button type="button" id="remove-image" title="Odstranit obrázek">×</button>
                    </div>
                    <img id="preview-image" src="#" alt="Náhled obrázku">
                </div>
            </div>
            
            <label for="obsah">Popis příspěvku</label>
            <textarea id="obsah" name="obsah" placeholder="Napište popis k obrázku..." required></textarea>
        
            <input type="submit" value="Publikovat příspěvek">
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

        // Image upload functionality
        function previewImage(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(event) {
                    const preview = document.getElementById('preview-image');
                    preview.src = event.target.result;
                    
                    const previewContainer = document.getElementById('image-preview');
                    previewContainer.style.display = 'block';
                    
                    document.getElementById('upload-container').style.border = 'none';
                    document.querySelector('.upload-instructions').style.display = 'none';
                }
                
                reader.readAsDataURL(file);
            }
        }

        document.getElementById('obrazek').addEventListener('change', previewImage);

        document.getElementById('remove-image').addEventListener('click', function(e) {
            e.stopPropagation();
            document.getElementById('obrazek').value = '';
            document.getElementById('image-preview').style.display = 'none';
            document.getElementById('upload-container').style.border = '2px dashed rgba(255, 255, 255, 0.3)';
            document.querySelector('.upload-instructions').style.display = 'flex';
        });

        // Make sure clicking anywhere in the container triggers the file input
        document.getElementById('upload-container').addEventListener('click', function(e) {
            if (e.target !== this && e.target.id !== 'remove-image') {
                document.getElementById('obrazek').click();
            }
        });
    </script>
</body>
</html>
