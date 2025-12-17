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
    <title>Modifier Profil - Ludology Vault</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <style>
        .update-container {
            max-width: 900px;
            margin: 4rem auto;
            padding: 2rem;
            position: relative;
            z-index: 10;
        }

        .update-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .update-title {
            font-size: 2rem;
            color: var(--primary-green);
            text-shadow: 0 0 20px var(--primary-green);
            margin-bottom: 0.5rem;
        }

        .update-subtitle {
            font-size: 0.7rem;
            color: var(--text-gray);
            font-family: 'VT323', monospace;
            font-size: 1.2rem;
        }

        .update-card {
            background: linear-gradient(135deg, var(--card-bg), var(--darker-bg));
            border: 3px solid var(--primary-green);
            padding: 3rem;
            box-shadow: 0 20px 60px rgba(0, 255, 65, 0.4);
            position: relative;
            overflow: hidden;
        }

        .update-card::before {
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

        .form-section {
            margin-bottom: 2.5rem;
        }

        .section-title {
            font-size: 0.9rem;
            color: var(--secondary-purple);
            text-shadow: 0 0 10px var(--secondary-purple);
            margin-bottom: 1.5rem;
            padding-bottom: 0.8rem;
            border-bottom: 2px solid var(--secondary-purple);
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .form-group {
            position: relative;
        }

        .form-label {
            display: block;
            font-size: 0.6rem;
            color: var(--primary-green);
            margin-bottom: 0.8rem;
            text-shadow: 0 0 5px var(--primary-green);
        }

        .form-input {
            width: 100%;
            padding: 1rem 1.2rem;
            padding-left: 3.2rem;
            height: 50px;
            font-size: 0.7rem;
            font-family: 'Press Start 2P', cursive;
            letter-spacing: 1px;
            color: var(--text-white);
            background-color: rgba(0, 0, 0, 0.4);
            border: 2px solid rgba(0, 255, 65, 0.3);
            transition: all 300ms ease;
            box-shadow: inset 0 2px 8px rgba(0, 0, 0, 0.6);
        }

        .form-input:focus {
            border-color: var(--primary-green);
            box-shadow: 
                0 0 20px rgba(0, 255, 65, 0.4), 
                inset 0 2px 8px rgba(0, 0, 0, 0.6);
            background-color: rgba(0, 0, 0, 0.6);
            outline: none;
        }

        .form-input::placeholder {
            color: var(--text-gray);
            opacity: 0.5;
            font-size: 0.6rem;
        }

        .input-icon {
            position: absolute;
            bottom: 13px;
            left: 1rem;
            font-size: 18px;
            color: var(--primary-green);
            pointer-events: none;
        }

        .form-group:focus-within .input-icon {
            text-shadow: 0 0 10px var(--primary-green);
        }

        input[type="date"].form-input {
            color-scheme: dark;
        }

        input[type="date"].form-input::-webkit-calendar-picker-indicator {
            filter: invert(1);
            cursor: pointer;
        }

        .profile-picture-section {
            text-align: center;
            padding: 2rem;
            border: 2px dashed var(--border-color);
            background: rgba(0, 0, 0, 0.3);
            margin-bottom: 2rem;
            transition: all 0.3s;
        }

        .profile-picture-section:hover {
            border-color: var(--primary-green);
            background: rgba(0, 255, 65, 0.05);
        }

        .current-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 3px solid var(--primary-green);
            background: linear-gradient(135deg, var(--primary-green), var(--secondary-purple));
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--darker-bg);
            font-size: 2.5rem;
            font-weight: bold;
            box-shadow: 0 0 20px rgba(0, 255, 65, 0.5);
            overflow: hidden;
            margin-bottom: 1rem;
        }

        .current-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .file-input-wrapper {
            position: relative;
            display: inline-block;
            margin-top: 1rem;
        }

        .file-input {
            position: absolute;
            left: -9999px;
        }

        .file-input-label {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            background: transparent;
            border: 2px solid var(--secondary-purple);
            color: var(--secondary-purple);
            font-family: 'Press Start 2P', cursive;
            font-size: 0.6rem;
            cursor: pointer;
            transition: all 0.3s;
        }

        .file-input-label:hover {
            background: var(--secondary-purple);
            color: var(--darker-bg);
            box-shadow: 0 0 20px rgba(189, 0, 255, 0.5);
        }

        .password-section {
            background: rgba(255, 0, 110, 0.05);
            border: 2px solid var(--accent-pink);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .form-actions {
            display: flex;
            gap: 1.5rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 2px solid var(--border-color);
        }

        .btn-update {
            padding: 1.2rem 2.5rem;
            font-family: 'Press Start 2P', cursive;
            font-size: 0.7rem;
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid;
            background: transparent;
        }

        .btn-save {
            border-color: var(--primary-green);
            color: var(--primary-green);
        }

        .btn-save:hover {
            background: var(--primary-green);
            color: var(--darker-bg);
            box-shadow: 0 0 30px rgba(0, 255, 65, 0.6);
            transform: translateY(-3px);
        }

        .btn-cancel {
            border-color: var(--accent-pink);
            color: var(--accent-pink);
        }

        .btn-cancel:hover {
            background: var(--accent-pink);
            color: var(--darker-bg);
            box-shadow: 0 0 30px rgba(255, 0, 110, 0.6);
            transform: translateY(-3px);
        }

        .error-message {
            display: none;
            color: #FF0055;
            font-size: 0.5rem;
            margin-top: 0.5rem;
            font-family: 'VT323', monospace;
            font-size: 0.9rem;
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
                <button class="btn-auth" onclick="window.location.href='profile.php'">
                    <span class="btn-icon">👤</span> MON PROFIL
                </button>
            </div>
        </div>
    </nav>

    <div class="update-container">
        <div class="update-header">
            <h2 class="update-title">◄ MODIFIER PROFIL ►</h2>
            <p class="update-subtitle">Mettez à jour vos informations personnelles</p>
        </div>

        <div class="update-card">
            <form id="updateProfileForm">
                <div class="profile-picture-section">
                    <div class="current-avatar">
                        <?php if ($user['profile_picture_url'] && file_exists("../../uploads/" . $user['profile_picture_url'])): ?>
                            <img src="../../uploads/<?php echo htmlspecialchars($user['profile_picture_url']); ?>" alt="Profile" id="avatarPreview">
                        <?php else: ?>
                            <span id="avatarInitials"><?php echo strtoupper(substr($user['username'], 0, 2)); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="file-input-wrapper">
                        <input type="file" id="profilePicture" name="profile_picture" class="file-input" accept="image/*">
                        <label for="profilePicture" class="file-input-label">📷 CHANGER PHOTO</label>
                    </div>
                </div>

                <div class="form-section">
                    <h3 class="section-title">◄ INFORMATIONS PERSONNELLES ►</h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">FIRST NAME</label>
                            <input type="text" name="first_name" class="form-input" value="<?php echo htmlspecialchars($user['first_name']); ?>" placeholder="Enter first name">
                            <i class="input-icon uil uil-user"></i>
                            <span class="error-message"></span>
                        </div>

                        <div class="form-group">
                            <label class="form-label">LAST NAME</label>
                            <input type="text" name="last_name" class="form-input" value="<?php echo htmlspecialchars($user['last_name']); ?>" placeholder="Enter last name">
                            <i class="input-icon uil uil-user"></i>
                            <span class="error-message"></span>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">EMAIL</label>
                            <input type="email" name="email" class="form-input" value="<?php echo htmlspecialchars($user['email']); ?>" placeholder="Enter email" required>
                            <i class="input-icon uil uil-at"></i>
                            <span class="error-message"></span>
                        </div>

                        <div class="form-group">
                            <label class="form-label">PHONE NUMBER</label>
                            <input type="tel" name="phone_number" class="form-input" value="<?php echo htmlspecialchars($user['phone_number']); ?>" placeholder="Enter phone number">
                            <i class="input-icon uil uil-phone"></i>
                            <span class="error-message"></span>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">DATE OF BIRTH</label>
                            <input type="date" name="date_of_birth" class="form-input" value="<?php echo htmlspecialchars($user['date_of_birth']); ?>">
                            <i class="input-icon uil uil-calendar-alt"></i>
                            <span class="error-message"></span>
                        </div>
                    </div>
                </div>

                <div class="password-section">
                    <h3 class="section-title">◄ CHANGER MOT DE PASSE ►</h3>
                    <p class="update-subtitle" style="margin-bottom: 1.5rem;">Laissez vide pour conserver le mot de passe actuel</p>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">CURRENT PASSWORD</label>
                            <input type="password" name="current_password" id="currentPassword" class="form-input" placeholder="Enter current password">
                            <i class="input-icon uil uil-lock-alt"></i>
                            <span class="error-message"></span>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">NEW PASSWORD</label>
                            <input type="password" name="new_password" id="newPassword" class="form-input" placeholder="Enter new password">
                            <i class="input-icon uil uil-lock-access"></i>
                            <span class="error-message"></span>
                        </div>

                        <div class="form-group">
                            <label class="form-label">CONFIRM PASSWORD</label>
                            <input type="password" name="confirm_password" id="confirmPassword" class="form-input" placeholder="Confirm new password">
                            <i class="input-icon uil uil-lock-access"></i>
                            <span class="error-message"></span>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-update btn-save">💾 SAUVEGARDER</button>
                    <button type="button" class="btn-update btn-cancel" onclick="window.location.href='profile.php'">✖ ANNULER</button>
                </div>
            </form>
        </div>
    </div>

    <div id="customNotification"></div>

    <script src="../../notification.js"></script>
    <script>
        document.getElementById('profilePicture').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const avatarPreview = document.getElementById('avatarPreview');
                    const avatarInitials = document.getElementById('avatarInitials');
                    
                    if (avatarPreview) {
                        avatarPreview.src = e.target.result;
                    } else {
                        const currentAvatar = document.querySelector('.current-avatar');
                        currentAvatar.innerHTML = '<img src="' + e.target.result + '" alt="Profile" id="avatarPreview">';
                    }
                };
                reader.readAsDataURL(file);
            }
        });

        document.getElementById('updateProfileForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const currentPassword = document.getElementById('currentPassword').value;
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            // Validate password change if attempted
            if (newPassword || confirmPassword || currentPassword) {
                if (!currentPassword) {
                    showNotification('Please enter your current password to change password', 'error');
                    return;
                }
                
                if (!newPassword || !confirmPassword) {
                    showNotification('Please enter and confirm your new password', 'error');
                    return;
                }
                
                if (newPassword !== confirmPassword) {
                    showNotification('New passwords do not match', 'error');
                    return;
                }
                
                if (newPassword.length < 8) {
                    showNotification('New password must be at least 8 characters', 'error');
                    return;
                }
            }
            
            // Create FormData with all form fields including file
            const formData = new FormData(this);
            formData.append('action', 'updateProfile');
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span>SAVING...</span>';
            submitBtn.disabled = true;
            
            // Update profile
            fetch('../../controller/user_controller.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // If password change was requested, handle it separately
                    if (currentPassword && newPassword) {
                        const passwordData = new FormData();
                        passwordData.append('action', 'changePassword');
                        passwordData.append('current_password', currentPassword);
                        passwordData.append('new_password', newPassword);
                        
                        return fetch('../../controller/user_controller.php', {
                            method: 'POST',
                            body: passwordData
                        }).then(response => response.json());
                    }
                    return data;
                } else {
                    throw new Error(data.message);
                }
            })
            .then(data => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                
                showNotification(data.message || 'Profile updated successfully!', 'success');
                setTimeout(() => {
                    window.location.href = 'profile.php';
                }, 2000);
            })
            .catch(error => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                showNotification(error.message || 'An error occurred', 'error');
            });
        });
    </script>
</body>
</html>
