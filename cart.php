<?php
include 'config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

// Handle remove from cart
if (isset($_GET['remove'])) {
    $cart_id = (int)$_GET['remove'];
    $query = "DELETE FROM cart WHERE cart_id = $cart_id AND user_id = {$_SESSION['user_id']}";
    mysqli_query($conn, $query);
    redirectWith('cart.php', 'Item removed from cart.');
}

// Handle update quantity
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $cart_id = (int)$_POST['cart_id'];
    $quantity = (int)$_POST['quantity'];
    
    if ($quantity > 0) {
        $query = "UPDATE cart SET quantity = $quantity WHERE cart_id = $cart_id AND user_id = {$_SESSION['user_id']}";
        mysqli_query($conn, $query);
        redirectWith('cart.php', 'Cart updated successfully.');
    }
}

// Handle checkout
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['checkout'])) {
    // Get cart items
    $query = "SELECT c.*, p.price, p.product_name 
              FROM cart c 
              JOIN products p ON c.product_id = p.product_id 
              WHERE c.user_id = {$_SESSION['user_id']}";
    $cart_items = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($cart_items) > 0) {
        // Calculate total amount
        $total_amount = 0;
        while ($item = mysqli_fetch_assoc($cart_items)) {
            $total_amount += $item['price'] * $item['quantity'];
        }
        
        // Create order
        $query = "INSERT INTO orders (user_id, total_amount) VALUES ({$_SESSION['user_id']}, $total_amount)";
        if (mysqli_query($conn, $query)) {
            $order_id = mysqli_insert_id($conn);
            
            // Add order items
            mysqli_data_seek($cart_items, 0);
            while ($item = mysqli_fetch_assoc($cart_items)) {
                $product_id = $item['product_id'];
                $quantity = $item['quantity'];
                $price = $item['price'];
                
                $query = "INSERT INTO order_items (order_id, product_id, quantity, price_per_unit) 
                         VALUES ($order_id, $product_id, $quantity, $price)";
                mysqli_query($conn, $query);
            }
            
            // Clear cart
            $query = "DELETE FROM cart WHERE user_id = {$_SESSION['user_id']}";
            mysqli_query($conn, $query);
            
            redirectWith('order_success.php?order_id=' . $order_id, 'Order placed successfully!');
        }
    }
}

// Get cart items
$query = "SELECT c.*, p.product_name, p.price, p.image_url 
          FROM cart c 
          JOIN products p ON c.product_id = p.product_id 
          WHERE c.user_id = {$_SESSION['user_id']}";
$cart_items = mysqli_query($conn, $query);

// Calculate total
$total = 0;
$cart_items_array = [];
while ($item = mysqli_fetch_assoc($cart_items)) {
    $total += $item['price'] * $item['quantity'];
    $cart_items_array[] = $item;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
      body {
        padding-top: 100px; /* Increased padding to create more space below navbar */
        background-color: #f8f9fa;
        background-image: url(./contactback.jpeg);
        background-size: cover;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
      }

      .container {
        padding: 25px;
        border-radius: 15px;
        margin-top: 30px;
        margin-bottom: 30px;
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

      .navbar {
        background-color: #2b572d;
        box-shadow: 0 2px 4px rgba(0,0,0,.1);
      }
      .navbar-brand {
        color: white;
        font-weight: bold;
      }
      .nav-link {
        color: white;
      }
      .nav-link:hover {
        color: #4caf50;
      }
      .cart-item {
        background-color: #fff;
        border-radius: 15px;
        margin-bottom: 20px; /* Increased spacing between cart items */
        padding: 20px; /* Increased internal padding */
        box-shadow: 0 2px 4px rgba(0,0,0,.05);
      }
      .cart-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,.1);
      }
      .cart-item img {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 10px;
      }
      .quantity-input {
        width: 80px;
      }
      .summary-card {
        background-color: #fff;
        border-radius: 15px;
        padding: 25px; /* Increased padding */
        box-shadow: 0 2px 4px rgba(0,0,0,.05);
        margin-top: 20px; /* Added top margin for mobile view */
      }
      h2.mb-4 {
        margin-bottom: 25px !important; /* Increased margin below heading */
        color: #2b572d;
        font-weight: bold;
      }
      .alert {
        margin-bottom: 25px; /* Increased margin below alerts */
      }
    </style>
</head>
<body>
    <!-- Navigation -->
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

    <div class="container">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show">
                <?php
                echo $_SESSION['message'];
                unset($_SESSION['message']);
                unset($_SESSION['message_type']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-8">
                <h2 class="mb-4">Shopping Cart</h2>
                
                <?php if (empty($cart_items_array)): ?>
                    <div class="alert alert-info">
                        Your cart is empty. <a href="shop.php" class="alert-link">Continue shopping</a>
                    </div>
                <?php else: ?>
                    <?php foreach ($cart_items_array as $item): ?>
                        <div class="cart-item">
                            <div class="row align-items-center">
                                <div class="col-md-2">
                                    <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                                         alt="<?php echo htmlspecialchars($item['product_name']); ?>" 
                                         class="img-fluid">
                                </div>
                                <div class="col-md-4">
                                    <h5 class="mb-0"><?php echo htmlspecialchars($item['product_name']); ?></h5>
                                    <p class="text-muted mb-0">$<?php echo number_format($item['price'], 2); ?> each</p>
                                </div>
                                <div class="col-md-3">
                                    <form method="POST" class="d-flex align-items-center">
                                        <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                        <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" 
                                               min="1" max="99" class="form-control quantity-input me-2">
                                        <button type="submit" name="update" class="btn btn-outline-primary btn-sm">
                                            Update
                                        </button>
                                    </form>
                                </div>
                                <div class="col-md-2 text-end">
                                    <h5 class="mb-0">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></h5>
                                </div>
                                <div class="col-md-1 text-end">
                                    <a href="?remove=<?php echo $item['cart_id']; ?>" 
                                       class="btn btn-outline-danger btn-sm"
                                       onclick="return confirm('Are you sure you want to remove this item?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <div class="col-md-4">
                <div class="summary-card">
                    <h3 class="mb-4">Order Summary</h3>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Subtotal</span>
                        <span>$<?php echo number_format($total, 2); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Tax (10%)</span>
                        <span>$<?php echo number_format($total * 0.1, 2); ?></span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4">
                        <strong>Total</strong>
                        <strong>$<?php echo number_format($total * 1.1, 2); ?></strong>
                    </div>
                    
                    <?php if (!empty($cart_items_array)): ?>
                        <form method="POST">
                            <button type="submit" name="checkout" class="btn btn-primary w-100">
                                <i class="fas fa-shopping-cart me-2"></i>Proceed to Checkout
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 