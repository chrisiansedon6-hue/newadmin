<?php
include 'connect.php';
session_start();

// Session guard
if (!isset($_SESSION['admin_id'])) {
    header('location:index.php');
    exit();
}

// Delete user (secure POST)
if (isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];

    // Delete user and related orders
    $conn->prepare("DELETE FROM `orders` WHERE user_id = ?")->execute([$delete_id]);
    $conn->prepare("DELETE FROM `users` WHERE id = ?")->execute([$delete_id]);

    $_SESSION['message'] = 'User and related orders deleted!';
    header('location:users.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <title>User Accounts</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="admin_style.css">
</head>
<body>

<?php include 'admin_header.php'; ?>

<section class="accounts">
   <h1 class="heading">Customer Accounts</h1>

   <?php if (isset($_SESSION['message'])): ?>
      <div class="alert success">
         <?= htmlspecialchars($_SESSION['message']); ?>
      </div>
      <?php unset($_SESSION['message']); ?>
   <?php endif; ?>

   <div class="box-container">

      <?php
         $select_account = $conn->prepare("SELECT * FROM `users`");
         $select_account->execute();

         if ($select_account->rowCount() > 0) {
            while ($fetch_accounts = $select_account->fetch(PDO::FETCH_ASSOC)) {
      ?>
      <div class="box">
         <p>User ID: <span><?= htmlspecialchars($fetch_accounts['id']); ?></span></p>
         <p>Username: <span><?= htmlspecialchars($fetch_accounts['name']); ?></span></p>
         <p>Email: <span><?= htmlspecialchars($fetch_accounts['email']); ?></span></p>

         <!-- Secure delete form -->
         <form method="POST" onsubmit="return confirm('Delete user and all orders?');">
            <input type="hidden" name="delete_id" value="<?= $fetch_accounts['id']; ?>">
            <button type="submit" class="delete-btn">Delete</button>
         </form>
      </div>
      <?php
            }
         } else {
            echo '<p class="empty">No users found.</p>';
         }
      ?>

   </div>
</section>

<script src="admin.js"></script>
</body>
</html>