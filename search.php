<?php
include 'connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id = $_SESSION['user_id'];
$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <title>HOLOGRAM | Vyhledávání</title>
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

        .search-container {
            position: relative;
            margin-bottom: 30px;
        }

        .search-box {
            width: 100%;
            padding: 15px 20px 15px 50px;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(50px);
            border: 2px solid rgba(0, 193, 219, 0.3);
            border-radius: 25px;
            color: white;
            font-size: 1rem;
            font-family: "Be Vietnam Pro", sans-serif;
            outline: none;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }

        .search-box:focus {
            border-color: rgba(0, 193, 219, 0.7);
            box-shadow: 0 0 20px rgba(0, 193, 219, 0.3);
        }

        .search-box::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .search-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.5);
            font-size: 1.2rem;
            pointer-events: none;
        }

        .search-results {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.95);
            backdrop-filter: blur(50px);
            border-radius: 15px;
            margin-top: 5px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            z-index: 1000;
            max-height: 400px;
            overflow-y: auto;
            display: none;
        }

        .search-results.show {
            display: block;
        }

        .search-result-item {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            color: white;
            gap: 12px;
        }

        .search-result-item:hover {
            background: rgba(0, 193, 219, 0.1);
        }

        .search-result-item img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(0, 193, 219, 0.3);
        }

        .search-result-info {
            display: flex;
            flex-direction: column;
        }

        .search-result-username {
            font-weight: 600;
            margin-bottom: 2px;
        }

        .search-result-fullname {
            font-size: 0.9rem;
            opacity: 0.7;
        }

        .no-results {
            padding: 20px;
            text-align: center;
            color: rgba(255, 255, 255, 0.7);
            font-style: italic;
        }

        .recent-searches {
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(50px);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .recent-searches h4 {
            margin: 0 0 15px 0;
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.9);
        }

        .recent-search-item {
            display: flex;
            align-items: center;
            padding: 10px 0;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            color: white;
            gap: 12px;
        }

        .recent-search-item:hover {
            opacity: 0.7;
        }

        .recent-search-item img {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(0, 193, 219, 0.3);
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
                        <a href="post.php">Post</a>
                        <a href="profile.php?user=<?php echo $username?>">Profile</a>
                        <a href="#" id="checked">Hledat</a>
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
        <h3>Hledat uživatele</h3>
        
        <div class="search-container">
            <div class="search-icon">🔍</div>
            <input type="text" class="search-box" id="searchInput" placeholder="Hledat uživatele...">
            <div class="search-results" id="searchResults"></div>
        </div>

        <div class="recent-searches" id="recentSearches" style="display: none;">
            <h4>Nedávné vyhledávání</h4>
            <div id="recentSearchList"></div>
        </div>
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

        // Search functionality
        const searchInput = document.getElementById('searchInput');
        const searchResults = document.getElementById('searchResults');
        const recentSearches = document.getElementById('recentSearches');
        const recentSearchList = document.getElementById('recentSearchList');
        
        let searchTimeout;
        
        // Load recent searches from localStorage
        function loadRecentSearches() {
            const recent = JSON.parse(localStorage.getItem('recentSearches') || '[]');
            if (recent.length > 0) {
                recentSearches.style.display = 'block';
                recentSearchList.innerHTML = recent.map(user => `
                    <a href="profile.php?user=${encodeURIComponent(user.username)}" class="recent-search-item">
                        <img src="${user.pfp}" alt="Profile">
                        <div>
                            <div class="search-result-username">${user.username}</div>
                        </div>
                    </a>
                `).join('');
            }
        }
        
        // Save to recent searches
        function saveToRecentSearches(user) {
            let recent = JSON.parse(localStorage.getItem('recentSearches') || '[]');
            
            // Remove if already exists
            recent = recent.filter(item => item.username !== user.username);
            
            // Add to beginning
            recent.unshift(user);
            
            // Keep only last 5
            recent = recent.slice(0, 5);
            
            localStorage.setItem('recentSearches', JSON.stringify(recent));
            loadRecentSearches();
        }
        
        // Search users
        function searchUsers(query) {
            if (query.length < 1) {
                searchResults.classList.remove('show');
                return;
            }
            
            fetch('search_users.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'query=' + encodeURIComponent(query)
            })
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    searchResults.innerHTML = data.map(user => `
                        <a href="profile.php?user=${encodeURIComponent(user.username)}" class="search-result-item" onclick="saveUserToRecent('${user.username}', '${user.pfp}')">
                            <img src="${user.pfp}" alt="Profile">
                            <div class="search-result-info">
                                <div class="search-result-username">${user.username}</div>
                            </div>
                        </a>
                    `).join('');
                } else {
                    searchResults.innerHTML = '<div class="no-results">Žádní uživatelé nenalezeni</div>';
                }
                searchResults.classList.add('show');
            })
            .catch(error => {
                console.error('Error:', error);
                searchResults.classList.remove('show');
            });
        }
        
        // Save user to recent searches (called from HTML)
        function saveUserToRecent(username, pfp) {
            saveToRecentSearches({ username, pfp });
        }
        
        // Search input event listeners
        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            const query = e.target.value.trim();
            
            if (query === '') {
                searchResults.classList.remove('show');
                return;
            }
            
            searchTimeout = setTimeout(() => {
                searchUsers(query);
            }, 300);
        });
        
        searchInput.addEventListener('focus', () => {
            if (searchInput.value.trim()) {
                searchResults.classList.add('show');
            }
        });
        
        // Hide search results when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.search-container')) {
                searchResults.classList.remove('show');
            }
        });
        
        // Load recent searches on page load
        loadRecentSearches();
        
        // Make saveUserToRecent globally available
        window.saveUserToRecent = saveUserToRecent;
    </script>
</body>
</html>
