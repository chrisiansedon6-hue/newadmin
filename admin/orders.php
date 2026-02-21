<?php
include 'connect.php';
session_start();

// Session guard
if (!isset($_SESSION['admin_id'])) {
    header('location:index.php');
    exit();
}

$admin_id = $_SESSION['admin_id'];

// Update payment/status
if (isset($_POST['update_payment'])) {
    $order_id = $_POST['order_id'];
    $payment_status = filter_var($_POST['payment_status'], FILTER_SANITIZE_STRING);

    $update_payment = $conn->prepare("UPDATE `orders` SET status = ? WHERE id = ?");
    $update_payment->execute([$payment_status, $order_id]);

    $_SESSION['message'] = 'Order status updated!';
    header('location:orders.php');
    exit();
}

// Delete order (secure POST)
if (isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];

    $delete_order = $conn->prepare("DELETE FROM `orders` WHERE id = ?");
    $delete_order->execute([$delete_id]);

    $_SESSION['message'] = 'Order deleted successfully!';
    header('location:orders.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <title>Orders</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="admin_style.css">
</head>
<body>

<?php include 'admin_header.php'; ?>

<section class="orders">
   <h1 class="heading">Placed Orders</h1>

   <?php if (isset($_SESSION['message'])): ?>
      <div class="alert success">
         <?= htmlspecialchars($_SESSION['message']); ?>
      </div>
      <?php unset($_SESSION['message']); ?>
   <?php endif; ?>

   <div class="box-container">

      <?php
         $select_orders = $conn->prepare("SELECT * FROM `orders` ORDER BY id DESC");
         $select_orders->execute();

         if ($select_orders->rowCount() > 0) {
            while ($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)) {
               $total = $fetch_orders['price'] * $fetch_orders['quantity'];
      ?>
      <div class="box">
         <p>Date: <span><?= htmlspecialchars($fetch_orders['order_date']); ?></span></p>
         <p>User ID: <span><?= htmlspecialchars($fetch_orders['user_id']); ?></span></p>
         <p>Product: <span><?= htmlspecialchars($fetch_orders['product_name']); ?></span></p>
         <p>Total: <span>₱<?= number_format($total, 2); ?></span></p>

         <form method="POST">
            <input type="hidden" name="order_id" value="<?= $fetch_orders['id']; ?>">

            <select name="payment_status" class="select" required>
               <option value="<?= htmlspecialchars($fetch_orders['status']); ?>" selected>
                  <?= htmlspecialchars($fetch_orders['status']); ?>
               </option>
               <option value="Pending">Pending</option>
               <option value="Completed">Completed</option>
            </select>

            <div class="flex-btn">
               <input type="submit" value="Update" class="option-btn" name="update_payment">

               <!-- Secure delete via POST -->
               <form method="POST" onsubmit="return confirm('Delete this order?');">
                  <input type="hidden" name="delete_id" value="<?= $fetch_orders['id']; ?>">
                  <button type="submit" class="delete-btn">Delete</button>
               </form>
            </div>
         </form>
      </div>
      <?php
            }
         } else {
            echo '<p class="empty">No orders placed yet!</p>';
         }
      ?>

   </div>
</section>

<script src="admin.js"></script>
</body>
</html>