<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("includes/header.php");
include("includes/db.php");

/* Kullanıcı adını baş harf olarak gösterir */
function getInitials($fullname) {
    $words = explode(" ", trim($fullname));
    $initials = "";

    foreach ($words as $word) {
        if (!empty($word)) {
            $initials .= mb_strtoupper(mb_substr($word, 0, 1, "UTF-8"), "UTF-8") . ". ";
        }
    }

    return trim($initials);
}

/* Yorum istatistikleri */
$totalCommentsQuery = mysqli_query($conn, "SELECT COUNT(*) AS total FROM comments");
$totalComments = mysqli_fetch_assoc($totalCommentsQuery)['total'] ?? 0;

$avgRatingQuery = mysqli_query($conn, "SELECT AVG(rating) AS average_rating FROM comments");
$avgRating = mysqli_fetch_assoc($avgRatingQuery)['average_rating'] ?? 0;

$commentedUniQuery = mysqli_query($conn, "SELECT COUNT(DISTINCT university_id) AS total FROM comments");
$commentedUni = mysqli_fetch_assoc($commentedUniQuery)['total'] ?? 0;

/* Tüm yorumları kullanıcı bilgisiyle getir */
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
  INNER JOIN universities ON comments.university_id = universities.id
  INNER JOIN users ON comments.user_id = users.id
  ORDER BY comments.created_at DESC
";

$commentsResult = mysqli_query($conn, $commentsQuery);
?>

<main class="reviews-page">

  <section class="reviews-hero">
    <span class="reviews-label">Öğrenci Deneyimleri</span>
    <h1>Üniversite Yorumları</h1>
    <p>
      Gerçek öğrencilerin üniversite, bölüm ve kampüs deneyimlerini inceleyerek
      tercih sürecinde daha bilinçli karar verebilirsin.
    </p>
  </section>

  <section class="reviews-stats" aria-label="Yorum istatistikleri">
    <article class="review-stat-card">
      <strong><?php echo (int)$totalComments; ?></strong>
      <span>Toplam Yorum</span>
    </article>

    <article class="review-stat-card">
      <strong><?php echo number_format((float)$avgRating, 1); ?></strong>
      <span>Ortalama Puan</span>
    </article>

    <article class="review-stat-card">
      <strong><?php echo (int)$commentedUni; ?></strong>
      <span>Yorumlanan Üniversite</span>
    </article>
  </section>

  <section class="reviews-list" aria-label="Öğrenci yorumları">

    <?php if ($commentsResult && mysqli_num_rows($commentsResult) > 0): ?>

      <?php while($row = mysqli_fetch_assoc($commentsResult)): ?>

        <article class="review-card">
          <header class="review-card-header">
            <div>
              <h2><?php echo htmlspecialchars($row['university_name']); ?></h2>
              <p><?php echo htmlspecialchars($row['university_city']); ?></p>
            </div>

            <span class="review-rating">
              ⭐ <?php echo (int)$row['rating']; ?>/5
            </span>
          </header>

          <p class="review-text">
            “<?php echo htmlspecialchars($row['comment']); ?>”
          </p>

          <footer class="review-footer">
            <div>
              <strong>
                <?php echo htmlspecialchars(getInitials($row['fullname'])); ?>
              </strong>

              <span>
                <?php echo htmlspecialchars($row['department'] ?: 'Bölüm belirtilmemiş'); ?>
              </span>
            </div>

            <time datetime="<?php echo htmlspecialchars($row['created_at']); ?>">
              <?php echo htmlspecialchars($row['created_at']); ?>
            </time>
          </footer>
        </article>

      <?php endwhile; ?>

    <?php else: ?>

      <article class="no-review-card">
        <h2>Henüz yorum yok</h2>
        <p>İlk yorumu bir üniversite detay sayfasından sen ekleyebilirsin.</p>
        <a href="universities.php">Üniversitelere Git</a>
      </article>

    <?php endif; ?>

  </section>

</main>

<?php include("includes/footer.php"); ?>