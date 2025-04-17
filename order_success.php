<?php
include 'config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

// Check if order_id is provided
if (!isset($_GET['order_id'])) {
    header("Location: index.php");
    exit();
}

$order_id = (int)$_GET['order_id'];

// Get order details
$query = "SELECT o.*, u.username, u.email, u.first_name, u.last_name, u.address, u.phone 
          FROM orders o 
          JOIN users u ON o.user_id = u.user_id 
          WHERE o.order_id = $order_id AND o.user_id = {$_SESSION['user_id']}";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    header("Location: index.php");
    exit();
}

$order = mysqli_fetch_assoc($result);

// Get order items
$query = "SELECT oi.*, p.product_name, p.image_url 
          FROM order_items oi 
          JOIN products p ON oi.product_id = p.product_id 
          WHERE oi.order_id = $order_id";
$order_items = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            padding-top: 100px;
            background-color: #f8f9fa;
            background-image: url(./contactback.jpeg);
            background-size: cover;
            min-height: 100vh;
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

        /* Keep existing success page styles */
        .success-card {
            background-color:#bdbcbb;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,.05);
            margin-bottom: 2rem;
        }

        .col-md-4 .success-card {
            background-color: #bdbcbb;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        }

        .order-item {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .order-item img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }

        .success-icon {
            font-size: 4rem;
            color: #28a745;
            margin-bottom: 1rem;
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
        <div class="success-card text-center">
            <i class="fas fa-check-circle success-icon"></i>
            <h2 class="mb-3">Order Placed Successfully!</h2>
            <p class="text-muted">Thank you for your purchase. Your order has been received.</p>
            <p class="mb-0">Order ID: #<?php echo $order_id; ?></p>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="success-card">
                    <h3 class="mb-4">Order Details</h3>
                    <?php while ($item = mysqli_fetch_assoc($order_items)): ?>
                        <div class="order-item">
                            <div class="row align-items-center">
                                <div class="col-md-2">
                                    <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                                         alt="<?php echo htmlspecialchars($item['product_name']); ?>" 
                                         class="img-fluid">
                                </div>
                                <div class="col-md-6">
                                    <h5 class="mb-0"><?php echo htmlspecialchars($item['product_name']); ?></h5>
                                    <p class="text-muted mb-0">Quantity: <?php echo $item['quantity']; ?></p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <p class="mb-0">$<?php echo number_format($item['price_per_unit'], 2); ?> each</p>
                                    <h5 class="mb-0">$<?php echo number_format($item['price_per_unit'] * $item['quantity'], 2); ?></h5>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <div class="col-md-4">
                <div class="success-card">
                    <h3 class="mb-4">Order Summary</h3>
                    <div class="mb-3">
                        <h5>Shipping Address</h5>
                        <p class="mb-0">
                            <?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?><br>
                            <?php echo htmlspecialchars($order['address']); ?><br>
                            Phone: <?php echo htmlspecialchars($order['phone']); ?>
                        </p>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span>
                        <span>$<?php echo number_format($order['total_amount'], 2); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tax (10%)</span>
                        <span>$<?php echo number_format($order['total_amount'] * 0.1, 2); ?></span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <strong>Total</strong>
                        <strong>$<?php echo number_format($order['total_amount'] * 1.1, 2); ?></strong>
                    </div>
                    <div class="text-center">
                        <a href="index.php" class="btn btn-primary">
                            <i class="fas fa-shopping-basket me-2"></i>Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>