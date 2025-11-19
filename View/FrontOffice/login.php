<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - User Management</title>
    <link rel="stylesheet" href="../../assets/style.css">
</head>
<body>
    <header class="header">
        <nav class="nav container">
            <div class="logo">
                <div class="logo-img"></div>
            </div>
            <ul class="nav-links">
                <li><a href="login.php" class="active">Login</a></li>
                <li><a href="signup.php">Sign Up</a></li>
            </ul>
        </nav>
    </header>

    <main class="main-content">
        <div class="container">
            <div class="form-container">
                <h2>Login to Your Account</h2>
                
                <?php if(isset($error)): ?>
                    <div class="error-message"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if(isset($_GET['message'])): ?>
                    <div class="success-message"><?php echo $_GET['message']; ?></div>
                <?php endif; ?>
                
                <form method="POST" action="test_connection.php">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" placeholder="Enter your email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Login</button>
                </form>
                
                <div class="form-links">
                    <p>Don't have an account? <a href="signup.php">Sign up here</a></p>
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