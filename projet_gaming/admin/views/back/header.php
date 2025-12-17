<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Gaming Events</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-sidebar {
            background: var(--darker);
            width: 250px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            padding: 2rem 1rem;
        }
        
        .admin-content {
            margin-left: 250px;
            padding: 2rem;
        }
        
        .admin-nav {
            list-style: none;
            margin-top: 2rem;
        }
        
        .admin-nav li {
            margin-bottom: 1rem;
        }
        
        .admin-nav a {
            color: var(--light);
            text-decoration: none;
            padding: 0.75rem 1rem;
            display: block;
            border-radius: 5px;
            transition: background 0.3s;
        }
        
        .admin-nav a:hover, .admin-nav a.active {
            background: var(--primary);
            color: var(--dark);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin: 2rem 0;
        }
        
        .stat-card {
            background: var(--card-bg);
            padding: 2rem;
            border-radius: 10px;
            text-align: center;
            border: 1px solid var(--border);
        }
        
        .stat-number {
            font-size: 3rem;
            font-weight: bold;
            color: var(--primary);
            margin: 1rem 0;
        }
        
        .stat-link {
            color: var(--secondary);
            text-decoration: none;
        }
        
        .table-container {
            background: var(--card-bg);
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid var(--border);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }
        
        th {
            background: rgba(0,0,0,0.3);
            color: var(--primary);
        }
        
        .btn-edit, .btn-delete {
            padding: 0.5rem 1rem;
            border-radius: 5px;
            text-decoration: none;
            font-size: 0.8rem;
            margin-right: 0.5rem;
        }
        
        .btn-edit {
            background: var(--warning);
            color: var(--dark);
        }
        
        .btn-delete {
            background: var(--danger);
            color: white;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--light);
        }
        
        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border);
            border-radius: 5px;
            background: rgba(255,255,255,0.1);
            color: white;
        }
    </style>
</head>
<body>
    <div class="admin-sidebar">
        <h2 style="color: var(--primary);">Administration</h2>
        <ul class="admin-nav">
            <li><a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Dashboard</a></li>
            <li><a href="evenements.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'evenements.php' ? 'active' : ''; ?>">Événements</a></li>
            <li><a href="participations.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'participations.php' ? 'active' : ''; ?>">Participations</a></li>
            <li><a href="../index.php" style="color: var(--secondary);">← Retour au site</a></li>
        </ul>
    </div>
    
    <div class="admin-content">