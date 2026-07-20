<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

include("includes/db.php");

/* Giriş yapmayan kullanıcı forumu kullanamaz */
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

/* Ad Soyad bilgisini P. R. şeklinde gösterir */
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

/* Soru ekleme */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["question_submit"])) {
    $user_id = (int)$_SESSION["user_id"];
    $category = trim($_POST["category"]);
    $title = trim($_POST["question_title"]);
    $question = trim($_POST["question_text"]);

    $stmt = mysqli_prepare($conn, "
        INSERT INTO forum_questions (user_id, category, title, question)
        VALUES (?, ?, ?, ?)
    ");

    mysqli_stmt_bind_param($stmt, "isss", $user_id, $category, $title, $question);
    mysqli_stmt_execute($stmt);

    header("Location: forum.php");
    exit;
}

/* Cevap ekleme */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["answer_submit"])) {

    $question_id = (int)$_POST["question_id"];
    $user_id = (int)$_SESSION["user_id"];
    $answer_text = trim($_POST["answer_text"]);

    if (!empty($answer_text)) {

        $answerStmt = mysqli_prepare(
            $conn,
            "INSERT INTO forum_answers (question_id, user_id, answer)
             VALUES (?, ?, ?)"
        );

        if (!$answerStmt) {
            die("SQL Hatası: " . mysqli_error($conn));
        }

        mysqli_stmt_bind_param(
            $answerStmt,
            "iis",
            $question_id,
            $user_id,
            $answer_text
        );

        if (!mysqli_stmt_execute($answerStmt)) {
            die("Kayıt Hatası: " . mysqli_stmt_error($answerStmt));
        }

        header("Location: forum.php");
        exit;
    }
}

/* =========================
   FORUM ARAMA VE FİLTRELEME
   ========================= */

$search = trim($_GET["search"] ?? "");
$filterCategory = trim($_GET["filter_category"] ?? "");

$sql = "
    SELECT
        forum_questions.*,
        users.fullname

    FROM forum_questions

    INNER JOIN users
    ON forum_questions.user_id = users.id

    WHERE 1 = 1
";

$params = [];
$types = "";

/* Arama kelimesi varsa başlık ve soru içinde arar */
if ($search !== "") {

    $sql .= "
        AND (
            forum_questions.title LIKE ?
            OR forum_questions.question LIKE ?
            OR forum_questions.category LIKE ?
        )
    ";

    $searchTerm = "%" . $search . "%";

    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;

    $types .= "sss";
}

/* Kategori seçilmişse kategoriye göre filtreler */
if ($filterCategory !== "") {

    $sql .= "
        AND forum_questions.category = ?
    ";

    $params[] = $filterCategory;
    $types .= "s";
}

$sql .= "
    ORDER BY forum_questions.id DESC
";

$questionsStmt = mysqli_prepare($conn, $sql);

if (!$questionsStmt) {
    die("SQL Hatası: " . mysqli_error($conn));
}

if (!empty($params)) {

    mysqli_stmt_bind_param(
        $questionsStmt,
        $types,
        ...$params
    );
}

mysqli_stmt_execute($questionsStmt);

$questions = mysqli_stmt_get_result($questionsStmt);
include("includes/header.php");
?>

