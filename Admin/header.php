<nav class="navbar">
    <style> 
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #ded9d9;
        overflow-x: hidden;
        background-blend-mode: overlay;
    }

    .navbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #396e38;
        padding: 10px 20px;
        color: white;
    }
    .nav-left,
    .nav-right {
        display: flex;
        align-items: center;
    }
    .nav-link {
        color: white;
        text-decoration: none;
        margin: 0 15px;
        font-size: 16px;
        padding: 8px 16px;
        border-radius: 4px;
        transition: background-color 0.3s ease;
    }

    .nav-link:hover {
        background-color: rgba(114, 117, 116, 0.23);
    }

    .navbar-logo {
        max-height: 50px;
    }
    </style>
    
    <div class="nav-left">
        <div class="navbar-brand">
            <a href="#">
                <img src="../img/logo.png" alt="WMS" class="navbar-logo">
            </a>
        </div>
    </div>

    <div class="nav-right">
        <a href="../User/logout.php" class="nav-link">Logout</a>
    </div>
    
</nav>
