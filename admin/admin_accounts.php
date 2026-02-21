<?php
include 'connect.php';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('location:index.php');
    exit();
}

$admin_id = $_SESSION['admin_id'];

// Handle delete request (secure via POST)
if (isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];

    // Prevent self-deletion (optional but recommended)
    if ($delete_id == $admin_id) {
        echo "<script>alert('You cannot delete your own account!');</script>";
    } else {
        $stmt = $conn->prepare("DELETE FROM `admins` WHERE id = ?");
        $stmt->execute([$delete_id]);
        header('location:admin_accounts.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Accounts</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="admin_style.css">
</head>
<body>

<?php include 'admin_header.php'; ?>

<section class="accounts">
    <h1 class="heading">Admin Accounts</h1>

    <div class="box-container">

        <div class="box">
            <p>Register New Admin</p>
            <a href="index.php" class="option-btn">Register</a>
        </div>

        <?php
        $select_accounts = $conn->prepare("SELECT * FROM `admins`");
        $select_accounts->execute();

        while ($fetch_accounts = $select_accounts->fetch(PDO::FETCH_ASSOC)) {
        ?>
        <div class="box">
            <p>Admin ID: <span><?= htmlspecialchars($fetch_accounts['id']); ?></span></p>
            <p>Name: <span><?= htmlspecialchars($fetch_accounts['name']); ?></span></p>

            <div class="flex-btn">
                <!-- Secure delete form -->
                <form method="POST" onsubmit="return confirm('Delete this admin?');">
                    <input type="hidden" name="delete_id" value="<?= $fetch_accounts['id']; ?>">
                    <button type="submit" class="delete-btn">Delete</button>
                </form>

                <?php if ($fetch_accounts['id'] == $admin_id) { ?>
                    <a href="update_profile.php" class="option-btn">Update</a>
                <?php } ?>
            </div>
        </div>
        <?php } ?>

    </div>
</section>

<script src="admin.js"></script>

</body>
</html>