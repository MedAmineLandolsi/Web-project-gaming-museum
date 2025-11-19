<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deactivate Account - User Management</title>
    <link rel="stylesheet" href="../../assets/style.css">
</head>
<body>
    <header class="header">
        <nav class="nav container">
            <div class="logo">
                <div class="logo-img"></div>
            </div>
            <ul class="nav-links">
                <li><a href="profile.php">Profile</a></li>
                <li><a href="changePassword.php">Change Password</a></li>
                <li><a href="deactivate.php" class="active">Deactivate Account</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main class="main-content">
        <div class="container">
            <div class="form-container">
                <h2>Deactivate Your Account</h2>
                
                <?php if(isset($error)): ?>
                    <div class="error-message"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <div class="warning-message" style="background: var(--warning); color: var(--dark); padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                    <strong>Warning:</strong> This action will deactivate your account. You will not be able to login until you contact support to reactivate.
                </div>
                
                <p>Are you sure you want to deactivate your account?</p>
                
                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <a href="profile.php" class="btn" style="background: var(--gray); color: var(--dark); text-decoration: none;">Cancel</a>
                    <a href="deactivate.php?confirm=true" class="btn btn-primary" style="background: var(--danger);">Yes, Deactivate Account</a>
                </div>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-bottom">
                <p>&copy; 2025 User Management System. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>