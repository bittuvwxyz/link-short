CREATE DATABASE IF NOT EXISTS url_shortener CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE url_shortener;
CREATE TABLE admin (
 id INT AUTO_INCREMENT PRIMARY KEY,
 username VARCHAR(80) UNIQUE NOT NULL,
 password_hash VARCHAR(255) NOT NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);
INSERT INTO admin(username,password_hash) VALUES('admin','$2y$10$S8GTfaYidVfMxNjvWfHH8.3rS8dKJv8zP8Qf9I4qNo6Ry7.n7Owq6');
CREATE TABLE urls (
 id INT AUTO_INCREMENT PRIMARY KEY,
 short_code VARCHAR(64) UNIQUE,
 long_url TEXT NOT NULL,
 creator_ip VARCHAR(64) DEFAULT NULL,
 expires_at DATETIME NULL,
 password_hash VARCHAR(255) NULL,
 created_at DATETIME NOT NULL,
 INDEX idx_code(short_code),
 INDEX idx_created(created_at)
);
CREATE TABLE url_clicks (
 id BIGINT AUTO_INCREMENT PRIMARY KEY,
 url_id INT NOT NULL,
 ip_address VARCHAR(64),
 country VARCHAR(120),
 city VARCHAR(120),
 browser VARCHAR(60),
 device VARCHAR(60),
 os VARCHAR(60),
 referrer TEXT,
 user_agent TEXT,
 unique_hash CHAR(64),
 clicked_at DATETIME NOT NULL,
 INDEX idx_url(url_id), INDEX idx_click(clicked_at), INDEX idx_unique(unique_hash),
 CONSTRAINT fk_click_url FOREIGN KEY (url_id) REFERENCES urls(id) ON DELETE CASCADE
);
CREATE TABLE settings (
 id INT AUTO_INCREMENT PRIMARY KEY,
 setting_key VARCHAR(120) UNIQUE,
 setting_value TEXT,
 updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
