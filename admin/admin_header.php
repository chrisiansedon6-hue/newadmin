<?php
// Ensure session is started in parent file before including this header
if (!isset($_SESSION)) {
    session_start();
}

// Check if admin_id exists
if (!isset($_SESSION['admin_id'])) {
    header('location:index.php');
    exit();
}

$admin_id = $_SESSION['admin_id'];

// Fetch profile safely
$select_profile = $conn->prepare("SELECT * FROM `admins` WHERE id = ?");
$select_profile->execute([$admin_id]);
$fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
?>

<header class="header">
   <div class="logo">Cake<span>Eats</span></div>

   <nav class="navbar">
      <a href="dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : ''; ?>">
         <i class="fas fa-home"></i> <span>Home</span>
      </a>
      <a href="products.php" class="<?= basename($_SERVER['PHP_SELF']) === 'products.php' ? 'active' : ''; ?>">
         <i class="fas fa-box"></i> <span>Products</span>
      </a>
      <a href="orders.php" class="<?= basename($_SERVER['PHP_SELF']) === 'orders.php' ? 'active' : ''; ?>">
         <i class="fas fa-shopping-cart"></i> <span>Orders</span>
      </a>
      <a href="users.php" class="<?= basename($_SERVER['PHP_SELF']) === 'users.php' ? 'active' : ''; ?>">
         <i class="fas fa-users"></i> <span>Users</span>
      </a>
      <a href="admin_accounts.php" class="<?= basename($_SERVER['PHP_SELF']) === 'admin_accounts.php' ? 'active' : ''; ?>">
         <i class="fas fa-user-shield"></i> <span>Admins</span>
      </a>
   </nav>

   <div class="profile">
      <p>Logged in as <b><?= htmlspecialchars($fetch_profile['name'] ?? 'Admin'); ?></b></p>
      <a href="admin_logout.php" class="delete-btn" onclick="return confirm('Logout of the dashboard?');">Logout</a>
   </div>
</header>