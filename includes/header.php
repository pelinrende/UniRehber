<?php

// Session başlatılmış mı kontrol eder
if (session_status() === PHP_SESSION_NONE) {

    // Session yoksa yeni session başlatır
    session_start();
}

// Kullanıcının ad soyadından baş harf oluşturur
function showInitials($fullname) {

    // Ad soyadı boşluklara göre ayırır
    $words = explode(" ", trim($fullname));

    // Baş harfleri tutacak değişken
    $initials = "";

    // Her kelimeyi döngüye alır
    foreach ($words as $word) {

        // Kelime boş değilse
        if (!empty($word)) {

            // İlk harfi büyük şekilde alır ve yanına nokta ekler
            $initials .= mb_strtoupper(
                mb_substr($word, 0, 1, "UTF-8"),
                "UTF-8"
            ) . ". ";
        }
    }

    // Fazladan boşlukları temizleyip geri döndürür
    return trim($initials);
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>

  <!-- Türkçe karakter desteği -->
  <meta charset="UTF-8">

  <!-- Responsive mobil uyumluluk -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Site başlığı -->
  <title>UniRotası</title>

  <!-- CSS dosyasını projeye bağlar -->
  <link rel="stylesheet" href="/unirehber/css/style.css?v=103">

</head>

<body>

<!-- Site üst kısmı -->
<header class="site-header">

  <!-- Navbar alanı -->
  <nav class="navbar" aria-label="Ana menü">

    <!-- Logo alanı -->
    <a href="/unirehber/index.php" class="logo">

      <!-- Logo ikonu -->
      <span class="logo-icon">🎓</span>

      <!-- Site adı -->
      <span><strong>Uni</strong>Rotası</span>

    </a>

    <!-- Menü linkleri -->
    <ul class="nav-links">

      <!-- Ana sayfa linki -->
      <li><a href="/unirehber/index.php">Ana Sayfa</a></li>

      <!-- Üniversiteler sayfası -->
      <li><a href="/unirehber/universities.php">Üniversiteler</a></li>

      <!-- Yorumlar sayfası -->
      <li><a href="/unirehber/yorumlar.php">Yorumlar</a></li>

      <!-- Forum sayfası -->
      <li><a href="/unirehber/forum.php">Forum</a></li>

      <!-- İletişim sayfası -->
      <li><a href="/unirehber/contact.php">İletişim</a></li>

      <!-- Kullanıcı giriş yapmış mı kontrol edilir -->
      <?php if (isset($_SESSION["user_id"])): ?>

        <li>

          <!-- Kullanıcının baş harflerini gösterir -->
          <span class="user-badge">

            👤 <?php echo htmlspecialchars(showInitials($_SESSION["user_name"])); ?>

          </span>

        </li>

        <li>

          <!-- Çıkış yap butonu -->
          <a href="/unirehber/logout.php" class="nav-button">

            Çıkış Yap

          </a>

        </li>

      <?php else: ?>

        <!-- Giriş yap linki -->
        <li><a href="/unirehber/login.php">Giriş</a></li>

        <li>

          <!-- Kayıt ol butonu -->
          <a href="/unirehber/register.php" class="nav-button">

            Kayıt Ol

          </a>

        </li>

      <?php endif; ?>

      <li>

        <!-- Admin panel linki -->
        <?php if (
    isset($_SESSION["user_role"]) &&
    $_SESSION["user_role"] === "admin"
): ?>

    <a href="/unirehber/admin/dashboard.php" class="nav-button">

        Admin

    </a>

<?php endif; ?>

      </li>

    </ul>

  </nav>

</header>