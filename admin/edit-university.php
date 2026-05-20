<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("../includes/db.php");

/* Veritabanı bağlantı kontrolü */
if (!isset($conn)) {
    die("Veritabanı bağlantısı bulunamadı.");
}

/* ID kontrolü */
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    die("Geçersiz üniversite ID.");
}

$id = (int) $_GET["id"];
$message = "";
$messageType = "";

/* Üniversite bilgilerini getirme */
$stmt = mysqli_prepare(
    $conn,
    "SELECT * FROM universities WHERE id = ?"
);

if (!$stmt) {
    die("SQL hazırlama hatası: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$university = mysqli_fetch_assoc($result);

if (!$university) {
    die("Üniversite bulunamadı.");
}

/* Güncelleme işlemi */
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"]);
    $country = trim($_POST["country"]);
    $city = trim($_POST["city"]);
    $type = trim($_POST["type"]);
    $description = trim($_POST["description"]);

    if ($name === "" || $country === "" || $city === "" || $type === "") {
        $message = "Lütfen zorunlu alanları doldurun.";
        $messageType = "error";
    } else {
        $updateStmt = mysqli_prepare(
            $conn,
            "UPDATE universities
             SET name = ?, country = ?, city = ?, type = ?, description = ?
             WHERE id = ?"
        );

        if (!$updateStmt) {
            die("SQL hazırlama hatası: " . mysqli_error($conn));
        }

        mysqli_stmt_bind_param(
            $updateStmt,
            "sssssi",
            $name,
            $country,
            $city,
            $type,
            $description,
            $id
        );

        if (mysqli_stmt_execute($updateStmt)) {
            $message = "Üniversite başarıyla güncellendi.";
            $messageType = "success";

            /* Güncel veriyi tekrar çek */
            $stmt = mysqli_prepare(
                $conn,
                "SELECT * FROM universities WHERE id = ?"
            );

            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);
            $university = mysqli_fetch_assoc($result);
        } else {
            $message = "Güncelleme sırasında hata oluştu: " . mysqli_stmt_error($updateStmt);
            $messageType = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Üniversite Düzenle</title>

    <link
        rel="stylesheet"
        href="/unirehber/css/style.css?v=130"
    >

    <style>
        body {
            margin: 0;
            background: #f8fafc;
            font-family: Arial, sans-serif;
            color: #1e293b;
        }

        .admin-form-page {
            min-height: 100vh;
            padding: 50px 20px;
        }

        .admin-form-container {
            max-width: 760px;
            margin: 0 auto;
            background: #ffffff;
            padding: 34px;
            border-radius: 22px;
            box-shadow: 0 12px 34px rgba(15, 23, 42, 0.08);
        }

        .admin-back-link {
            display: inline-block;
            margin-bottom: 22px;
            color: #4f46e5;
            font-weight: 700;
            text-decoration: none;
        }

        .admin-form-container h1 {
            margin: 0 0 10px;
            color: #0f172a;
            font-size: 32px;
        }

        .admin-form-container p {
            margin-bottom: 28px;
            color: #64748b;
            line-height: 1.7;
        }

        .admin-form label {
            display: block;
            margin-bottom: 8px;
            font-weight: 700;
            color: #334155;
        }

        .admin-form input,
        .admin-form textarea,
        .admin-form select {
            width: 100%;
            padding: 14px;
            margin-bottom: 18px;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            font-size: 15px;
            box-sizing: border-box;
        }

        .admin-form input:focus,
        .admin-form textarea:focus,
        .admin-form select:focus {
            outline: none;
            border-color: #4f46e5;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.12);
        }

        .admin-form button {
            width: 100%;
            border: none;
            background: linear-gradient(135deg, #4f46e5, #8b5cf6);
            color: #ffffff;
            padding: 15px;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            font-size: 15px;
        }

        .admin-message {
            padding: 14px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .admin-message.success {
            background: #dcfce7;
            color: #166534;
        }

        .admin-message.error {
            background: #fee2e2;
            color: #991b1b;
        }
    </style>
</head>

<body>

<main class="admin-form-page">

    <section class="admin-form-container">

        <a class="admin-back-link" href="list-universities.php">
            ← Listeye dön
        </a>

        <h1>Üniversite Düzenle</h1>

        <p>
            Üniversite bilgilerini güncellemek için aşağıdaki formu düzenleyin.
        </p>

        <?php if ($message): ?>
            <div class="admin-message <?php echo htmlspecialchars($messageType); ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="admin-form">

            <label for="name">Üniversite Adı</label>
            <input
                type="text"
                id="name"
                name="name"
                value="<?php echo htmlspecialchars($university["name"]); ?>"
                required
            >

            <label for="country">Ülke</label>
            <select id="country" name="country" required>
                <option
                    value="Türkiye"
                    <?php echo $university["country"] === "Türkiye" ? "selected" : ""; ?>
                >
                    Türkiye
                </option>

                <option
                    value="KKTC"
                    <?php echo $university["country"] === "KKTC" ? "selected" : ""; ?>
                >
                    KKTC
                </option>
            </select>

            <label for="city">Şehir</label>
            <input
                type="text"
                id="city"
                name="city"
                value="<?php echo htmlspecialchars($university["city"]); ?>"
                required
            >

            <label for="type">Tür</label>
            <select id="type" name="type" required>
                <option
                    value="Devlet"
                    <?php echo $university["type"] === "Devlet" ? "selected" : ""; ?>
                >
                    Devlet
                </option>

                <option
                    value="Vakıf"
                    <?php echo $university["type"] === "Vakıf" ? "selected" : ""; ?>
                >
                    Vakıf
                </option>
            </select>

            <label for="description">Açıklama</label>
            <textarea
                id="description"
                name="description"
                rows="5"
            ><?php echo htmlspecialchars($university["description"]); ?></textarea>

            <button type="submit">
                Güncelle
            </button>

        </form>

    </section>

</main>

</body>
</html>