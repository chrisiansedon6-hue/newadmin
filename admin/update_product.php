<?php
include 'connect.php';
session_start();

// Session guard
if (!isset($_SESSION['admin_id'])) {
    header('location:index.php');
    exit();
}

$admin_id = $_SESSION['admin_id'];

// Update product
if (isset($_POST['update'])) {
    $pid = $_POST['pid'];
    $name = trim(filter_var($_POST['name'], FILTER_SANITIZE_STRING));
    $price = filter_var($_POST['price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $details = trim(filter_var($_POST['details'], FILTER_SANITIZE_STRING));

    // Update basic details
    $update_product = $conn->prepare("UPDATE `products` SET name = ?, price = ?, details = ? WHERE id = ?");
    $update_product->execute([$name, $price, $details, $pid]);

    // Image handling
    $old_image = $_POST['old_image'];
    $image = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];

    if (!empty($image)) {
        $image_ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($image_ext, $allowed)) {
            $_SESSION['message'] = 'Invalid image type!';
        } else {
            $image_name = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $image);
            $image_folder = 'uploaded_img/' . $image_name;

            move_uploaded_file($image_tmp, $image_folder);

            // Update DB with new image
            $update_image = $conn->prepare("UPDATE `products` SET image = ? WHERE id = ?");
            $update_image->execute([$image_name, $pid]);

            // Remove old image
            if (!empty($old_image) && file_exists('uploaded_img/' . $old_image)) {
                unlink('uploaded_img/' . $old_image);
            }

            $_SESSION['message'] = 'Product updated successfully!';
        }
    } else {
        $_SESSION['message'] = 'Product details updated!';
    }

    header('location:products.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <title>Update Product</title>
   <link rel="stylesheet" href="admin_style.css">
</head>
<body>

<?php include 'admin_header.php'; ?>

<section class="update-product">
   <h1 class="heading">Edit Product</h1>

   <?php
      if (!isset($_GET['update'])) {
         echo '<p class="empty">No product selected.</p>';
         exit();
      }

      $update_id = $_GET['update'];

      $select_products = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
      $select_products->execute([$update_id]);

      if ($select_products->rowCount() > 0) {
         $f = $select_products->fetch(PDO::FETCH_ASSOC);
   ?>

   <?php if (isset($_SESSION['message'])): ?>
      <div class="alert success">
         <?= htmlspecialchars($_SESSION['message']); ?>
      </div>
      <?php unset($_SESSION['message']); ?>
   <?php endif; ?>

   <form method="POST" enctype="multipart/form-data" class="product-form">
      <input type="hidden" name="pid" value="<?= $f['id']; ?>">
      <input type="hidden" name="old_image" value="<?= htmlspecialchars($f['image']); ?>">

      <img src="uploaded_img/<?= htmlspecialchars($f['image']); ?>" alt="Product image" class="preview-img">

      <input type="text" name="name" required class="box-input" value="<?= htmlspecialchars($f['name']); ?>">
      <input type="number" name="price" required class="box-input" value="<?= htmlspecialchars($f['price']); ?>" min="0">
      <textarea name="details" class="box-input" required><?= htmlspecialchars($f['details']); ?></textarea>

      <input type="file" name="image" accept="image/*" class="box-input">

      <div class="flex-btn">
         <input type="submit" name="update" class="btn" value="Save Changes">
         <a href="products.php" class="option-btn">Back</a>
      </div>
   </form>

   <?php
      } else {
         echo '<p class="empty">Product not found.</p>';
      }
   ?>

</section>

</body>
</html>