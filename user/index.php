<?php
require_once 'config.php';

// Fetch products from database
$products_query = "SELECT * FROM products ORDER BY id DESC";
$products_result = $conn->query($products_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artisan Pastries - Handcrafted Since 1985</title>
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
                <a href="#menu" class="nav-link">Menu</a>
                <a href="#about" class="nav-link">About</a>
                <a href="#contact" class="nav-link">Contact</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="cart.php" class="nav-link">
                        <i class="fas fa-shopping-cart"></i> Cart
                        <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                            <span class="cart-badge"><?php echo count($_SESSION['cart']); ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="orders.php" class="nav-link">My Orders</a>
                    <div class="dropdown">
                        <button class="dropdown-btn"><?php echo htmlspecialchars($_SESSION['user_name']); ?> <i class="fas fa-chevron-down"></i></button>
                        <div class="dropdown-content">
                            <a href="profile.php">Profile</a>
                            <a href="logout.php">Logout</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="nav-link">Sign In</a>
                    <a href="signup.php" class="btn-primary">Sign Up</a>
                <?php endif; ?>
            </div>
            <button class="mobile-menu-btn" id="mobileMenuBtn">
                <i class="fas fa-bars"></i>
            </button>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-image">
            <img src="https://images.unsplash.com/photo-1736520537688-1f1f06b71605?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxhcnRpc2FuJTIwcGFzdHJpZXMlMjBjcm9pc3NhbnRzJTIwYmFrZXJ5fGVufDF8fHx8MTc3MTY0NzE4NHww&ixlib=rb-4.1.0&q=80&w=1080" alt="Artisan pastries">
            <div class="hero-overlay"></div>
        </div>
        <div class="hero-content">
            <h1 class="hero-title">
                Handcrafted Pastries
                <br>
                <span class="hero-subtitle">Since 1985</span>
            </h1>
            <p class="hero-text">
                Experience the art of traditional French pastry making with our daily selection of croissants, danishes, and sweet treats.
            </p>
            <div class="hero-buttons">
                <button class="btn-primary-large" onclick="scrollToMenu()">View Menu</button>
                <button class="btn-secondary-large">Visit Us</button>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="features">
        <div class="container">
            <div class="features-grid">
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3 class="feature-title">Fresh Daily</h3>
                    <p class="feature-desc">Baked fresh every morning using traditional techniques</p>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-award"></i>
                    </div>
                    <h3 class="feature-title">Award Winning</h3>
                    <p class="feature-desc">Recognized for excellence in artisan pastry making</p>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h3 class="feature-title">Made with Love</h3>
                    <p class="feature-desc">Each pastry crafted with care and premium ingredients</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Menu Section -->
    <section id="menu" class="menu-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Our Signature Selection</h2>
                <p class="section-subtitle">Freshly baked every morning</p>
            </div>
            <div class="menu-grid">
                <?php if ($products_result && $products_result->num_rows > 0): ?>
                    <?php while($product = $products_result->fetch_assoc()): ?>
                        <div class="menu-item">
                            <div class="menu-image">
                                <?php 
                                $image_path = 'uploads/' . $product['image'];
                                if (file_exists($image_path)): 
                                ?>
                                    <img src="<?php echo $image_path; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                <?php else: ?>
                                    <img src="https://via.placeholder.com/400x400?text=<?php echo urlencode($product['name']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                <?php endif; ?>
                            </div>
                            <div class="menu-info">
                                <div>
                                    <h3 class="menu-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                                    <p class="menu-desc"><?php echo htmlspecialchars($product['details']); ?></p>
                                </div>
                                <span class="menu-price">₱<?php echo number_format($product['price'], 2); ?></span>
                            </div>
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <form method="POST" action="add_to_cart.php" class="add-to-cart-form">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <button type="submit" class="btn-add-cart">
                                        <i class="fas fa-cart-plus"></i> Add to Cart
                                    </button>
                                </form>
                            <?php else: ?>
                                <a href="login.php" class="btn-add-cart">
                                    <i class="fas fa-sign-in-alt"></i> Login to Order
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p style="grid-column: 1/-1; text-align: center; color: #6b7280;">No products available at the moment.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about-section">
        <div class="container">
            <div class="about-content">
                <h2 class="section-title">Our Story</h2>
                <p class="about-text">
                    For over three decades, we've been bringing the authentic taste of French pastries to our community. Our master bakers start each day at 4 AM, carefully preparing every item by hand using time-honored techniques and the finest ingredients.
                </p>
                <p class="about-text">
                    From our flaky croissants to our delicate éclairs, each pastry tells a story of passion, tradition, and dedication to the craft.
                </p>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <h2 class="cta-title">Visit Us Today</h2>
            <p class="cta-text">
                Open daily from 7 AM to 7 PM. Come taste the difference that passion and quality make.
            </p>
            <button class="btn-white-large">Get Directions</button>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact" class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <div class="footer-logo">
                        <i class="fas fa-shopping-bag"></i>
                        <span>Artisan Pastries</span>
                    </div>
                    <p class="footer-text">Crafting delicious memories since 1985</p>
                </div>
                <div class="footer-col">
                    <h3 class="footer-title">Hours</h3>
                    <p class="footer-text">Monday - Sunday</p>
                    <p class="footer-text">7:00 AM - 7:00 PM</p>
                </div>
                <div class="footer-col">
                    <h3 class="footer-title">Contact</h3>
                    <p class="footer-text">123 Bakery Lane</p>
                    <p class="footer-text">contact@artisanpastries.com</p>
                    <p class="footer-text">(555) 123-4567</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 Artisan Pastries. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="script.js"></script>
</body>
</html>
