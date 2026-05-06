<?php
include("includes/header.php");
include("includes/db.php");

$comments = mysqli_query($conn, "
  SELECT comments.*, universities.name AS university_name 
  FROM comments 
  INNER JOIN universities ON comments.university_id = universities.id
  ORDER BY comments.created_at DESC
");
?>

<main class="page">
  <section class="page-hero">
    <h2>Yorumlar</h2>
    <p>Öğrencilerin üniversiteler hakkındaki deneyimlerini incele.</p>
  </section>

  <section class="comment-list">
    <?php if ($comments && mysqli_num_rows($comments) > 0): ?>
      <?php while($row = mysqli_fetch_assoc($comments)): ?>
        <article class="comment-card">
          <h3><?php echo htmlspecialchars($row['university_name']); ?></h3>
          <p><strong>Bölüm:</strong> <?php echo htmlspecialchars($row['department'] ?? 'Belirtilmemiş'); ?></p>
          <p><strong>Puan:</strong> <?php echo (int)$row['rating']; ?>/5</p>
          <p><?php echo htmlspecialchars($row['comment']); ?></p>
          <small><?php echo htmlspecialchars($row['created_at']); ?></small>
        </article>
      <?php endwhile; ?>
    <?php else: ?>
      <p class="no-result">Henüz yorum eklenmemiş.</p>
    <?php endif; ?>
  </section>
</main>

<?php include("includes/footer.php"); ?>