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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grocery Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
      body {
        padding-top: 70px;
        background-color: #f8f9fa;
        background-image: url(./contactback.jpeg);
        background-size: cover;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
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

      .category-card {
        border: none;
        border-radius: 15px;
        transition: all 0.3s ease;
        cursor: pointer;
        overflow: hidden;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      }
      .category-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.2);
      }
      .category-card img {
        height: 200px;
        object-fit: cover;
      }
      .product-card {
        border: none;
        border-radius: 15px;
        transition: transform 0.3s ease;
        background-color: #bdbcbb;
        width: 300px;
        margin: 0 auto;
      }
      .product-card:hover {
        transform: translateY(-5px);
      }
      .product-card img {
        height: 180px;
        object-fit: cover;
        border-radius: 15px 15px 0 0;
      }
      .product-card .card-body {
        padding: 20px;
      }
      .product-card .card-title {
        font-size: 1.2rem;
        margin-bottom: 0.75rem;
      }
      .product-card .card-text {
        font-size: 1rem;
        margin-bottom: 0.75rem;
      }
      .product-card .h5 {
        font-size: 1.3rem;
      }
      .quantity-input {
        width: 70px;
      }
      .category-heading {
        text-align: center;
        margin-bottom: 3rem;
        color: #2b572d;
      }
      .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 2rem;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
      }
      .category-section {
        margin: auto 0;
      }
      .category-row {
        display: flex;
        justify-content: center;
        gap: 2rem;
        flex-wrap: wrap;
        margin: 0 -1rem;
      }
      .category-col {
        flex: 0 0 calc(25% - 2rem);
        max-width: calc(25% - 2rem);
        padding: 0 1rem;
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

      <?php if (!$selected_category): ?>
        <!-- Categories -->
        <div class="category-section">
          <h2 class="category-heading" style="font-family: serif; font-weight: 600;">Shop by Category</h2>
          <div class="row g-4">
            <?php while ($category = mysqli_fetch_assoc($categories)): ?>
              <div class="col-md-3">
                <a href="?category=<?php echo $category['category_id']; ?>" class="text-decoration-none">
                  <div class="card category-card">
                    <?php if (strtolower($category['category_name']) === 'fruits'): ?>
                    <img src="assets/images/categories/fruitback.jpg" class="card-img-top" alt="Fruits">
                    <?php elseif (strtolower($category['category_name']) === 'dairy products'): ?>
                    <img src="assets/images/categories/diaryback.png" class="card-img-top" alt="Dairy Products">
                    <?php elseif (strtolower($category['category_name']) === 'vegetables'): ?>
                    <img src="assets/images/categories/veggiesback.jpg" class="card-img-top" alt="Vegetables">
                    <?php elseif (strtolower($category['category_name']) === 'daily essentials'): ?>
                    <img src="assets/images/categories/needsback.jpg" class="card-img-top" alt="Daily Essentials">
                    <?php else: ?>
                    <img src="assets/images/categories/<?php echo strtolower(str_replace(' ', '_', $category['category_name'])); ?>.jpg" 
                         class="card-img-top" alt="<?php echo htmlspecialchars($category['category_name']); ?>">
                    <?php endif; ?>
                    <div class="card-body text-center">
                      <h5 class="card-title text-dark"><?php echo htmlspecialchars($category['category_name']); ?></h5>
                    </div>
                  </div>
                </a>
              </div>
            <?php endwhile; ?>
          </div>
        </div>
      <?php else: ?>
        <!-- Products -->
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h2>Products</h2>
          <a href="shop.php" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left"></i> Back to Categories
          </a>
        </div>
        <div class="row g-4">
          <?php while ($product = mysqli_fetch_assoc($products)): ?>
            <div class="col-md-4">
              <div class="card product-card">
                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                     class="card-img-top" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                <div class="card-body">
                  <h5 class="card-title"><?php echo htmlspecialchars($product['product_name']); ?></h5>
                  <p class="card-text"><?php echo htmlspecialchars($product['description']); ?></p>
                  <div class="d-flex justify-content-between align-items-center">
                    <span class="h5 mb-0">$<?php echo number_format($product['price'], 2); ?></span>
                    <form method="POST" class="d-flex align-items-center">
                      <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                      <input type="number" name="quantity" value="1" min="1" max="99" 
                             class="form-control quantity-input me-2">
                      <button type="submit" name="add_to_cart" class="btn btn-primary">
                        <i class="fas fa-cart-plus"></i> Add
                      </button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          <?php endwhile; ?>
        </div>
      <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      // Update cart badge
      function updateCartBadge() {
        fetch('get_cart_count.php')
          .then(response => response.json())
          .then(data => {
            document.querySelector('.cart-badge').textContent = data.count;
          });
      }
      
      // Update cart badge on page load
      updateCartBadge();
    </script>
  </body>
</html> 