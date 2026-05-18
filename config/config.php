<?php
// Core configuration
session_start();

date_default_timezone_set('UTC');

$DB_HOST = 'localhost';
$DB_NAME = 'url_shortener';
$DB_USER = 'root';
$DB_PASS = '';
$BASE_URL = 'http://localhost/link-short';
$SITE_NAME = 'Core PHP URL Shortener';
$RATE_LIMIT_PER_HOUR = 30;

// Gmail SMTP settings for PHPMailer
$SMTP_HOST = 'smtp.gmail.com';
$SMTP_PORT = 587;
$SMTP_SECURE = 'tls';
$SMTP_USER = 'yourgmail@gmail.com';
$SMTP_PASS = 'your_app_password';
$SMTP_FROM = 'yourgmail@gmail.com';
$SMTP_FROM_NAME = 'URL Shortener';
$NOTIFY_TO = 'admin@example.com';

try {
    $pdo = new PDO("mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4", $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die('Database connection failed.');
}
