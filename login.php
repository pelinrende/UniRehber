<?php

/* =========================
   SESSION VE HATA AYARLARI
   ========================= */

session_start();

error_reporting(E_ALL);
ini_set("display_errors", 1);

/* =========================
   VERİTABANI BAĞLANTISI
   ========================= */

include("includes/db.php");

$message = "";
$successMessage = "";

if (
    isset($_GET["registered"]) &&
    $_GET["registered"] === "1"
) {
    $successMessage = "Hesabınız başarıyla oluşturuldu. Giriş yapabilirsiniz.";
}

/* =========================
   GİRİŞ YAPMA İŞLEMİ
   ========================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    $stmt = mysqli_prepare(
        $conn,
        "SELECT * FROM users WHERE email = ? LIMIT 1"
    );

    if (!$stmt) {
        die("SQL hazırlama hatası: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if (
        $user &&
        password_verify($password, $user["password"])
    ) {

        session_regenerate_id(true);

        $_SESSION["user_id"] = $user["id"];
        $_SESSION["user_name"] = $user["fullname"];
        $_SESSION["user_role"] = $user["role"] ?? "user";

        header("Location: index.php");
        exit;

    } else {

        $message = "E-posta veya şifre hatalı.";
    }
}


include("includes/header.php");
?>

<main class="auth-page">

    <!-- =========================
         GİRİŞ YAP SAYFASI
         ========================= -->

    <div class="auth-main-box">

        <!-- Sol bilgi alanı -->
        <div class="auth-left">

            <h2>Tekrar Hoş Geldin 👋</h2>

            <p>
                UniRotası hesabına giriş yaparak
                forumlara katılabilir,
                yorum paylaşabilir ve
                üniversiteleri keşfedebilirsin.
            </p>

            <!-- Bilgilendirme kartı -->
            <div class="auth-info-card">

                <h4>Neler Yapabilirsin?</h4>

                <ul>
                    <li>Üniversite yorumları paylaş</li>
                    <li>Forumlarda soru sor</li>
                    <li>Diğer öğrencilerle etkileşim kur</li>
                    <li>Kampüs hayatını keşfet</li>
                </ul>

            </div>

        </div>

        <!-- Sağ giriş formu alanı -->
        <div class="auth-right">

            <h2>Giriş Yap</h2>
            <?php if ($successMessage): ?>

    <div class="auth-success">
        <?php echo htmlspecialchars($successMessage); ?>
    </div>

<?php endif; ?>

            <!-- Hata mesajı -->
            <?php if($message): ?>

                <div class="auth-error">

                    <?php echo htmlspecialchars($message); ?>

                </div>

            <?php endif; ?>

            <!-- Kullanıcı giriş formu -->
            <form method="POST">

                <!-- E-posta alanı -->
                <div class="form-group">

                    <label>E-posta</label>

                    <input
                        type="email"
                        name="email"
                        placeholder="ornek@gmail.com"
                        required
                    >

                </div>

                <!-- Şifre alanı -->
                <div class="form-group">

                    <label>Şifre</label>

                    <input
                        type="password"
                        name="password"
                        placeholder="Şifrenizi girin"
                        required
                    >

                </div>

                <!-- Giriş yap butonu -->
                <button type="submit" class="auth-btn">

                    Giriş Yap

                </button>

            </form>

            <!-- Kayıt ol yönlendirmesi -->
            <p class="auth-switch">

                Hesabın yok mu?

                <a href="register.php">
                    Kayıt Ol
                </a>

            </p>

        </div>

    </div>

</main>

<!-- Footer dosyasını projeye dahil eder -->
<?php include("includes/footer.php"); ?>