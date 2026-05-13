CREATE DATABASE IF NOT EXISTS ecommerce_db
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ecommerce_db;

CREATE TABLE IF NOT EXISTS users (
    id                 INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name               VARCHAR(120) NOT NULL,
    email              VARCHAR(191) NOT NULL,
    password_hash      VARCHAR(255) NOT NULL,
    phone              VARCHAR(30)  DEFAULT NULL,
    role               ENUM('customer','admin') NOT NULL DEFAULT 'customer',
    shipping_addresses JSON         DEFAULT NULL,
    remember_token     VARCHAR(64)  DEFAULT NULL,
    created_at         DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_users_email (email)
);

CREATE TABLE IF NOT EXISTS categories (
    id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    parent_id INT UNSIGNED DEFAULT NULL,
    name      VARCHAR(120) NOT NULL,
    FOREIGN KEY (parent_id) REFERENCES categories(id)
);

CREATE TABLE IF NOT EXISTS products (
    id                 INT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
    category_id        INT UNSIGNED  NOT NULL,
    name               VARCHAR(255)  NOT NULL,
    description        TEXT          DEFAULT NULL,
    price              DECIMAL(10,2) NOT NULL,
    stock_qty          INT UNSIGNED  NOT NULL DEFAULT 0,
    primary_image_path VARCHAR(500)  DEFAULT NULL,
    is_available       TINYINT(1)    NOT NULL DEFAULT 1,
    created_at         DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

CREATE TABLE IF NOT EXISTS orders (
    id               INT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
    user_id          INT UNSIGNED  NOT NULL,
    shipping_address TEXT          NOT NULL,
    payment_method   ENUM('Cash','Card') NOT NULL,
    total_amount     DECIMAL(10,2) NOT NULL,
    status           ENUM('Pending','Processing','Shipped','Delivered','Cancelled')
                     NOT NULL DEFAULT 'Pending',
    created_at       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS order_items (
    id         INT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
    order_id   INT UNSIGNED  NOT NULL,
    product_id INT UNSIGNED  NOT NULL,
    quantity   INT UNSIGNED  NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id)   REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE IF NOT EXISTS reviews (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id  INT UNSIGNED NOT NULL,
    user_id     INT UNSIGNED NOT NULL,
    rating      TINYINT(1)   NOT NULL CHECK (rating BETWEEN 1 AND 5),
    review_text TEXT         DEFAULT NULL,
    created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (user_id)    REFERENCES users(id),
    UNIQUE KEY uq_one_review (product_id, user_id)
);
CREATE INDEX IF NOT EXISTS idx_products_category   ON products(category_id);
CREATE INDEX IF NOT EXISTS idx_orders_user_created ON orders(user_id, created_at DESC);
CREATE INDEX IF NOT EXISTS idx_reviews_product     ON reviews(product_id);
CREATE INDEX IF NOT EXISTS idx_categories_parent   ON categories(parent_id);

INSERT IGNORE INTO users (name, email, password_hash, role)
VALUES ('Admin','admin@store.com',
  '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','admin');