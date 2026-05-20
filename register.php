<?php
include("includes/db.php");

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $fullname = trim($_POST["fullname"]);
    $email = trim($_POST["email"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $stmt = mysqli_prepare($conn,
        "INSERT INTO users(fullname,email,password)
         VALUES(?,?,?)"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "sss",
        $fullname,
        $email,
        $password
    );

    if(mysqli_stmt_execute($stmt)) {

        header("Location: login.php");
        exit;

    } else {

        $message = "Bu e-posta zaten kayıtlı.";

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