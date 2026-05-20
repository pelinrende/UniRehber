<?php

// Tüm PHP hatalarını gösterir
error_reporting(E_ALL);

// Hataların ekranda görünmesini sağlar
ini_set('display_errors', 1);

/* 
   Veritabanı bağlantı dosyasını projeye dahil eder.
   Bu dosya içinde $conn bağlantısı bulunur.
*/
include("includes/db.php");

/* 
   universities tablosuna
   view_count adında yeni sütun ekler.

   INT:
   Sayısal veri tipi

   NOT NULL:
   Boş geçilemez

   DEFAULT 0:
   Varsayılan değer 0 olur
*/
$sql = "
    ALTER TABLE universities
    ADD COLUMN view_count INT NOT NULL DEFAULT 0
";

/* 
   SQL sorgusunu çalıştırır.
   Eğer başarılıysa başarı mesajı gösterir.
*/
if (mysqli_query($conn, $sql)) {

    echo "view_count sütunu eklendi.";

} else {

    /* 
       Eğer hata varsa veya sütun zaten mevcutsa
       hata mesajını ekrana yazdırır.
    */
    echo "Hata veya zaten mevcut olabilir: " .
         mysqli_error($conn);
}
?>