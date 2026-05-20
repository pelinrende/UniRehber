<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function showInitials($fullname) {
    $words = explode(" ", trim($fullname));
    $initials = "";

    foreach ($words as $word) {
        if (!empty($word)) {
            $initials .= mb_strtoupper(mb_substr($word, 0, 1, "UTF-8"), "UTF-8") . ". ";
        }
    }

    return trim($initials);
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>UniRotası</title>
  <link rel="stylesheet" href="/unirehber/css/style.css?v=101">
</head>

<body>

<header class="site-header">
  <nav class="navbar" aria-label="Ana menü">

    <a href="/unirehber/index.php" class="logo">
      <span class="logo-icon">🎓</span>
      <span><strong>Uni</strong>Rotası</span>
    </a>

    <ul class="nav-links">
      <li><a href="/unirehber/index.php">Ana Sayfa</a></li>
      <li><a href="/unirehber/universities.php">Üniversiteler</a></li>
      <li><a href="/unirehber/yorumlar.php">Yorumlar</a></li>
      <li><a href="/unirehber/forum.php">Forum</a></li>
      <li><a href="/unirehber/contact.php">İletişim</a></li>

      <?php if (isset($_SESSION["user_id"])): ?>
        <li>
          <span class="user-badge">
            👤 <?php echo htmlspecialchars(showInitials($_SESSION["user_name"])); ?>
          </span>
        </li>

        <li>
          <a href="/unirehber/logout.php" class="nav-button">Çıkış Yap</a>
        </li>
      <?php else: ?>
        <li><a href="/unirehber/login.php">Giriş</a></li>

        <li>
          <a href="/unirehber/register.php" class="nav-button">Kayıt Ol</a>
        </li>
      <?php endif; ?>

      <li>
        <a href="/unirehber/admin/dashboard.php" class="nav-button">Admin</a>
      </li>
    </ul>

  </nav>
</header>