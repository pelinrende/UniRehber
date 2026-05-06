<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("includes/db.php");

$sql = "ALTER TABLE universities ADD COLUMN view_count INT NOT NULL DEFAULT 0";

if (mysqli_query($conn, $sql)) {
    echo "view_count sütunu eklendi.";
} else {
    echo "Hata veya zaten mevcut olabilir: " . mysqli_error($conn);
}
?>