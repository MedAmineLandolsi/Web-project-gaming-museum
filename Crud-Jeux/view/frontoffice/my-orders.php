<?php
session_start();
require_once '../../config.php';
require_once '../../controller/user_controller.php';
require_once '../../controller/JeuxController.php';
require_once '../../controller/CommandeController.php';

// Initialize controllers
$userController = new UserController();
$jeuxController = new JeuxController();
$commandeController = new CommandeController();

// Check if user is logged in
$isLoggedIn = $userController->isLoggedIn();
$user = null;
$username = '';
$profilePicture = '';
$role = '';

if ($isLoggedIn) {
    $result = $userController->viewProfile($_SESSION['user_id']);
    $user = $result['user'];
    $username = $user['username'];
    $profilePicture = $user['profile_picture_url'] ?? '';
    $role = $user['role'];
} else {
    // Redirect to login if not logged in
    header('Location: login.php?redirect=my-orders.php');
    exit();
}

// Get user order statistics
$orderStats = $commandeController->getUserOrderCount($_SESSION['user_id']);

// Get filter parameters
$statusFilter = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';
$sortBy = $_GET['sortby'] ?? 'Date';
$order = $_GET['order'] ?? 'desc';

// Get user's orders
$userOrders = $commandeController->getUserCommandes($_SESSION['user_id']);

// Apply filters
$filteredOrders = $userOrders;

// Filter by status
if ($statusFilter && $statusFilter !== 'all') {
    $filteredOrders = array_filter($filteredOrders, function($order) use ($statusFilter) {
        return $order['statut'] === $statusFilter;
    });
}

// Filter by search
if ($search) {
    $search = strtolower($search);
    $filteredOrders = array_filter($filteredOrders, function($order) use ($search) {
        return strpos(strtolower($order['order_ref']), $search) !== false ||
               strpos(strtolower($order['produit_nom']), $search) !== false;
    });
}

// Sort orders
usort($filteredOrders, function($a, $b) use ($sortBy, $order) {
    $valueA = $a[$sortBy] ?? '';
    $valueB = $b[$sortBy] ?? '';
    
    if ($sortBy === 'Total') {
        $valueA = floatval($valueA);
        $valueB = floatval($valueB);
    }
    
    if ($order === 'asc') {
        return $valueA <=> $valueB;
    } else {
        return $valueB <=> $valueA;
    }
});

