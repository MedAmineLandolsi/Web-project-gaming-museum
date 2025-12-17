<?php

session_start();

require_once '../../config.php';

require_once '../../controller/user_controller.php';



$controller = new UserController();



if (!$controller->isLoggedIn()) {

    header('Location: login.php');

    exit();

}



$profileData = $controller->viewProfile($_SESSION['user_id']);

$user = $profileData['user'];

?>

<!DOCTYPE html>

<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Mon Profil - Ludology Vault</title>

    <link rel="stylesheet" href="style.css">

    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">

    <style>

        .profile-container {

            max-width: 1200px;

            margin: 4rem auto;

            padding: 2rem;

            position: relative;

            z-index: 10;

        }



        .profile-header {

            text-align: center;

            margin-bottom: 3rem;

        }



        .profile-title {

            font-size: 2rem;

            color: var(--primary-green);

            text-shadow: 0 0 20px var(--primary-green);

            margin-bottom: 1rem;

        }



        .profile-card {

            background: linear-gradient(135deg, var(--card-bg), var(--darker-bg));

            border: 3px solid var(--primary-green);

            padding: 3rem;

            box-shadow: 0 20px 60px rgba(0, 255, 65, 0.4);

            position: relative;

            overflow: hidden;

        }



        .profile-card::before {

            content: '';

            position: absolute;

            top: 0;

            left: 0;

            width: 100%;

            height: 100%;

            background-image: 

                repeating-linear-gradient(

                    0deg,

                    transparent,

                    transparent 2px,

                    rgba(0, 255, 65, 0.03) 2px,

                    rgba(0, 255, 65, 0.03) 4px

                );

            pointer-events: none;

        }



        .profile-avatar-section {

            text-align: center;

            margin-bottom: 3rem;

            position: relative;

        }



        .profile-avatar-large {

            width: 150px;

            height: 150px;

            border-radius: 50%;

            border: 4px solid var(--primary-green);

            background: linear-gradient(135deg, var(--primary-green), var(--secondary-purple));

            display: inline-flex;

            align-items: center;

            justify-content: center;

            color: var(--darker-bg);

            font-size: 3rem;

            font-weight: bold;

            box-shadow: 0 0 30px rgba(0, 255, 65, 0.6);

            overflow: hidden;

            margin-bottom: 1.5rem;

        }



        .profile-avatar-large img {

            width: 100%;

            height: 100%;

            object-fit: cover;

        }



        .profile-username {

            font-size: 1.5rem;

            color: var(--primary-green);

            text-shadow: 0 0 15px var(--primary-green);

            margin-bottom: 0.5rem;

        }



        .profile-role-badge {

            display: inline-block;

            padding: 0.5rem 1.5rem;

            background: rgba(189, 0, 255, 0.2);

            border: 2px solid var(--secondary-purple);

            color: var(--secondary-purple);

            font-size: 0.6rem;

            margin-top: 1rem;

        }



        .profile-info-grid {

            display: grid;

            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));

            gap: 2rem;

            margin-bottom: 2rem;

        }



        .info-group {

            border: 2px solid var(--border-color);

            padding: 1.5rem;

            background: rgba(0, 0, 0, 0.3);

            transition: all 0.3s;

        }



        .info-group:hover {

            border-color: var(--primary-green);

            background: rgba(0, 255, 65, 0.05);

        }



        .info-label {

            font-size: 0.6rem;

            color: var(--primary-green);

            margin-bottom: 0.8rem;

            text-shadow: 0 0 5px var(--primary-green);

        }



        .info-value {

            font-size: 0.8rem;

            color: var(--text-white);

            font-family: 'VT323', monospace;

            font-size: 1.3rem;

        }



        .profile-actions {

            display: flex;

            gap: 1.5rem;

            justify-content: center;

            flex-wrap: wrap;

            margin-top: 3rem;

        }



        .btn-profile {

            padding: 1rem 2rem;

            font-family: 'Press Start 2P', cursive;

            font-size: 0.7rem;

            cursor: pointer;

            transition: all 0.3s;

            border: 2px solid;

            background: transparent;

        }



        .btn-edit {

            border-color: var(--primary-green);

            color: var(--primary-green);

        }



        .btn-edit:hover {

            background: var(--primary-green);

            color: var(--darker-bg);

            box-shadow: 0 0 30px rgba(0, 255, 65, 0.6);

            transform: translateY(-3px);

        }



        .btn-back {

            border-color: var(--secondary-purple);

            color: var(--secondary-purple);

        }



        .btn-back:hover {

            background: var(--secondary-purple);

            color: var(--darker-bg);

            box-shadow: 0 0 30px rgba(189, 0, 255, 0.6);

            transform: translateY(-3px);

        }



        .user-menu {

            position: relative;

        }



        .user-profile-btn {

            display: flex;

            align-items: center;

            gap: 0.8rem;

            padding: 0.8rem 1.5rem;

            background: linear-gradient(135deg, rgba(0, 255, 65, 0.1), rgba(189, 0, 255, 0.1));

            border: 2px solid var(--primary-green);

            color: var(--text-white);

            font-family: 'Press Start 2P', cursive;

            font-size: 0.6rem;

            cursor: pointer;

            transition: all 0.3s;

            box-shadow: 0 0 20px rgba(0, 255, 65, 0.3);

        }



        .user-profile-btn:hover {

            background: linear-gradient(135deg, rgba(0, 255, 65, 0.2), rgba(189, 0, 255, 0.2));

            transform: translateY(-2px);

            box-shadow: 0 0 30px rgba(0, 255, 65, 0.5);

        }



        .user-avatar {

            width: 35px;

            height: 35px;

            border-radius: 50%;

            border: 2px solid var(--primary-green);

            background: linear-gradient(135deg, var(--primary-green), var(--secondary-purple));

            display: flex;

            align-items: center;

            justify-content: center;

            color: var(--darker-bg);

            font-size: 0.8rem;

            font-weight: bold;

            overflow: hidden;

        }



        .user-avatar img {

            width: 100%;

            height: 100%;

            object-fit: cover;

        }



        .user-name {

            color: var(--primary-green);

            text-shadow: 0 0 10px var(--primary-green);

        }



        .dropdown-icon {

            font-size: 0.8rem;

            transition: transform 0.3s;

        }



        .user-profile-btn:hover .dropdown-icon {

            transform: translateY(2px);

        }



        .user-dropdown {

            position: absolute;

            top: calc(100% + 0.5rem);

            right: 0;

            min-width: 250px;

            background: linear-gradient(135deg, var(--card-bg), var(--darker-bg));

            border: 2px solid var(--primary-green);

            box-shadow: 0 10px 40px rgba(0, 255, 65, 0.4);

            opacity: 0;

            visibility: hidden;

            transform: translateY(-10px);

            transition: all 0.3s;

            z-index: 1000;

            overflow: hidden;

        }



        .user-dropdown::before {

            content: '';

            position: absolute;

            top: 0;

            left: 0;

            width: 100%;

            height: 100%;

            background-image: 

                repeating-linear-gradient(

                    0deg,

                    transparent,

                    transparent 2px,

                    rgba(0, 255, 65, 0.03) 2px,

                    rgba(0, 255, 65, 0.03) 4px

                );

            pointer-events: none;

        }



        .user-menu:hover .user-dropdown {

            opacity: 1;

            visibility: visible;

            transform: translateY(0);

        }



        .dropdown-header {

            padding: 1.5rem;

            border-bottom: 2px solid var(--primary-green);

            background: linear-gradient(135deg, rgba(0, 255, 65, 0.1), transparent);

        }



        .dropdown-header-title {

            font-size: 0.6rem;

            color: var(--primary-green);

            margin-bottom: 0.5rem;

        }



        .dropdown-header-subtitle {

            font-size: 0.5rem;

            color: var(--text-gray);

            font-family: 'VT323', monospace;

            font-size: 0.9rem;

        }



        .dropdown-menu-list {

            list-style: none;

            padding: 0.5rem 0;

        }



        .dropdown-menu-item {

            margin: 0;

        }



        .dropdown-menu-link {

            display: flex;

            align-items: center;

            gap: 1rem;

            padding: 1rem 1.5rem;

            color: var(--text-light-gray);

            text-decoration: none;

            font-size: 0.6rem;

            transition: all 0.3s;

            border-left: 3px solid transparent;

        }



        .dropdown-menu-link:hover {

            background: rgba(0, 255, 65, 0.1);

            color: var(--primary-green);

            border-left-color: var(--primary-green);

        }



        .dropdown-menu-link.admin {

            border-top: 1px solid var(--border-color);

            color: var(--secondary-purple);

        }



        .dropdown-menu-link.admin:hover {

            background: rgba(189, 0, 255, 0.1);

            color: var(--secondary-purple);

            border-left-color: var(--secondary-purple);

        }



        .dropdown-menu-link.logout {

            border-top: 1px solid var(--border-color);

            color: var(--accent-pink);

        }



        .dropdown-menu-link.logout:hover {

            background: rgba(255, 0, 110, 0.1);

            color: var(--accent-pink);

            border-left-color: var(--accent-pink);

        }



        .dropdown-icon-left {

            font-size: 1rem;

        }

    </style>

