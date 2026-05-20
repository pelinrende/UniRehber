<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("../includes/db.php");

/* Veritabanı kontrolü */
if (!isset($conn)) {
    die("Veritabanı bağlantısı bulunamadı.");
}

/* ID kontrolü */
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    die("Geçersiz üniversite ID.");
}

$id = (int) $_GET["id"];

/* Üniversite var mı kontrol et */
$checkStmt = mysqli_prepare(
    $conn,
    "SELECT id, name FROM universities WHERE id = ?"
);

mysqli_stmt_bind_param($checkStmt, "i", $id);
mysqli_stmt_execute($checkStmt);

$result = mysqli_stmt_get_result($checkStmt);

if (!$result || mysqli_num_rows($result) === 0) {
    die("Üniversite bulunamadı.");
}

$university = mysqli_fetch_assoc($result);

/* Silme işlemi */
$deleteStmt = mysqli_prepare(
    $conn,
    "DELETE FROM universities WHERE id = ?"
);

if (!$deleteStmt) {
    die("SQL hazırlama hatası: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($deleteStmt, "i", $id);

if (mysqli_stmt_execute($deleteStmt)) {

    header(
        "Location: list-universities.php?success=" .
        urlencode($university["name"] . " silindi")
    );

    exit;

} else {

    die(
        "Silme işlemi başarısız: " .
        mysqli_stmt_error($deleteStmt)
    );
}
?>