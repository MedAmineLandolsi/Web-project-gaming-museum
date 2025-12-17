<?php
/**
 * Fichier de test pour vérifier que le template admin s'affiche correctement
 * Accédez à : http://localhost/projet-web/ProjetWeb/test-admin-template.php
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Admin Template</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/projet-web/ProjetWeb/assets/css/admin.css">
    <style>
        .test-info {
            position: fixed;
            top: 10px;
            right: 10px;
            background: rgba(0, 255, 65, 0.2);
            border: 2px solid #00FF41;
            padding: 1rem;
            z-index: 10000;
            font-family: 'VT323', monospace;
            font-size: 1rem;
            color: #00FF41;
        }
    </style>
</head>
<body>
    <div class="test-info">
        <strong>TEST MODE</strong><br>
        CSS chargé: <span id="css-status">Vérification...</span>
    </div>
    
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="admin-logo">
                <div class="logo-box">⚙</div>
                <div class="admin-title">
                    <h2>SYSTÈME ADMIN</h2>
                    <div class="admin-badge">PANEL DE CONTROLE</div>
                </div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <ul class="nav-list">
                <li class="nav-item active">
                    <a href="#">
                        <span class="nav-icon">📊</span>
                        <span class="nav-text">DASHBOARD</span>
                        <span class="nav-count">0</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#">
                        <span class="nav-icon">📋</span>
                        <span class="nav-text">RÉCLAMATIONS</span>
                        <span class="nav-count">0</span>
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="top-bar">
            <div class="top-bar-left">
                <h1 class="page-title">Test Template Admin</h1>
            </div>
        </div>

        <div class="stats-overview">
            <div class="stat-card stat-primary">
                <div class="stat-icon">📋</div>
                <div class="stat-content">
                    <div class="stat-label">TEST</div>
                    <div class="stat-value">0</div>
                    <div class="stat-change positive">Test réussi</div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Vérifier si le CSS est chargé
        window.addEventListener('load', function() {
            const body = document.body;
            const computedStyle = window.getComputedStyle(body);
            const bgColor = computedStyle.backgroundColor;
            
            // Si le background est noir ou très sombre, le CSS est chargé
            if (bgColor.includes('rgb(10, 10, 10)') || bgColor.includes('rgb(5, 5, 5)')) {
                document.getElementById('css-status').textContent = '✅ CHARGÉ';
                document.getElementById('css-status').style.color = '#00FF41';
            } else {
                document.getElementById('css-status').textContent = '❌ NON CHARGÉ';
                document.getElementById('css-status').style.color = '#FF0055';
            }
        });
    </script>
</body>
</html>

