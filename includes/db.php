<?php

include("env.php");

// .env dosyasını yükle
loadEnv(__DIR__ . "/../.env");

// ENV değerlerini al
$host = $_ENV['DB_HOST'];
$user = $_ENV['DB_USER'];
$pass = $_ENV['DB_PASS'];
$db   = $_ENV['DB_NAME'];

// bağlantı
$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Veritabanı bağlantı hatası: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");