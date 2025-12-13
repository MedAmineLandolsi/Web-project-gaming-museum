<?php
require_once __DIR__ . '/../config.php';

class CommandeController {
    
    
    // Add this method to your CommandeController class0
public function getUserCommandes($userId) {
    $db = config::connect();
    
    try {
        // First try with full join
        $sql = "SELECT 
                    c.*,
                    j.nom as game_name,
                    j.categorie as game_category,
                    j.prix as unit_price,
                    u.username,
                    u.email
                FROM commande c
                LEFT JOIN jeux j ON c.Produit_id = j.id
                LEFT JOIN users u ON c.user_id = u.id
                WHERE c.user_id = :user_id
                ORDER BY c.Date DESC, c.id DESC";
        
        $query = $db->prepare($sql);
        $query->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $query->execute();
        
        $orders = $query->fetchAll(PDO::FETCH_ASSOC);
        
        // Format the data
        foreach ($orders as &$order) {
            // Ensure numeric values are properly typed
            $order['Total'] = floatval($order['Total'] ?? 0);
            $order['quantity'] = intval($order['quantity'] ?? 1);
            $order['unit_price'] = floatval($order['unit_price'] ?? 0);
            
            // Format date if needed
            if (!empty($order['Date'])) {
                $order['formatted_date'] = date('d/m/Y', strtotime($order['Date']));
            }
            
            // Add status badge class
            if (!empty($order['statut'])) {
                switch ($order['statut']) {
                    case 'completed':
                    case 'delivered':
                        $order['status_class'] = 'status-active';
                        break;
                    case 'pending':
                    case 'processing':
                        $order['status_class'] = 'status-pending';
                        break;
                    case 'cancelled':
                        $order['status_class'] = 'status-cancelled';
                        break;
                    default:
                        $order['status_class'] = '';
                }
            }
        }
        
        return $orders;
        
    } catch (PDOException $e) {
        error_log("Error in getUserCommandes (main query): " . $e->getMessage());
        
        // Try simpler query if join fails
        try {
            $sql = "SELECT * FROM commande 
                    WHERE user_id = :user_id 
                    ORDER BY Date DESC, id DESC";
            
            $query = $db->prepare($sql);
            $query->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $query->execute();
            
            $orders = $query->fetchAll(PDO::FETCH_ASSOC);
            
            // Get additional info separately
            foreach ($orders as &$order) {
                // Get game info
                if (!empty($order['Produit_id'])) {
                    $game_sql = "SELECT nom, categorie, prix FROM jeux WHERE id = :id";
                    $game_query = $db->prepare($game_sql);
                    $game_query->execute(['id' => $order['Produit_id']]);
                    $game = $game_query->fetch();
                    
                    if ($game) {
                        $order['game_name'] = $game['nom'] ?? '';
                        $order['game_category'] = $game['categorie'] ?? '';
                        $order['unit_price'] = floatval($game['prix'] ?? 0);
                    }
                }
                
                // Get user info
                if (!empty($order['user_id'])) {
                    $user_sql = "SELECT username, email FROM users WHERE id = :id";
                    $user_query = $db->prepare($user_sql);
                    $user_query->execute(['id' => $order['user_id']]);
                    $user = $user_query->fetch();
                    
                    if ($user) {
                        $order['username'] = $user['username'] ?? '';
                        $order['email'] = $user['email'] ?? '';
                    }
                }
                
                // Format data
                $order['Total'] = floatval($order['Total'] ?? 0);
                $order['quantity'] = intval($order['quantity'] ?? 1);
                
                if (!empty($order['Date'])) {
                    $order['formatted_date'] = date('d/m/Y', strtotime($order['Date']));
                }
            }
            
            return $orders;
            
        } catch (PDOException $e2) {
            error_log("Error in getUserCommandes (fallback): " . $e2->getMessage());
            return [];
        }
    }
}

// Also add this method for getting a single user's order count
public function getUserOrderCount($userId) {
    $db = config::connect();
    
    try {
        $sql = "SELECT COUNT(*) as order_count, 
                       SUM(Total) as total_spent,
                       MAX(Date) as last_order_date
                FROM commande 
                WHERE user_id = :user_id";
        
        $query = $db->prepare($sql);
        $query->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $query->execute();
        
        return $query->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error in getUserOrderCount: " . $e->getMessage());
        return ['order_count' => 0, 'total_spent' => 0, 'last_order_date' => null];
    }
}
    public function listCommandes() {
    $db = config::connect();
    
    try {
        // Try to join with both jeux and users tables
        $sql = "SELECT c.*, j.nom as jeu_nom, u.username, u.email 
                FROM commande c 
                LEFT JOIN jeux j ON c.Produit_id = j.id 
                LEFT JOIN users u ON c.user_id = u.id 
                ORDER BY c.Date DESC";
        
        $result = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        
        // Debug: Check what columns we get
        if (!empty($result)) {
            error_log("Columns in result: " . print_r(array_keys($result[0]), true));
        }
        
        return $result;
        
    } catch (PDOException $e) {
        // If join fails, try simpler query
        error_log("Error in listCommandes: " . $e->getMessage());
        
        try {
            // Try without user join first
            $sql = "SELECT c.*, j.nom as jeu_nom 
                    FROM commande c 
                    LEFT JOIN jeux j ON c.Produit_id = j.id 
                    ORDER BY c.Date DESC";
            $result = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
            
            // Try to add user info separately
            foreach ($result as &$order) {
                if (!empty($order['user_id'])) {
                    $user_sql = "SELECT username, email FROM users WHERE id = :id";
                    $user_query = $db->prepare($user_sql);
                    $user_query->execute(['id' => $order['user_id']]);
                    $user = $user_query->fetch();
                    
                    if ($user) {
                        $order['username'] = $user['username'] ?? null;
                        $order['email'] = $user['email'] ?? null;
                    }
                }
            }
            
            return $result;
            
        } catch (PDOException $e2) {
            // If everything fails, return basic commande data
            error_log("Fallback error in listCommandes: " . $e2->getMessage());
            
            $sql = "SELECT * FROM commande ORDER BY Date DESC";
            return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        }
    }
}

