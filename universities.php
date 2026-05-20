<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("includes/header.php");
include("includes/db.php");

/* Arama ve filtre değerleri */
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$city = isset($_GET['city']) ? trim($_GET['city']) : '';

/* Şehirleri getir */
$citiesResult = mysqli_query($conn, "SELECT DISTINCT city FROM universities ORDER BY city ASC");

/* Üniversiteleri filtreli getir */
$sql = "SELECT * FROM universities WHERE 1=1";
$params = [];
$types = "";

if (!empty($search)) {
    $sql .= " AND name LIKE ?";
    $params[] = "%" . $search . "%";
    $types .= "s";
}

if (!empty($city)) {
    $sql .= " AND city = ?";
    $params[] = $city;
    $types .= "s";
}

$sql .= " ORDER BY name ASC";

$stmt = mysqli_prepare($conn, $sql);

if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<main class="universities-page">

  <section class="page-hero">
    <h2>Üniversiteler</h2>
    <p>Üniversite adı arayabilir veya şehir seçerek filtreleme yapabilirsin.</p>
  </section>

  <section class="filter-section">
    <form method="GET" action="universities.php" class="filter-form">

      <div>
        <label for="search">Üniversite Ara</label>
        <input
          type="text"
          id="search"
          name="search"
          placeholder="Okul adını yaz..."
          value="<?php echo htmlspecialchars($search); ?>"
        >
      </div>

      <div>
        <label for="city">Şehir Seç</label>
        <select name="city" id="city">
          <option value="">Tüm Şehirler</option>

          <?php while($cityRow = mysqli_fetch_assoc($citiesResult)): ?>
            <option
              value="<?php echo htmlspecialchars($cityRow['city']); ?>"
              <?php echo ($city === $cityRow['city']) ? 'selected' : ''; ?>
            >
              <?php echo htmlspecialchars($cityRow['city']); ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <button type="submit">Filtrele</button>
      <a href="universities.php" class="clear-button">Temizle</a>

    </form>
  </section>

  <section class="university-list">

    <?php if ($result && mysqli_num_rows($result) > 0): ?>

      <?php while($row = mysqli_fetch_assoc($result)): ?>

        <article class="university-card">
          <div class="card-content">
            <h3><?php echo htmlspecialchars($row['name']); ?></h3>

            <p><strong>Şehir:</strong> <?php echo htmlspecialchars($row['city']); ?></p>
            <p><strong>Ülke:</strong> <?php echo htmlspecialchars($row['country']); ?></p>
            <p><strong>Tür:</strong> <?php echo htmlspecialchars($row['type']); ?></p>
            <p><?php echo htmlspecialchars($row['description']); ?></p>

            <a href="detail.php?id=<?php echo (int)$row['id']; ?>" class="detail-btn">
              İncele
            </a>
          </div>
        </article>

      <?php endwhile; ?>

    <?php else: ?>

      <p class="no-result">Aramana uygun üniversite bulunamadı.</p>

    <?php endif; ?>

  </section>

</main>

<?php include("includes/footer.php"); ?>