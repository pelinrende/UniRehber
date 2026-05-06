<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("../includes/db.php");

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Geçersiz üniversite ID.");
}

$id = (int)$_GET['id'];

$stmt = mysqli_prepare($conn, "SELECT * FROM universities WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$university = mysqli_fetch_assoc($result);

if (!$university) {
    die("Üniversite bulunamadı.");
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    $country = trim($_POST['country']);
    $city = trim($_POST['city']);
    $type = trim($_POST['type']);
    $description = trim($_POST['description']);

    $updateStmt = mysqli_prepare($conn, "UPDATE universities SET name = ?, country = ?, city = ?, type = ?, description = ? WHERE id = ?");
    mysqli_stmt_bind_param($updateStmt, "sssssi", $name, $country, $city, $type, $description, $id);

    if (mysqli_stmt_execute($updateStmt)) {
        $message = "Üniversite başarıyla güncellendi.";

        $stmt = mysqli_prepare($conn, "SELECT * FROM universities WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $university = mysqli_fetch_assoc($result);
    } else {
        $message = "Güncelleme sırasında hata oluştu.";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Üniversite Düzenle</title>
    <style>
        body { font-family: Arial, sans-serif; background:#f8fafc; margin:0; padding:30px; }
        .container { max-width:700px; margin:0 auto; background:white; padding:30px; border-radius:16px; box-shadow:0 8px 24px rgba(0,0,0,0.06); }
        label { display:block; margin-bottom:8px; font-weight:bold; }
        input, textarea, select {
            width:100%; padding:12px; margin-bottom:18px; border:1px solid #cbd5e1;
            border-radius:10px; box-sizing:border-box;
        }
        button {
            background:linear-gradient(135deg, #4f46e5, #8b5cf6);
            color:white; border:none; padding:12px 18px; border-radius:10px; cursor:pointer;
        }
        .message { margin-bottom:16px; color:#166534; font-weight:bold; }
        .back-link { display:inline-block; margin-bottom:20px; text-decoration:none; color:#4f46e5; }
    </style>
</head>
<body>
    <div class="container">
        <a class="back-link" href="list-universities.php">← Listeye dön</a>
        <h1>Üniversite Düzenle</h1>

        <?php if($message): ?>
            <p class="message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <form method="POST">
            <label>Üniversite Adı</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($university['name']); ?>" required>

            <label>Ülke</label>
            <select name="country" required>
                <option value="Türkiye" <?php echo $university['country'] === 'Türkiye' ? 'selected' : ''; ?>>Türkiye</option>
                <option value="KKTC" <?php echo $university['country'] === 'KKTC' ? 'selected' : ''; ?>>KKTC</option>
            </select>

            <label>Şehir</label>
            <input type="text" name="city" value="<?php echo htmlspecialchars($university['city']); ?>" required>

            <label>Tür</label>
            <select name="type" required>
                <option value="Devlet" <?php echo $university['type'] === 'Devlet' ? 'selected' : ''; ?>>Devlet</option>
                <option value="Vakıf" <?php echo $university['type'] === 'Vakıf' ? 'selected' : ''; ?>>Vakıf</option>
            </select>

            <label>Açıklama</label>
            <textarea name="description" rows="5"><?php echo htmlspecialchars($university['description']); ?></textarea>

            <button type="submit">Güncelle</button>
        </form>
    </div>
</body>
</html>