<main class="ur-forum-page">

  <!-- =========================
       FORUM ÜST ALANI
       ========================= -->

  <section class="ur-forum-hero">

    <div class="ur-forum-hero-content">

      <span class="ur-forum-label">
        💬 Öğrenci Topluluğu
      </span>

      <h1>
        Öğrenci Forumu
      </h1>

      <p>
        Üniversite, bölüm, kampüs ve barınma hakkında merak ettiklerini
        sorabilir; öğrencilerin gerçek deneyimlerinden yararlanabilirsin.
      </p>

    </div>

    <div class="ur-forum-hero-info">

      <div class="ur-forum-hero-icon">
        🎓
      </div>

      <div>
        <strong>Birlikte Keşfet</strong>
        <span>Sor, cevapla ve deneyimlerini paylaş.</span>
      </div>

    </div>

  </section>

  <!-- =========================
       FORUM ANA İÇERİĞİ
       ========================= -->

  <section class="ur-forum-container">

    <!-- =========================
         SORU OLUŞTURMA FORMU
         ========================= -->

    <aside class="ur-question-create-card">

      <div class="ur-question-create-heading">

        <span class="ur-question-create-icon">
          ✏️
        </span>

        <div>
          <h2>Yeni Soru Oluştur</h2>

          <p>
            Merak ettiğin konuyu öğrenci topluluğuna sor.
          </p>
        </div>

      </div>

      <form
        class="ur-forum-form"
        method="POST"
        action="forum.php"
      >

        <div class="ur-forum-form-group">

          <label for="question-category">
            Kategori
          </label>

          <select
            id="question-category"
            name="category"
            required
          >
            <option value="">Kategori seçin</option>
            <option value="Tercih">Tercih</option>
            <option value="Kampüs">Kampüs</option>
            <option value="Bölüm">Bölüm</option>
            <option value="Barınma">Barınma</option>
          </select>

        </div>

        <div class="ur-forum-form-group">

          <label for="question-title">
            Soru Başlığı
          </label>

          <input
            type="text"
            id="question-title"
            name="question_title"
            placeholder="Örn: Kampüs hayatı nasıl?"
            required
          >

        </div>

        <div class="ur-forum-form-group">

          <label for="question-text">
            Sorunuz
          </label>

          <textarea
            id="question-text"
            name="question_text"
            rows="5"
            placeholder="Sorunuzu detaylı şekilde yazın..."
            required
          ></textarea>

        </div>

        <button
          type="submit"
          name="question_submit"
          class="ur-forum-submit-button"
        >
          <span>＋</span>
          Soruyu Paylaş
        </button>

      </form>

      <div class="ur-forum-tip">

        <span>💡</span>

        <p>
          Açık ve anlaşılır sorular daha hızlı cevap alır.
        </p>

      </div>

    </aside>

    <!-- =========================
         SON SORULAR
         ========================= -->

    <section class="ur-forum-questions">

      <div class="ur-forum-section-heading">        

        <div>

          <span class="ur-forum-section-label">
            Topluluk Paylaşımları
          </span>

          <h2>Son Sorular</h2>

          <p>
            Öğrencilerin son paylaştığı soruları ve cevapları incele.
          </p>

        </div>

        <span class="ur-forum-question-count">

          <?php
          echo $questions
            ? mysqli_num_rows($questions)
            : 0;
          ?>

          Soru

        </span>

      </div>
<!-- =========================
     FORUM ARAMA ALANI
     ========================= -->

<form
    class="ur-forum-search"
    method="GET"
    action="forum.php"
>

    <div class="ur-forum-search-input">

        <span>
            🔍
        </span>

        <input
            type="search"
            name="search"
            placeholder="Soru başlığı veya konu ara..."
            value="<?php echo htmlspecialchars($search); ?>"
        >

    </div>

    <select
        name="filter_category"
        class="ur-forum-search-category"
    >

        <option value="">
            Tüm Kategoriler
        </option>

        <option
            value="Tercih"
            <?php echo $filterCategory === "Tercih" ? "selected" : ""; ?>
        >
            Tercih
        </option>

        <option
            value="Kampüs"
            <?php echo $filterCategory === "Kampüs" ? "selected" : ""; ?>
        >
            Kampüs
        </option>

        <option
            value="Bölüm"
            <?php echo $filterCategory === "Bölüm" ? "selected" : ""; ?>
        >
            Bölüm
        </option>

        <option
            value="Barınma"
            <?php echo $filterCategory === "Barınma" ? "selected" : ""; ?>
        >
            Barınma
        </option>

    </select>

    <button
        type="submit"
        class="ur-forum-search-button"
    >
        Ara
    </button>

    <?php if (
        $search !== "" ||
        $filterCategory !== ""
    ): ?>

        <a
            href="forum.php"
            class="ur-forum-search-clear"
        >
            Temizle
        </a>

    <?php endif; ?>

