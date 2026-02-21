<?php
include 'connect.php';
session_start();

// Session guard
if (!isset($_SESSION['admin_id'])) {
    header('location:index.php');
    exit();
}

$admin_id = $_SESSION['admin_id'];

// Ensure upload directory exists
$upload_dir = 'uploaded_img/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Add Product
if (isset($_POST['add_product'])) {
    $name = trim(htmlspecialchars($_POST['name'], ENT_QUOTES));
    $price = $_POST['price'];
    $details = trim(htmlspecialchars($_POST['details'], ENT_QUOTES));

    // Image validation
    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        $image_tmp = $_FILES['image']['tmp_name'];
        $image_ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($image_ext, $allowed)) {
            $_SESSION['message'] = 'Invalid image type!';
        } else {
            $image_name = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $image);
            $image_path = $upload_dir . $image_name;

            // Check duplicate product
            $check = $conn->prepare("SELECT id FROM `products` WHERE name = ?");
            $check->execute([$name]);

            if ($check->rowCount() > 0) {
                $_SESSION['message'] = 'Product already exists!';
            } else {
                move_uploaded_file($image_tmp, $image_path);

                $insert = $conn->prepare("INSERT INTO `products` (name, details, price, image) VALUES (?, ?, ?, ?)");
                $insert->execute([$name, $details, $price, $image_name]);

                $_SESSION['message'] = 'Product added successfully!';
            }
        }
    } else {
        $_SESSION['message'] = 'Please upload an image!';
    }

    header('location:products.php');
    exit();
}

// Delete Product (POST secure)
if (isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];

    $stmt = $conn->prepare("SELECT image FROM `products` WHERE id = ?");
    $stmt->execute([$delete_id]);
    $img = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($img && file_exists($upload_dir . $img['image'])) {
        unlink($upload_dir . $img['image']);
    }

    $conn->prepare("DELETE FROM `products` WHERE id = ?")->execute([$delete_id]);

    $_SESSION['message'] = 'Product removed!';
    header('location:products.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <title>Products | CakeEats Menu</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="admin_style.css">
</head>
<body>

<?php include 'admin_header.php'; ?>

<section class="products">
   <h1 class="heading">Add New Item</h1>

   <?php if (isset($_SESSION['message'])): ?>
      <div class="alert success">
         <?= htmlspecialchars($_SESSION['message']); ?>
      </div>
      <?php unset($_SESSION['message']); ?>
   <?php endif; ?>

   <form method="POST" enctype="multipart/form-data" class="product-form">
      <input type="text" name="name" required placeholder="Product name" class="box-input">
      <input type="number" name="price" required placeholder="Price" class="box-input" min="0">
      <textarea name="details" required placeholder="Product description" class="box-input"></textarea>
      <input type="file" name="image" accept="image/*" class="box-input" required>
      <input type="submit" value="Add to Menu" name="add_product" class="delete-btn" style="width:100%;">
   </form>

   <h1 class="heading">Explore Menu</h1>

   <div class="box-container show-products">
      <?php
         $select = $conn->prepare("SELECT * FROM `products` ORDER BY id DESC");
         $select->execute();

         if ($select->rowCount() > 0) {
            while ($product = $select->fetch(PDO::FETCH_ASSOC)) {
      ?>
         <div class="box">
            <img src="uploaded_img/<?= htmlspecialchars($product['image']); ?>" alt="Product image">
            <div class="name"><?= htmlspecialchars($product['name']); ?></div>
            <div class="price">₱<?= number_format($product['price'], 2); ?></div>
            <p class="details"><?= htmlspecialchars($product['details']); ?></p>

            <div class="flex-btn">
               <a href="update_product.php?update=<?= $product['id']; ?>" class="btn">Edit</a>

               <!-- Secure delete -->
               <form method="POST" onsubmit="return confirm('Delete this item?');">
                  <input type="hidden" name="delete_id" value="<?= $product['id']; ?>">
                  <button type="submit" class="delete-btn">Remove</button>
               </form>
            </div>
         </div>
      <?php
            }
         } else {
            echo '<p class="empty">No products available.</p>';
         }
      ?>
   </div>
</section>

<script src="admin.js"></script>
</body>
</html>