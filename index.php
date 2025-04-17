<?php
include 'config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

// Get categories
$query = "SELECT * FROM categories";
$categories = mysqli_query($conn, $query);

// Get products if category is selected
$selected_category = isset($_GET['category']) ? (int)$_GET['category'] : null;
$products = [];

if ($selected_category) {
    $query = "SELECT * FROM products WHERE category_id = $selected_category";
    $products = mysqli_query($conn, $query);
}

// Handle add to cart
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    
    // Check if product already in cart
    $query = "SELECT * FROM cart WHERE user_id = {$_SESSION['user_id']} AND product_id = $product_id";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        // Update quantity
        $cart_item = mysqli_fetch_assoc($result);
        $new_quantity = $cart_item['quantity'] + $quantity;
        $query = "UPDATE cart SET quantity = $new_quantity WHERE cart_id = {$cart_item['cart_id']}";
    } else {
        // Add new item
        $query = "INSERT INTO cart (user_id, product_id, quantity) VALUES ({$_SESSION['user_id']}, $product_id, $quantity)";
    }
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['message'] = "Product added to cart successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Failed to add product to cart.";
        $_SESSION['message_type'] = "danger";
    }
    
    // Redirect to prevent form resubmission
    header("Location: " . $_SERVER['PHP_SELF'] . ($selected_category ? "?category=$selected_category" : ""));
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>FarmEasy: A Transparent E-Commerce Platform for Farmers 🌱🛍️</title>
    <script src="https://kit.fontawesome.com/c3e707a6b1.js" crossorigin="anonymous"></script>
    <style>
      body {
        padding-top: 70px;
      }

      .header {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        background: rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 20px;
        z-index: 1000;
        box-sizing: border-box;
      }

      .logo {
        display: flex;
        align-items: center;
        font-size: 20px;
        color: green;
        font-weight: bolder;
        text-decoration: none;
      }

      .logo i {
        font-size: 50px;
        margin-right: 20px;
      }

      .nav-list {
        list-style: none;
        display: flex;
        gap: 20px;
        padding: 0;
        margin: 0;
      }

      .nav-list li {
        display: inline;
      }

      .nav-list a {
        text-decoration: none;
        color: black;
        font-size: 35px;
        font-weight: bolder;
      }

      .profile i {
        font-size: 45px;
        margin-left: 10px;
      }

      .cart {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        min-width: 70px;
        margin-left: 10px;
        text-decoration: none;
      }

      .cart i {
        font-size: 50px;
        color: black;
        font-weight: bolder;
      }

      .cart-count {
        position: absolute;
        top: 10px;
        right: 5px;
        background: white;
        color: black;
        padding: 3px 6px;
        border-radius: 50%;
        font-size: 14px;
        font-weight: bold;
      }
      
      body {
        display: flex;
        align-items: center;
        background-image: url("./contactback.jpeg");
        height: 100vh;
        background-size: cover;
        font-family: "Arial", sans-serif;
        margin: 0;
      }

      .matter {
        margin-left: 10%;
        font-family: serif;
        font-weight: bolder;
        font-size: x-large;
      }

      .gan {
        font-family: sans-serif;
        font-weight: 500;
        font-size: x-large;
      }

      .shop-btn {
        background-color: #2b572d;
        color: white;
        border: none;
        padding: 10px 20px;
        font-size: 18px;
        cursor: pointer;
        border-radius: 5px;
        transition: background 0.3s;
        margin-right: 10px;
      }

      .cart-btn {
        background-color: #2b572d;
        color: white;
        border: none;
        padding: 10px 20px;
        font-size: 18px;
        cursor: pointer;
        border-radius: 5px;
        transition: background 0.3s;
      }

      .shop-btn:hover, .cart-btn:hover {
        background-color: #4caf50;
      }

      .category {
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
      }
    </style>
  </head>
  <body>
  <header class="header">
      <a href="index.php" class="logo">
        <i class="fas fa-shopping-basket"></i>
        <span><b>FarmEasy</b></span>
      </a>
      <ul class="nav-list">
        <li><a href="index.php">Home</a></li>
        <li><a href="shop.php">Shop</a></li>
      </ul>
      <div style="display: flex; align-items: center;">
        <i class="fas fa-user-circle" style="font-size: 45px; margin-right: 10px; color: black;"></i>
        <span style="font-size: 20px; margin-right: 20px;">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
        <a href="cart.php" class="cart">
          <i class="fas fa-shopping-cart"></i>
          <span class="cart-count cart-badge">0</span>
        </a>
        <a href="logout.php" style="margin-left: 20px; text-decoration: none;">
          <i class="fas fa-sign-out-alt" style="font-size: 45px; color: black;"></i>
        </a>
      </div>
    </header>

    <div class="main matter">
      <h1>Go through products by clicking on shop.</h1>
      
      <h2 class="gan">We deliver fresh Veggies and Fruits straight from the farm</h2>
      <h2 class="gan">healthy and protein rich dairy products available</h2>
      <h2 class="gan">home needs at very reasonable price, Grab em!!!</h2>
      <br>

      <a href="shop.php"><button class="shop-btn">SHOP</button></a>
      <a href="cart.php"><button class="cart-btn">CART</button></a><br><br>
    </div>

    <script>
      // Update cart badge
      function updateCartBadge() {
        fetch('get_cart_count.php')
          .then(response => response.json())
          .then(data => {
            document.querySelector('.cart-count').textContent = data.count;
          });
      }
      
      // Update cart badge on page load
      updateCartBadge();
    </script>
  </body>
</html> 