    // Other methods remain the same...
    public function addCommande($cmd) {
        $db = config::connect();
        $sql = "INSERT INTO commande (Produit_id, Total, quantity, Date, user_id)
                VALUES (:game, :total, :qty, :date, :user_id)";
        $query = $db->prepare($sql);

        $query->bindValue(':game', $cmd->getProduitId());
        $query->bindValue(':total', $cmd->getTotal());
        $query->bindValue(':qty', $cmd->getQuantity());
        $query->bindValue(':date', $cmd->getDate());
        $query->bindValue(':user_id', $cmd->getUserId());

        return $query->execute();
    }

    public function deleteCommande($id) {
        $db = config::connect();
        $sql = "DELETE FROM commande WHERE ID = :id";
        $query = $db->prepare($sql);
        $query->bindValue(':id', $id);
        return $query->execute();
    }
    
    public function getCommande($id) {
        $db = config::connect();

        try {
            // First get the game to see column names
            $test_sql = "SELECT * FROM jeux LIMIT 1";
            $test_result = $db->query($test_sql)->fetch(PDO::FETCH_ASSOC);
            $game_columns = array_keys($test_result);
            
            // Find the name column
            $name_column = 'nom'; // default
            foreach (['nom', 'titre', 'name', 'game_name', 'title'] as $col) {
                if (in_array($col, $game_columns)) {
                    $name_column = $col;
                    break;
                }
            }
            
            $query = $db->prepare("
                SELECT c.*, j.$name_column AS jeu
                FROM commande c
                JOIN jeux j ON c.Produit_id = j.id
                WHERE c.id = :id
            ");

            $query->execute(['id' => $id]);
            $commande = $query->fetch(PDO::FETCH_ASSOC);

            return $commande;

        } catch (PDOException $e) {
            // Fallback if join fails
            $query = $db->prepare("SELECT * FROM commande WHERE id = :id");
            $query->execute(['id' => $id]);
            $commande = $query->fetch(PDO::FETCH_ASSOC);
            
            if ($commande) {
                // Get game name separately
                $game_query = $db->prepare("SELECT * FROM jeux WHERE id = :id");
                $game_query->execute(['id' => $commande['Produit_id']]);
                $game = $game_query->fetch();
                
                $commande['jeu'] = $game['nom'] ?? $game['titre'] ?? $game['name'] ?? 'Unknown';
            }
            
            return $commande;
        }
    }
    
    // Update the updateCommande method to include user_id
    public function updateCommande($commande)
    {
        $db = config::connect();

        try {
            $query = $db->prepare("
                UPDATE commande 
                SET Produit_id = :Produit_id,
                    quantity = :quantity,
                    Total = :Total,
                    Date = :Date,
                    user_id = :user_id
                WHERE ID = :ID
            ");

            $query->execute([
                'Produit_id' => $commande['Produit_id'],
                'quantity'   => $commande['quantity'],
                'Total'      => $commande['Total'],
                'Date'       => $commande['Date'],
                'user_id'    => $commande['user_id'] ?? null, // Add this
                'ID'         => $commande['ID']
            ]);

            return true;

        } catch (PDOException $e) {
            die('Erreur update Commande : ' . $e->getMessage());
        }
    }
    
    // In your CommandeController.php class
public function getStatistics() {
    $db = config::connect();
    
    $stats = [
        'total_orders' => 0,
        'total_revenue' => 0,
        'avg_order_value' => 0,
        'pending_orders' => 0,
        'completed_orders' => 0,
        'orders_by_month' => [],
        'top_users' => [],
        'top_games' => []
    ];

    try {
        // 1. Total number of orders
        $query = $db->query("SELECT COUNT(*) as total FROM commande");
        $result = $query->fetch(PDO::FETCH_ASSOC);
        $stats['total_orders'] = $result['total'] ?? 0;

        // 2. Total revenue (sum of all totals)
        $query = $db->query("SELECT SUM(Total) as revenue FROM commande");
        $result = $query->fetch(PDO::FETCH_ASSOC);
        $stats['total_revenue'] = $result['revenue'] ?? 0;

        // 3. Average order value
        if ($stats['total_orders'] > 0) {
            $stats['avg_order_value'] = round($stats['total_revenue'] / $stats['total_orders'], 2);
        }

        // 4. Pending orders (if you have a status column)
        // If you don't have status, you can remove this or add one
        $query = $db->query("SELECT COUNT(*) as pending FROM commande WHERE statut = 'pending' OR statut IS NULL");
        $result = $query->fetch(PDO::FETCH_ASSOC);
        $stats['pending_orders'] = $result['pending'] ?? 0;

        // 5. Completed orders (if you have a status column)
        $query = $db->query("SELECT COUNT(*) as completed FROM commande WHERE statut = 'completed'");
        $result = $query->fetch(PDO::FETCH_ASSOC);
        $stats['completed_orders'] = $result['completed'] ?? 0;

        // 6. Orders by month (last 6 months)
        $query = $db->query("
            SELECT 
                DATE_FORMAT(Date, '%Y-%m') as month,
                COUNT(*) as order_count,
                SUM(Total) as monthly_revenue
            FROM commande 
            WHERE Date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            GROUP BY DATE_FORMAT(Date, '%Y-%m')
            ORDER BY month DESC
        ");
        $stats['orders_by_month'] = $query->fetchAll(PDO::FETCH_ASSOC);

        // 7. Top users by total spent (only if user_id is populated)
        $query = $db->query("
            SELECT 
                u.username,
                u.email,
                COUNT(c.id) as order_count,
                SUM(c.Total) as total_spent
            FROM commande c
            LEFT JOIN users u ON c.user_id = u.id
            GROUP BY c.user_id
            HAVING c.user_id IS NOT NULL
            ORDER BY total_spent DESC
            LIMIT 5
        ");
        $stats['top_users'] = $query->fetchAll(PDO::FETCH_ASSOC);

        // 8. Top games by orders (most popular games)
        $query = $db->query("
            SELECT 
                j.nom as game_name,
                COUNT(c.id) as times_ordered,
                SUM(c.quantity) as total_quantity,
                SUM(c.Total) as total_revenue
            FROM commande c
            JOIN jeux j ON c.Produit_id = j.id
            GROUP BY c.Produit_id
            ORDER BY times_ordered DESC
            LIMIT 5
        ");
        $stats['top_games'] = $query->fetchAll(PDO::FETCH_ASSOC);

        // 9. Today's orders
        $query = $db->query("
            SELECT COUNT(*) as today_orders, 
                   SUM(Total) as today_revenue 
            FROM commande 
            WHERE DATE(Date) = CURDATE()
        ");
        $today = $query->fetch(PDO::FETCH_ASSOC);
        $stats['today_orders'] = $today['today_orders'] ?? 0;
        $stats['today_revenue'] = $today['today_revenue'] ?? 0;

        // 10. This week's orders
        $query = $db->query("
            SELECT COUNT(*) as week_orders, 
                   SUM(Total) as week_revenue 
            FROM commande 
            WHERE WEEK(Date) = WEEK(CURDATE())
            AND YEAR(Date) = YEAR(CURDATE())
        ");
        $week = $query->fetch(PDO::FETCH_ASSOC);
        $stats['week_orders'] = $week['week_orders'] ?? 0;
        $stats['week_revenue'] = $week['week_revenue'] ?? 0;

        // 11. This month's orders
        $query = $db->query("
            SELECT COUNT(*) as month_orders, 
                   SUM(Total) as month_revenue 
            FROM commande 
            WHERE MONTH(Date) = MONTH(CURDATE())
            AND YEAR(Date) = YEAR(CURDATE())
        ");
        $month = $query->fetch(PDO::FETCH_ASSOC);
        $stats['month_orders'] = $month['month_orders'] ?? 0;
        $stats['month_revenue'] = $month['month_revenue'] ?? 0;

    } catch (PDOException $e) {
        error_log("Error getting statistics: " . $e->getMessage());
        // Return empty stats on error
    }

    return $stats;
}

// You might also want a simpler function for dashboard stats
public function getDashboardStats() {
    $db = config::connect();
    
    $stats = [
        'total_orders' => 0,
        'total_revenue' => 0,
        'today_orders' => 0,
        'today_revenue' => 0,
        'avg_order_value' => 0
    ];

    try {
        // Total orders
        $query = $db->query("SELECT COUNT(*) as total FROM commande");
        $result = $query->fetch();
        $stats['total_orders'] = $result['total'] ?? 0;

        // Total revenue
        $query = $db->query("SELECT SUM(Total) as revenue FROM commande");
        $result = $query->fetch();
        $stats['total_revenue'] = $result['revenue'] ?? 0;

        // Today's orders
        $query = $db->query("
            SELECT COUNT(*) as today_orders, 
                   SUM(Total) as today_revenue 
            FROM commande 
            WHERE DATE(Date) = CURDATE()
        ");
        $result = $query->fetch();
        $stats['today_orders'] = $result['today_orders'] ?? 0;
        $stats['today_revenue'] = $result['today_revenue'] ?? 0;

        // Average order value
        if ($stats['total_orders'] > 0) {
            $stats['avg_order_value'] = round($stats['total_revenue'] / $stats['total_orders'], 2);
        }

    } catch (PDOException $e) {
        error_log("Dashboard stats error: " . $e->getMessage());
    }

    return $stats;
}

// Function to get recent orders for dashboard
public function getRecentOrders($limit = 5) {
    $db = config::connect();
    
    try {
        $query = $db->prepare("
            SELECT c.*, j.nom as game_name, u.username, u.email
            FROM commande c
            LEFT JOIN jeux j ON c.Produit_id = j.id
            LEFT JOIN users u ON c.user_id = u.id
            ORDER BY c.Date DESC
            LIMIT :limit
        ");
        $query->bindValue(':limit', $limit, PDO::PARAM_INT);
        $query->execute();
        
        return $query->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error getting recent orders: " . $e->getMessage());
        return [];
    }
}
}