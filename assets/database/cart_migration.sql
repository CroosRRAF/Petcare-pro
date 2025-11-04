-- Migration script to update cart table for service support
-- Run this if you already have an existing database

USE petcare;

-- First, backup existing cart data to a temporary table
CREATE TEMPORARY TABLE cart_backup AS SELECT * FROM cart;

-- Drop the old cart table
DROP TABLE IF EXISTS cart;

-- Create the new cart table with service support
CREATE TABLE cart (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  product_id INT NULL,
  service_id INT NULL,
  item_type ENUM('product', 'service') NOT NULL,
  quantity INT DEFAULT 1,
  added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE,
  CHECK ((item_type = 'product' AND product_id IS NOT NULL AND service_id IS NULL)
     OR (item_type = 'service' AND service_id IS NOT NULL AND product_id IS NULL))
);

-- Restore old cart data (products only)
INSERT INTO cart (id, user_id, product_id, service_id, item_type, quantity, added_at)
SELECT id, user_id, product_id, NULL, 'product', quantity, added_at
FROM cart_backup;

-- Drop the temporary table
DROP TEMPORARY TABLE cart_backup;

SELECT 'Cart table successfully updated to support both products and services!' as message;