// Get all statuses for filter dropdown
$allStatuses = $commandeController->getStatuses();

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_action'])) {
    header('Content-Type: application/json');
    
    switch ($_POST['ajax_action']) {
        case 'get_order_details':
            if (isset($_POST['order_id'])) {
                $orderDetails = $commandeController->getCommandeDetails($_POST['order_id']);
                echo json_encode($orderDetails);
            } else {
                echo json_encode(['success' => false, 'error' => 'Order ID not provided']);
            }
            exit();
            
        case 'cancel_order':
            if (isset($_POST['order_id'])) {
                $orderId = $_POST['order_id'];
                $order = $commandeController->getCommandeById($orderId);
                
                if ($order && $order['statut'] === 'pending') {
                    $result = $commandeController->updateCommande($orderId, ['statut' => 'cancelled']);
                    if ($result['success']) {
                        echo json_encode(['success' => true, 'message' => 'Order cancelled successfully']);
                    } else {
                        echo json_encode(['success' => false, 'error' => 'Failed to cancel order']);
                    }
                } else {
                    echo json_encode(['success' => false, 'error' => 'Order cannot be cancelled']);
                }
            } else {
                echo json_encode(['success' => false, 'error' => 'Order ID not provided']);
            }
            exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Commandes - LUDOLOGY VAULT</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    <style>
        .pixel-placeholder img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 6px;
        }

        .game-card {
            position: relative;
        }

        .game-desc {
            opacity: 0;
            height: 0;
            overflow: hidden;
            transform: translateY(10px);
            transition: all 0.35s ease-in-out;
            pointer-events: none;
        }

        .game-card:hover .game-desc {
            opacity: 1;
            height: auto;
            transform: translateY(0);
        }
        
        /* User menu styles (copied from index) */
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
        
        /* Header search bar */
        .header-search {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }
        
        .header-search-input {
            padding: 0.5rem 1rem;
            background: rgba(0, 255, 65, 0.1);
            border: 2px solid var(--primary-green);
            color: var(--text-white);
            font-family: 'VT323', monospace;
            font-size: 1rem;
            width: 200px;
        }
        
        .header-search-btn {
            padding: 0.5rem 1rem;
            background: var(--primary-green);
            border: 2px solid var(--primary-green);
            color: var(--darker-bg);
            font-family: 'Press Start 2P', cursive;
            font-size: 0.5rem;
            cursor: pointer;
        }
        
        .header-search-btn:hover {
            background: #00cc33;
        }
        
        /* Games header */
        .games-header {
            margin: 2rem 0;
            text-align: center;
        }
        
        .games-title {
            font-size: 2rem;
            color: var(--primary-green);
            text-shadow: 0 0 10px var(--primary-green);
            margin-bottom: 1rem;
        }
        
        .games-count {
            font-size: 1rem;
            color: var(--text-gray);
            font-family: 'VT323', monospace;
        }
        
        /* Sort and filter bar */
        .sort-filter-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 2rem 0;
            padding: 1rem;
            background: rgba(0, 255, 65, 0.05);
            border: 2px solid var(--border-color);
        }
    </style>
    <style> 
        /* Main container */
        .main-container {
            max-width: 1200px;
            margin: 120px auto 60px;
            padding: 0 20px;
        }
        
        /* Page header */
        .page-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .page-title {
            font-size: 2rem;
            color: var(--primary-green);
            text-shadow: 0 0 10px var(--primary-green);
            margin-bottom: 1rem;
            font-family: 'Press Start 2P', cursive;
        }
        
        .page-subtitle {
            font-size: 1.2rem;
            color: var(--text-gray);
            font-family: 'VT323', monospace;
        }
        
        /* Stats cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }
        
        .stat-card {
            background: linear-gradient(135deg, rgba(0, 255, 65, 0.05), rgba(189, 0, 255, 0.05));
            border: 2px solid var(--border-color);
            border-radius: 8px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s;
        }
        
        .stat-card:hover {
            border-color: var(--primary-green);
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 255, 65, 0.2);
        }
        
        .stat-icon {
            font-size: 2rem;
            margin-bottom: 1rem;
        }
        
        .stat-number {
            font-family: 'Press Start 2P', cursive;
            font-size: 1.5rem;
            color: var(--secondary-purple);
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            font-family: 'VT323', monospace;
            font-size: 1.1rem;
            color: var(--text-light-gray);
        }
        
        /* Filters section */
        .filters-section {
            background: rgba(0, 255, 65, 0.05);
            border: 2px solid var(--border-color);
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .filters-title {
            font-family: 'Press Start 2P', cursive;
            font-size: 0.9rem;
            color: var(--primary-green);
            margin-bottom: 1.5rem;
        }
        
        .filters-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        
        .filter-group {
            display: flex;
            flex-direction: column;
        }
        
        .filter-label {
            margin-bottom: 0.5rem;
            color: var(--text-gray);
            font-family: 'VT323', monospace;
            font-size: 1rem;
        }
        
        .filter-select,
        .filter-input {
            padding: 0.5rem 1rem;
            background: var(--darker-bg);
            border: 2px solid var(--border-color);
            color: var(--text-white);
            font-family: 'VT323', monospace;
            font-size: 1rem;
        }
        
        .filter-select:focus,
        .filter-input:focus {
            border-color: var(--primary-green);
            outline: none;
        }
        
        .filter-buttons {
            display: flex;
            gap: 1rem;
            align-items: flex-end;
        }
        
        .filter-btn {
            padding: 0.5rem 1rem;
            background: transparent;
            border: 2px solid var(--primary-green);
            color: var(--primary-green);
            font-family: 'Press Start 2P', cursive;
            font-size: 0.6rem;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .filter-btn:hover {
            background: rgba(0, 255, 65, 0.1);
        }
        
        .filter-btn.primary {
            background: var(--primary-green);
            color: var(--darker-bg);
        }
        
        .filter-btn.primary:hover {
            background: #00cc33;
        }
        
        /* Orders table */
        .orders-container {
            background: rgba(0, 255, 65, 0.05);
            border: 2px solid var(--border-color);
            border-radius: 8px;
            padding: 1.5rem;
            overflow-x: auto;
        }
        
        .orders-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .orders-table th {
            padding: 1rem;
            text-align: left;
            font-family: 'Press Start 2P', cursive;
            font-size: 0.6rem;
            color: var(--primary-green);
            border-bottom: 2px solid var(--border-color);
        }
        
        .orders-table th.sortable {
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .orders-table th.sortable:hover {
            background: rgba(0, 255, 65, 0.05);
        }
        
        .orders-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
            font-family: 'VT323', monospace;
            font-size: 1rem;
        }
        
        .orders-table tbody tr:hover {
            background: rgba(0, 255, 65, 0.02);
        }
        
        /* Order cells styling */
        .order-ref {
            font-family: 'Press Start 2P', cursive;
            font-size: 0.6rem;
            color: var(--secondary-purple);
        }
        
        .order-game {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .game-image {
            width: 60px;
            height: 60px;
            border-radius: 6px;
            overflow: hidden;
            border: 2px solid var(--primary-green);
            flex-shrink: 0;
        }
        
        .game-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .game-name {
            font-family: 'Press Start 2P', cursive;
            font-size: 0.7rem;
            color: var(--text-white);
        }
        
        .order-price {
            color: var(--secondary-purple);
            font-weight: bold;
        }
        
        .order-date {
            color: var(--text-gray);
        }
        
        /* Status badges */
        .status-badge {
            display: inline-block;
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-family: 'Press Start 2P', cursive;
            font-size: 0.5rem;
            text-transform: uppercase;
        }
        
        .status-pending { background: rgba(255, 158, 0, 0.1); color: #ff9e00; border: 1px solid #ff9e00; }
        .status-processing { background: rgba(0, 150, 255, 0.1); color: #0096ff; border: 1px solid #0096ff; }
        .status-shipped { background: rgba(0, 255, 65, 0.1); color: var(--primary-green); border: 1px solid var(--primary-green); }
        .status-delivered { background: rgba(189, 0, 255, 0.1); color: var(--secondary-purple); border: 1px solid var(--secondary-purple); }
        .status-completed { background: rgba(0, 200, 83, 0.1); color: #00c853; border: 1px solid #00c853; }
        .status-cancelled { background: rgba(255, 0, 110, 0.1); color: var(--accent-pink); border: 1px solid var(--accent-pink); }
        
        /* Actions */
        .order-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .action-btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            font-family: 'Press Start 2P', cursive;
            font-size: 0.5rem;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }
        
        .action-btn.view {
            background: rgba(0, 255, 65, 0.1);
            border: 1px solid var(--primary-green);
            color: var(--primary-green);
        }
        
        .action-btn.view:hover {
            background: rgba(0, 255, 65, 0.2);
        }
        
        .action-btn.track {
            background: rgba(189, 0, 255, 0.1);
            border: 1px solid var(--secondary-purple);
            color: var(--secondary-purple);
        }
        
        .action-btn.track:hover {
            background: rgba(189, 0, 255, 0.2);
        }
        
        .action-btn.cancel {
            background: rgba(255, 0, 110, 0.1);
            border: 1px solid var(--accent-pink);
            color: var(--accent-pink);
        }
        
        .action-btn.cancel:hover {
            background: rgba(255, 0, 110, 0.2);
        }
        
        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 4rem;
        }
        
        .empty-icon {
            font-size: 4rem;
            color: var(--text-gray);
            margin-bottom: 2rem;
            opacity: 0.5;
        }
        
        .empty-title {
            font-family: 'Press Start 2P', cursive;
            font-size: 1rem;
            color: var(--text-gray);
            margin-bottom: 1rem;
        }
        
        .empty-message {
            font-family: 'VT323', monospace;
            font-size: 1.2rem;
            color: var(--text-light-gray);
            margin-bottom: 2rem;
        }
        
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 2000;
            align-items: center;
            justify-content: center;
        }
        
        .modal-content {
            background: var(--darker-bg);
            border: 2px solid var(--primary-green);
            border-radius: 8px;
            width: 90%;
            max-width: 800px;
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .modal-header {
            padding: 1.5rem;
            border-bottom: 2px solid var(--primary-green);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-title {
            font-family: 'Press Start 2P', cursive;
            font-size: 1rem;
            color: var(--primary-green);
        }
        
        .modal-close {
            background: none;
            border: none;
            color: var(--text-white);
            font-size: 1.5rem;
            cursor: pointer;
            line-height: 1;
        }
        
        .modal-body {
            padding: 1.5rem;
        }
        
        /* Order details */
        .order-details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        @media (max-width: 768px) {
            .order-details-grid {
                grid-template-columns: 1fr;
            }
        }
        
        .detail-section {
            background: rgba(0, 255, 65, 0.05);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            padding: 1rem;
        }
        
        .detail-section h3 {
            font-family: 'Press Start 2P', cursive;
            font-size: 0.7rem;
            color: var(--primary-green);
            margin-bottom: 1rem;
        }
        
        .detail-item {
            margin-bottom: 0.8rem;
        }
        
        .detail-label {
            font-family: 'VT323', monospace;
            color: var(--text-gray);
            font-size: 0.9rem;
            display: block;
        }
        
        .detail-value {
            font-family: 'Press Start 2P', cursive;
            color: var(--text-white);
            font-size: 0.7rem;
        }
        
        .timeline {
            display: flex;
            justify-content: space-between;
            margin-top: 1rem;
        }
        
        .timeline-step {
            text-align: center;
            flex: 1;
            opacity: 0.5;
            position: relative;
        }
        
        .timeline-step.active {
            opacity: 1;
        }
        
        .timeline-step::after {
            content: '';
            position: absolute;
            top: 15px;
            right: -50%;
            width: 100%;
            height: 2px;
            background: var(--border-color);
            z-index: -1;
        }
        
        .timeline-step:last-child::after {
            display: none;
        }
        
        .timeline-icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: var(--darker-bg);
            border: 2px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.5rem;
            font-size: 0.8rem;
        }
        
        .timeline-step.active .timeline-icon {
            border-color: var(--primary-green);
            background: rgba(0, 255, 65, 0.1);
        }
        
        .timeline-label {
            font-family: 'VT323', monospace;
            font-size: 0.8rem;
            color: var(--text-gray);
        }
        
        /* Messages */
        .message {
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 4px;
            text-align: center;
            font-family: 'VT323', monospace;
            font-size: 1rem;
        }
        
        .message-success {
            background: rgba(0, 255, 65, 0.1);
            border: 2px solid var(--primary-green);
            color: var(--primary-green);
        }
        
        .message-error {
            background: rgba(255, 0, 110, 0.1);
            border: 2px solid var(--danger-red);
            color: var(--danger-red);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .filters-form {
                grid-template-columns: 1fr;
            }
            
            .filter-buttons {
                justify-content: center;
            }
            
            .orders-table {
                display: block;
                overflow-x: auto;
            }
            
            .order-game {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
            
            .order-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- Background particles -->
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

    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-left">
                <div class="logo-container">
                    <div class="logo-placeholder">🎮</div>
                    <h1 class="site-title">LUDOLOGY VAULT</h1>
                </div>
            </div>
            
            <div class="nav-center">
                <ul class="nav-menu">
                    <li><a href="index.php">HOME</a></li>
                    <li><a href="games.php">JEUX</a></li>
                    
                    <?php if ($isLoggedIn): ?>
                    <li><a href="my-orders.php" class="active">MES COMMANDES</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            
            <div class="nav-right">
                <?php if ($isLoggedIn): ?>
                    <div class="user-menu">
                        <button class="user-profile-btn">
                            <div class="user-avatar">
                                <?php if ($profilePicture && file_exists("../../uploads/" . $profilePicture)): ?>
                                    <img src="../../uploads/<?php echo htmlspecialchars($profilePicture); ?>" alt="Profile">
                                <?php else: ?>
                                    <?php echo strtoupper(substr($username, 0, 2)); ?>
                                <?php endif; ?>
                            </div>
                            <span class="user-name"><?php echo htmlspecialchars($username); ?></span>
                            <span class="dropdown-icon">▼</span>
                        </button>
                        
                        <div class="user-dropdown">
                            <div class="dropdown-header">
                                <div class="dropdown-header-title">WELCOME BACK</div>
                                <div class="dropdown-header-subtitle"><?php echo htmlspecialchars($username); ?></div>
                            </div>
                            <ul class="dropdown-menu-list">
                                <li class="dropdown-menu-item">
                                    <a href="profile.php" class="dropdown-menu-link">
                                        <span class="dropdown-icon-left">👤</span>
                                        MON PROFIL
                                    </a>
                                </li>
                                <li class="dropdown-menu-item">
                                    <a href="my-orders.php" class="dropdown-menu-link">
                                        <span class="dropdown-icon-left">🛒</span>
                                        MES COMMANDES
                                    </a>
                                </li>
                                <li class="dropdown-menu-item">
                                    <a href="../../../gaming_museum/view/frontoffice/index.php" class="dropdown-menu-link">
                                        <span class="dropdown-icon-left">🎮</span>
                                        gaming museum
                                    </a>
                                </li>
                                <?php if ($role === 'admin'): ?>
                                <li class="dropdown-menu-item">
                                    <a href="../backoffice/dashboard.php" class="dropdown-menu-link admin">
                                        <span class="dropdown-icon-left">⚙</span>
                                        ADMIN DASHBOARD
                                    </a>
                                </li>
                                <?php endif; ?>
                                <li class="dropdown-menu-item">
                                    <a href="#" class="dropdown-menu-link logout" id="logoutBtn">
                                        <span class="dropdown-icon-left">🚪</span>
                                        DECONNEXION
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="login.php" style="text-decoration: none;">
                        <button class="btn-auth">
                            <span class="btn-icon">▶</span> SIGN IN / SIGN UP
                        </button>
                    </a>
                <?php endif; ?>
                <a href="cart.php" style="text-decoration: none; margin-left: 1rem;">
                    <button class="btn-auth">
                        <span class="btn-icon">🛒</span> 
                    </button>
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-container">
        <!-- Page Header -->
        <header class="page-header">
            <h1 class="page-title">📦 MES COMMANDES</h1>
            <p class="page-subtitle">Suivez l'état de toutes vos commandes</p>
        </header>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">📊</div>
                <div class="stat-number"><?php echo $orderStats['order_count']; ?></div>
                <div class="stat-label">Commandes passées</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">💰</div>
                <div class="stat-number"><?php echo number_format($orderStats['total_spent'], 2); ?> €</div>
                <div class="stat-label">Total dépensé</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">🎮</div>
                <div class="stat-number"><?php echo $orderStats['games_count']; ?></div>
                <div class="stat-label">Jeux achetés</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">⭐</div>
                <div class="stat-number">
                    <?php 
                    $avgPerOrder = $orderStats['order_count'] > 0 ? $orderStats['total_spent'] / $orderStats['order_count'] : 0;
                    echo number_format($avgPerOrder, 2); 
                    ?> €
                </div>
                <div class="stat-label">Moyenne par commande</div>
            </div>
        </div>

        <!-- Filters Section -->
        <section class="filters-section">
            <h2 class="filters-title">FILTRER ET TRIER</h2>
            <form method="GET" action="my-orders.php" class="filters-form">
                <div class="filter-group">
                    <label class="filter-label">Statut</label>
                    <select name="status" class="filter-select" onchange="this.form.submit()">
                        <option value="all" <?php echo $statusFilter === 'all' ? 'selected' : ''; ?>>Tous les statuts</option>
                        <?php foreach ($allStatuses as $value => $label): ?>
                            <option value="<?php echo $value; ?>" <?php echo $statusFilter === $value ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label class="filter-label">Rechercher</label>
                    <input type="text" 
                           name="search" 
                           class="filter-input" 
                           placeholder="Référence ou nom du jeu..."
                           value="<?php echo htmlspecialchars($search); ?>">
                </div>
                
                <div class="filter-group">
                    <label class="filter-label">Trier par</label>
                    <select name="sortby" class="filter-select" onchange="this.form.submit()">
                        <option value="Date" <?php echo $sortBy === 'Date' ? 'selected' : ''; ?>>Date</option>
                        <option value="Total" <?php echo $sortBy === 'Total' ? 'selected' : ''; ?>>Prix</option>
                        <option value="statut" <?php echo $sortBy === 'statut' ? 'selected' : ''; ?>>Statut</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label class="filter-label">Ordre</label>
                    <select name="order" class="filter-select" onchange="this.form.submit()">
                        <option value="desc" <?php echo $order === 'desc' ? 'selected' : ''; ?>>Décroissant</option>
                        <option value="asc" <?php echo $order === 'asc' ? 'selected' : ''; ?>>Croissant</option>
                    </select>
                </div>
                
                <div class="filter-buttons">
                    <button type="submit" class="filter-btn primary">
                        APPLIQUER
                    </button>
                    <a href="my-orders.php" class="filter-btn">
                        RÉINITIALISER
                    </a>
                </div>
            </form>
        </section>

        <!-- Orders List -->
        <div class="orders-container">
            <?php if (empty($filteredOrders)): ?>
                <div class="empty-state">
                    <div class="empty-icon">📭</div>
                    <h2 class="empty-title">AUCUNE COMMANDE TROUVÉE</h2>
                    <p class="empty-message">
                        <?php if ($statusFilter || $search): ?>
                            Aucune commande ne correspond à vos critères de recherche.
                        <?php else: ?>
                            Vous n'avez pas encore passé de commande.
                        <?php endif; ?>
                    </p>
                    <a href="games.php">
                        <button class="btn-auth">
                            <span class="btn-icon">🎮</span> EXPLORER LA BOUTIQUE
                        </button>
                    </a>
                </div>
            <?php else: ?>
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th class="sortable" onclick="sortTable('order_ref')">
                                RÉFÉRENCE
                                <?php if ($sortBy === 'order_ref'): ?>
                                    <span><?php echo $order === 'asc' ? '↑' : '↓'; ?></span>
                                <?php endif; ?>
                            </th>
                            <th>JEU</th>
                            <th class="sortable" onclick="sortTable('Date')">
                                DATE
                                <?php if ($sortBy === 'Date'): ?>
                                    <span><?php echo $order === 'asc' ? '↑' : '↓'; ?></span>
                                <?php endif; ?>
                            </th>
                            <th>QUANTITÉ</th>
                            <th class="sortable" onclick="sortTable('Total')">
                                TOTAL
                                <?php if ($sortBy === 'Total'): ?>
                                    <span><?php echo $order === 'asc' ? '↑' : '↓'; ?></span>
                                <?php endif; ?>
                            </th>
                            <th>STATUT</th>
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($filteredOrders as $orderItem): ?>
                            <tr>
                                <td class="order-ref"><?php echo htmlspecialchars($orderItem['order_ref']); ?></td>
                                <td>
                                    <div class="order-game">
                                        <?php if (!empty($orderItem['produit_image']) && file_exists("../../uploads/" . $orderItem['produit_image'])): ?>
                                            <div class="game-image">
                                                <img src="../../uploads/<?php echo htmlspecialchars($orderItem['produit_image']); ?>" 
                                                     alt="<?php echo htmlspecialchars($orderItem['produit_nom']); ?>">
                                            </div>
                                        <?php else: ?>
                                            <div class="game-image" style="background: linear-gradient(135deg, var(--primary-green), var(--secondary-purple)); 
                                                                          display: flex; align-items: center; justify-content: center; color: white; font-size: 0.8rem;">
                                                🎮
                                            </div>
                                        <?php endif; ?>
                                        <div class="game-name"><?php echo htmlspecialchars($orderItem['produit_nom'] ?? 'Jeu'); ?></div>
                                    </div>
                                </td>
                                <td class="order-date">
                                    <?php echo date('d/m/Y H:i', strtotime($orderItem['Date'])); ?>
                                </td>
                                <td><?php echo $orderItem['quantity']; ?></td>
                                <td class="order-price"><?php echo number_format($orderItem['Total'], 2); ?> €</td>
                                <td>
                                    <?php 
                                    $statusClass = 'status-pending';
                                    switch ($orderItem['statut']) {
                                        case 'processing': $statusClass = 'status-processing'; break;
                                        case 'shipped': $statusClass = 'status-shipped'; break;
                                        case 'delivered': $statusClass = 'status-delivered'; break;
                                        case 'completed': $statusClass = 'status-completed'; break;
                                        case 'cancelled': $statusClass = 'status-cancelled'; break;
                                    }
                                    ?>
                                    <div class="status-badge <?php echo $statusClass; ?>">
                                        <?php 
                                        $statusText = [
                                            'pending' => 'En attente',
                                            'processing' => 'En traitement',
                                            'shipped' => 'Expédiée',
                                            'delivered' => 'Livrée',
                                            'completed' => 'Terminée',
                                            'cancelled' => 'Annulée'
                                        ];
                                        echo $statusText[$orderItem['statut']] ?? $orderItem['statut'];
                                        ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="order-actions">
                                        <button class="action-btn view" onclick="viewOrderDetails(<?php echo $orderItem['ID']; ?>)">
                                            <span>👁</span> VOIR
                                        </button>
                                        <?php if ($orderItem['statut'] === 'pending'): ?>
                                            <button class="action-btn cancel" onclick="cancelOrder(<?php echo $orderItem['ID']; ?>)">
                                                <span>✕</span> ANNULER
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </main>

    <!-- Order Details Modal -->
    <div id="orderDetailsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">📋 DÉTAILS DE LA COMMANDE</h2>
                <button class="modal-close" onclick="closeModal()">×</button>
            </div>
            <div class="modal-body" id="orderDetailsContent">
                <!-- Order details will be loaded here via AJAX -->
                <div style="text-align: center; padding: 2rem;">
                    <div style="font-size: 2rem; color: var(--text-gray); margin-bottom: 1rem;">⌛</div>
                    <p style="font-family: 'VT323', monospace; color: var(--text-light-gray);">Chargement des détails...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-top">
            <div class="footer-content">
                <div class="footer-section footer-about">
                    <div class="footer-logo">
                        <div class="footer-logo-placeholder">🎮</div>
                        <h3>LUDOLOGY VAULT</h3>
                    </div>
                    <p class="footer-tagline">Préserver l'histoire du jeu vidéo pour les générations futures</p>
                    <div class="social-links">
                        <a href="#" class="social-icon" title="Facebook">
                            <span>FB</span>
                        </a>
                        <a href="#" class="social-icon" title="Twitter">
                            <span>TW</span>
                        </a>
                        <a href="#" class="social-icon" title="Instagram">
                            <span>IG</span>
                        </a>
                        <a href="#" class="social-icon" title="YouTube">
                            <span>YT</span>
                        </a>
                        <a href="#" class="social-icon" title="Discord">
                            <span>DC</span>
                        </a>
                    </div>
                </div>

                <div class="footer-section">
                    <h3 class="footer-title">NAVIGATION</h3>
                    <ul class="footer-links">
                        <li><a href="index.php">► Accueil</a></li>
                        <li><a href="games.php">► Collection de Jeux</a></li>
                        <li><a href="blog.php">► Blog & Actualités</a></li>
                        <li><a href="events.php">► Événements</a></li>
                        <li><a href="#">► À Propos</a></li>
                        <?php if ($isLoggedIn): ?>
                        <li><a href="profile.php">► Mon Profil</a></li>
                        <li><a href="my-orders.php">► Mes Commandes</a></li>
                        <?php endif; ?>
                    </ul>
                </div>

                <div class="footer-section">
                    <h3 class="footer-title">RESSOURCES</h3>
                    <ul class="footer-links">
                        <li><a href="#">► Base de Données</a></li>
                        <li><a href="#">► Archives Historiques</a></li>
                        <li><a href="#">► Guides & Tutoriels</a></li>
                        <li><a href="reclamation.php">► Support & Réclamations</a></li>
                        <li><a href="#">► FAQ</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h3 class="footer-title">MUSÉE</h3>
                    <div class="footer-info">
                        <p class="info-item">
                            <span class="info-icon">🕐</span>
                            <span class="info-content">
                                <strong>Horaires:</strong><br>
                                Lun-Ven: 09:00 - 18:00<br>
                                Sam-Dim: 10:00 - 20:00
                            </span>
                        </p>
                        <p class="info-item">
                            <span class="info-icon">📧</span>
                            <span class="info-content">contact@ludologyvault.tn</span>
                        </p>
                        <p class="info-item">
                            <span class="info-icon">📞</span>
                            <span class="info-content">+216 XX XXX XXX</span>
                        </p>
                        <p class="info-item">
                            <span class="info-icon">📍</span>
                            <span class="info-content">Tunis, Tunisia</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="pixel-divider"></div>
            <div class="footer-bottom-content">
                <p class="copyright">&copy; 2024 LUDOLOGY VAULT - Tous droits réservés</p>
                <div class="footer-bottom-links">
                    <a href="#">Mentions Légales</a>
                    <span>•</span>
                    <a href="#">Politique de Confidentialité</a>
                    <span>•</span>
                    <a href="#">Conditions d'Utilisation</a>
                </div>
                <p class="made-with">Made with <span class="heart">♥</span> for gamers worldwide</p>
            </div>
        </div>
    </footer>

    <!-- Scroll to Top Button -->
    <button class="scroll-top" id="scrollTop">
        <span>▲</span>
    </button>

    <script>
        <?php if ($isLoggedIn): ?>
        document.getElementById('logoutBtn').addEventListener('click', function(e) {
            e.preventDefault();
            
            if (confirm('Êtes-vous sûr de vouloir vous déconnecter?')) {
                const formData = new FormData();
                formData.append('action', 'logout');
                
                fetch('../../controller/user_controller.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = 'index.php';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    window.location.href = 'index.php';
                });
            }
        });
        <?php endif; ?>

        // Function to view order details
        function viewOrderDetails(orderId) {
            const modal = document.getElementById('orderDetailsModal');
            const content = document.getElementById('orderDetailsContent');
            
            // Show loading state
            content.innerHTML = `
                <div style="text-align: center; padding: 2rem;">
                    <div style="font-size: 2rem; color: var(--text-gray); margin-bottom: 1rem;">⌛</div>
                    <p style="font-family: 'VT323', monospace; color: var(--text-light-gray);">Chargement des détails...</p>
                </div>
            `;
            
            // Show modal
            modal.style.display = 'flex';
            
            // Fetch order details
            const formData = new FormData();
            formData.append('ajax_action', 'get_order_details');
            formData.append('order_id', orderId);
            
            fetch('my-orders.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const order = data.commande;
                    
                    // Create timeline based on status
                    const timelineSteps = [
                        { status: 'pending', icon: '📝', label: 'Confirmée' },
                        { status: 'processing', icon: '⚙', label: 'En traitement' },
                        { status: 'shipped', icon: '🚚', label: 'Expédiée' },
                        { status: 'delivered', icon: '✓', label: 'Livrée' }
                    ];
                    
                    let timelineHtml = '';
                    let currentStatusReached = false;
                    
                    timelineSteps.forEach(step => {
                        const isActive = currentStatusReached || step.status === order.statut;
                        if (step.status === order.statut) currentStatusReached = true;
                        
                        timelineHtml += `
                            <div class="timeline-step ${isActive ? 'active' : ''}">
                                <div class="timeline-icon">${step.icon}</div>
                                <div class="timeline-label">${step.label}</div>
                            </div>
                        `;
                    });
                    
                    content.innerHTML = `
                        <div class="order-details-grid">
                            <div class="detail-section">
                                <h3>Informations de commande</h3>
                                <div class="detail-item">
                                    <span class="detail-label">Référence</span>
                                    <span class="detail-value">${order.order_ref}</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Date</span>
                                    <span class="detail-value">${new Date(order.Date).toLocaleDateString('fr-FR', {
                                        day: '2-digit',
                                        month: '2-digit',
                                        year: 'numeric',
                                        hour: '2-digit',
                                        minute: '2-digit'
                                    })}</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Statut</span>
                                    <span class="detail-value" style="color: var(--primary-green);">${getStatusText(order.statut)}</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Paiement</span>
                                    <span class="detail-value">${order.payment_method || 'Non spécifié'}</span>
                                </div>
                            </div>
                            
                            <div class="detail-section">
                                <h3>Produit commandé</h3>
                                <div class="detail-item">
                                    <span class="detail-label">Jeu</span>
                                    <span class="detail-value">${order.produit_nom || 'Non spécifié'}</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Quantité</span>
                                    <span class="detail-value">${order.quantity}</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Prix unitaire</span>
                                    <span class="detail-value">${order.produit_prix ? order.produit_prix + ' €' : 'N/A'}</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Total</span>
                                    <span class="detail-value" style="color: var(--secondary-purple); font-size: 1rem;">${order.Total} €</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="detail-section" style="margin-top: 1rem;">
                            <h3>Suivi de commande</h3>
                            <div class="timeline">
                                ${timelineHtml}
                            </div>
                        </div>
                        
                        <div style="margin-top: 2rem; text-align: center;">
                            <button onclick="closeModal()" class="filter-btn" style="padding: 0.8rem 2rem;">
                                FERMER
                            </button>
                        </div>
                    `;
                } else {
                    content.innerHTML = `
                        <div style="text-align: center; padding: 2rem;">
                            <div style="font-size: 2rem; color: var(--accent-pink); margin-bottom: 1rem;">❌</div>
                            <p style="font-family: 'VT323', monospace; color: var(--text-light-gray);">
                                Erreur lors du chargement des détails: ${data.error || 'Erreur inconnue'}
                            </p>
                            <button onclick="closeModal()" class="filter-btn" style="margin-top: 1rem;">
                                FERMER
                            </button>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                content.innerHTML = `
                    <div style="text-align: center; padding: 2rem;">
                        <div style="font-size: 2rem; color: var(--accent-pink); margin-bottom: 1rem;">❌</div>
                        <p style="font-family: 'VT323', monospace; color: var(--text-light-gray);">
                            Erreur de connexion. Veuillez réessayer.
                        </p>
                        <button onclick="closeModal()" class="filter-btn" style="margin-top: 1rem;">
                            FERMER
                        </button>
                    </div>
                `;
            });
        }
        
        // Function to cancel order
        function cancelOrder(orderId) {
            if (!confirm('Êtes-vous sûr de vouloir annuler cette commande?')) {
                return;
            }
            
            const formData = new FormData();
            formData.append('ajax_action', 'cancel_order');
            formData.append('order_id', orderId);
            
            fetch('my-orders.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload(); // Reload page to update status
                } else {
                    alert('Erreur: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Erreur lors de l\'annulation de la commande');
            });
        }
        
        // Function to close modal
        function closeModal() {
            document.getElementById('orderDetailsModal').style.display = 'none';
        }
        
        // Function to sort table
        function sortTable(column) {
            const urlParams = new URLSearchParams(window.location.search);
            let order = 'desc';
            
            if (urlParams.get('sortby') === column) {
                order = urlParams.get('order') === 'desc' ? 'asc' : 'desc';
            }
            
            urlParams.set('sortby', column);
            urlParams.set('order', order);
            
            window.location.href = 'my-orders.php?' + urlParams.toString();
        }
        
        // Helper function to get status text
        function getStatusText(status) {
            const statusText = {
                'pending': 'En attente',
                'processing': 'En traitement',
                'shipped': 'Expédiée',
                'delivered': 'Livrée',
                'completed': 'Terminée',
                'cancelled': 'Annulée'
            };
            return statusText[status] || status;
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('orderDetailsModal');
            if (event.target === modal) {
                closeModal();
            }
        }
        
        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeModal();
            }
        });
    </script>
</body>
</html>