<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("includes/env.php");
loadEnv(__DIR__ . "/.env");

$host = $_ENV['DB_HOST'] ?? 'localhost';
$user = $_ENV['DB_USER'] ?? 'root';
$pass = $_ENV['DB_PASS'] ?? '';
$dbName = $_ENV['DB_NAME'] ?? 'unirehber';

$conn = mysqli_connect($host, $user, $pass);

if (!$conn) {
    die("MySQL bağlantı hatası: " . mysqli_connect_error());
}

$sql = "CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
if (!mysqli_query($conn, $sql)) {
    die("Veritabanı oluşturma hatası: " . mysqli_error($conn));
}

mysqli_select_db($conn, $dbName);

$createUniversities = "
CREATE TABLE IF NOT EXISTS universities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    country VARCHAR(50) NOT NULL,
    city VARCHAR(100) NOT NULL,
    type VARCHAR(50) DEFAULT NULL,
    description TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

if (!mysqli_query($conn, $createUniversities)) {
    die("universities tablosu oluşturma hatası: " . mysqli_error($conn));
}

$check = mysqli_query($conn, "SELECT COUNT(*) AS total FROM universities");
$row = mysqli_fetch_assoc($check);

if ((int)$row['total'] === 0) {
    $insert = "
    INSERT INTO universities (name, country, city, type, description) VALUES
    ('Boğaziçi Üniversitesi', 'Türkiye', 'İstanbul', 'Devlet', 'Köklü akademik yapısı ve güçlü kampüs kültürüyle öne çıkar.'),
    ('İstanbul Teknik Üniversitesi', 'Türkiye', 'İstanbul', 'Devlet', 'Mühendislik alanlarında güçlü bir eğitim sunar.'),
    ('Marmara Üniversitesi', 'Türkiye', 'İstanbul', 'Devlet', 'Geniş bölüm seçenekleri ve merkezi konumuyla dikkat çeker.'),
    ('Orta Doğu Teknik Üniversitesi', 'Türkiye', 'Ankara', 'Devlet', 'Geniş kampüsü ve teknik bölümlerdeki başarısıyla bilinir.'),
    ('Hacettepe Üniversitesi', 'Türkiye', 'Ankara', 'Devlet', 'Sağlık ve araştırma alanında güçlü bir üniversitedir.'),
    ('Ege Üniversitesi', 'Türkiye', 'İzmir', 'Devlet', 'Geniş kampüsü ve öğrenci yaşamıyla öne çıkar.'),
    ('Dokuz Eylül Üniversitesi', 'Türkiye', 'İzmir', 'Devlet', 'Şehirle bütünleşmiş yapısıyla dikkat çeker.'),
    ('Doğu Akdeniz Üniversitesi', 'KKTC', 'Gazimağusa', 'Vakıf', 'Uluslararası öğrenci kitlesiyle öne çıkan bir üniversitedir.'),
    ('Yakın Doğu Üniversitesi', 'KKTC', 'Lefkoşa', 'Vakıf', 'Geniş kampüsü ve çok sayıda bölümüyle dikkat çeker.'),
    ('Girne Üniversitesi', 'KKTC', 'Girne', 'Vakıf', 'Farklı program seçenekleriyle öne çıkar.');
    ";

    if (!mysqli_query($conn, $insert)) {
        die("Örnek veri ekleme hatası: " . mysqli_error($conn));
    }

    echo "Kurulum tamamlandı. Veritabanı, tablo ve örnek veriler oluşturuldu.";
} else {
    echo "Kurulum tamamlandı. Veritabanı ve tablo zaten mevcut.";
}
?>