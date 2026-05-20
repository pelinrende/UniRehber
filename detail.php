<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

include("includes/db.php");
include("includes/header.php");

/* Kullanıcı adını baş harf şeklinde göster */
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

/* Üniversite ID kontrolü */
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    die("Geçersiz üniversite.");
}

$university_id = (int)$_GET["id"];

/* Görüntülenme artır */
$viewStmt = mysqli_prepare(
    $conn,
    "UPDATE universities
     SET view_count = view_count + 1
     WHERE id = ?"
);

mysqli_stmt_bind_param($viewStmt, "i", $university_id);
mysqli_stmt_execute($viewStmt);

/* Üniversite bilgisi getir */
$stmt = mysqli_prepare(
    $conn,
    "SELECT * FROM universities WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $university_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$university = mysqli_fetch_assoc($result);

if (!$university) {
    die("Üniversite bulunamadı.");
}

/* =========================
   YORUM EKLEME
   ========================= */

if (
    $_SERVER["REQUEST_METHOD"] === "POST"
    &&
    isset($_POST["comment_submit"])
) {

    if (!isset($_SESSION["user_id"])) {

        header("Location: login.php");
        exit;
    }

    $user_id = (int)$_SESSION["user_id"];

    $department = trim($_POST["department"]);

    $rating = (int)$_POST["rating"];

    $comment = trim($_POST["comment"]);

    $insertStmt = mysqli_prepare(
        $conn,
        "INSERT INTO comments
        (
            university_id,
            user_id,
            department,
            rating,
            comment
        )
        VALUES (?, ?, ?, ?, ?)"
    );

    if (!$insertStmt) {
        die("SQL Hatası: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param(
        $insertStmt,
        "iisis",
        $university_id,
        $user_id,
        $department,
        $rating,
        $comment
    );

    if (!mysqli_stmt_execute($insertStmt)) {
        die("Kayıt Hatası: " . mysqli_stmt_error($insertStmt));
    }

    header("Location: detail.php?id=" . $university_id);
    exit;
}

/* =========================
   YORUMLARI GETİR
   ========================= */

$commentsStmt = mysqli_prepare(
    $conn,
    "
    SELECT
        comments.*,
        users.fullname

    FROM comments

    INNER JOIN users
    ON comments.user_id = users.id

    WHERE comments.university_id = ?

    ORDER BY comments.created_at DESC
    "
);

mysqli_stmt_bind_param(
    $commentsStmt,
    "i",
    $university_id
);

mysqli_stmt_execute($commentsStmt);

$commentsResult = mysqli_stmt_get_result($commentsStmt);
?>

<main class="detail-page">

    <section class="detail-hero">

        <h2>
            <?php echo htmlspecialchars($university["name"]); ?>
        </h2>

        <p>
            <?php echo htmlspecialchars($university["city"]); ?>
            /
            <?php echo htmlspecialchars($university["country"]); ?>
        </p>

    </section>

    <section class="detail-container">

        <!-- Üniversite Bilgisi -->

        <article class="detail-card">

            <h3>Üniversite Bilgileri</h3>

            <p>
                <strong>Tür:</strong>
                <?php echo htmlspecialchars($university["type"]); ?>
            </p>

            <p>
                <strong>Açıklama:</strong>
                <?php echo htmlspecialchars($university["description"]); ?>
            </p>

            <p>
                <strong>Görüntülenme:</strong>
                <?php echo (int)$university["view_count"]; ?>
            </p>

        </article>

        <!-- Yorum Formu -->

        <article class="comment-form-card">

            <h3>Yorum Yap</h3>

            <?php if (isset($_SESSION["user_id"])): ?>

                <form
                    method="POST"
                    action="detail.php?id=<?php echo $university_id; ?>"
                >

                    <div class="form-group">

                        <label for="department">
                            Bölüm
                        </label>

                        <input
                            type="text"
                            id="department"
                            name="department"
                            placeholder="Örn: Bilgisayar Mühendisliği"
                        >

                    </div>

                    <div class="form-group">

                        <label for="rating">
                            Puan
                        </label>

                        <select
                            id="rating"
                            name="rating"
                            required
                        >

                            <option value="5">
                                5 - Çok İyi
                            </option>

                            <option value="4">
                                4 - İyi
                            </option>

                            <option value="3">
                                3 - Orta
                            </option>

                            <option value="2">
                                2 - Kötü
                            </option>

                            <option value="1">
                                1 - Çok Kötü
                            </option>

                        </select>

                    </div>

                    <div class="form-group">

                        <label for="comment">
                            Yorumunuz
                        </label>

                        <textarea
                            id="comment"
                            name="comment"
                            rows="5"
                            placeholder="Üniversite hakkındaki deneyimini yaz..."
                            required
                        ></textarea>

                    </div>

                    <button
                        type="submit"
                        name="comment_submit"
                    >
                        Yorumu Gönder
                    </button>

                </form>

            <?php else: ?>

                <p>
                    Yorum yapabilmek için giriş yapmalısın.
                </p>

                <a
                    href="login.php"
                    class="detail-btn"
                >
                    Giriş Yap
                </a>

            <?php endif; ?>

        </article>

        <!-- Yorumlar -->

        <article class="comments-card">

            <h3>Yorumlar</h3>

            <?php if (
                $commentsResult
                &&
                mysqli_num_rows($commentsResult) > 0
            ): ?>

                <?php while(
                    $commentRow = mysqli_fetch_assoc($commentsResult)
                ): ?>

                    <section class="single-comment">

                        <h4>

                            <?php
                            echo htmlspecialchars(
                                getInitials(
                                    $commentRow["fullname"]
                                )
                            );
                            ?>

                        </h4>

                        <p>

                            <strong>Bölüm:</strong>

                            <?php
                            echo htmlspecialchars(
                                $commentRow["department"]
                            );
                            ?>

                        </p>

                        <p>

                            <strong>Puan:</strong>

                            <?php
                            echo (int)$commentRow["rating"];
                            ?>/5

                        </p>

                        <p>

                            <?php
                            echo nl2br(
                                htmlspecialchars(
                                    $commentRow["comment"]
                                )
                            );
                            ?>

                        </p>

                        <small>

                            <?php
                            echo htmlspecialchars(
                                $commentRow["created_at"]
                            );
                            ?>

                        </small>

                    </section>

                <?php endwhile; ?>

            <?php else: ?>

                <p>
                    Bu üniversite için henüz yorum yapılmamış.
                </p>

            <?php endif; ?>

        </article>

    </section>

</main>

<?php include("includes/footer.php"); ?>