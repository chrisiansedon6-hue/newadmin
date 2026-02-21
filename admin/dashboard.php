<?php
include 'connect.php';
session_start();

// Session protection
if (!isset($_SESSION['admin_id'])) {
    header('location:index.php');
    exit();
}

$admin_id = $_SESSION['admin_id'];

/**
 * Get row count safely
 */
function getCount($conn, $table) {
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM `$table`");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['total'] ?? 0;
}

/**
 * Calculate total completed revenue
 */
function getTotalRevenue($conn) {
    $stmt = $conn->prepare("SELECT SUM(price * quantity) AS revenue FROM `orders` WHERE status = ?");
    $stmt->execute(['Completed']);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['revenue'] ?? 0;
}

$total_completes = getTotalRevenue($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <title>Dashboard | Admin</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="admin_style.css">
</head>
<body>

<?php include 'admin_header.php'; ?>

<section class="dashboard">
   <h1 class="heading">Dashboard Overview</h1>

   <div class="box-container">

      <div class="box">
         <div class="icon"><i class="fas fa-wallet"></i></div>
         <h3>₱<?= number_format($total_completes, 2); ?></h3>
         <p>Total Revenue</p>
         <a href="orders.php" class="btn">View Sales</a>
      </div>

      <div class="box">
         <div class="icon"><i class="fas fa-box"></i></div>
         <h3><?= getCount($conn, 'products'); ?></h3>
         <p>Active Products</p>
         <a href="products.php" class="btn">Manage Menu</a>
      </div>

      <div class="box">
         <div class="icon"><i class="fas fa-users"></i></div>
         <h3><?= getCount($conn, 'users'); ?></h3>
         <p>Total Customers</p>
         <a href="users.php" class="btn">View Users</a>
      </div>

      <div class="box">
         <div class="icon"><i class="fas fa-user-shield"></i></div>
         <h3><?= getCount($conn, 'admins'); ?></h3>
         <p>Team Members</p>
         <a href="admin_accounts.php" class="btn">Admin Settings</a>
      </div>

   </div>
</section>

<script src="admin.js"></script>

</body>
</html>