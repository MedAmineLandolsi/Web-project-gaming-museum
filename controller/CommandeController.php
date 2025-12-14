
<?php
// controller/CommandeController.php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../model/Commande.php';

class CommandeController {
    /**
     * Get all commandes for dashboard (with user info)
     */
    public function listCommandes($search = null, $sortBy = null, $order = null)
    {
        $db = config::getConnexion();

        try {
            $sql = "SELECT c.*, u.username, u.email, j.nom as produit_nom 
                    FROM commande c
                    LEFT JOIN users u ON c.user_id = u.id
                    LEFT JOIN jeux j ON c.Produit_id = j.id
                    WHERE 1=1";
            $params = [];

            if ($search) {
                $sql .= " AND (c.order_ref LIKE :search OR u.username LIKE :search OR j.nom LIKE :search)";
                $params['search'] = '%' . $search . '%';
            }

            $validColumns = ['c.ID', 'c.Total', 'c.Date', 'c.statut'];
            $validOrder = ['asc', 'desc'];

            if ($sortBy && in_array(strtolower($sortBy), array_map('strtolower', $validColumns))) {
                $sql .= " ORDER BY " . $sortBy;
                $sql .= " " . (in_array(strtolower($order), $validOrder) ? strtoupper($order) : "DESC");
            } else {
                $sql .= " ORDER BY c.ID DESC";
            }

            $query = $db->prepare($sql);
            $query->execute($params);

            return $query->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            error_log('Error listing commandes: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get dashboard statistics
     */
    public function getDashboardStats()
    {
        $db = config::getConnexion();

        try {
            $stats = [];
            
            // Total orders
            $sql = "SELECT COUNT(*) as total_orders FROM commande";
            $query = $db->query($sql);
            $result = $query->fetch(PDO::FETCH_ASSOC);
            $stats['total_orders'] = $result['total_orders'] ?? 0;
            
            // Total revenue
            $sql = "SELECT SUM(Total) as total_revenue FROM commande WHERE statut IN ('completed', 'shipped', 'delivered')";
            $query = $db->query($sql);
            $result = $query->fetch(PDO::FETCH_ASSOC);
            $stats['total_revenue'] = $result['total_revenue'] ?? 0;
            
            // Pending orders
            $sql = "SELECT COUNT(*) as pending_orders FROM commande WHERE statut = 'pending'";
            $query = $db->query($sql);
            $result = $query->fetch(PDO::FETCH_ASSOC);
            $stats['pending_orders'] = $result['pending_orders'] ?? 0;
            
            // Recent revenue (last 30 days)
            $sql = "SELECT SUM(Total) as recent_revenue FROM commande 
                    WHERE Date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) 
                    AND statut IN ('completed', 'shipped', 'delivered')";
            $query = $db->query($sql);
            $result = $query->fetch(PDO::FETCH_ASSOC);
            $stats['recent_revenue'] = $result['recent_revenue'] ?? 0;
            
            return $stats;

        } catch (Exception $e) {
            error_log('Error getting dashboard stats: ' . $e->getMessage());
            return [
                'total_orders' => 0,
                'total_revenue' => 0,
                'pending_orders' => 0,
                'recent_revenue' => 0
            ];
        }
    }

    /**
     * Add a new commande
     */
    public function addCommande($data)
    {
        try {
            // Validate required fields
            if (empty($data['Produit_id']) || empty($data['quantity']) || empty($data['statut'])) {
                return ['success' => false, 'error' => 'Champs requis manquants'];
            }

            // Generate order reference if not provided
            if (empty($data['order_ref'])) {
                $data['order_ref'] = 'ORD-' . date('Ymd') . '-' . strtoupper(uniqid());
            }

            // Get product price to calculate total if not provided
            if (empty($data['Total']) && !empty($data['Produit_id']) && !empty($data['quantity'])) {
                $gameController = new JeuxController();
                $game = $gameController->getGameById($data['Produit_id']);
                if ($game) {
                    $data['Total'] = $game['prix'] * $data['quantity'];
                } else {
                    return ['success' => false, 'error' => 'Produit non trouvé'];
                }
            }

            $sql = "INSERT INTO commande (
                Produit_id, Total, quantity, Date, user_id, 
                order_ref, statut, shipping_name, shipping_email,
                shipping_phone, shipping_address, shipping_city,
                shipping_zip, shipping_country, billing_name,
                billing_address, billing_city, billing_zip,
                billing_country, payment_method, notes
            ) VALUES (
                :Produit_id, :Total, :quantity, :Date, :user_id,
                :order_ref, :statut, :shipping_name, :shipping_email,
                :shipping_phone, :shipping_address, :shipping_city,
                :shipping_zip, :shipping_country, :billing_name,
                :billing_address, :billing_city, :billing_zip,
                :billing_country, :payment_method, :notes
            )";

            $db = config::getConnexion();
            $query = $db->prepare($sql);

            // Set default values if not provided
            $defaults = [
                'Total' => 0,
                'Date' => date('Y-m-d'),
                'user_id' => null,
                'shipping_name' => null,
                'shipping_email' => null,
                'shipping_phone' => null,
                'shipping_address' => null,
                'shipping_city' => null,
                'shipping_zip' => null,
                'shipping_country' => null,
                'billing_name' => null,
                'billing_address' => null,
                'billing_city' => null,
                'billing_zip' => null,
                'billing_country' => null,
                'payment_method' => null,
                'notes' => null
            ];

            foreach ($defaults as $key => $value) {
                if (!isset($data[$key])) {
                    $data[$key] = $value;
                }
            }

            $query->execute([
                'Produit_id' => $data['Produit_id'],
                'Total' => $data['Total'],
                'quantity' => $data['quantity'],
                'Date' => $data['Date'],
                'user_id' => $data['user_id'],
                'order_ref' => $data['order_ref'],
                'statut' => $data['statut'],
                'shipping_name' => $data['shipping_name'],
                'shipping_email' => $data['shipping_email'],
                'shipping_phone' => $data['shipping_phone'],
                'shipping_address' => $data['shipping_address'],
                'shipping_city' => $data['shipping_city'],
                'shipping_zip' => $data['shipping_zip'],
                'shipping_country' => $data['shipping_country'],
                'billing_name' => $data['billing_name'],
                'billing_address' => $data['billing_address'],
                'billing_city' => $data['billing_city'],
                'billing_zip' => $data['billing_zip'],
                'billing_country' => $data['billing_country'],
                'payment_method' => $data['payment_method'],
                'notes' => $data['notes']
            ]);

            $lastId = $db->lastInsertId();
            
            // Update stock if order is completed/shipped
            if ($data['statut'] === 'completed' || $data['statut'] === 'shipped' || $data['statut'] === 'delivered') {
                $gameController = new JeuxController();
                $gameController->updateStock($data['Produit_id'], -$data['quantity']);
            }

            return ['success' => true, 'id' => $lastId, 'order_ref' => $data['order_ref']];

        } catch (Exception $e) {
            error_log('Error adding commande: ' . $e->getMessage());
            return ['success' => false, 'error' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Update a commande
     */
    public function updateCommande($id, $data)
    {
        try {
            $db = config::getConnexion();

            // Get old commande data to track stock changes
            $oldCommande = $this->getCommandeById($id);
            if (!$oldCommande) {
                return ['success' => false, 'error' => 'Commande non trouvée'];
            }

            // Build update query
            $fields = [];
            $params = ['id' => $id];
            
            $updatableFields = [
                'Produit_id', 'Total', 'quantity', 'Date', 'user_id',
                'order_ref', 'statut', 'shipping_name', 'shipping_email',
                'shipping_phone', 'shipping_address', 'shipping_city',
                'shipping_zip', 'shipping_country', 'billing_name',
                'billing_address', 'billing_city', 'billing_zip',
                'billing_country', 'payment_method', 'notes'
            ];
            
            foreach ($updatableFields as $field) {
                if (isset($data[$field])) {
                    $fields[] = "$field = :$field";
                    $params[$field] = $data[$field];
                }
            }
            
            if (empty($fields)) {
                return ['success' => false, 'error' => 'Aucun champ à mettre à jour'];
            }
            
            $sql = "UPDATE commande SET " . implode(', ', $fields) . " WHERE ID = :id";
            
            $query = $db->prepare($sql);
            $query->execute($params);
            
            // Handle stock updates if status or quantity changed
            if (isset($data['statut']) || isset($data['quantity']) || isset($data['Produit_id'])) {
                $this->handleStockUpdate($oldCommande, $data);
            }
            
            return ['success' => true, 'affected_rows' => $query->rowCount()];

        } catch (Exception $e) {
            error_log('Error updating commande: ' . $e->getMessage());
            return ['success' => false, 'error' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Delete a commande
     */
    public function deleteCommande($id)
    {
        try {
            $db = config::getConnexion();
            
            // Get commande before deleting to restore stock
            $commande = $this->getCommandeById($id);
            if (!$commande) {
                return ['success' => false, 'error' => 'Commande non trouvée'];
            }
            
            // Restore stock if order was completed/shipped/delivered
            if ($commande['statut'] === 'completed' || $commande['statut'] === 'shipped' || $commande['statut'] === 'delivered') {
                $gameController = new JeuxController();
                $gameController->updateStock($commande['Produit_id'], $commande['quantity']);
            }
            
            $sql = "DELETE FROM commande WHERE ID = :id";
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            
            return ['success' => true, 'affected_rows' => $query->rowCount()];

        } catch (Exception $e) {
            error_log('Error deleting commande: ' . $e->getMessage());
            return ['success' => false, 'error' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Get commande by ID with full details
     */
    public function getCommandeById($id)
    {
        $db = config::getConnexion();

        try {
            $sql = "SELECT c.*, u.username, u.email, j.nom as produit_nom, j.prix as produit_prix 
                    FROM commande c
                    LEFT JOIN users u ON c.user_id = u.id
                    LEFT JOIN jeux j ON c.Produit_id = j.id
                    WHERE c.ID = :id";
            
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            
            return $query->fetch(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            error_log('Error getting commande by id: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all available statuses
     */
    public function getStatuses()
    {
        return [
            'pending' => 'En attente',
            'processing' => 'En traitement',
            'shipped' => 'Expédié',
            'delivered' => 'Livré',
            'completed' => 'Terminé',
            'cancelled' => 'Annulé'
        ];
    }

    /**
     * Handle stock updates when commande changes
     */
    private function handleStockUpdate($oldCommande, $newData)
    {
        $gameController = new JeuxController();
        
        $oldStatus = $oldCommande['statut'];
        $newStatus = $newData['statut'] ?? $oldStatus;
        $oldQuantity = $oldCommande['quantity'];
        $newQuantity = $newData['quantity'] ?? $oldQuantity;
        $oldProductId = $oldCommande['Produit_id'];
        $newProductId = $newData['Produit_id'] ?? $oldProductId;
        
        // Define completed statuses
        $completedStatuses = ['completed', 'shipped', 'delivered'];
        $wasCompleted = in_array($oldStatus, $completedStatuses);
        $isNowCompleted = in_array($newStatus, $completedStatuses);
        
        // Case 1: Product changed
        if ($newProductId != $oldProductId) {
            // Restore stock from old product
            if ($wasCompleted) {
                $gameController->updateStock($oldProductId, $oldQuantity);
            }
            // Deduct stock from new product
            if ($isNowCompleted) {
                $gameController->updateStock($newProductId, -$newQuantity);
            }
        }
        // Case 2: Quantity changed
        else if ($newQuantity != $oldQuantity) {
            $quantityDiff = $oldQuantity - $newQuantity;
            if ($wasCompleted) {
                $gameController->updateStock($oldProductId, $quantityDiff);
            }
        }
        // Case 3: Status changed
        else if ($newStatus != $oldStatus) {
            if ($wasCompleted && !$isNowCompleted) {
                // Restore stock
                $gameController->updateStock($oldProductId, $oldQuantity);
            } else if (!$wasCompleted && $isNowCompleted) {
                // Deduct stock
                $gameController->updateStock($oldProductId, -$oldQuantity);
            }
        }
    }

    /**
     * Get commande details for modal view
     */
    public function getCommandeDetails($id)
    {
        $commande = $this->getCommandeById($id);
        
        if (!$commande) {
            return ['success' => false, 'error' => 'Commande non trouvée'];
        }
        
        return [
            'success' => true,
            'commande' => $commande,
            'user_info' => [
                'username' => $commande['username'] ?? null,
                'email' => $commande['email'] ?? null
            ],
            'produit_info' => [
                'nom' => $commande['produit_nom'] ?? null,
                'prix' => $commande['produit_prix'] ?? 0
            ]
        ];
    }

    /**
     * Get recent commandes for dashboard (last 5)
     */
    public function getRecentCommandes($limit = 5)
    {
        $db = config::getConnexion();

        try {
            $sql = "SELECT c.*, u.username, j.nom as produit_nom 
                    FROM commande c
                    LEFT JOIN users u ON c.user_id = u.id
                    LEFT JOIN jeux j ON c.Produit_id = j.id
                    ORDER BY c.Date DESC, c.ID DESC 
                    LIMIT :limit";
            
            $query = $db->prepare($sql);
            $query->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $query->execute();
            
            return $query->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            error_log('Error getting recent commandes: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * AJAX handler for frontend requests
     */
    public function handleAjaxRequest()
    {
        if (!isset($_GET['action'])) {
            return ['success' => false, 'error' => 'Action non spécifiée'];
        }

        $action = $_GET['action'];

        switch ($action) {
            case 'getDetails':
                if (!isset($_GET['id'])) {
                    return ['success' => false, 'error' => 'ID non spécifié'];
                }
                return $this->getCommandeDetails($_GET['id']);
                
            case 'delete':
                if (!isset($_GET['id'])) {
                    return ['success' => false, 'error' => 'ID non spécifié'];
                }
                return $this->deleteCommande($_GET['id']);
                
            default:
                return ['success' => false, 'error' => 'Action non reconnue'];
        }
    }

    /**
     * Get commandes for a specific user
     */
    public function getUserCommandes($userId)
    {
        $db = config::getConnexion();

        try {
            $sql = "SELECT c.*, j.nom as produit_nom, j.prix as produit_prix, j.image as produit_image
                    FROM commande c
                    LEFT JOIN jeux j ON c.Produit_id = j.id
                    WHERE c.user_id = :user_id
                    ORDER BY c.Date DESC, c.ID DESC";
            
            $query = $db->prepare($sql);
            $query->execute(['user_id' => $userId]);
            
            return $query->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            error_log('Error getting user commandes: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get user order count and total spent
     */
    public function getUserOrderCount($userId)
    {
        $db = config::getConnexion();

        try {
            $sql = "SELECT 
                        COUNT(*) as order_count,
                        SUM(Total) as total_spent,
                        SUM(quantity) as games_count
                    FROM commande 
                    WHERE user_id = :user_id 
                    AND statut IN ('completed', 'shipped', 'delivered')";
            
            $query = $db->prepare($sql);
            $query->execute(['user_id' => $userId]);
            
            $result = $query->fetch(PDO::FETCH_ASSOC);
            
            return [
                'order_count' => $result['order_count'] ?? 0,
                'total_spent' => $result['total_spent'] ?? 0,
                'games_count' => $result['games_count'] ?? 0
            ];

        } catch (Exception $e) {
            error_log('Error getting user order count: ' . $e->getMessage());
            return ['order_count' => 0, 'total_spent' => 0, 'games_count' => 0];
        }
    }

    /**
     * Create order from cart
     */
    public function createOrderFromCart($userId, $cartItems, $shippingData, $paymentMethod)
    {
        try {
            $db = config::getConnexion();
            
            // Start transaction
            $db->beginTransaction();
            
            $orderIds = [];
            
            foreach ($cartItems as $item) {
                // Check stock
                $gameController = new JeuxController();
                $game = $gameController->getGameById($item['game_id']);
                
                if (!$game || $game['stock'] < $item['quantity']) {
                    throw new Exception("Stock insuffisant pour: " . $item['name']);
                }
                
                // Create order reference
                $orderRef = 'ORD-' . date('Ymd') . '-' . strtoupper(uniqid());
                
                // Calculate total
                $total = $item['price'] * $item['quantity'];
                
                $sql = "INSERT INTO commande (
                    Produit_id, Total, quantity, Date, user_id, 
                    order_ref, statut, shipping_name, shipping_email,
                    shipping_phone, shipping_address, shipping_city,
                    shipping_zip, shipping_country, payment_method
                ) VALUES (
                    :Produit_id, :Total, :quantity, :Date, :user_id,
                    :order_ref, :statut, :shipping_name, :shipping_email,
                    :shipping_phone, :shipping_address, :shipping_city,
                    :shipping_zip, :shipping_country, :payment_method
                )";
                
                $query = $db->prepare($sql);
                $query->execute([
                    'Produit_id' => $item['game_id'],
                    'Total' => $total,
                    'quantity' => $item['quantity'],
                    'Date' => date('Y-m-d H:i:s'),
                    'user_id' => $userId,
                    'order_ref' => $orderRef,
                    'statut' => 'pending',
                    'shipping_name' => $shippingData['name'] ?? null,
                    'shipping_email' => $shippingData['email'] ?? null,
                    'shipping_phone' => $shippingData['phone'] ?? null,
                    'shipping_address' => $shippingData['address'] ?? null,
                    'shipping_city' => $shippingData['city'] ?? null,
                    'shipping_zip' => $shippingData['zip'] ?? null,
                    'shipping_country' => $shippingData['country'] ?? null,
                    'payment_method' => $paymentMethod
                ]);
                
                $orderIds[] = [
                    'id' => $db->lastInsertId(),
                    'ref' => $orderRef,
                    'game' => $item['name']
                ];
                
                // Update stock
                $gameController->updateStock($item['game_id'], -$item['quantity']);
            }
            
            // Commit transaction
            $db->commit();
            
            return [
                'success' => true,
                'order_ids' => $orderIds,
                'message' => count($orderIds) . ' commande(s) créée(s) avec succès'
            ];

        } catch (Exception $e) {
            // Rollback on error
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            
            error_log('Error creating order from cart: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Erreur lors de la création de la commande: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get monthly revenue statistics
     */
    public function getMonthlyRevenue($year = null)
    {
        $db = config::getConnexion();
        
        if (!$year) {
            $year = date('Y');
        }

        try {
            $sql = "SELECT 
                        DATE_FORMAT(Date, '%Y-%m') as month,
                        COUNT(*) as order_count,
                        SUM(Total) as revenue,
                        SUM(quantity) as games_sold
                    FROM commande 
                    WHERE YEAR(Date) = :year 
                    AND statut IN ('completed', 'shipped', 'delivered')
                    GROUP BY DATE_FORMAT(Date, '%Y-%m')
                    ORDER BY month";
            
            $query = $db->prepare($sql);
            $query->execute(['year' => $year]);
            
            return $query->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            error_log('Error getting monthly revenue: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get top selling games
     */
    public function getTopSellingGames($limit = 10)
    {
        $db = config::getConnexion();

        try {
            $sql = "SELECT 
                        j.id,
                        j.nom,
                        j.categorie,
                        COUNT(c.ID) as total_orders,
                        SUM(c.quantity) as total_quantity,
                        SUM(c.Total) as total_revenue
                    FROM commande c
                    JOIN jeux j ON c.Produit_id = j.id
                    WHERE c.statut IN ('completed', 'shipped', 'delivered')
                    GROUP BY j.id, j.nom, j.categorie
                    ORDER BY total_quantity DESC
                    LIMIT :limit";
            
            $query = $db->prepare($sql);
            $query->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $query->execute();
            
            return $query->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            error_log('Error getting top selling games: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Export orders to CSV
     */
    public function exportOrdersToCSV($startDate = null, $endDate = null)
    {
        $db = config::getConnexion();

        try {
            $sql = "SELECT 
                        c.ID,
                        c.order_ref,
                        c.Date,
                        u.username,
                        u.email,
                        j.nom as produit_nom,
                        c.quantity,
                        c.Total,
                        c.statut,
                        c.payment_method,
                        c.shipping_name,
                        c.shipping_address,
                        c.shipping_city,
                        c.shipping_country
                    FROM commande c
                    LEFT JOIN users u ON c.user_id = u.id
                    LEFT JOIN jeux j ON c.Produit_id = j.id
                    WHERE 1=1";
            
            $params = [];
            
            if ($startDate) {
                $sql .= " AND c.Date >= :start_date";
                $params['start_date'] = $startDate;
            }
            
            if ($endDate) {
                $sql .= " AND c.Date <= :end_date";
                $params['end_date'] = $endDate;
            }
            
            $sql .= " ORDER BY c.Date DESC";
            
            $query = $db->prepare($sql);
            $query->execute($params);
            
            $orders = $query->fetchAll(PDO::FETCH_ASSOC);
            
            // Generate CSV
            $csv = "ID,Référence,Date,Client,Email,Produit,Quantité,Total,Statut,Paiement,Nom Livraison,Adresse,Ville,Pays\n";
            
            foreach ($orders as $order) {
                $csv .= '"' . implode('","', [
                    $order['ID'],
                    $order['order_ref'],
                    $order['Date'],
                    $order['username'] ?? '',
                    $order['email'] ?? '',
                    $order['produit_nom'] ?? '',
                    $order['quantity'],
                    $order['Total'],
                    $order['statut'],
                    $order['payment_method'] ?? '',
                    $order['shipping_name'] ?? '',
                    $order['shipping_address'] ?? '',
                    $order['shipping_city'] ?? '',
                    $order['shipping_country'] ?? ''
                ]) . "\"\n";
            }
            
            return $csv;

        } catch (Exception $e) {
            error_log('Error exporting orders to CSV: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Count total commandes
     */
    public function countCommandes($status = null)
    {
        $db = config::getConnexion();
        
        try {
            $sql = "SELECT COUNT(*) as total FROM commande WHERE 1=1";
            $params = [];
            
            if ($status) {
                $sql .= " AND statut = :statut";
                $params['statut'] = $status;
            }
            
            $query = $db->prepare($sql);
            $query->execute($params);
            $result = $query->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch (Exception $e) {
            error_log('Error counting commandes: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get total revenue
     */
    public function getTotalRevenue($status = null)
    {
        $db = config::getConnexion();
        
        try {
            $sql = "SELECT SUM(Total) as revenue FROM commande WHERE 1=1";
            $params = [];
            
            if ($status) {
                $sql .= " AND statut = :statut";
                $params['statut'] = $status;
            }
            
            $query = $db->prepare($sql);
            $query->execute($params);
            $result = $query->fetch(PDO::FETCH_ASSOC);
            return $result['revenue'] ?? 0;
        } catch (Exception $e) {
            error_log('Error getting total revenue: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get orders by status
     */
    public function getOrdersByStatus($status)
    {
        $db = config::getConnexion();
        
        try {
            $sql = "SELECT c.*, u.username, j.nom as produit_nom 
                    FROM commande c
                    LEFT JOIN users u ON c.user_id = u.id
                    LEFT JOIN jeux j ON c.Produit_id = j.id
                    WHERE c.statut = :statut
                    ORDER BY c.Date DESC";
            
            $query = $db->prepare($sql);
            $query->execute(['statut' => $status]);
            
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Error getting orders by status: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get orders between dates
     */
    public function getOrdersBetweenDates($startDate, $endDate)
    {
        $db = config::getConnexion();
        
        try {
            $sql = "SELECT c.*, u.username, j.nom as produit_nom 
                    FROM commande c
                    LEFT JOIN users u ON c.user_id = u.id
                    LEFT JOIN jeux j ON c.Produit_id = j.id
                    WHERE c.Date BETWEEN :start_date AND :end_date
                    ORDER BY c.Date DESC";
            
            $query = $db->prepare($sql);
            $query->execute([
                'start_date' => $startDate,
                'end_date' => $endDate
            ]);
            
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Error getting orders between dates: ' . $e->getMessage());
            return [];
        }
    }
}
?>
