CREATE DATABASE IF NOT EXISTS symfony_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'symfony_test'@'%' IDENTIFIED BY 'password_test';
GRANT ALL PRIVILEGES ON symfony_test.* TO 'symfony_test'@'%';
FLUSH PRIVILEGES;
