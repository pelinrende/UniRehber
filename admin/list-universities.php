<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("../includes/db.php");

/* Veritabanı bağlantı kontrolü */
if (!isset($conn)) {
    die("Veritabanı bağlantısı bulunamadı.");
}

/* Üniversiteleri getir */
$result = mysqli_query(
    $conn,
    "SELECT * FROM universities ORDER BY id DESC"
);

if (!$result) {
    die("Üniversiteler getirilemedi: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Üniversiteleri Yönet</title>

    <link
        rel="stylesheet"
        href="/unirehber/css/style.css?v=140"
    >

    <style>
        body {
            margin: 0;
            background: #f8fafc;
            font-family: Arial, sans-serif;
            color: #1e293b;
        }

        .admin-list-page {
            min-height: 100vh;
            padding: 45px 20px;
        }

        .admin-list-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .admin-list-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 18px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }

        .admin-list-header h1 {
            margin: 0;
            color: #0f172a;
            font-size: 34px;
        }

        .admin-list-header p {
            margin: 8px 0 0;
            color: #64748b;
        }

        .top-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .admin-btn {
            display: inline-block;
            text-decoration: none;
            background: linear-gradient(135deg, #4f46e5, #8b5cf6);
            color: #ffffff;
            padding: 12px 16px;
            border-radius: 12px;
            font-weight: 700;
        }

        .admin-btn.secondary {
            background: #eef2ff;
            color: #4f46e5;
        }

        .success-message {
            background: #dcfce7;
            color: #166534;
            padding: 14px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .table-card {
            background: #ffffff;
            border-radius: 22px;
            box-shadow: 0 12px 34px rgba(15, 23, 42, 0.08);
            overflow-x: auto;
        }

        table {
            width: 100%;
            min-width: 850px;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 15px 16px;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
            vertical-align: middle;
        }

        th {
            background: #0f172a;
            color: #ffffff;
            font-size: 14px;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tbody tr:hover {
            background: #f8fafc;
        }

        .uni-name {
            font-weight: 700;
            color: #0f172a;
        }

        .badge {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 999px;
            background: #eef2ff;
            color: #4f46e5;
            font-size: 13px;
            font-weight: 700;
        }

        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .action-link {
            text-decoration: none;
            font-weight: 700;
            padding: 8px 11px;
            border-radius: 10px;
        }

        .edit {
            background: #dbeafe;
            color: #2563eb;
        }

        .delete {
            background: #fee2e2;
            color: #dc2626;
        }

        .empty-row {
            text-align: center;
            color: #64748b;
            padding: 30px;
        }

        @media (max-width: 700px) {
            .admin-list-header {
                align-items: flex-start;
            }

            .admin-list-header h1 {
                font-size: 28px;
            }

            .admin-btn {
                width: 100%;
                text-align: center;
            }

            .top-actions {
                width: 100%;
            }
        }
    </style>
</head>

<body>

<main class="admin-list-page">

    <section class="admin-list-container">

        <div class="admin-list-header">
            <div>
                <h1>Üniversiteleri Yönet</h1>
                <p>Sistemde kayıtlı üniversiteleri düzenleyebilir veya silebilirsin.</p>
            </div>

            <div class="top-actions">
                <a class="admin-btn secondary" href="dashboard.php">
                    Dashboard
                </a>

                <a class="admin-btn" href="add-university.php">
                    Yeni Üniversite Ekle
                </a>
            </div>
        </div>

        <?php if (isset($_GET["success"])): ?>
            <div class="success-message">
                <?php echo htmlspecialchars($_GET["success"]); ?>
            </div>
        <?php endif; ?>

        <div class="table-card">

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Üniversite Adı</th>
                        <th>Ülke</th>
                        <th>Şehir</th>
                        <th>Tür</th>
                        <th>Görüntüleme</th>
                        <th>İşlem</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (mysqli_num_rows($result) > 0): ?>

                        <?php while($row = mysqli_fetch_assoc($result)): ?>

                            <tr>
                                <td>
                                    <?php echo (int)$row["id"]; ?>
                                </td>

                                <td class="uni-name">
                                    <?php echo htmlspecialchars($row["name"]); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($row["country"]); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($row["city"]); ?>
                                </td>

                                <td>
                                    <span class="badge">
                                        <?php echo htmlspecialchars($row["type"]); ?>
                                    </span>
                                </td>

                                <td>
                                    <?php echo (int)$row["view_count"]; ?>
                                </td>

                                <td>
                                    <div class="actions">
                                        <a
                                            class="action-link edit"
                                            href="edit-university.php?id=<?php echo (int)$row["id"]; ?>"
                                        >
                                            Düzenle
                                        </a>

                                        <a
                                            class="action-link delete"
                                            href="delete-university.php?id=<?php echo (int)$row["id"]; ?>"
                                            onclick="return confirm('Bu üniversite silinsin mi?');"
                                        >
                                            Sil
                                        </a>
                                    </div>
                                </td>
                            </tr>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="7" class="empty-row">
                                Henüz üniversite eklenmemiş.
                            </td>
                        </tr>

                    <?php endif; ?>

                </tbody>
            </table>

        </div>

    </section>

</main>

</body>
</html>