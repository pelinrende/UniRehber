<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("../includes/db.php");

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Geçersiz üniversite ID.");
}

$id = (int)$_GET['id'];

$stmt = mysqli_prepare($conn, "DELETE FROM universities WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    header("Location: list-universities.php");
    exit;
} else {
    echo "Silme işlemi başarısız.";
}
?>