</form>


      <div class="ur-forum-question-grid">

        <?php if (
          $questions &&
          mysqli_num_rows($questions) > 0
        ): ?>

          <?php while (
            $row = mysqli_fetch_assoc($questions)
          ): ?>

            <article class="ur-forum-question-card">

              <!-- Soru üst bilgileri -->

              <div class="ur-forum-question-top">

                <span class="ur-forum-category">

                  <?php
                  echo htmlspecialchars(
                    $row["category"]
                  );
                  ?>

                </span>

                <span class="ur-forum-author">

                  👤
                  <?php
                  echo htmlspecialchars(
                    getInitials(
                      $row["fullname"]
                    )
                  );
                  ?>

                </span>

              </div>

              <!-- Soru başlığı -->

              <h3>

                <?php
                echo htmlspecialchars(
                  $row["title"]
                );
                ?>

              </h3>

              <!-- Soru metni -->

              <div class="ur-forum-question-text">

                <span class="ur-forum-quote">
                  “
                </span>

                <p>

                  <?php
                  echo nl2br(
                    htmlspecialchars(
                      $row["question"]
                    )
                  );
                  ?>

                </p>

              </div>

              <!-- =========================
                   CEVAPLAR
                   ========================= -->

              <div class="ur-forum-answers">

                <div class="ur-forum-answers-heading">

                  <h4>
                    💬 Cevaplar
                  </h4>

                </div>

                <?php
                $questionId = (int)$row["id"];

                $answersStmt = mysqli_prepare(
                  $conn,
                  "
                  SELECT
                    forum_answers.*,
                    users.fullname

                  FROM forum_answers

                  INNER JOIN users
                  ON forum_answers.user_id = users.id

                  WHERE forum_answers.question_id = ?

                  ORDER BY forum_answers.created_at DESC
                  "
                );

                mysqli_stmt_bind_param(
                  $answersStmt,
                  "i",
                  $questionId
                );

                mysqli_stmt_execute($answersStmt);

                $answersResult =
                  mysqli_stmt_get_result($answersStmt);
                ?>
                <?php if (
    $answersResult &&
    mysqli_num_rows($answersResult) > 0
): ?>

    <div class="ur-forum-answer-list">

        <?php while (
            $answer = mysqli_fetch_assoc($answersResult)
        ): ?>

            <div class="ur-forum-answer-item">

                <span class="ur-forum-answer-avatar">

                    <?php
                    echo htmlspecialchars(
                        getInitials(
                            $answer["fullname"]
                        )
                    );
                    ?>

                </span>

                <div>

                    <strong>

                        <?php
                        echo htmlspecialchars(
                            getInitials(
                                $answer["fullname"]
                            )
                        );
                        ?>

                    </strong>

                    <p>

                        <?php
                        echo nl2br(
                            htmlspecialchars(
                                $answer["answer"]
                            )
                        );
                        ?>

                    </p>

                    <?php if (!empty($answer["created_at"])): ?>

                        <time>

                            🕒
                            <?php
                            echo htmlspecialchars(
                                date(
                                    "d.m.Y",
                                    strtotime(
                                        $answer["created_at"]
                                    )
                                )
                            );
                            ?>

                        </time>

                    <?php endif; ?>

                </div>

            </div>

        <?php endwhile; ?>

    </div>

<?php else: ?>

    <div class="ur-forum-no-answer">

        <span>💭</span>

        <p>
            Henüz cevap verilmemiş. İlk cevabı sen yazabilirsin.
        </p>

    </div>

<?php endif; ?>

</div>


              <!-- =========================
                   CEVAP YAZMA FORMU
                   ========================= -->

              <div class="ur-answer-form-box">

                <details class="ur-answer-details">

                  <summary>
                    <span>✍️</span>
                    Cevap Yaz
                  </summary>

                  <form
                    class="ur-answer-form"
                    method="POST"
                    action="forum.php"
                  >

                    <input
                      type="hidden"
                      name="question_id"
                      value="<?php echo (int)$row["id"]; ?>"
                    >

                    <div class="ur-forum-form-group">

                      <label
                        for="answer-text-<?php echo (int)$row["id"]; ?>"
                      >
                        Cevabınız
                      </label>

                      <textarea
                        id="answer-text-<?php echo (int)$row["id"]; ?>"
                        name="answer_text"
                        rows="4"
                        placeholder="Deneyimini veya bilgini paylaş..."
                        required
                      ></textarea>

                    </div>

                    <button
                      type="submit"
                      name="answer_submit"
                      class="ur-answer-submit-button"
                    >
                      Cevabı Gönder
                    </button>

                  </form>

                </details>

              </div>

            </article>  
                        </article>

        <?php endwhile; ?>

    <?php else: ?>

        <article class="ur-forum-empty-state">

            <span class="ur-forum-empty-icon">
                💬
            </span>

            <?php if (
                $search !== "" ||
                $filterCategory !== ""
            ): ?>

                <h3>
                    Aramana uygun soru bulunamadı
                </h3>

                <p>
                    Farklı bir kelime veya kategoriyle tekrar arama yapabilirsin.
                </p>

                <a
                    href="forum.php"
                    class="ur-forum-empty-clear"
                >
                    Tüm Soruları Göster
                </a>

            <?php else: ?>

                <h3>
                    Henüz soru paylaşılmamış
                </h3>

                <p>
                    Öğrenci topluluğundaki ilk soruyu sen oluşturabilirsin.
                </p>

            <?php endif; ?>

        </article>

    <?php endif; ?>

      </div>

    </section>

  </section>

</main>

<?php include("includes/footer.php"); ?>