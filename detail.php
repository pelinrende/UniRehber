<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("includes/header.php");
include("includes/db.php");

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Geçersiz üniversite.");
}

$university_id = (int)$_GET['id'];

/* Görüntüleme sayısını artır */
$viewStmt = mysqli_prepare($conn, "UPDATE universities SET view_count = view_count + 1 WHERE id = ?");
mysqli_stmt_bind_param($viewStmt, "i", $university_id);
mysqli_stmt_execute($viewStmt);

/* Üniversite bilgilerini çek */
$stmt = mysqli_prepare($conn, "SELECT * FROM universities WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $university_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$university = mysqli_fetch_assoc($result);

if (!$university) {
    die("Üniversite bulunamadı.");
}

/* Yorum ekleme */
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_name = trim($_POST['user_name']);
    $department = trim($_POST['department']);
    $rating = (int)$_POST['rating'];
    $comment = trim($_POST['comment']);

    $commentStmt = mysqli_prepare($conn, "
        INSERT INTO comments (university_id, user_name, department, rating, comment)
        VALUES (?, ?, ?, ?, ?)
    ");

    mysqli_stmt_bind_param($commentStmt, "issis", $university_id, $user_name, $department, $rating, $comment);
    mysqli_stmt_execute($commentStmt);

    header("Location: detail.php?id=" . $university_id);
    exit;
}

/* Bu üniversitenin yorumlarını çek */
$commentsStmt = mysqli_prepare($conn, "
    SELECT * FROM comments 
    WHERE university_id = ? 
    ORDER BY created_at DESC
");
mysqli_stmt_bind_param($commentsStmt, "i", $university_id);
mysqli_stmt_execute($commentsResult = $commentsStmt);
$commentsResult = mysqli_stmt_get_result($commentsStmt);
?>

<main class="detail-page">
    <section class="detail-hero">
        <h2><?php echo htmlspecialchars($university['name']); ?></h2>
        <p>
            <?php echo htmlspecialchars($university['city']); ?> /
            <?php echo htmlspecialchars($university['country']); ?>
        </p>
    </section>

    <section class="detail-container">
        <div class="detail-card">
            <h3>Üniversite Bilgileri</h3>
            <p><strong>Tür:</strong> <?php echo htmlspecialchars($university['type']); ?></p>
            <p><strong>Açıklama:</strong> <?php echo htmlspecialchars($university['description']); ?></p>
            <p><strong>Görüntülenme:</strong> <?php echo (int)$university['view_count']; ?></p>
        </div>

        <div class="comment-form-card">
            <h3>Yorum Yap</h3>

            <form method="POST">
                <label for="user_name">Adınız</label>
                <input type="text" id="user_name" name="user_name" required>

                <label for="department">Bölüm</label>
                <input type="text" id="department" name="department" placeholder="Örn: Bilgisayar Mühendisliği">

                <label for="rating">Puan</label>
                <select id="rating" name="rating" required>
                    <option value="5">5 - Çok İyi</option>
                    <option value="4">4 - İyi</option>
                    <option value="3">3 - Orta</option>
                    <option value="2">2 - Kötü</option>
                    <option value="1">1 - Çok Kötü</option>
                </select>

                <label for="comment">Yorumunuz</label>
                <textarea id="comment" name="comment" rows="5" required></textarea>

                <button type="submit">Yorumu Gönder</button>
            </form>
        </div>

        <div class="comments-card">
            <h3>Yorumlar</h3>

            <?php if (mysqli_num_rows($commentsResult) > 0): ?>
                <?php while($comment = mysqli_fetch_assoc($commentsResult)): ?>
                    <article class="single-comment">
                        <h4><?php echo htmlspecialchars($comment['user_name']); ?></h4>
                        <p><strong>Bölüm:</strong> <?php echo htmlspecialchars($comment['department']); ?></p>
                        <p><strong>Puan:</strong> <?php echo (int)$comment['rating']; ?>/5</p>
                        <p><?php echo htmlspecialchars($comment['comment']); ?></p>
                        <small><?php echo htmlspecialchars($comment['created_at']); ?></small>
                    </article>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Bu üniversite için henüz yorum yapılmamış.</p>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php include("includes/footer.php"); ?>