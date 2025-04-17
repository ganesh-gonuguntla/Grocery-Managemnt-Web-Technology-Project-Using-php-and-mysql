-- Create database
CREATE DATABASE IF NOT EXISTS grocery_management;
USE grocery_management;

-- Create users table
CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    address TEXT NOT NULL,
    phone VARCHAR(20) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create categories table
CREATE TABLE categories (
    category_id INT PRIMARY KEY AUTO_INCREMENT,
    category_name VARCHAR(50) NOT NULL,
    description TEXT,
    image_url VARCHAR(255)
);

-- Create products table
CREATE TABLE products (
    product_id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT,
    product_name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock_quantity INT NOT NULL DEFAULT 0,
    image_url VARCHAR(255),
    FOREIGN KEY (category_id) REFERENCES categories(category_id)
);

-- Create orders table
CREATE TABLE orders (
    order_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    total_amount DECIMAL(10,2) NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Create order_items table
CREATE TABLE order_items (
    order_item_id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT,
    product_id INT,
    quantity INT NOT NULL,
    price_per_unit DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id),
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

-- Create cart table
CREATE TABLE cart (
    cart_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    product_id INT,
    quantity INT NOT NULL DEFAULT 1,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

-- Insert default categories
INSERT INTO categories (category_name, description, image_url) VALUES
('Fruits', 'Fresh and seasonal fruits', 'assets/images/products/fruits.jpg'),
('Vegetables', 'Fresh and organic vegetables', 'assets/images/products/vegetables.jpg'),
('Dairy Products', 'Fresh dairy products', 'assets/images/products/dairy.jpg'),
('Daily Essentials', 'Essential household items', 'assets/images/products/essentials.jpg');

-- Insert sample products (9 items per category)
INSERT INTO products (category_id, product_name, description, price, stock_quantity, image_url) VALUES
-- Fruits
(1, 'Apple', 'Fresh red apples', 2.99, 100, 'assets/images/products/apple.jpg'),
(1, 'Banana', 'Yellow ripe bananas', 1.99, 150, 'assets/images/products/banana.webp'),
(1, 'Orange', 'Sweet juicy oranges', 3.49, 80, 'assets/images/products/orange.jpg'),
(1, 'Grapes', 'Fresh green grapes', 4.99, 60, 'assets/images/products/grapes.jpg'),
(1, 'Mango', 'Sweet ripe mangoes', 5.99, 40, 'assets/images/products/mango.jpg'),
(1, 'Pineapple', 'Fresh pineapples', 6.99, 30, 'assets/images/products/pineapple.webp'),
(1, 'Strawberry', 'Fresh strawberries', 4.49, 50, 'assets/images/products/strawberry.jpg'),
(1, 'Watermelon', 'Sweet watermelons', 7.99, 20, 'assets/images/products/watermelon.jpg'),
(1, 'Kiwi', 'Fresh kiwis', 3.99, 45, 'assets/images/products/kiwi.jpg'),

-- Vegetables
(2, 'Tomato', 'Fresh red tomatoes', 1.99, 120, 'assets/images/products/tomato.jpg'),
(2, 'Potato', 'Fresh potatoes', 2.49, 200, 'assets/images/products/potato.jpg'),
(2, 'Onion', 'Fresh onions', 1.49, 180, 'assets/images/products/onion.jpg'),
(2, 'Carrot', 'Fresh carrots', 1.99, 150, 'assets/images/products/carrot.jpg'),
(2, 'Cucumber', 'Fresh cucumbers', 1.29, 100, 'assets/images/products/cucmber.webp'),
(2, 'Bell Pepper', 'Fresh bell peppers', 2.99, 80, 'assets/images/products/bell_pepper.jpg'),
(2, 'Broccoli', 'Fresh broccoli', 3.49, 60, 'assets/images/products/brocoli.jpg'),
(2, 'Spinach', 'Fresh spinach', 2.99, 70, 'assets/images/products/spinach.jpg'),
(2, 'Lettuce', 'Fresh lettuce', 1.99, 90, 'assets/images/products/lettuce.avif'),

-- Dairy Products
(3, 'Milk', 'Fresh milk', 3.99, 100, 'assets/images/products/milk.jpg'),
(3, 'Cheese', 'Cheddar cheese', 4.99, 80, 'assets/images/products/cheese.webp'),
(3, 'Yogurt', 'Plain yogurt', 2.99, 120, 'assets/images/products/yogurt.webp'),
(3, 'Butter', 'Fresh butter', 3.49, 60, 'assets/images/products/butter.webp'),
(3, 'Cream', 'Fresh cream', 4.49, 40, 'assets/images/products/cream.png'),
(3, 'Eggs', 'Fresh eggs (12)', 5.99, 150, 'assets/images/products/eggs.avif'),
(3, 'Cottage Cheese', 'Fresh cottage cheese', 3.99, 70, 'assets/images/products/cottage_cheese.jpg'),
(3, 'Sour Cream', 'Fresh sour cream', 2.99, 50, 'assets/images/products/sour_cream.jpg'),
(3, 'Ice Cream', 'Vanilla ice cream', 4.99, 90, 'assets/images/products/icecream.jpg'),

-- Daily Essentials
(4, 'Bread', 'Fresh bread', 2.99, 100, 'assets/images/products/bread.jpg'),
(4, 'Rice', 'Basmati rice', 15.99, 80, 'assets/images/products/rice.jpg'),
(4, 'Pasta', 'Spaghetti pasta', 2.49, 120, 'assets/images/products/pasta.jpg'),
(4, 'Oil', 'Cooking oil', 5.99, 60, 'assets/images/products/oil.jpg'),
(4, 'Sugar', 'Granulated sugar', 3.99, 100, 'assets/images/products/sugar.jpg'),
(4, 'Salt', 'Table salt', 1.99, 150, 'assets/images/products/salt.jpg'),
(4, 'Coffee', 'Ground coffee', 8.99, 70, 'assets/images/products/coffee.jpg'),
(4, 'Tea', 'Black tea', 4.99, 90, 'assets/images/products/tea.webp'),
(4, 'Flour', 'All-purpose flour', 4.49, 110, 'assets/images/products/flour.jpg');

-- Create trigger to update stock after order
DELIMITER //
CREATE TRIGGER after_order_insert
AFTER INSERT ON order_items
FOR EACH ROW
BEGIN
    UPDATE products 
    SET stock_quantity = stock_quantity - NEW.quantity
    WHERE product_id = NEW.product_id;
END//
DELIMITER ;

-- Create view for product inventory
CREATE VIEW product_inventory AS
SELECT 
    p.product_id,
    p.product_name,
    c.category_name,
    p.price,
    p.stock_quantity,
    (p.price * p.stock_quantity) as total_value
FROM products p
JOIN categories c ON p.category_id = c.category_id;

-- Create view for order summary
CREATE VIEW order_summary AS
SELECT 
    o.order_id,
    u.username,
    o.order_date,
    o.total_amount,
    o.status,
    COUNT(oi.order_item_id) as total_items
FROM orders o
JOIN users u ON o.user_id = u.user_id
JOIN order_items oi ON o.order_id = oi.order_id
GROUP BY o.order_id; 