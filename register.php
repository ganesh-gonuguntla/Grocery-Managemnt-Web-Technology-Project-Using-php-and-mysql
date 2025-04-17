<?php
include 'config.php';

// Check if user is already logged in
if (isLoggedIn()) {
    header("Location: index.php");
    exit();
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $email = sanitize($_POST['email']);
    $first_name = sanitize($_POST['first_name']);
    $last_name = sanitize($_POST['last_name']);
    $address = sanitize($_POST['address']);
    $phone = sanitize($_POST['phone']);

    // Validation
    if (empty($username)) $errors[] = "Username is required";
    if (empty($password)) $errors[] = "Password is required";
    if ($password !== $confirm_password) $errors[] = "Passwords do not match";
    if (empty($email)) $errors[] = "Email is required";
    if (empty($first_name)) $errors[] = "First name is required";
    if (empty($last_name)) $errors[] = "Last name is required";
    if (empty($address)) $errors[] = "Address is required";
    if (empty($phone)) $errors[] = "Phone number is required";

    // Check if username exists
    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        $errors[] = "Username already exists";
    }

    // Check if email exists
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        $errors[] = "Email already exists";
    }
    
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO users (username, password, email, first_name, last_name, address, phone) 
                 VALUES ('$username', '$hashed_password', '$email', '$first_name', '$last_name', '$address', '$phone')";
        
        if (mysqli_query($conn, $query)) {
            redirectWith('login.php', 'Registration successful! Please login.');
        } else {
            $errors[] = "Registration failed. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>FarmEasy - Create Account</title>
    <script src="https://kit.fontawesome.com/c3e707a6b1.js" crossorigin="anonymous"></script>
    <style>
      body {
        margin: 0;
        padding: 0;
        background-image: url("./background2.avif");
        background-size: cover;
        height: 100vh;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        font-family: Arial, sans-serif;
        padding-right: 50px;
      }

      .container {
        background: #bdbcbb;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        width: 400px;
        text-align: center;
        max-height: 90vh;
        overflow-y: auto;
      }

      .logo {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
      }

      .logo i {
        font-size: 40px;
        color: #2b572d;
        margin-right: 10px;
      }

      .logo h1 {
        color: #2b572d;
        margin: 0;
        font-size: 24px;
      }

      .form-group {
        margin-bottom: 15px;
        text-align: left;
        position: relative;
      }

      .form-group i {
        position: absolute;
        left: 10px;
        top: 35px;
        color: #000000;
      }

      .form-group label {
        display: block;
        margin-bottom: 5px;
        color: #333;
        font-weight: bold;
        font-size: 14px;
      }

      .form-group input,
      .form-group textarea {
        width: 100%;
        padding: 8px 35px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 14px;
        box-sizing: border-box;
      }

      .form-group textarea {
        padding-left: 35px;
      }

      .form-group input:focus,
      .form-group textarea:focus {
        outline: none;
        border-color: #2b572d;
      }

      .btn {
        background-color:  #3498db;
        color: snow;
        border: none;
        padding: 10px 15px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
        width: 100%;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
      }

      .btn:hover {
        background-color:#2980b9;
        color:snow;
      }

      .btn i {
        font-size: 16px;
      }

      .login-link {
        margin-top: 15px;
        color: black;
        font-size: 14px;
      }

      .login-link a {
        color: #2980b9;
        text-decoration: none;
        font-weight: bold;
      }

      .login-link a:hover {
        text-decoration: underline;
      }

      .error-message {
        color: #dc3545;
        margin-bottom: 15px;
        text-align: left;
        font-size: 12px;
      }

      .row {
        display: flex;
        gap: 10px;
      }

      .col-md-6 {
        flex: 1;
      }

      /* Custom scrollbar for the container */
      .container::-webkit-scrollbar {
        width: 6px;
      }

      .container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
      }

      .container::-webkit-scrollbar-thumb {
        background: #2b572d;
        border-radius: 3px;
      }

      .container::-webkit-scrollbar-thumb:hover {
        background: #4caf50;
      }

      /* Adjust eye icon position to center of left end */
      .toggle-password {
        position: absolute;
        left: 10px;
        top: 30%; /* Changed from fixed top to 50% */
        transform: translateY(-50%); /* Center vertically using transform */
        cursor: pointer;
        color: #666;
        z-index: 10;
        display: block;
        padding: 5px;
        background: transparent;
      }

      .toggle-password:hover {
        color: #2b572d;
      }

      /* Keep existing padding for password inputs */
      .form-group input[type="password"],
      .form-group input[type="text"] {
        padding-left: 35px;
        padding-right: 10px;
        z-index: 1;
      }
    </style>
    <script>
      // Completely rework toggle password function
      function togglePassword(inputId, iconId, event) {
        // Prevent event from bubbling up
        event.preventDefault();
        event.stopPropagation();
        
        const passwordInput = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        
        if (passwordInput.type === 'password') {
          passwordInput.type = 'text';
          icon.classList.remove('fa-eye');
          icon.classList.add('fa-eye-slash');
        } else {
          passwordInput.type = 'password';
          icon.classList.remove('fa-eye-slash');
          icon.classList.add('fa-eye');
        }
        
        // Return focus to input if it was focused before
        if (document.activeElement === icon) {
          passwordInput.focus();
        }
      }

      function validateForm() {
        // Get form values
        const firstName = document.getElementById('first_name').value;
        const lastName = document.getElementById('last_name').value;
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        const phone = document.getElementById('phone').value;

        // Name validation (only letters and spaces)
        const nameRegex = /^[A-Za-z\s]+$/;
        if (!nameRegex.test(firstName)) {
            alert('First name should only contain letters and spaces');
            return false;
        }
        if (!nameRegex.test(lastName)) {
            alert('Last name should only contain letters and spaces');
            return false;
        }

        // Email validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.com$/;
        if (!emailRegex.test(email)) {
            alert('Please enter a valid email address ending with .com');
            return false;
        }

        // Password matching validation
        if (password !== confirmPassword) {
            alert('Passwords do not match');
            return false;
        }

        // Phone validation (exactly 10 digits)
        const phoneRegex = /^\d{10}$/;
        if (!phoneRegex.test(phone)) {
            alert('Phone number must be exactly 10 digits');
            return false;
        }

        return true;
      }
    </script>
  </head>
  <body>
    <div class="container">
      <div class="logo">
        <i class="fas fa-shopping-basket"></i>
        <h1>FarmEasy</h1>
      </div>
      
      <?php if (!empty($errors)): ?>
        <div class="error-message">
          <?php foreach ($errors as $error): ?>
            <p><?php echo $error; ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="" onsubmit="return validateForm()">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="first_name">First Name</label>
              <i class="fas fa-user"></i>
              <input type="text" id="first_name" name="first_name" 
                     value="<?php echo isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : ''; ?>" 
                     placeholder="Enter your first name" required pattern="[A-Za-z\s]+" title="First name should only contain letters and spaces">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="last_name">Last Name</label>
              <i class="fas fa-user"></i>
              <input type="text" id="last_name" name="last_name" 
                     value="<?php echo isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : ''; ?>" 
                     placeholder="Enter your last name" required pattern="[A-Za-z\s]+" title="Last name should only contain letters and spaces">
            </div>
          </div>
        </div>

        <div class="form-group">
          <label for="username">Username</label>
          <i class="fas fa-user-circle"></i>
          <input type="text" id="username" name="username" 
                 value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" 
                 placeholder="Choose a username" required>
        </div>

        <div class="form-group">
          <label for="email">Email</label>
          <i class="fas fa-envelope"></i>
          <input type="email" id="email" name="email" 
                 value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                 placeholder="Enter your email address" required pattern="[^\s@]+@[^\s@]+\.com$" title="Please enter a valid email address ending with .com">
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <i class="fas fa-lock"></i>
          <input type="password" id="password" name="password" 
                 placeholder="Create a password" required>
        </div>

        <div class="form-group">
          <label for="confirm_password">Confirm Password</label>
          <i class="fas fa-lock"></i>
          <input type="password" id="confirm_password" name="confirm_password" 
                 placeholder="Re-enter your password" required>
        </div>

        <div class="form-group">
          <label for="address">Address</label>
          <i class="fas fa-map-marker-alt"></i>
          <textarea id="address" name="address" rows="2" 
                    placeholder="Enter your complete address" required><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
        </div>

        <div class="form-group">
          <label for="phone">Phone Number</label>
          <i class="fas fa-phone"></i>
          <input type="tel" id="phone" name="phone" 
                 value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>" 
                 placeholder="Enter your phone number" required pattern="\d{10}" title="Phone number must be exactly 10 digits">
        </div>

        <button type="submit" class="btn">
          <i class="fas fa-user-plus"></i>
          Create Account
        </button>
      </form>

      <div class="login-link">
        Already have an account? <a href="login.php">Login here</a>
      </div>
    </div>
  </body>
</html> 