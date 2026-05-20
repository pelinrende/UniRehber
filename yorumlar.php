<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

/* Header ve veritabanı bağlantı dosyalarını dahil eder */
include("includes/header.php");
include("includes/db.php");

/* 
   Kullanıcının ad soyad bilgisini baş harf şeklinde gösterir.
*/
function getInitials($fullname) {

    $words = explode(" ", trim($fullname));
    $initials = "";

    foreach ($words as $word) {

        if (!empty($word)) {

            $initials .= mb_strtoupper(
                mb_substr($word, 0, 1, "UTF-8"),
                "UTF-8"
            ) . ". ";
        }
    }

    return trim($initials);
}

/*YORUM İSTATİSTİKLERİ*/

/* Toplam yorum sayısını getirir */
$totalCommentsQuery = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM comments"
);

$totalComments =
    mysqli_fetch_assoc($totalCommentsQuery)['total'] ?? 0;

/* Ortalama puanı hesaplar */
$avgRatingQuery = mysqli_query(
    $conn,
    "SELECT AVG(rating) AS average_rating FROM comments"
);

$avgRating =
    mysqli_fetch_assoc($avgRatingQuery)['average_rating'] ?? 0;

/* Yorum yapılan toplam üniversite sayısını getirir */
$commentedUniQuery = mysqli_query(
    $conn,
    "SELECT COUNT(DISTINCT university_id) AS total FROM comments"
);

$commentedUni =
    mysqli_fetch_assoc($commentedUniQuery)['total'] ?? 0;

/* =========================
   TÜM YORUMLARI GETİRİR
   ========================= */

/*
   comments tablosu:
   Yorum bilgileri

   universities tablosu:
   Üniversite bilgileri

   users tablosu:
   Kullanıcı bilgileri

   INNER JOIN kullanılarak
   tüm veriler birleştirilir.
*/

$commentsQuery = "

  SELECT 
    comments.id,
    comments.department,
    comments.rating,
    comments.comment,
    comments.created_at,

    universities.name AS university_name,
    universities.city AS university_city,

    users.fullname

  FROM comments

  INNER JOIN universities
  ON comments.university_id = universities.id

  INNER JOIN users
  ON comments.user_id = users.id

  ORDER BY comments.created_at DESC
";

/* SQL sorgusunu çalıştırır */
$commentsResult = mysqli_query($conn, $commentsQuery);
?>

<main class="reviews-page">

  <!-- =========================
       SAYFA ÜST ALANI
       ========================= -->

  <section class="reviews-hero">

    <span class="reviews-label">
      Öğrenci Deneyimleri
    </span>

    <h1>Üniversite Yorumları</h1>

    <p>
      Gerçek öğrencilerin üniversite, bölüm ve kampüs deneyimlerini inceleyerek
      tercih sürecinde daha bilinçli karar verebilirsin.
    </p>

  </section>

  <!-- =========================
       İSTATİSTİK KARTLARI
       ========================= -->

  <section
      class="reviews-stats"
      aria-label="Yorum istatistikleri"
  >

    <!-- Toplam yorum -->
    <article class="review-stat-card">

      <strong>
        <?php echo (int)$totalComments; ?>
      </strong>

      <span>Toplam Yorum</span>

    </article>

    <!-- Ortalama puan -->
    <article class="review-stat-card">

      <strong>
        <?php echo number_format((float)$avgRating, 1); ?>
      </strong>

      <span>Ortalama Puan</span>

    </article>

    <!-- Yorumlanan üniversite -->
    <article class="review-stat-card">

      <strong>
        <?php echo (int)$commentedUni; ?>
      </strong>

      <span>Yorumlanan Üniversite</span>

    </article>

  </section>

  <!-- =========================
       YORUM LİSTESİ
       ========================= -->

  <section
      class="reviews-list"
      aria-label="Öğrenci yorumları"
  >

    <!-- Eğer yorum varsa -->
    <?php if ($commentsResult && mysqli_num_rows($commentsResult) > 0): ?>

      <!-- Tüm yorumları döngü ile ekrana basar -->
      <?php while($row = mysqli_fetch_assoc($commentsResult)): ?>

        <article class="review-card">

          <!-- Üniversite adı ve puan -->
          <header class="review-card-header">

            <div>

              <h2>
                <?php echo htmlspecialchars($row['university_name']); ?>
              </h2>

              <p>
                <?php echo htmlspecialchars($row['university_city']); ?>
              </p>

            </div>

            <span class="review-rating">

              ⭐ <?php echo (int)$row['rating']; ?>/5

            </span>

          </header>

          <!-- Kullanıcının yorumu -->
          <p class="review-text">

            “<?php echo htmlspecialchars($row['comment']); ?>”

          </p>

          <!-- Kullanıcı bilgileri -->
          <footer class="review-footer">

            <div>

              <!-- Kullanıcının baş harfleri -->
              <strong>

                <?php
                echo htmlspecialchars(
                    getInitials($row['fullname'])
                );
                ?>

              </strong>

              <!-- Bölüm bilgisi -->
              <span>

                <?php
                echo htmlspecialchars(
                    $row['department']
                    ?: 'Bölüm belirtilmemiş'
                );
                ?>

              </span>

            </div>

            <!-- Yorum tarihi -->
            <time datetime="<?php echo htmlspecialchars($row['created_at']); ?>">

              <?php echo htmlspecialchars($row['created_at']); ?>

            </time>

          </footer>

        </article>

      <?php endwhile; ?>

    <?php else: ?>

      <!-- Eğer yorum yoksa -->
      <article class="no-review-card">

        <h2>Henüz yorum yok</h2>

        <p>
          İlk yorumu bir üniversite detay sayfasından sen ekleyebilirsin.
        </p>

        <a href="universities.php">
          Üniversitelere Git
        </a>

      </article>

    <?php endif; ?>

  </section>

</main>

<!-- Footer dosyasını dahil eder -->
<?php include("includes/footer.php"); ?>