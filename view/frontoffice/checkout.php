<?php
// checkout.php
session_start();

if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

// Calculate total
$grandTotal = 0;
foreach ($_SESSION['cart'] as $item) {
    $grandTotal += $item['prix'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Checkout - Informations de Livraison</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .checkout-container {
            max-width: 800px;
            margin: 120px auto 60px;
            padding: 30px;
            background: rgba(5, 5, 8, 0.9);
            border-radius: 16px;
            border: 1px solid rgba(0, 255, 170, 0.45);
            box-shadow: 0 0 22px rgba(0, 255, 170, 0.3);
        }
        
        .checkout-section {
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid rgba(0, 255, 170, 0.3);
            border-radius: 12px;
            background: rgba(0, 255, 170, 0.05);
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #0aff9d;
            font-family: 'VT323', monospace;
            font-size: 18px;
        }
        
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid rgba(0, 255, 170, 0.6);
            border-radius: 8px;
            background: #020617;
            color: #e5fff8;
            font-family: 'VT323', monospace;
            font-size: 16px;
        }
        
        .form-group textarea {
            height: 100px;
            resize: vertical;
        }
        
        .same-as-shipping {
            margin: 20px 0;
        }
        
        .order-summary {
            background: rgba(0, 255, 170, 0.1);
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
        }
        
        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid rgba(0, 255, 170, 0.2);
        }
        
        .order-total {
            font-size: 24px;
            font-weight: bold;
            text-align: right;
            margin-top: 20px;
            color: #0aff9d;
        }
    </style>
</head>
<body>
    <!-- Reuse your navbar from cart.php here -->
    <nav class="navbar">
        <!-- ... same navbar code ... -->
    </nav>

    <main class="checkout-container">
        <h1 style="color: #0aff9d; text-align: center; margin-bottom: 30px;">
            🚚 Informations de Livraison
        </h1>
        
        <!-- Order Summary -->
        <div class="order-summary">
            <h3 style="color: #0aff9d; margin-bottom: 20px;">Récapitulatif de votre commande</h3>
            <?php foreach ($_SESSION['cart'] as $item): ?>
                <div class="order-item">
                    <span><?= htmlspecialchars($item['nom']) ?> (x<?= $item['quantity'] ?>)</span>
                    <span><?= number_format($item['prix'] * $item['quantity'], 2) ?> €</span>
                </div>
            <?php endforeach; ?>
            <div class="order-total">Total: <?= number_format($grandTotal, 2) ?> €</div>
        </div>
        
        <!-- Address Form -->
        <form action="process_payment.php" method="POST">
            <!-- Shipping Address -->
            <div class="checkout-section">
                <h3 style="color: #0aff9d; margin-bottom: 20px;">Adresse de Livraison</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="shipping_name">Nom complet *</label>
                        <input type="text" id="shipping_name" name="shipping_name" required>
                    </div>
                    <div class="form-group">
                        <label for="shipping_email">Email *</label>
                        <input type="email" id="shipping_email" name="shipping_email" required>
                    </div>
                    <div class="form-group">
                        <label for="shipping_phone">Téléphone *</label>
                        <input type="tel" id="shipping_phone" name="shipping_phone" required>
                    </div>
                    <div class="form-group">
                        <label for="shipping_address">Adresse *</label>
                        <input type="text" id="shipping_address" name="shipping_address" required>
                    </div>
                    <div class="form-group">
                        <label for="shipping_city">Ville *</label>
                        <input type="text" id="shipping_city" name="shipping_city" required>
                    </div>
                    <div class="form-group">
                        <label for="shipping_zip">Code Postal *</label>
                        <input type="text" id="shipping_zip" name="shipping_zip" required>
                    </div>
                    <div class="form-group">
                        <label for="shipping_country">Pays *</label>
                        <input type="text" id="shipping_country" name="shipping_country" value="France" required>
                    </div>
                </div>
            </div>
            
            <!-- Billing Address -->
            <div class="checkout-section">
                <h3 style="color: #0aff9d; margin-bottom: 20px;">Adresse de Facturation</h3>
                <div class="same-as-shipping">
                    <label>
                        <input type="checkbox" id="same_as_shipping" checked>
                        Même adresse que la livraison
                    </label>
                </div>
                
                <div id="billing_fields">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="billing_name">Nom complet *</label>
                            <input type="text" id="billing_name" name="billing_name">
                        </div>
                        <div class="form-group">
                            <label for="billing_address">Adresse *</label>
                            <input type="text" id="billing_address" name="billing_address">
                        </div>
                        <div class="form-group">
                            <label for="billing_city">Ville *</label>
                            <input type="text" id="billing_city" name="billing_city">
                        </div>
                        <div class="form-group">
                            <label for="billing_zip">Code Postal *</label>
                            <input type="text" id="billing_zip" name="billing_zip">
                        </div>
                        <div class="form-group">
                            <label for="billing_country">Pays *</label>
                            <input type="text" id="billing_country" name="billing_country" value="France">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Payment Method -->
            <div class="checkout-section">
                <h3 style="color: #0aff9d; margin-bottom: 20px;">Méthode de Paiement</h3>
                <div class="form-group">
                    <label>
                        <input type="radio" name="payment_method" value="card" checked>
                        Carte de crédit
                    </label>
                    <label style="margin-left: 20px;">
                        <input type="radio" name="payment_method" value="paypal">
                        PayPal
                    </label>
                </div>
            </div>
            
            <!-- Notes -->
            <div class="form-group">
                <label for="order_notes">Notes pour la commande (optionnel)</label>
                <textarea id="order_notes" name="order_notes" placeholder="Instructions spéciales..."></textarea>
            </div>
            
            <!-- Submit Button -->
            <div style="text-align: center; margin-top: 30px;">
                <button type="submit" class="btn-auth btn-primary" style="padding: 15px 40px; font-size: 18px;">
                    <span class="btn-icon">▶</span> Confirmer et Payer
                </button>
            </div>
        </form>
    </main>

    <script>
        // Handle "same as shipping" checkbox
        document.getElementById('same_as_shipping').addEventListener('change', function() {
            const billingFields = document.getElementById('billing_fields');
            const billingInputs = billingFields.querySelectorAll('input');
            
            if (this.checked) {
                billingFields.style.display = 'none';
                billingInputs.forEach(input => input.removeAttribute('required'));
            } else {
                billingFields.style.display = 'block';
                billingInputs.forEach(input => input.setAttribute('required', 'required'));
            }
        });
        
        // Initialize
        document.getElementById('same_as_shipping').dispatchEvent(new Event('change'));
    </script>
</body>
</html>