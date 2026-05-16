<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("includes/header.php");
include("includes/db.php");

/* Arama ve şehir filtre değerleri */
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$city = isset($_GET['city']) ? trim($_GET['city']) : '';

/* Şehirleri listeleme */
$citiesResult = mysqli_query($conn, "SELECT DISTINCT city FROM universities ORDER BY city ASC");

/* Üniversite arama sorgusu */
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
$universitiesResult = mysqli_stmt_get_result($stmt);
?>

<main class="discover-page">

  <!-- En üst arama alanı -->
  <section class="top-search-section" aria-label="Üniversite arama alanı">
    <form class="top-search-form" method="GET" action="kesfet.php">
      <label for="search">Üniversite Ara</label>

      <div class="top-search-row">
        <input
          type="text"
          id="search"
          name="search"
          value="<?php echo htmlspecialchars($search); ?>"
          placeholder="Okul adını yazın..."
        >

        <button type="submit">Ara</button>
      </div>
    </form>
  </section>

  <!-- Sayfa başlığı -->
  <section class="discover-hero">
    <h1>Üniversiteleri Keşfet</h1>
    <p>Okul adı veya şehir seçimi ile üniversiteleri kolayca bul.</p>
  </section>

  <!-- Şehir filtreleme alanı -->
  <section class="search-section" aria-label="Şehir filtreleme alanı">
    <form class="search-form" method="GET" action="kesfet.php">

      <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">

      <div class="form-control">
        <label for="city">Şehir Seç</label>
        <select id="city" name="city">
          <option value="">Tüm Şehirler</option>

          <?php while($cityRow = mysqli_fetch_assoc($citiesResult)): ?>
            <option value="<?php echo htmlspecialchars($cityRow['city']); ?>"
              <?php echo ($city === $cityRow['city']) ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($cityRow['city']); ?>
            </option>
          <?php endwhile; ?>

        </select>
      </div>

      <button type="submit" class="search-button">Filtrele</button>
      <a href="kesfet.php" class="clear-button">Temizle</a>
    </form>
  </section>

  <!-- Üniversite sonuçları -->
  <section class="results-section" aria-label="Üniversite sonuçları">
    <div class="section-title">
      <h2>Sonuçlar</h2>
      <p><?php echo mysqli_num_rows($universitiesResult); ?> üniversite listelendi.</p>
    </div>

    <div class="university-grid">
      <?php if (mysqli_num_rows($universitiesResult) > 0): ?>

        <?php while($row = mysqli_fetch_assoc($universitiesResult)): ?>
          <article class="university-card">
            <div class="university-card-header">
              <span class="university-type">
                <?php echo htmlspecialchars($row['type']); ?>
              </span>

              <span class="university-city">
                <?php echo htmlspecialchars($row['city']); ?>
              </span>
            </div>

            <h3><?php echo htmlspecialchars($row['name']); ?></h3>

            <p><?php echo htmlspecialchars($row['description']); ?></p>

            <a href="detail.php?id=<?php echo (int)$row['id']; ?>" class="detail-btn">
              İncele
            </a>
          </article>
        <?php endwhile; ?>

      <?php else: ?>
        <article class="empty-result">
          <h3>Sonuç bulunamadı</h3>
          <p>Arama kelimesini değiştirerek veya şehir filtresini temizleyerek tekrar deneyebilirsin.</p>
        </article>
      <?php endif; ?>
    </div>
  </section>

</main>

<?php include("includes/footer.php"); ?>