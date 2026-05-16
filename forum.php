<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("includes/db.php");
include("includes/header.php");

/* Soru ekleme */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["question_submit"])) {
    $name = trim($_POST["student_name"]);
    $category = trim($_POST["category"]);
    $title = trim($_POST["question_title"]);
    $question = trim($_POST["question_text"]);

    $stmt = mysqli_prepare($conn,
        "INSERT INTO forum_questions (name, category, title, question)
         VALUES (?, ?, ?, ?)"
    );

    mysqli_stmt_bind_param($stmt, "ssss", $name, $category, $title, $question);
    mysqli_stmt_execute($stmt);

    header("Location: forum.php");
    exit;
}

/* Cevap ekleme */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["answer_submit"])) {
    $question_id = (int)$_POST["question_id"];
    $answer_name = trim($_POST["answer_name"]);
    $answer_text = trim($_POST["answer_text"]);

    $answerStmt = mysqli_prepare($conn,
        "INSERT INTO forum_answers (question_id, name, answer)
         VALUES (?, ?, ?)"
    );

    mysqli_stmt_bind_param($answerStmt, "iss", $question_id, $answer_name, $answer_text);
    mysqli_stmt_execute($answerStmt);

    header("Location: forum.php");
    exit;
}

/* Soruları listeleme */
$questions = mysqli_query($conn, "SELECT * FROM forum_questions ORDER BY id DESC");
?>

<main class="forum-page">

  <section class="forum-hero">
    <h1>Öğrenci Forumu</h1>
    <p>
      Üniversite, bölüm, barınma ve kampüs hayatı hakkında soru sorabilir;
      diğer öğrencilerin cevaplarını inceleyebilirsin.
    </p>
  </section>

  <section class="forum-main-container">

    <section class="question-create-box">
      <h2>Soru Oluştur</h2>
      <p>Merak ettiğin konuyu sor, diğer öğrenciler deneyimlerini paylaşsın.</p>

      <form class="forum-form" method="POST" action="forum.php">
        <div class="form-group">
          <label for="student-name">Adınız</label>
          <input type="text" id="student-name" name="student_name" placeholder="Adınızı yazın" required>
        </div>

        <div class="form-group">
          <label for="question-category">Kategori</label>
          <select id="question-category" name="category" required>
            <option value="">Kategori seçin</option>
            <option value="Tercih">Tercih</option>
            <option value="Kampüs">Kampüs</option>
            <option value="Bölüm">Bölüm</option>
            <option value="Barınma">Barınma</option>
          </select>
        </div>

        <div class="form-group">
          <label for="question-title">Soru Başlığı</label>
          <input type="text" id="question-title" name="question_title" placeholder="Örn: Kampüs hayatı nasıl?" required>
        </div>

        <div class="form-group">
          <label for="question-text">Sorunuz</label>
          <textarea id="question-text" name="question_text" rows="5" placeholder="Sorunuzu detaylı yazın..." required></textarea>
        </div>

        <button type="submit" name="question_submit" class="forum-submit-btn">
          Soruyu Paylaş
        </button>
      </form>
    </section>

    <section class="forum-questions">
      <h2>Son Sorular</h2>

      <?php if ($questions && mysqli_num_rows($questions) > 0): ?>
        <?php while($row = mysqli_fetch_assoc($questions)): ?>

          <article class="forum-question-card">
            <header class="forum-question-header">
              <span class="forum-tag">
                <?php echo htmlspecialchars($row['category']); ?>
              </span>

              <small>
                <?php echo htmlspecialchars($row['name']); ?> tarafından soruldu
              </small>
            </header>

            <h3><?php echo htmlspecialchars($row['title']); ?></h3>

            <p>
              <?php echo nl2br(htmlspecialchars($row['question'])); ?>
            </p>

            <section class="forum-answer-box">
              <h4>Cevaplar</h4>

              <?php
              $questionId = (int)$row['id'];

              $answersStmt = mysqli_prepare(
                  $conn,
                  "SELECT * FROM forum_answers WHERE question_id = ? ORDER BY created_at DESC"
              );

              mysqli_stmt_bind_param($answersStmt, "i", $questionId);
              mysqli_stmt_execute($answersStmt);
              $answersResult = mysqli_stmt_get_result($answersStmt);
              ?>

              <?php if ($answersResult && mysqli_num_rows($answersResult) > 0): ?>
                <?php while($answer = mysqli_fetch_assoc($answersResult)): ?>
                  <p>
                    <strong><?php echo htmlspecialchars($answer['name']); ?>:</strong>
                    <?php echo htmlspecialchars($answer['answer']); ?>
                  </p>
                <?php endwhile; ?>
              <?php else: ?>
                <p>Henüz cevap yok.</p>
              <?php endif; ?>
            </section>

            <div class="answer-form-box">
              <h4>Cevap Yaz</h4>

              <form class="answer-form" method="POST" action="forum.php">
                <input type="hidden" name="question_id" value="<?php echo (int)$row['id']; ?>">

                <div class="form-group">
                  <label for="answer-name-<?php echo (int)$row['id']; ?>">Adınız</label>
                  <input
                    type="text"
                    id="answer-name-<?php echo (int)$row['id']; ?>"
                    name="answer_name"
                    placeholder="Adınızı yazın"
                    required
                  >
                </div>

                <div class="form-group">
                  <label for="answer-text-<?php echo (int)$row['id']; ?>">Cevabınız</label>
                  <textarea
                    id="answer-text-<?php echo (int)$row['id']; ?>"
                    name="answer_text"
                    rows="4"
                    placeholder="Cevabınızı yazın..."
                    required
                  ></textarea>
                </div>

                <button type="submit" name="answer_submit">Cevapla</button>
              </form>
            </div>
          </article>

        <?php endwhile; ?>
      <?php else: ?>

        <article class="forum-question-card">
          <h3>Henüz soru yok</h3>
          <p>İlk soruyu sen paylaşabilirsin.</p>
        </article>

      <?php endif; ?>

    </section>

  </section>

</main>

<?php include("includes/footer.php"); ?>