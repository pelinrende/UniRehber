<?php
session_start();
include("includes/db.php");

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user["password"])) {

        $_SESSION["user_id"] = $user["id"];
        $_SESSION["user_name"] = $user["fullname"];

        header("Location: index.php");
        exit;

    } else {
        $message = "E-posta veya şifre hatalı.";
    }
}

include("includes/header.php");
?>

<main class="auth-page">

    <div class="auth-main-box">

        <div class="auth-left">

            <h2>Tekrar Hoş Geldin 👋</h2>

            <p>
                UniRotası hesabına giriş yaparak
                forumlara katılabilir,
                yorum paylaşabilir ve
                üniversiteleri keşfedebilirsin.
            </p>

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

        <div class="auth-right">

            <h2>Giriş Yap</h2>

            <?php if($message): ?>
                <div class="auth-error">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form method="POST">

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
                        placeholder="Şifrenizi girin"
                        required
                    >
                </div>

                <button type="submit" class="auth-btn">
                    Giriş Yap
                </button>

            </form>

            <p class="auth-switch">
                Hesabın yok mu?
                <a href="register.php">Kayıt Ol</a>
            </p>

        </div>

    </div>

</main>

<?php include("includes/footer.php"); ?>