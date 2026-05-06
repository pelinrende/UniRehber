<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("includes/header.php");
include("includes/db.php");

$selectedCity = isset($_GET['city']) ? $_GET['city'] : '';

$citiesQuery = "SELECT DISTINCT city FROM universities ORDER BY city ASC";
$citiesResult = mysqli_query($conn, $citiesQuery);

if (!empty($selectedCity)) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM universities WHERE city = ? ORDER BY name ASC");
    mysqli_stmt_bind_param($stmt, "s", $selectedCity);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $result = mysqli_query($conn, "SELECT * FROM universities ORDER BY name ASC");
}
?>

<main class="universities-page">
  <section class="page-hero">
    <h2>Üniversiteler</h2>
    <p>Şehir seçerek üniversiteleri filtreleyebilir ve detaylarını inceleyebilirsin.</p>
  </section>

  <section class="filter-section">
    <form method="GET" action="universities.php" class="filter-form">
      <div>
        <label for="city">Şehir Seç</label>
        <select name="city" id="city">
          <option value="">Tüm Şehirler</option>
          <?php while($cityRow = mysqli_fetch_assoc($citiesResult)) : ?>
            <option value="<?php echo htmlspecialchars($cityRow['city']); ?>" <?php echo ($selectedCity == $cityRow['city']) ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($cityRow['city']); ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <button type="submit">Filtrele</button>
    </form>
  </section>

  <section class="university-list">
    <?php if(mysqli_num_rows($result) > 0): ?>
      <?php while($row = mysqli_fetch_assoc($result)): ?>
        <article class="university-card">
          <div class="card-content">
            <h3><?php echo htmlspecialchars($row['name']); ?></h3>
            <p><strong>Şehir:</strong> <?php echo htmlspecialchars($row['city']); ?></p>
            <p><strong>Ülke:</strong> <?php echo htmlspecialchars($row['country']); ?></p>
            <p><strong>Tür:</strong> <?php echo htmlspecialchars($row['type']); ?></p>
            <p><?php echo htmlspecialchars($row['description']); ?></p>
            <a href="detail.php?id=<?php echo $row['id']; ?>" class="detail-btn">İncele</a>
          </div>
        </article>
      <?php endwhile; ?>
    <?php else: ?>
      <p class="no-result">Bu şehirde üniversite bulunamadı.</p>
    <?php endif; ?>
  </section>
</main>

<?php include("includes/footer.php"); ?>