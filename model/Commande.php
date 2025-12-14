
<?php
class Commande {
    private $id;
    private $produit_id;
    private $total;
    private $quantity;
    private $date;
    private $user_id;
    private $order_ref;
    private $statut;
    private $shipping_name;
    private $shipping_email;
    private $shipping_phone;
    private $shipping_address;
    private $shipping_city;
    private $shipping_zip;
    private $shipping_country;
    private $billing_name;
    private $billing_address;
    private $billing_city;
    private $billing_zip;
    private $billing_country;
    private $payment_method;
    private $notes;

    // Status constants for better type safety
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    // Payment method constants
    const PAYMENT_CASH = 'cash';
    const PAYMENT_CARD = 'card';
    const PAYMENT_TRANSFER = 'transfer';

    public function __construct($produit_id, $total, $quantity, $date = null, $user_id = null, 
                                $order_ref = null, $statut = self::STATUS_PENDING,
                                $shipping_name = null, $shipping_email = null,
                                $shipping_phone = null, $shipping_address = null,
                                $shipping_city = null, $shipping_zip = null,
                                $shipping_country = null, $billing_name = null,
                                $billing_address = null, $billing_city = null,
                                $billing_zip = null, $billing_country = null,
                                $payment_method = null, $notes = null, $id = null) {
        
        $this->id = $id;
        $this->produit_id = $produit_id;
        $this->total = $total;
        $this->quantity = $quantity;
        $this->date = $date ?? date('Y-m-d H:i:s');
        $this->user_id = $user_id;
        $this->order_ref = $order_ref ?? $this->generateOrderRef();
        $this->statut = $this->validateStatus($statut);
        $this->shipping_name = $shipping_name;
        $this->shipping_email = $shipping_email;
        $this->shipping_phone = $shipping_phone;
        $this->shipping_address = $shipping_address;
        $this->shipping_city = $shipping_city;
        $this->shipping_zip = $shipping_zip;
        $this->shipping_country = $shipping_country;
        $this->billing_name = $billing_name;
        $this->billing_address = $billing_address;
        $this->billing_city = $billing_city;
        $this->billing_zip = $billing_zip;
        $this->billing_country = $billing_country;
        $this->payment_method = $payment_method;
        $this->notes = $notes;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getProduitId() { return $this->produit_id; }
    public function getTotal() { return $this->total; }
    public function getQuantity() { return $this->quantity; }
    public function getDate() { return $this->date; }
    public function getUserId() { return $this->user_id; }
    public function getOrderRef() { return $this->order_ref; }
    public function getStatut() { return $this->statut; }
    public function getShippingName() { return $this->shipping_name; }
    public function getShippingEmail() { return $this->shipping_email; }
    public function getShippingPhone() { return $this->shipping_phone; }
    public function getShippingAddress() { return $this->shipping_address; }
    public function getShippingCity() { return $this->shipping_city; }
    public function getShippingZip() { return $this->shipping_zip; }
    public function getShippingCountry() { return $this->shipping_country; }
    public function getBillingName() { return $this->billing_name; }
    public function getBillingAddress() { return $this->billing_address; }
    public function getBillingCity() { return $this->billing_city; }
    public function getBillingZip() { return $this->billing_zip; }
    public function getBillingCountry() { return $this->billing_country; }
    public function getPaymentMethod() { return $this->payment_method; }
    public function getNotes() { return $this->notes; }

    // Setters with validation
    public function setStatut($statut) { 
        $this->statut = $this->validateStatus($statut); 
    }
    
    public function setOrderRef($order_ref) { 
        $this->order_ref = $order_ref; 
    }
    
    public function setPaymentMethod($payment_method) {
        $this->payment_method = $payment_method;
    }
    
    public function setNotes($notes) {
        $this->notes = $notes;
    }
    
    public function setShippingInfo($name, $email, $phone, $address, $city, $zip, $country) {
        $this->shipping_name = $name;
        $this->shipping_email = $email;
        $this->shipping_phone = $phone;
        $this->shipping_address = $address;
        $this->shipping_city = $city;
        $this->shipping_zip = $zip;
        $this->shipping_country = $country;
    }
    
    public function setBillingInfo($name, $address, $city, $zip, $country) {
        $this->billing_name = $name;
        $this->billing_address = $address;
        $this->billing_city = $city;
        $this->billing_zip = $zip;
        $this->billing_country = $country;
    }

    // Helper methods
    private function generateOrderRef() {
        return 'ORD-' . date('Ymd') . '-' . strtoupper(uniqid());
    }
    
    private function validateStatus($status) {
        $validStatuses = [
            self::STATUS_PENDING,
            self::STATUS_PROCESSING,
            self::STATUS_SHIPPED,
            self::STATUS_DELIVERED,
            self::STATUS_COMPLETED,
            self::STATUS_CANCELLED
        ];
        
        return in_array($status, $validStatuses) ? $status : self::STATUS_PENDING;
    }
    
    // Business logic methods
    public function calculateSubtotal($unitPrice) {
        return $unitPrice * $this->quantity;
    }
    
    public function calculateTax($taxRate = 0.20) {
        return $this->total * $taxRate;
    }
    
    public function getFormattedTotal() {
        return number_format($this->total, 2) . ' €';
    }
    
    public function getFormattedDate($format = 'd/m/Y H:i') {
        return date($format, strtotime($this->date));
    }
    
    public function isPending() {
        return $this->statut === self::STATUS_PENDING;
    }
    
    public function isCompleted() {
        return in_array($this->statut, [
            self::STATUS_COMPLETED,
            self::STATUS_SHIPPED,
            self::STATUS_DELIVERED
        ]);
    }
    
    public function isCancelled() {
        return $this->statut === self::STATUS_CANCELLED;
    }
    
    public function canBeCancelled() {
        return $this->isPending() || $this->statut === self::STATUS_PROCESSING;
    }
    
    // Static methods for convenience
    public static function getAllStatuses() {
        return [
            self::STATUS_PENDING => 'En attente',
            self::STATUS_PROCESSING => 'En traitement',
            self::STATUS_SHIPPED => 'Expédié',
            self::STATUS_DELIVERED => 'Livré',
            self::STATUS_COMPLETED => 'Terminé',
            self::STATUS_CANCELLED => 'Annulé'
        ];
    }
    
    public static function getStatusText($status) {
        $statuses = self::getAllStatuses();
        return $statuses[$status] ?? $status;
    }
    
    public static function getAllPaymentMethods() {
        return [
            self::PAYMENT_CASH => 'Paiement à la livraison',
            self::PAYMENT_CARD => 'Carte bancaire',
            self::PAYMENT_TRANSFER => 'Virement bancaire'
        ];
    }
    
    // Convert to array (useful for JSON responses)
    public function toArray() {
        return [
            'id' => $this->id,
            'produit_id' => $this->produit_id,
            'total' => $this->total,
            'quantity' => $this->quantity,
            'date' => $this->date,
            'user_id' => $this->user_id,
            'order_ref' => $this->order_ref,
            'statut' => $this->statut,
            'statut_text' => self::getStatusText($this->statut),
            'shipping_name' => $this->shipping_name,
            'shipping_email' => $this->shipping_email,
            'shipping_phone' => $this->shipping_phone,
            'shipping_address' => $this->shipping_address,
            'shipping_city' => $this->shipping_city,
            'shipping_zip' => $this->shipping_zip,
            'shipping_country' => $this->shipping_country,
            'billing_name' => $this->billing_name,
            'billing_address' => $this->billing_address,
            'billing_city' => $this->billing_city,
            'billing_zip' => $this->billing_zip,
            'billing_country' => $this->billing_country,
            'payment_method' => $this->payment_method,
            'notes' => $this->notes
        ];
    }
    
    // Factory method to create from array (useful for database results)
    public static function createFromArray(array $data) {
        return new self(
            $data['produit_id'] ?? null,
            $data['total'] ?? 0,
            $data['quantity'] ?? 1,
            $data['date'] ?? null,
            $data['user_id'] ?? null,
            $data['order_ref'] ?? null,
            $data['statut'] ?? self::STATUS_PENDING,
            $data['shipping_name'] ?? null,
            $data['shipping_email'] ?? null,
            $data['shipping_phone'] ?? null,
            $data['shipping_address'] ?? null,
            $data['shipping_city'] ?? null,
            $data['shipping_zip'] ?? null,
            $data['shipping_country'] ?? null,
            $data['billing_name'] ?? null,
            $data['billing_address'] ?? null,
            $data['billing_city'] ?? null,
            $data['billing_zip'] ?? null,
            $data['billing_country'] ?? null,
            $data['payment_method'] ?? null,
            $data['notes'] ?? null,
            $data['id'] ?? null
        );
    }
    
    // Validation methods
    public function validate() {
        $errors = [];
        
        if (empty($this->produit_id)) {
            $errors[] = "Produit ID est requis";
        }
        
        if ($this->total < 0) {
            $errors[] = "Le total ne peut pas être négatif";
        }
        
        if ($this->quantity <= 0) {
            $errors[] = "La quantité doit être supérieure à 0";
        }
        
        if (empty($this->order_ref)) {
            $errors[] = "Référence de commande est requise";
        }
        
        return $errors;
    }
    
    public function isValid() {
        return empty($this->validate());
    }
}
?>
