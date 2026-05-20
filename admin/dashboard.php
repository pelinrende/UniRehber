<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("../includes/db.php");

if (!isset($conn)) {
    die("Veritabanı bağlantısı bulunamadı.");
}

/* Güvenli sayaç fonksiyonu */
function getCount($conn, $table) {
    $check = mysqli_query($conn, "SHOW TABLES LIKE '$table'");
    if ($check && mysqli_num_rows($check) > 0) {
        $result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM $table");
        return mysqli_fetch_assoc($result)['total'] ?? 0;
    }
    return 0;
}

$uniCount = getCount($conn, "universities");
$commentCount = getCount($conn, "comments");
$userCount = getCount($conn, "users");
$forumQuestionCount = getCount($conn, "forum_questions");
$forumAnswerCount = getCount($conn, "forum_answers");

/* Görüntüleme sayısı */
$viewCount = 0;
$viewResult = mysqli_query($conn, "SELECT SUM(view_count) AS total FROM universities");
if ($viewResult) {
    $viewCount = mysqli_fetch_assoc($viewResult)['total'] ?? 0;
}

/* Son eklenen üniversiteler */
$latestUniversities = mysqli_query($conn, "
    SELECT * FROM universities
    ORDER BY id DESC
    LIMIT 5
");

/* En çok görüntülenenler */
$popularUniversities = mysqli_query($conn, "
    SELECT * FROM universities
    ORDER BY view_count DESC, id DESC
    LIMIT 5
");
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - UniRotası</title>
    <link rel="stylesheet" href="/unirehber/css/style.css?v=120">

    <style>
        body {
            margin: 0;
            background: #f8fafc;
            color: #1e293b;
            font-family: Arial, sans-serif;
        }

        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 260px;
            background: #0f172a;
            color: white;
            padding: 28px 20px;
        }

        .sidebar h2 {
            margin: 0 0 28px;
            font-size: 24px;
        }

        .sidebar a {
            display: block;
            color: white;
            padding: 13px 14px;
            border-radius: 12px;
            margin-bottom: 10px;
            background: rgba(255,255,255,0.05);
            text-decoration: none;
            font-weight: 700;
        }

        .sidebar a:hover {
            background: rgba(255,255,255,0.14);
        }

        .admin-content {
            flex: 1;
            padding: 35px;
        }

        .page-title {
            margin: 0 0 8px;
            font-size: 36px;
            color: #0f172a;
        }

        .page-desc {
            margin-bottom: 30px;
            color: #64748b;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 35px;
        }

        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.08);
        }

        .stat-card h3 {
            margin: 0 0 12px;
            color: #475569;
            font-size: 15px;
        }

        .stat-card strong {
            font-size: 36px;
            color: #4f46e5;
        }

        .panel-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 22px;
        }

        .panel {
            background: white;
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.08);
        }

        .panel h3 {
            margin-top: 0;
            color: #0f172a;
        }

        .panel ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .panel li {
            padding: 13px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .panel li:last-child {
            border-bottom: none;
        }

        .panel small {
            color: #64748b;
        }

        .quick-actions {
            margin-top: 30px;
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
        }

        .admin-btn {
            display: inline-block;
            background: linear-gradient(135deg, #4f46e5, #8b5cf6);
            color: white;
            padding: 13px 18px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 700;
        }

        @media (max-width: 950px) {
            .admin-wrapper {
                flex-direction: column;
            }

            .sidebar {
                width: auto;
            }

            .stats-grid,
            .panel-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

<div class="admin-wrapper">

    <aside class="sidebar">
        <h2>UniRotası Admin</h2>
        <a href="dashboard.php">Dashboard</a>
        <a href="add-university.php">Üniversite Ekle</a>
        <a href="list-universities.php">Üniversiteleri Yönet</a>
        <a href="../index.php">Site Ana Sayfa</a>
        <a href="../universities.php">Üniversiteleri Gör</a>
    </aside>

    <main class="admin-content">

        <h1 class="page-title">Dashboard</h1>
        <p class="page-desc">Sisteme ait genel istatistikler ve son veriler burada görüntülenir.</p>

        <section class="stats-grid">
            <article class="stat-card">
                <h3>Toplam Üniversite</h3>
                <strong><?php echo (int)$uniCount; ?></strong>
            </article>

            <article class="stat-card">
                <h3>Toplam Yorum</h3>
                <strong><?php echo (int)$commentCount; ?></strong>
            </article>

            <article class="stat-card">
                <h3>Toplam Görüntüleme</h3>
                <strong><?php echo (int)$viewCount; ?></strong>
            </article>

            <article class="stat-card">
                <h3>Kayıtlı Kullanıcı</h3>
                <strong><?php echo (int)$userCount; ?></strong>
            </article>

            <article class="stat-card">
                <h3>Forum Sorusu</h3>
                <strong><?php echo (int)$forumQuestionCount; ?></strong>
            </article>

            <article class="stat-card">
                <h3>Forum Cevabı</h3>
                <strong><?php echo (int)$forumAnswerCount; ?></strong>
            </article>
        </section>

        <section class="panel-grid">

            <article class="panel">
                <h3>Son Eklenen Üniversiteler</h3>
                <ul>
                    <?php if ($latestUniversities && mysqli_num_rows($latestUniversities) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($latestUniversities)): ?>
                            <li>
                                <strong><?php echo htmlspecialchars($row["name"]); ?></strong><br>
                                <small>
                                    <?php echo htmlspecialchars($row["city"]); ?> /
                                    <?php echo htmlspecialchars($row["country"]); ?>
                                </small>
                            </li>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <li>Henüz üniversite eklenmemiş.</li>
                    <?php endif; ?>
                </ul>
            </article>

            <article class="panel">
                <h3>En Çok Görüntülenen Üniversiteler</h3>
                <ul>
                    <?php if ($popularUniversities && mysqli_num_rows($popularUniversities) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($popularUniversities)): ?>
                            <li>
                                <strong><?php echo htmlspecialchars($row["name"]); ?></strong><br>
                                <small>Görüntüleme: <?php echo (int)$row["view_count"]; ?></small>
                            </li>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <li>Henüz görüntüleme yok.</li>
                    <?php endif; ?>
                </ul>
            </article>

        </section>

        <div class="quick-actions">
            <a class="admin-btn" href="add-university.php">Yeni Üniversite Ekle</a>
            <a class="admin-btn" href="list-universities.php">Üniversiteleri Yönet</a>
            <a class="admin-btn" href="../forum.php">Foruma Git</a>
        </div>

    </main>

</div>

</body>
</html>