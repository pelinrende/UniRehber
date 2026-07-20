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

<main class="ur-reviews-page">

  <!-- =========================
       SAYFA ÜST ALANI
       ========================= -->

  <section class="ur-reviews-hero">

    <div class="ur-reviews-hero-content">

      <span class="ur-reviews-label">
        💬 Öğrenci Deneyimleri
      </span>

      <h1>
        Üniversite Yorumları
      </h1>

      <p>
        Öğrencilerin üniversite, bölüm ve kampüs deneyimlerini
        inceleyerek tercihlerini daha bilinçli yap.
      </p>

    </div>

    <div class="ur-reviews-hero-actions">

      <a
        href="universities.php"
        class="ur-primary-button"
      >
        Yorum Yap
      </a>

      <a
        href="forum.php"
        class="ur-secondary-button"
      >
        Foruma Katıl
      </a>

    </div>

  </section>

  <!-- =========================
       İSTATİSTİK ALANI
       ========================= -->

  <section
    class="ur-reviews-stats"
    aria-label="Yorum istatistikleri"
  >

    <article class="ur-stat-card">

      <span class="ur-stat-icon">
        💬
      </span>

      <div>

        <strong>
          <?php echo (int)$totalComments; ?>
        </strong>

        <span>
          Toplam Yorum
        </span>

      </div>

    </article>

    <article class="ur-stat-card">

      <span class="ur-stat-icon">
        ⭐
      </span>

      <div>

        <strong>
          <?php echo number_format((float)$avgRating, 1); ?>
        </strong>

        <span>
          Ortalama Puan
        </span>

      </div>

    </article>

    <article class="ur-stat-card">

      <span class="ur-stat-icon">
        🏫
      </span>

      <div>

        <strong>
          <?php echo (int)$commentedUni; ?>
        </strong>

        <span>
          Yorumlanan Üniversite
        </span>

      </div>

    </article>

  </section>

  <!-- =========================
       YORUMLAR İÇERİĞİ
       ========================= -->

  <section class="ur-reviews-content">

    <div class="ur-reviews-heading">

      <div>

        <span class="ur-heading-label">
          Öğrenciler Ne Diyor?
        </span>

        <h2>
          Son Paylaşılan Yorumlar
        </h2>

        <p>
          Öğrencilerin bölüm ve kampüs deneyimlerini keşfet.
        </p>

      </div>

      <a
        href="universities.php"
        class="ur-add-review-button"
      >
        <span>＋</span>
        Yeni Yorum
      </a>

    </div>

    <!-- =========================
         YORUM KARTLARI
         ========================= -->

    <div class="ur-reviews-grid">

      <?php if (
        $commentsResult &&
        mysqli_num_rows($commentsResult) > 0
      ): ?>

        <?php while (
          $row = mysqli_fetch_assoc($commentsResult)
        ): ?>

          <?php
          $rating = (int)$row["rating"];
          ?>

          <article class="ur-review-card">

            <!-- Üniversite bilgisi -->

            <div class="ur-review-card-top">

              <div class="ur-university-area">

                <span class="ur-university-icon">
                  🏛️
                </span>

                <div>

                  <h3>
                    <?php
                    echo htmlspecialchars(
                      $row["university_name"]
                    );
                    ?>
                  </h3>

                  <span class="ur-university-city">

                    📍
                    <?php
                    echo htmlspecialchars(
                      $row["university_city"]
                    );
                    ?>

                  </span>

                </div>

              </div>

              <span class="ur-rating-number">

                <?php echo $rating; ?>/5

              </span>

            </div>

            <!-- Yıldız alanı -->

            <div
              class="ur-review-stars"
              aria-label="<?php echo $rating; ?> üzerinden 5 puan"
            >

              <?php for ($i = 1; $i <= 5; $i++): ?>

                <span class="<?php echo $i <= $rating ? "ur-star-active" : "ur-star-empty"; ?>">
                  ★
                </span>

              <?php endfor; ?>

            </div>

            <!-- Yorum metni -->

            <div class="ur-review-message">

              <span class="ur-quote-mark">
                “
              </span>

              <p>
                <?php
                echo nl2br(
                  htmlspecialchars(
                    $row["comment"]
                  )
                );
                ?>
              </p>

            </div>

            <!-- Bölüm bilgisi -->

            <div class="ur-department">

              <span>
                📚
              </span>

              <p>
                <?php
                echo htmlspecialchars(
                  $row["department"]
                  ?: "Bölüm belirtilmemiş"
                );
                ?>
              </p>

            </div>

            <!-- Kullanıcı ve tarih -->

            <div class="ur-review-card-bottom">

              <div class="ur-review-user">

                <span class="ur-review-avatar">

                  <?php
                  echo htmlspecialchars(
                    getInitials(
                      $row["fullname"]
                    )
                  );
                  ?>

                </span>

                <div>

                  <strong>
                    <?php
                    echo htmlspecialchars(
                      getInitials(
                        $row["fullname"]
                      )
                    );
                    ?>
                  </strong>

                  <span>
                    Öğrenci
                  </span>

                </div>

              </div>

              <time
                datetime="<?php echo htmlspecialchars($row["created_at"]); ?>"
                class="ur-review-date"
              >
                🕒
                <?php
                echo htmlspecialchars(
                  date(
                    "d.m.Y",
                    strtotime($row["created_at"])
                  )
                );
                ?>
              </time>

            </div>

          </article>

        <?php endwhile; ?>

      <?php else: ?>

        <!-- Yorum bulunmadığında -->

        <article class="ur-empty-reviews">

          <span class="ur-empty-icon">
            💬
          </span>

          <h2>
            Henüz yorum paylaşılmamış
          </h2>

          <p>
            Üniversite deneyimini paylaşarak tercih yapacak
            öğrencilere yardımcı olabilirsin.
          </p>

          <a
            href="universities.php"
            class="ur-primary-button"
          >
            İlk Yorumu Yap
          </a>

        </article>

      <?php endif; ?>

    </div>

  </section>

</main>

<!-- Footer dosyasını dahil eder -->
<?php include("includes/footer.php"); ?>