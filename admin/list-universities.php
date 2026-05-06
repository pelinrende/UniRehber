<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("../includes/db.php");

$result = mysqli_query($conn, "SELECT * FROM universities ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Üniversiteleri Yönet</title>
    <style>
        body { font-family: Arial, sans-serif; background:#f8fafc; margin:0; padding:30px; }
        .container { max-width:1200px; margin:0 auto; }
        h1 { margin-top:0; }
        .top-actions { margin-bottom:20px; display:flex; gap:10px; flex-wrap:wrap; }
        .btn {
            text-decoration:none;
            background:linear-gradient(135deg, #4f46e5, #8b5cf6);
            color:white;
            padding:10px 14px;
            border-radius:10px;
            display:inline-block;
        }
        table {
            width:100%;
            border-collapse:collapse;
            background:white;
            border-radius:16px;
            overflow:hidden;
            box-shadow:0 8px 24px rgba(0,0,0,0.06);
        }
        th, td {
            padding:14px;
            border-bottom:1px solid #e2e8f0;
            text-align:left;
        }
        th {
            background:#0f172a;
            color:white;
        }
        tr:last-child td {
            border-bottom:none;
        }
        .action-link {
            text-decoration:none;
            margin-right:10px;
            font-weight:bold;
        }
        .edit { color:#2563eb; }
        .delete { color:#dc2626; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Üniversiteleri Yönet</h1>

        <div class="top-actions">
            <a class="btn" href="dashboard.php">Dashboard</a>
            <a class="btn" href="add-university.php">Yeni Üniversite Ekle</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ad</th>
                    <th>Ülke</th>
                    <th>Şehir</th>
                    <th>Tür</th>
                    <th>Görüntüleme</th>
                    <th>İşlem</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo (int)$row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['country']); ?></td>
                        <td><?php echo htmlspecialchars($row['city']); ?></td>
                        <td><?php echo htmlspecialchars($row['type']); ?></td>
                        <td><?php echo (int)$row['view_count']; ?></td>
                        <td>
                            <a class="action-link edit" href="edit-university.php?id=<?php echo $row['id']; ?>">Düzenle</a>
                            <a class="action-link delete" href="delete-university.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Bu üniversite silinsin mi?');">Sil</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>