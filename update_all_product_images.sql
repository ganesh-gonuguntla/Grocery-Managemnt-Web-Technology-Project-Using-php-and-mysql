-- Update all product images to use local files
USE grocery_management;

-- Fruits
UPDATE products SET image_url = 'assets/images/products/apple.jpg' WHERE product_name = 'Apple';
UPDATE products SET image_url = 'assets/images/products/banana.webp' WHERE product_name = 'Banana';
UPDATE products SET image_url = 'assets/images/products/orange.jpg' WHERE product_name = 'Orange';
UPDATE products SET image_url = 'assets/images/products/grapes.jpg' WHERE product_name = 'Grapes';
UPDATE products SET image_url = 'assets/images/products/mango.jpg' WHERE product_name = 'Mango';
UPDATE products SET image_url = 'assets/images/products/pineapple.webp' WHERE product_name = 'Pineapple';
UPDATE products SET image_url = 'assets/images/products/strawberry.jpg' WHERE product_name = 'Strawberry';
UPDATE products SET image_url = 'assets/images/products/watermelon.jpg' WHERE product_name = 'Watermelon';
UPDATE products SET image_url = 'assets/images/products/kiwi.jpg' WHERE product_name = 'Kiwi';

-- Vegetables
UPDATE products SET image_url = 'assets/images/products/tomato.jpg' WHERE product_name = 'Tomato';
UPDATE products SET image_url = 'assets/images/products/potato.jpg' WHERE product_name = 'Potato';
UPDATE products SET image_url = 'assets/images/products/onion.jpg' WHERE product_name = 'Onion';
UPDATE products SET image_url = 'assets/images/products/carrot.jpg' WHERE product_name = 'Carrot';
UPDATE products SET image_url = 'assets/images/products/cucmber.webp' WHERE product_name = 'Cucumber';
UPDATE products SET image_url = 'assets/images/products/bell_pepper.jpg' WHERE product_name = 'Bell Pepper';
UPDATE products SET image_url = 'assets/images/products/brocoli.jpg' WHERE product_name = 'Broccoli';
UPDATE products SET image_url = 'assets/images/products/spinach.jpg' WHERE product_name = 'Spinach';
UPDATE products SET image_url = 'assets/images/products/lettuce.avif' WHERE product_name = 'Lettuce';

-- Dairy Products
UPDATE products SET image_url = 'assets/images/products/milk.jpg' WHERE product_name = 'Milk';
UPDATE products SET image_url = 'assets/images/products/cheese.webp' WHERE product_name = 'Cheese';
UPDATE products SET image_url = 'assets/images/products/yogurt.webp' WHERE product_name = 'Yogurt';
UPDATE products SET image_url = 'assets/images/products/butter.webp' WHERE product_name = 'Butter';
UPDATE products SET image_url = 'assets/images/products/cream.png' WHERE product_name = 'Cream';
UPDATE products SET image_url = 'assets/images/products/eggs.avif' WHERE product_name = 'Eggs';
UPDATE products SET image_url = 'assets/images/products/cottage_cheese.jpg' WHERE product_name = 'Cottage Cheese';
UPDATE products SET image_url = 'assets/images/products/sour_cream.jpg' WHERE product_name = 'Sour Cream';
UPDATE products SET image_url = 'assets/images/products/icecream.jpg' WHERE product_name = 'Ice Cream';

-- Daily Essentials
UPDATE products SET image_url = 'assets/images/products/bread.jpg' WHERE product_name = 'Bread';
UPDATE products SET image_url = 'assets/images/products/rice.jpg' WHERE product_name = 'Rice';
UPDATE products SET image_url = 'assets/images/products/pasta.jpg' WHERE product_name = 'Pasta';
UPDATE products SET image_url = 'assets/images/products/oil.jpg' WHERE product_name = 'Oil';
UPDATE products SET image_url = 'assets/images/products/sugar.jpg' WHERE product_name = 'Sugar';
UPDATE products SET image_url = 'assets/images/products/salt.jpg' WHERE product_name = 'Salt';
UPDATE products SET image_url = 'assets/images/products/coffee.jpg' WHERE product_name = 'Coffee';
UPDATE products SET image_url = 'assets/images/products/tea.webp' WHERE product_name = 'Tea';
UPDATE products SET image_url = 'assets/images/products/flour.jpg' WHERE product_name = 'Flour'; 