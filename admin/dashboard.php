<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("../includes/db.php");

/* Toplam üniversite sayısı */
$uniCountResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM universities");
$uniCount = mysqli_fetch_assoc($uniCountResult)['total'] ?? 0;

/* Toplam yorum sayısı */
$commentCount = 0;
$commentTableCheck = mysqli_query($conn, "SHOW TABLES LIKE 'comments'");
if (mysqli_num_rows($commentTableCheck) > 0) {
    $commentCountResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM comments");
    $commentCount = mysqli_fetch_assoc($commentCountResult)['total'] ?? 0;
}

/* Toplam görüntüleme sayısı */
$viewCount = 0;
$viewCountResult = mysqli_query($conn, "SELECT SUM(view_count) AS total_views FROM universities");
if ($viewCountResult) {
    $viewCount = mysqli_fetch_assoc($viewCountResult)['total_views'] ?? 0;
}

/* Son eklenen üniversiteler */
$latestUniversities = mysqli_query($conn, "SELECT * FROM universities ORDER BY id DESC LIMIT 5");

/* En çok görüntülenen üniversiteler */
$popularUniversities = mysqli_query($conn, "SELECT * FROM universities ORDER BY view_count DESC, id DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - UniRehber</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f8fafc;
            color: #1e293b;
        }

        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background: #0f172a;
            color: white;
            padding: 24px 18px;
        }

        .sidebar h2 {
            margin-top: 0;
            font-size: 24px;
            margin-bottom: 30px;
        }

        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 12px 14px;
            border-radius: 10px;
            margin-bottom: 10px;
            background: rgba(255,255,255,0.04);
        }

        .sidebar a:hover {
            background: rgba(255,255,255,0.12);
        }

        .content {
            flex: 1;
            padding: 30px;
        }

        .page-title {
            margin-top: 0;
            font-size: 32px;
            color: #0f172a;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin: 25px 0 35px;
        }

        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.06);
        }

        .stat-card h3 {
            margin: 0 0 10px;
            font-size: 16px;
            color: #475569;
        }

        .stat-card .number {
            font-size: 36px;
            font-weight: bold;
            color: #4f46e5;
        }

        .panel-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .panel {
            background: white;
            border-radius: 16px;
            padding: 22px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.06);
        }

        .panel h3 {
            margin-top: 0;
            margin-bottom: 16px;
            color: #0f172a;
        }

        .panel ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .panel ul li {
            padding: 12px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .panel ul li:last-child {
            border-bottom: none;
        }

        .quick-actions {
            margin-top: 30px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .btn {
            text-decoration: none;
            background: linear-gradient(135deg, #4f46e5, #8b5cf6);
            color: white;
            padding: 12px 18px;
            border-radius: 10px;
            font-weight: bold;
            display: inline-block;
        }

        @media (max-width: 992px) {
            .stats-grid,
            .panel-grid {
                grid-template-columns: 1fr;
            }

            .admin-wrapper {
                flex-direction: column;
            }

            .sidebar {
                width: auto;
            }
        }
    </style>
</head>
<body>
<div class="admin-wrapper">
    <aside class="sidebar">
        <h2>Admin Panel</h2>
        <a href="dashboard.php">Dashboard</a>
        <a href="add-university.php">Üniversite Ekle</a>
        <a href="list-universities.php">Üniversiteleri Yönet</a>
        <a href="../universities.php">Siteyi Görüntüle</a>
    </aside>

    <main class="content">
        <h1 class="page-title">Dashboard</h1>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Toplam Üniversite</h3>
                <div class="number"><?php echo $uniCount; ?></div>
            </div>

            <div class="stat-card">
                <h3>Toplam Yorum</h3>
                <div class="number"><?php echo $commentCount; ?></div>
            </div>

            <div class="stat-card">
                <h3>Toplam Görüntüleme</h3>
                <div class="number"><?php echo $viewCount; ?></div>
            </div>
        </div>

        <div class="panel-grid">
            <section class="panel">
                <h3>Son Eklenen Üniversiteler</h3>
                <ul>
                    <?php while($row = mysqli_fetch_assoc($latestUniversities)): ?>
                        <li>
                            <strong><?php echo htmlspecialchars($row['name']); ?></strong><br>
                            <small><?php echo htmlspecialchars($row['city']); ?> / <?php echo htmlspecialchars($row['country']); ?></small>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </section>

            <section class="panel">
                <h3>En Çok Görüntülenen Üniversiteler</h3>
                <ul>
                    <?php while($row = mysqli_fetch_assoc($popularUniversities)): ?>
                        <li>
                            <strong><?php echo htmlspecialchars($row['name']); ?></strong><br>
                            <small>Görüntüleme: <?php echo (int)$row['view_count']; ?></small>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </section>
        </div>

        <div class="quick-actions">
            <a class="btn" href="add-university.php">Yeni Üniversite Ekle</a>
            <a class="btn" href="list-universities.php">Üniversiteleri Yönet</a>
        </div>
    </main>
</div>
</body>
</html>