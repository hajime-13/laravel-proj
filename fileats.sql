-- ============================================================
--  FIL EATS — Full Database SQL
--  Database: fileats
--  Run this in phpMyAdmin > SQL tab
-- ============================================================

CREATE DATABASE IF NOT EXISTS `fileats`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `fileats`;

-- ------------------------------------------------------------
-- 1. USERS
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`              VARCHAR(255)    NOT NULL,
  `email`             VARCHAR(255)    NOT NULL UNIQUE,
  `address`           VARCHAR(500)    DEFAULT NULL,
  `gender`            VARCHAR(20)     DEFAULT NULL,
  `avatar`            VARCHAR(255)    DEFAULT NULL,
  `is_admin`          TINYINT(1)      NOT NULL DEFAULT 0,
  `email_verified_at` TIMESTAMP       DEFAULT NULL,
  `password`          VARCHAR(255)    NOT NULL,
  `remember_token`    VARCHAR(100)    DEFAULT NULL,
  `created_at`        TIMESTAMP       DEFAULT NULL,
  `updated_at`        TIMESTAMP       DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 2. MENU ITEMS
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `menu_items` (
  `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`     BIGINT UNSIGNED NOT NULL,
  `name`        VARCHAR(255)    NOT NULL,
  `category`    VARCHAR(100)    NOT NULL DEFAULT 'Main',
  `description` TEXT            DEFAULT NULL,
  `image`       VARCHAR(255)    DEFAULT NULL,
  `price`       DECIMAL(10,2)   NOT NULL,
  `available`   TINYINT(1)      NOT NULL DEFAULT 1,
  `created_at`  TIMESTAMP       DEFAULT NULL,
  `updated_at`  TIMESTAMP       DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_menu_user`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 3. ORDERS
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `orders` (
  `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`       BIGINT UNSIGNED NOT NULL,
  `customer_name` VARCHAR(255)    NOT NULL,
  `table_number`  VARCHAR(50)     DEFAULT NULL,
  `status`        VARCHAR(20)     NOT NULL DEFAULT 'pending'
                  COMMENT 'pending | preparing | served | cancelled',
  `total_amount`  DECIMAL(10,2)   NOT NULL DEFAULT 0.00,
  `notes`         TEXT            DEFAULT NULL,
  `created_at`    TIMESTAMP       DEFAULT NULL,
  `updated_at`    TIMESTAMP       DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_order_user`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 4. ORDER ITEMS
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `order_items` (
  `id`           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id`     BIGINT UNSIGNED NOT NULL,
  `menu_item_id` BIGINT UNSIGNED NOT NULL,
  `quantity`     INT             NOT NULL,
  `unit_price`   DECIMAL(10,2)   NOT NULL,
  `subtotal`     DECIMAL(10,2)   NOT NULL,
  `created_at`   TIMESTAMP       DEFAULT NULL,
  `updated_at`   TIMESTAMP       DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_item_order`
    FOREIGN KEY (`order_id`)     REFERENCES `orders`     (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_item_menu`
    FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 5. LARAVEL SYSTEM TABLES
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email`      VARCHAR(255) NOT NULL,
  `token`      VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP    DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `sessions` (
  `id`            VARCHAR(255)    NOT NULL,
  `user_id`       BIGINT UNSIGNED DEFAULT NULL,
  `ip_address`    VARCHAR(45)     DEFAULT NULL,
  `user_agent`    TEXT            DEFAULT NULL,
  `payload`       LONGTEXT        NOT NULL,
  `last_activity` INT             NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cache` (
  `key`        VARCHAR(255) NOT NULL,
  `value`      MEDIUMTEXT   NOT NULL,
  `expiration` INT          NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key`        VARCHAR(255) NOT NULL,
  `owner`      VARCHAR(255) NOT NULL,
  `expiration` INT          NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 6. SAMPLE DATA
-- ------------------------------------------------------------

-- Admin user  (password: "password")
INSERT INTO `users` (`id`, `name`, `email`, `address`, `gender`, `is_admin`, `password`, `created_at`, `updated_at`) VALUES
(1, 'Admin User', 'admin@example.com', 'Manila, Philippines', 'Male', 1,
 '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 NOW(), NOW());

-- Menu items
INSERT INTO `menu_items` (`user_id`, `name`, `category`, `description`, `image`, `price`, `available`, `created_at`, `updated_at`) VALUES
(1, 'Chicken Adobo',    'Main',      'Classic Filipino chicken adobo',          NULL, 120.00, 1, NOW(), NOW()),
(1, 'Pork Sinigang',    'Main',      'Sour tamarind broth with pork',           NULL, 140.00, 1, NOW(), NOW()),
(1, 'Beef Caldereta',   'Main',      'Rich tomato-based beef stew',             NULL, 160.00, 1, NOW(), NOW()),
(1, 'Pancit Canton',    'Main',      'Stir-fried noodles with vegetables',      NULL, 100.00, 1, NOW(), NOW()),
(1, 'Lumpiang Shanghai','Appetizer', 'Crispy Filipino spring rolls',            NULL,  60.00, 1, NOW(), NOW()),
(1, 'Tokwa\'t Baboy',   'Appetizer', 'Tofu and pork with vinegar dip',         NULL,  75.00, 1, NOW(), NOW()),
(1, 'Steamed Rice',     'Side',      'Plain steamed white rice',               NULL,  25.00, 1, NOW(), NOW()),
(1, 'Garlic Rice',      'Side',      'Fried rice with garlic',                 NULL,  35.00, 1, NOW(), NOW()),
(1, 'Halo-Halo',        'Dessert',   'Mixed Filipino shaved ice dessert',      NULL,  80.00, 1, NOW(), NOW()),
(1, 'Leche Flan',       'Dessert',   'Classic Filipino caramel custard',       NULL,  55.00, 1, NOW(), NOW()),
(1, 'Coke (Regular)',   'Beverage',  '350ml can',                              NULL,  40.00, 1, NOW(), NOW()),
(1, 'Iced Mango Juice', 'Beverage',  'Fresh blended mango with ice',           NULL,  65.00, 1, NOW(), NOW()),
(1, 'Mineral Water',    'Beverage',  '500ml bottle',                           NULL,  25.00, 1, NOW(), NOW());

-- Sample orders
INSERT INTO `orders` (`user_id`, `customer_name`, `table_number`, `status`, `total_amount`, `created_at`, `updated_at`) VALUES
(1, 'Maria Santos',  'Table 1', 'served',    305.00, DATE_SUB(NOW(), INTERVAL 6 DAY), DATE_SUB(NOW(), INTERVAL 6 DAY)),
(1, 'Jose Reyes',    'Table 3', 'served',    240.00, DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_SUB(NOW(), INTERVAL 5 DAY)),
(1, 'Ana Cruz',      'Table 2', 'served',    290.00, DATE_SUB(NOW(), INTERVAL 4 DAY), DATE_SUB(NOW(), INTERVAL 4 DAY)),
(1, 'Pedro Lim',     'Table 5', 'served',    185.00, DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_SUB(NOW(), INTERVAL 3 DAY)),
(1, 'Rosa Bautista', 'Table 4', 'served',    370.00, DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_SUB(NOW(), INTERVAL 2 DAY)),
(1, 'Luis Garcia',   'Table 1', 'served',    260.00, DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY)),
(1, 'Carla Ramos',   'Table 2', 'preparing', 185.00, NOW(), NOW()),
(1, 'Ben Flores',    'Table 6', 'pending',   160.00, NOW(), NOW());

-- Order items (order_id, menu_item_id, quantity, unit_price, subtotal)
INSERT INTO `order_items` (`order_id`, `menu_item_id`, `quantity`, `unit_price`, `subtotal`, `created_at`, `updated_at`) VALUES
-- Order 1: Maria Santos
(1, 1, 1, 120.00, 120.00, NOW(), NOW()),
(1, 7, 2,  25.00,  50.00, NOW(), NOW()),
(1, 11,1,  40.00,  40.00, NOW(), NOW()),
-- Order 2: Jose Reyes
(2, 2, 1, 140.00, 140.00, NOW(), NOW()),
(2, 8, 1,  35.00,  35.00, NOW(), NOW()),
(2, 12,1,  65.00,  65.00, NOW(), NOW()),
-- Order 3: Ana Cruz
(3, 3, 1, 160.00, 160.00, NOW(), NOW()),
(3, 7, 1,  25.00,  25.00, NOW(), NOW()),
(3, 10,1,  55.00,  55.00, NOW(), NOW()),
-- Order 4: Pedro Lim
(4, 4, 1, 100.00, 100.00, NOW(), NOW()),
(4, 5, 1,  60.00,  60.00, NOW(), NOW()),
(4, 13,1,  25.00,  25.00, NOW(), NOW()),
-- Order 5: Rosa Bautista
(5, 1, 1, 120.00, 120.00, NOW(), NOW()),
(5, 2, 1, 140.00, 140.00, NOW(), NOW()),
(5, 7, 1,  25.00,  25.00, NOW(), NOW()),
(5, 9, 1,  80.00,  80.00, NOW(), NOW()),
-- Order 6: Luis Garcia
(6, 3, 1, 160.00, 160.00, NOW(), NOW()),
(6, 6, 1,  75.00,  75.00, NOW(), NOW()),
(6, 8, 1,  35.00,  35.00, NOW(), NOW()),
-- Order 7: Carla Ramos
(7, 1, 1, 120.00, 120.00, NOW(), NOW()),
(7, 7, 1,  25.00,  25.00, NOW(), NOW()),
(7, 12,1,  65.00,  65.00, NOW(), NOW()),
-- Order 8: Ben Flores
(8, 4, 1, 100.00, 100.00, NOW(), NOW()),
(8, 8, 1,  35.00,  35.00, NOW(), NOW()),
(8, 13,1,  25.00,  25.00, NOW(), NOW());

-- ============================================================
-- Done! Login: admin@example.com / password
-- ============================================================
