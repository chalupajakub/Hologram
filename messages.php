<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <title>HOLOGRAM | Zprávy</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
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

        .messages-container {
            display: flex;
            height: calc(100vh - 80px);
            margin-top: 80px;
            max-width: 1400px;
            margin-left: auto;
            margin-right: auto;
        }

        .conversations-list {
            width: 350px;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(20px);
            border-radius: 15px 0 0 15px;
            overflow: hidden;
            border-right: 1px solid rgba(255, 255, 255, 0.1);
        }

        .conversations-header {
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(0, 0, 0, 0.3);
        }

        .conversations-header h2 {
            margin: 0 0 15px 0;
            font-size: 1.4rem;
            font-weight: 600;
            background: linear-gradient(20deg, rgb(0, 153, 255), rgb(0, 193, 219));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .search-box {
            width: 100%;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 25px;
            padding: 12px 20px;
            color: white;
            font-family: inherit;
            font-size: 0.9rem;
            outline: none;
            transition: all 0.3s ease;
        }

        .search-box:focus {
            border-color: rgba(0, 193, 219, 0.5);
            background: rgba(0, 193, 219, 0.05);
            box-shadow: 0 0 15px rgba(0, 193, 219, 0.2);
        }

        .search-box::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .conversations-scroll {
            height: calc(100vh - 80px - 120px);
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(0, 193, 219, 0.3) transparent;
        }

        .conversations-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .conversations-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        .conversations-scroll::-webkit-scrollbar-thumb {
            background: rgba(0, 193, 219, 0.3);
            border-radius: 3px;
        }

        .conversation-item {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            position: relative;
        }

        .conversation-item:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        .conversation-item.active {
            background: linear-gradient(20deg, rgba(0, 153, 255, 0.1), rgba(0, 193, 219, 0.1));
            border-left: 3px solid rgb(0, 193, 219);
        }

        .conversation-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 15px;
            border: 2px solid rgba(0, 193, 219, 0.3);
            position: relative;
            overflow: hidden;
            flex-shrink: 0;
        }

        .conversation-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }

        .online-indicator {
            position: absolute;
            bottom: 2px;
            right: 2px;
            width: 12px;
            height: 12px;
            background: #00ff88;
            border-radius: 50%;
            border: 2px solid rgba(0, 0, 0, 0.8);
            z-index: 2;
        }

        .conversation-info {
            flex: 1;
            min-width: 0;
        }

        .conversation-name {
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 4px;
            color: white;
        }

        .conversation-preview {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.7);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .conversation-meta {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 5px;
        }

        .conversation-time {
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.5);
        }

        .unread-badge {
            background: linear-gradient(20deg, rgb(0, 153, 255), rgb(0, 193, 219));
            color: white;
            font-size: 0.7rem;
            font-weight: 600;
            padding: 2px 6px;
            border-radius: 10px;
            min-width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .chat-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(20px);
            border-radius: 0 15px 15px 0;
        }

        .chat-header {
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(0, 0, 0, 0.3);
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .chat-user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(0, 193, 219, 0.3);
        }

        .chat-user-info h3 {
            margin: 0 0 2px 0;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .chat-user-status {
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.6);
        }

        .chat-user-status.online {
            color: #00ff88;
        }

        .chat-messages {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 15px;
            scrollbar-width: thin;
            scrollbar-color: rgba(0, 193, 219, 0.3) transparent;
        }

        .chat-messages::-webkit-scrollbar {
            width: 6px;
        }

        .chat-messages::-webkit-scrollbar-track {
            background: transparent;
        }

        .chat-messages::-webkit-scrollbar-thumb {
            background: rgba(0, 193, 219, 0.3);
            border-radius: 3px;
        }

        .message {
            display: flex;
            align-items: flex-end;
            gap: 10px;
            max-width: 70%;
            animation: fadeInUp 0.3s ease;
        }

        .message.sent {
            align-self: flex-end;
            flex-direction: row-reverse;
        }

        .message-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .message-bubble {
            padding: 12px 16px;
            border-radius: 18px;
            position: relative;
            word-wrap: break-word;
        }

        .message.received .message-bubble {
            background: rgba(255, 255, 255, 0.1);
            border-bottom-left-radius: 6px;
        }

        .message.sent .message-bubble {
            background: linear-gradient(20deg, rgb(0, 153, 255), rgb(0, 193, 219));
            border-bottom-right-radius: 6px;
        }

        .message-text {
            margin: 0;
            line-height: 1.4;
        }

        .message-time {
            font-size: 0.7rem;
            color: rgba(255, 255, 255, 0.5);
            margin-top: 4px;
            text-align: right;
        }

        .message.received .message-time {
            text-align: left;
        }

        .chat-input {
            padding: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(0, 0, 0, 0.3);
        }

        .input-container {
            display: flex;
            gap: 12px;
            align-items: flex-end;
        }

        .message-input {
            flex: 1;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 25px;
            padding: 12px 20px;
            color: white;
            font-family: inherit;
            font-size: 0.9rem;
            outline: none;
            transition: all 0.3s ease;
            resize: none;
            min-height: 44px;
            max-height: 120px;
        }

        .message-input:focus {
            border-color: rgba(0, 193, 219, 0.5);
            background: rgba(0, 193, 219, 0.05);
            box-shadow: 0 0 15px rgba(0, 193, 219, 0.2);
        }

        .message-input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .send-button {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            border: none;
            background: linear-gradient(20deg, rgb(0, 153, 255), rgb(0, 193, 219));
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .send-button:hover {
            transform: scale(1.05);
            box-shadow: 0 0 20px rgba(0, 193, 219, 0.4);
        }

        .no-conversation {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: rgba(255, 255, 255, 0.6);
            text-align: center;
        }

        .no-conversation-icon {
            width: 80px;
            height: 80px;
            margin-bottom: 20px;
            opacity: 0.3;
        }

        .no-results {
            padding: 20px;
            text-align: center;
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.9rem;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
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
            
            .messages-container {
                flex-direction: column;
                height: auto;
                min-height: calc(100vh - 80px);
            }
            
            .conversations-list {
                width: 100%;
                height: 300px;
                border-radius: 15px 15px 0 0;
                border-right: none;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            }
            
            .conversations-scroll {
                height: 200px;
            }
            
            .chat-area {
                border-radius: 0 0 15px 15px;
                min-height: calc(100vh - 380px);
            }
        }

        @media (max-width: 768px) {
            .messages-container {
                margin: 80px 10px 20px 10px;
            }
            
            .conversations-list {
                border-radius: 15px 15px 0 0;
            }
            
            .conversation-item {
                padding: 12px 15px;
            }
            
            .conversation-avatar {
                width: 45px;
                height: 45px;
                margin-right: 12px;
            }
            
            .chat-header {
                padding: 15px;
            }
            
            .chat-messages {
                padding: 15px;
            }
            
            .message {
                max-width: 85%;
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
                        <a href="profile.php">Profile</a>
                        <a href="#" id="checked">Messages</a>
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

    <div class="messages-container">
        <div class="conversations-list">
            <div class="conversations-header">
                <h2>Zprávy</h2>
                <input type="text" class="search-box" placeholder="Hledat konverzace..." id="user-search">
            </div>
            <div class="conversations-scroll" id="conversations-container">
                <!-- Konverzace se načtou přes JavaScript -->
                <div class="no-results">Načítání konverzací...</div>
            </div>
        </div>

        <div class="chat-area">
            <div class="chat-header">
                <img src="https://images.unsplash.com/photo-1494790108755-2616b612b647?w=100&h=100&fit=crop&crop=face" alt="Avatar" class="chat-user-avatar">
                <div class="chat-user-info">
                    <h3 id="chat-with-name">Vyberte konverzaci</h3>
                    <div class="chat-user-status" id="chat-status"></div>
                </div>
            </div>

            <div class="chat-messages" id="chat-messages">
                <div class="no-conversation">
                    <svg class="no-conversation-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                    </svg>
                    <p>Vyberte konverzaci nebo začněte nový chat</p>
                </div>
            </div>

            <div class="chat-input" id="chat-input" style="display: none;">
                <div class="input-container">
                    <textarea class="message-input" placeholder="Napiš zprávu..." rows="1" id="message-input"></textarea>
                    <button class="send-button" id="send-button">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="22" y1="2" x2="11" y2="13"></line>
                            <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

   <script>
        // Aktuálně vybraná konverzace
        let currentConversation = null;

        // Data konverzací
        const conversations = [
            {
                id: 1,
                name: "Anna Nová",
                username: "annan",
                avatar: "holomy.jpg",
                online: true,
                lastMessage: "Ahoj! Jak se máš? 😊",
                time: "14:32",
                unread: 2,
                messages: [
                    { text: "Ahoj! Jak se máš? 😊", time: "14:32", sent: false },
                    { text: "Ahoj Anno! Mám se skvěle, děkuji za optání. A co ty?", time: "14:35", sent: true },
                    { text: "Také dobře! Chtěla jsem se tě zeptat, jestli nejdeš zítra na ten koncert v centru?", time: "14:36", sent: false }
                ]
            },
            {
                id: 2,
                name: "Tomáš Svoboda",
                username: "tomas",
                avatar: "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&h=100&fit=crop&crop=face",
                online: false,
                lastMessage: "Díky za tip na tu restauraci!",
                time: "včera",
                unread: 0,
                messages: [
                    { text: "Ahoj, nevíš o nějaké dobré italské restauraci v centru?", time: "09:15", sent: false },
                    { text: "Ahoj Tomáši, zkus La Piccola na Náměstí. Mají skvělé těstoviny!", time: "09:30", sent: true },
                    { text: "Díky za tip na tu restauraci!", time: "včera", sent: false }
                ]
            }
        ];

        // Načtení konverzací při startu
        document.addEventListener('DOMContentLoaded', function() {
            loadConversations();
        });

        // Načtení seznamu konverzací
        function loadConversations() {
            const container = document.getElementById('conversations-container');
            
            if (conversations.length === 0) {
                container.innerHTML = '<div class="no-results">Nemáte žádné konverzace</div>';
                return;
            }
            
            let html = '';
            conversations.forEach(conversation => {
                html += `
                    <div class="conversation-item" data-id="${conversation.id}" onclick="selectConversation(${JSON.stringify(conversation).replace(/"/g, '&quot;')})">
                        <div class="conversation-avatar">
                            <img src="${conversation.avatar}" alt="${conversation.name}">
                            ${conversation.online ? '<div class="online-indicator"></div>' : ''}
                        </div>
                        <div class="conversation-info">
                            <div class="conversation-name">${conversation.name}</div>
                            <div class="conversation-preview">${conversation.lastMessage}</div>
                        </div>
                        <div class="conversation-meta">
                            <div class="conversation-time">${conversation.time}</div>
                            ${conversation.unread > 0 ? `<div class="unread-badge">${conversation.unread}</div>` : ''}
                        </div>
                    </div>
                `;
            });
            
            container.innerHTML = html;
        }

        // Výběr konverzace
        function selectConversation(conversation) {
            currentConversation = conversation;
            
            // Odstranění aktivní třídy ze všech konverzací
            document.querySelectorAll('.conversation-item').forEach(item => {
                item.classList.remove('active');
            });
            
            // Přidání aktivní třídy k vybrané konverzaci
            document.querySelector(`.conversation-item[data-id="${conversation.id}"]`).classList.add('active');
            
            // Aktualizace hlavičky chatu
            document.getElementById('chat-with-name').textContent = conversation.name;
            const statusElement = document.getElementById('chat-status');
            statusElement.textContent = conversation.online ? 'Online' : 'Offline';
            statusElement.className = `chat-user-status ${conversation.online ? 'online' : ''}`;
            
            // Aktualizace avataru v hlavičce chatu
            document.querySelector('.chat-user-avatar').src = conversation.avatar;
            document.querySelector('.chat-user-avatar').alt = conversation.name;
            
            // Načtení zpráv
            loadMessages(conversation.messages);
            
            // Zobrazení vstupního pole
            document.getElementById('chat-input').style.display = 'block';
            
            // Resetování nepřečtených zpráv
            conversation.unread = 0;
            loadConversations();
        }

        // Načtení zpráv
        function loadMessages(messages) {
            const container = document.getElementById('chat-messages');
            
            if (!messages || messages.length === 0) {
                container.innerHTML = '<div class="no-conversation"><p>Žádné zprávy v této konverzaci</p></div>';
                return;
            }
            
            let html = '';
            messages.forEach(message => {
                const avatar = message.sent 
                    ? 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=100&h=100&fit=crop&crop=face' 
                    : currentConversation.avatar;
                
                html += `
                    <div class="message ${message.sent ? 'sent' : 'received'}">
                        <img src="${avatar}" class="message-avatar" alt="Avatar">
                        <div class="message-bubble">
                            <p class="message-text">${message.text}</p>
                            <p class="message-time">${message.time}</p>
                        </div>
                    </div>
                `;
            });
            
            container.innerHTML = html;
            container.scrollTop = container.scrollHeight;
        }

        // Odeslání zprávy
        document.getElementById('send-button').addEventListener('click', function() {
            const input = document.getElementById('message-input');
            const messageText = input.value.trim();
            
            if (messageText === '' || !currentConversation) return;
            
            const newMessage = {
                text: messageText,
                time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                sent: true
            };
            
            // Přidání zprávy do konverzace
            currentConversation.messages.push(newMessage);
            currentConversation.lastMessage = messageText.length > 30 ? messageText.substring(0, 30) + '...' : messageText;
            currentConversation.time = 'nyní';
            
            // Aktualizace UI
            loadMessages(currentConversation.messages);
            loadConversations();
            input.value = '';
            
            // Simulace odpovědi
            setTimeout(() => {
                const replyMessage = {
                    text: getRandomReply(),
                    time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                    sent: false
                };
                
                currentConversation.messages.push(replyMessage);
                currentConversation.lastMessage = replyMessage.text.length > 30 ? replyMessage.text.substring(0, 30) + '...' : replyMessage.text;
                currentConversation.time = 'nyní';
                
                loadMessages(currentConversation.messages);
                loadConversations();
            }, 1000 + Math.random() * 2000);
        });

        // Náhodné odpovědi pro simulaci
        function getRandomReply() {
            const replies = [
                 "Jo, no... to mě fakt zajímá. (Ne.)",  
    "To zní jako problém, ale ne můj.",  
    "Kdybych měl zájem, už bych se ptal.",  
    "Wow. Máš talent říkat nic zajímavého.",  
    "Promiň, zrovna řeším důležitější věc. (Třeba co budu večeřet.)",  
    "To je skvělý! (Pro tebe. Já jsem zklamán.)",  
    "Můžeš to zopakovat?",  
    "Jsem rád, že ses rozepsal.",  
    "Kdybych chtěl odpověď, googlil bych si to.",  
    "idk",  
    "Ne.",  
    "Ano.",  
    "Chápu... ale idc",  
    "Dík za názor. ",  
    "To by mohlo být relevantní... v paralelním vesmíru.",  
    "Jsem unavený. Z tebe.",  
    "Tohle si uložím... do koše.",  
    "Máš můj obdiv. (Ne, nemáš.)",  
    "To je hezký! (Pro někoho jiného.)",  
    "Zajímavý příběh. Miluju negry!!"
            ];
            return replies[Math.floor(Math.random() * replies.length)];
        }

        // Automatické zvětšení textarea při psaní
        document.getElementById('message-input').addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });

        // Odeslání zprávy pomocí Enter
        document.getElementById('message-input').addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                document.getElementById('send-button').click();
            }
        });
    </script>
</body>
</html>
