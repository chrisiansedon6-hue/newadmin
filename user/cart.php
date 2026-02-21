<?php
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Handle cart updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $product_id = intval($_POST['product_id']);
        
        switch ($_POST['action']) {
            case 'update':
                $quantity = intval($_POST['quantity']);
                if ($quantity > 0) {
                    foreach ($_SESSION['cart'] as &$item) {
                        if ($item['id'] == $product_id) {
                            $item['quantity'] = $quantity;
                            break;
                        }
                    }
                }
                break;
                
            case 'remove':
                foreach ($_SESSION['cart'] as $key => $item) {
                    if ($item['id'] == $product_id) {
                        unset($_SESSION['cart'][$key]);
                        $_SESSION['cart'] = array_values($_SESSION['cart']); // Re-index array
                        break;
                    }
                }
                break;
                
            case 'clear':
                $_SESSION['cart'] = [];
                break;
        }
    }
    
    header('Location: cart.php');
    exit;
}

// Calculate cart totals
$cart_total = 0;
$cart_items = $_SESSION['cart'] ?? [];
foreach ($cart_items as $item) {
    $cart_total += $item['price'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Artisan Pastries</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <nav class="nav-container">
            <a href="index.php" class="logo">
                <i class="fas fa-shopping-bag"></i>
                <span>Artisan Pastries</span>
            </a>
            <div class="nav-menu" id="navMenu">
                <a href="index.php#menu" class="nav-link">Menu</a>
                <a href="cart.php" class="nav-link active">
                    <i class="fas fa-shopping-cart"></i> Cart
                </a>
                <a href="orders.php" class="nav-link">My Orders</a>
                <a href="logout.php" class="btn-primary">Logout</a>
            </div>
            <button class="mobile-menu-btn" id="mobileMenuBtn">
                <i class="fas fa-bars"></i>
            </button>
        </nav>
    </header>

    <div class="page-content">
        <div class="container">
            <h1 class="page-title">Shopping Cart</h1>
            
            <?php if (empty($cart_items)): ?>
                <div class="empty-state">
                    <i class="fas fa-shopping-cart"></i>
                    <h2>Your cart is empty</h2>
                    <p>Add some delicious pastries to your cart!</p>
                    <a href="index.php#menu" class="btn-primary-large">Browse Menu</a>
                </div>
            <?php else: ?>
                <div class="cart-layout">
                    <div class="cart-items">
                        <?php foreach ($cart_items as $item): ?>
                            <div class="cart-item">
                                <div class="cart-item-image">
                                    <?php 
                                    $image_path = 'uploads/' . $item['image'];
                                    if (file_exists($image_path)): 
                                    ?>
                                        <img src="<?php echo $image_path; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                    <?php else: ?>
                                        <img src="https://via.placeholder.com/100x100?text=<?php echo urlencode($item['name']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                    <?php endif; ?>
                                </div>
                                <div class="cart-item-details">
                                    <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                                    <p class="cart-item-price">₱<?php echo number_format($item['price'], 2); ?></p>
                                </div>
                                <div class="cart-item-quantity">
                                    <form method="POST" action="cart.php" class="quantity-form">
                                        <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                        <input type="hidden" name="action" value="update">
                                        <button type="submit" name="quantity" value="<?php echo $item['quantity'] - 1; ?>" class="qty-btn" <?php echo $item['quantity'] <= 1 ? 'disabled' : ''; ?>>-</button>
                                        <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" class="qty-input" onchange="this.form.submit()">
                                        <button type="submit" name="quantity" value="<?php echo $item['quantity'] + 1; ?>" class="qty-btn">+</button>
                                    </form>
                                </div>
                                <div class="cart-item-total">
                                    <p>₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
                                </div>
                                <form method="POST" action="cart.php" class="cart-item-remove">
                                    <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                    <input type="hidden" name="action" value="remove">
                                    <button type="submit" class="btn-remove">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="cart-summary">
                        <h2>Order Summary</h2>
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span>₱<?php echo number_format($cart_total, 2); ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Delivery Fee</span>
                            <span>₱50.00</span>
                        </div>
                        <div class="summary-divider"></div>
                        <div class="summary-row summary-total">
                            <span>Total</span>
                            <span>₱<?php echo number_format($cart_total + 50, 2); ?></span>
                        </div>
                        <a href="checkout.php" class="btn-checkout">Proceed to Checkout</a>
                        <form method="POST" action="cart.php" style="margin-top: 1rem;">
                            <input type="hidden" name="action" value="clear">
                            <button type="submit" class="btn-clear-cart">Clear Cart</button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
