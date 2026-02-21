<?php
include 'connect.php';
session_start();

// Redirect if already logged in
if (isset($_SESSION['admin_id'])) {
    header('location:dashboard.php');
    exit();
}

// Flash message helper
function flash($msg) {
    $_SESSION['message'] = $msg;
}

// Login Logic
if (isset($_POST['login'])) {
    $name = trim($_POST['name']);
    $pass = $_POST['pass'];

    $stmt = $conn->prepare("SELECT * FROM `admins` WHERE name = ?");
    $stmt->execute([$name]);

    if ($stmt->rowCount() > 0) {
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if (password_verify($pass, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            header('location:dashboard.php');
            exit();
        } else {
            flash('Incorrect password!');
        }
    } else {
        flash('Account not found!');
    }
}

// Registration Logic
if (isset($_POST['register'])) {
    $name = trim($_POST['name']);
    $pass = $_POST['pass'];
    $cpass = $_POST['cpass'];

    if ($pass !== $cpass) {
        flash('Passwords do not match!');
    } else {
        $stmt = $conn->prepare("SELECT id FROM `admins` WHERE name = ?");
        $stmt->execute([$name]);

        if ($stmt->rowCount() > 0) {
            flash('Username already exists!');
        } else {
            $hashed = password_hash($pass, PASSWORD_DEFAULT);

            $insert = $conn->prepare("INSERT INTO `admins` (name, password) VALUES (?, ?)");
            $insert->execute([$name, $hashed]);

            flash('Account created! Please login.');
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <title>Admin Login | CakeEats</title>
   <link rel="stylesheet" href="admin_style.css">
   <style>
      body {
         display: flex;
         align-items: center;
         justify-content: center;
         background: linear-gradient(135deg, #fff1f5 0%, #ffe7ef 100%);
      }

      .form-container {
         width: 100%;
         max-width: 450px;
      }

      .box-input {
         width: 100%;
         padding: 12px;
         margin: 8px 0;
         border-radius: 8px;
         border: 1px solid rgba(0,0,0,0.12);
      }

      .toggle-link {
         margin-top: 18px;
         text-align: center;
         font-size: 0.9rem;
      }

      .toggle-link span {
         color: #e91e63;
         font-weight: 600;
         cursor: pointer;
      }

      .alert {
         padding: 12px;
         border-radius: 8px;
         margin-bottom: 15px;
      }

      .alert.success {
         background: #e7f9ee;
         color: #2e7d32;
      }

      .alert.error {
         background: #fdecea;
         color: #c62828;
      }
   </style>
</head>
<body>

<section class="form-container">

<?php if (isset($_SESSION['message'])): ?>
   <div class="alert error">
      <?= htmlspecialchars($_SESSION['message']); ?>
   </div>
   <?php unset($_SESSION['message']); ?>
<?php endif; ?>

<form action="" method="POST" id="login-form">
   <h2 style="text-align:center; color:#e91e63;">CakeEats</h2>
   <p style="text-align:center; color:#666;">Admin Login</p>

   <input type="text" name="name" required placeholder="Username" class="box-input">
   <input type="password" name="pass" required placeholder="Password" class="box-input">

   <input type="submit" value="Login" name="login" class="delete-btn" style="width:100%; margin-top:10px;">
   <p class="toggle-link">No account? <span onclick="toggleForm()">Register</span></p>
</form>

<form action="" method="POST" id="reg-form" style="display:none;">
   <h2 style="text-align:center; color:#e91e63;">Create Admin</h2>
   <p style="text-align:center; color:#666;">New Account</p>

   <input type="text" name="name" required placeholder="Username" class="box-input">
   <input type="password" name="pass" required placeholder="Password" class="box-input">
   <input type="password" name="cpass" required placeholder="Confirm Password" class="box-input">

   <input type="submit" value="Register" name="register" class="delete-btn" style="width:100%; margin-top:10px;">
   <p class="toggle-link">Already have account? <span onclick="toggleForm()">Login</span></p>
</form>

</section>

<script>
function toggleForm() {
   const login = document.getElementById('login-form');
   const reg = document.getElementById('reg-form');

   if (login.style.display === 'none') {
      login.style.display = 'block';
      reg.style.display = 'none';
   } else {
      login.style.display = 'none';
      reg.style.display = 'block';
   }
}
</script>

</body>
</html>