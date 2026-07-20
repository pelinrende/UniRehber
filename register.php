<?php

/* SESSION VE HATA AYARLARI */

session_start();

error_reporting(E_ALL);
ini_set("display_errors", 1);

/*VERİTABANI BAĞLANTISI*/

include("includes/db.php");

$message = "";

/* KAYIT OLMA İŞLEMİ*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $fullname = trim($_POST["fullname"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $plainPassword = $_POST["password"] ?? "";

    if (
        $fullname === "" ||
        $email === "" ||
        $plainPassword === ""
    ) {

        $message = "Lütfen bütün alanları doldurun.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Geçerli bir e-posta adresi girin.";

    } elseif (strlen($plainPassword) < 6) {

        $message = "Şifre en az 6 karakter olmalıdır.";

    } else {

        /* Aynı e-posta daha önce kullanılmış mı kontrol eder */

        $checkStmt = mysqli_prepare(
            $conn,
            "SELECT id FROM users WHERE email = ? LIMIT 1"
        );

        if (!$checkStmt) {
            die("SQL hazırlama hatası: " . mysqli_error($conn));
        }

        mysqli_stmt_bind_param($checkStmt, "s", $email);
        mysqli_stmt_execute($checkStmt);

        $checkResult = mysqli_stmt_get_result($checkStmt);

        if (mysqli_num_rows($checkResult) > 0) {

            $message = "Bu e-posta zaten kayıtlı.";

        } else {

            /* Şifreyi güvenli biçimde şifreler */

            $password = password_hash(
                $plainPassword,
                PASSWORD_DEFAULT
            );

            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO users
                (fullname, email, password, role)
                VALUES (?, ?, ?, 'user')"
            );

            if (!$stmt) {
                die("SQL hazırlama hatası: " . mysqli_error($conn));
            }

            mysqli_stmt_bind_param(
                $stmt,
                "sss",
                $fullname,
                $email,
                $password
            );

            if (mysqli_stmt_execute($stmt)) {

    header("Location: login.php?registered=1");
    exit;
} 
else {

                $message = "Kayıt sırasında hata oluştu: " .
                    mysqli_stmt_error($stmt);
            }
        }
    }
}
include("includes/header.php");
?>

<main class="auth-page">

    <div class="auth-main-box">

        <div class="auth-left">

            <h2>UniRotası’na Katıl 🎓</h2>

            <p>
                Üniversite deneyimlerini paylaş,
                öğrencilerle iletişim kur
                ve kampüs hayatını keşfet.
            </p>

            <div class="auth-info-card">

                <h4>Avantajlar</h4>

                <ul>
                    <li>Yorum paylaşabilirsin</li>
                    <li>Forumlara katılabilirsin</li>
                    <li>Soru sorabilirsin</li>
                    <li>Gerçek öğrenci deneyimleri okuyabilirsin</li>
                </ul>

            </div>

        </div>

        <div class="auth-right">

            <h2>Kayıt Ol</h2>

            <?php if($message): ?>
                <div class="auth-error">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form method="POST">

                <div class="form-group">
                    <label>Ad Soyad</label>

                    <input
                        type="text"
                        name="fullname"
                        placeholder="Adınızı girin"
                        required
                    >
                </div>

                <div class="form-group">
                    <label>E-posta</label>

                    <input
                        type="email"
                        name="email"
                        placeholder="ornek@gmail.com"
                        required
                    >
                </div>

                <div class="form-group">
                    <label>Şifre</label>

                    <input
                        type="password"
                        name="password"
                        placeholder="Şifre oluşturun"
                        required
                    >
                </div>

                <button type="submit" class="auth-btn">
                    Kayıt Ol
                </button>

            </form>

            <p class="auth-switch">
                Zaten hesabın var mı?
                <a href="login.php">Giriş Yap</a>
            </p>

        </div>

    </div>

</main>

<?php include("includes/footer.php"); ?>