</head>

<body>

    <div class="particles">

        <div class="particle"></div>

        <div class="particle"></div>

        <div class="particle"></div>

        <div class="particle"></div>

        <div class="particle"></div>

        <div class="particle"></div>

        <div class="particle"></div>

        <div class="particle"></div>

        <div class="particle"></div>

        <div class="particle"></div>

    </div>



    <nav class="navbar">

        <div class="nav-container">

            <div class="nav-left">

                <div class="logo-container">

                    <div class="logo-placeholder">[LOGO]</div>

                    <h1 class="site-title">LUDOLOGY VAULT</h1>

                </div>

            </div>

            

            <div class="nav-center">

                <ul class="nav-menu">

                    <li><a href="index.php">HOME</a></li>

                    <li><a href="games.php">JEUX</a></li>

                    <li><a href="blog.php">BLOG</a></li>

                    <li><a href="events.php">EVENTS</a></li>

                    <li><a href="reclamation.php">RECLAMATION</a></li>

                </ul>

            </div>

            

            <div class="nav-right">

                <div class="user-menu">

                    <button class="user-profile-btn">

                        <div class="user-avatar">

                            <?php if ($user['profile_picture_url'] && file_exists("../../uploads/" . $user['profile_picture_url'])): ?>

                                <img src="../../uploads/<?php echo htmlspecialchars($user['profile_picture_url']); ?>" alt="Profile">

                            <?php else: ?>

                                <?php echo strtoupper(substr($user['username'], 0, 2)); ?>

                            <?php endif; ?>

                        </div>

                        <span class="user-name"><?php echo htmlspecialchars($user['username']); ?></span>

                        <span class="dropdown-icon">â–¼</span>

                    </button>

                    

                    <div class="user-dropdown">

                        <div class="dropdown-header">

                            <div class="dropdown-header-title">WELCOME BACK</div>

                            <div class="dropdown-header-subtitle"><?php echo htmlspecialchars($user['username']); ?></div>

                        </div>

                        <ul class="dropdown-menu-list">

                            <li class="dropdown-menu-item">

                                <a href="profile.php" class="dropdown-menu-link">

                                    <span class="dropdown-icon-left">ðŸ‘¤</span>

                                    MON PROFIL

                                </a>

                            </li>

                            <?php if ($user['role'] === 'admin'): ?>

                            <li class="dropdown-menu-item">

                                <a href="../backoffice/dashboard.php" class="dropdown-menu-link admin">

                                    <span class="dropdown-icon-left">âš™</span>

                                    ADMIN DASHBOARD

                                </a>

                            </li>

                            <?php endif; ?>

                            <li class="dropdown-menu-item">

                                <a href="#" class="dropdown-menu-link logout" id="logoutBtn">

                                    <span class="dropdown-icon-left">ðŸšª</span>

                                    DECONNEXION

                                </a>

                            </li>

                        </ul>

                    </div>

                </div>

            </div>

        </div>

    </nav>



    <div class="profile-container">

        <div class="profile-header">

            <h2 class="profile-title">â—„ MON PROFIL â–º</h2>

        </div>



        <div class="profile-card">

            <div class="profile-avatar-section">

                <div class="profile-avatar-large">

                    <?php if ($user['profile_picture_url'] && file_exists("../../uploads/" . $user['profile_picture_url'])): ?>

                        <img src="../../uploads/<?php echo htmlspecialchars($user['profile_picture_url']); ?>" alt="Profile">

                    <?php else: ?>

                        <?php echo strtoupper(substr($user['username'], 0, 2)); ?>

                    <?php endif; ?>

                </div>

                <div class="profile-username"><?php echo htmlspecialchars($user['username']); ?></div>

                <div class="profile-role-badge"><?php echo strtoupper($user['role']); ?></div>

            </div>



            <div class="profile-info-grid">

                <div class="info-group">

                    <div class="info-label">ðŸ“§ EMAIL</div>

                    <div class="info-value"><?php echo htmlspecialchars($user['email']); ?></div>

                </div>



                <div class="info-group">

                    <div class="info-label">ðŸ‘¤ USERNAME</div>

                    <div class="info-value"><?php echo htmlspecialchars($user['username']); ?></div>

                </div>



                <div class="info-group">

                    <div class="info-label">ðŸ“ FIRST NAME</div>

                    <div class="info-value"><?php echo $user['first_name'] ? htmlspecialchars($user['first_name']) : 'Not set'; ?></div>

                </div>



                <div class="info-group">

                    <div class="info-label">ðŸ“ LAST NAME</div>

                    <div class="info-value"><?php echo $user['last_name'] ? htmlspecialchars($user['last_name']) : 'Not set'; ?></div>

                </div>



                <div class="info-group">

                    <div class="info-label">ðŸ“ž PHONE NUMBER</div>

                    <div class="info-value"><?php echo $user['phone_number'] ? htmlspecialchars($user['phone_number']) : 'Not set'; ?></div>

                </div>



                <div class="info-group">

                    <div class="info-label">ðŸŽ‚ DATE OF BIRTH</div>

                    <div class="info-value"><?php echo $user['date_of_birth'] ? htmlspecialchars($user['date_of_birth']) : 'Not set'; ?></div>

                </div>



                <div class="info-group">

                    <div class="info-label">âš™ ROLE</div>

                    <div class="info-value"><?php echo strtoupper($user['role']); ?></div>

                </div>



                <div class="info-group">

                    <div class="info-label">âœ“ STATUS</div>

                    <div class="info-value"><?php echo strtoupper($user['status']); ?></div>

                </div>

            </div>



            <div class="profile-actions">

                <button class="btn-profile btn-edit" onclick="window.location.href='updateprofile.php'">

                    âœ MODIFIER PROFIL

                </button>

                <button class="btn-profile btn-back" onclick="window.location.href='index.php'">

                    â† RETOUR

                </button>

            </div>

        </div>

    </div>



    <div id="customNotification"></div>



    <script src="../../notification.js"></script>

    <script>

        document.getElementById('logoutBtn').addEventListener('click', function(e) {

            e.preventDefault();

            

            showNotification('Are you sure you want to logout?', 'warning', true, function() {

                const formData = new FormData();

                formData.append('action', 'logout');

                

                fetch('../../controller/user_controller.php', {

                    method: 'POST',

                    body: formData

                })

                .then(response => response.json())

                .then(data => {

                    showNotification('Logout successful!', 'success');

                    setTimeout(() => {

                        window.location.href = 'index.php';

                    }, 1500);

                })

                .catch(error => {

                    console.error('Error:', error);

                    window.location.href = 'index.php';

                });

            });

        });

    </script>

</body>

</html>