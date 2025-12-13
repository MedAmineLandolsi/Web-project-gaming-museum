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

    public function __construct($produit_id, $total, $quantity, $date, $user_id = null, 
                                $order_ref = null, $statut = 'pending',
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
        $this->date = $date;
        $this->user_id = $user_id;
        $this->order_ref = $order_ref;
        $this->statut = $statut;
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

    // Setters (optional, but useful)
    public function setStatut($statut) { $this->statut = $statut; }
    public function setOrderRef($order_ref) { $this->order_ref = $order_ref; }
}