-- Create orders table for storing checkout orders
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_code` varchar(50) NOT NULL UNIQUE,
  `user_name` varchar(100) NOT NULL,
  `user_email` varchar(100) NOT NULL,
  `user_phone` varchar(20) NOT NULL,
  `user_address` text NOT NULL,
  `total_price` decimal(10, 2) NOT NULL,
  `payment_method` varchar(50) DEFAULT 'vnpay',
  `order_status` varchar(20) DEFAULT 'pending',
  `vnpay_transaction_id` varchar(100),
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `order_code_idx` (`order_code`),
  KEY `vnpay_transaction_id_idx` (`vnpay_transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Create order_items table for storing items in each order
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10, 2) NOT NULL,
  `subtotal` decimal(10, 2) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  KEY `order_id_idx` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
