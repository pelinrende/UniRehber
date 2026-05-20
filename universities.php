<?php

// Tüm PHP hatalarını gösterir
error_reporting(E_ALL);

// Hataların ekranda görünmesini sağlar
ini_set('display_errors', 1);

/* 
   Header ve veritabanı bağlantı dosyalarını dahil eder
*/
include("includes/header.php");
include("includes/db.php");

/* =========================
   ARAMA VE FİLTRE DEĞERLERİ
   ========================= */

/* 
   URL üzerinden gelen search değerini alır.
   Örnek:
   universities.php?search=istanbul
*/
$search = isset($_GET['search'])
    ? trim($_GET['search'])
    : '';

/* 
   URL üzerinden gelen şehir filtresini alır
*/
$city = isset($_GET['city'])
    ? trim($_GET['city'])
    : '';

/* =========================
   ŞEHİRLERİ GETİRİR
   ========================= */

/* 
   Üniversitelerde bulunan tüm şehirleri
   tekrar etmeyecek şekilde getirir
*/
$citiesResult = mysqli_query(
    $conn,
    "SELECT DISTINCT city FROM universities ORDER BY city ASC"
);

/* =========================
   ÜNİVERSİTELERİ FİLTRELER
   ========================= */

/* 
   Temel SQL sorgusu oluşturulur
*/
$sql = "
    SELECT * FROM universities
    WHERE 1=1
";

/* 
   Parametreleri tutacak dizi
*/
$params = [];

/* 
   Parametre tiplerini tutar
   s = string
*/
$types = "";

/* 
   Eğer arama yapılmışsa
   üniversite adına göre filtre eklenir
*/
if (!empty($search)) {

    $sql .= " AND name LIKE ?";

    $params[] = "%" . $search . "%";

    $types .= "s";
}

/* 
   Eğer şehir seçilmişse
   şehir filtresi uygulanır
*/
if (!empty($city)) {

    $sql .= " AND city = ?";

    $params[] = $city;

    $types .= "s";
}

/* 
   Sonuçları alfabetik sıralar
*/
$sql .= " ORDER BY name ASC";

/* 
   SQL sorgusunu hazırlar
*/
$stmt = mysqli_prepare($conn, $sql);

/* 
   Eğer parametre varsa bind işlemi yapılır
*/
if (!empty($params)) {

    mysqli_stmt_bind_param(
        $stmt,
        $types,
        ...$params
    );
}

/* 
   SQL sorgusunu çalıştırır
*/
mysqli_stmt_execute($stmt);

/* 
   Sonuçları alır
*/
$result = mysqli_stmt_get_result($stmt);
?>

<main class="universities-page">

  <!-- =========================
       SAYFA ÜST ALANI
       ========================= -->

  <section class="page-hero">

    <h2>Üniversiteler</h2>

    <p>
      Üniversite adı arayabilir veya şehir seçerek filtreleme yapabilirsin.
    </p>

  </section>

  <!-- =========================
       FİLTRELEME FORMU
       ========================= -->

  <section class="filter-section">

    <!-- GET yöntemiyle filtreleme formu -->
    <form
        method="GET"
        action="universities.php"
        class="filter-form"
    >

      <!-- Üniversite arama alanı -->
      <div>

        <label for="search">
          Üniversite Ara
        </label>

        <input
          type="text"
          id="search"
          name="search"
          placeholder="Okul adını yaz..."
          value="<?php echo htmlspecialchars($search); ?>"
        >

      </div>

      <!-- Şehir filtreleme alanı -->
      <div>

        <label for="city">
          Şehir Seç
        </label>

        <select name="city" id="city">

          <option value="">
            Tüm Şehirler
          </option>

          <!-- Şehirleri döngüyle ekrana basar -->
          <?php while($cityRow = mysqli_fetch_assoc($citiesResult)): ?>

            <option
              value="<?php echo htmlspecialchars($cityRow['city']); ?>"

              <?php
              echo ($city === $cityRow['city'])
              ? 'selected'
              : '';
              ?>
            >

              <?php echo htmlspecialchars($cityRow['city']); ?>

            </option>

          <?php endwhile; ?>

        </select>

      </div>

      <!-- Filtreleme butonu -->
      <button type="submit">
        Filtrele
      </button>

      <!-- Filtreleri temizleme -->
      <a href="universities.php" class="clear-button">
        Temizle
      </a>

    </form>

  </section>

  <!-- =========================
       ÜNİVERSİTE LİSTESİ
       ========================= -->

  <section class="university-list">

    <!-- Eğer üniversite varsa -->
    <?php if ($result && mysqli_num_rows($result) > 0): ?>

      <!-- Tüm üniversiteleri döngüyle ekrana basar -->
      <?php while($row = mysqli_fetch_assoc($result)): ?>

        <article class="university-card">

          <div class="card-content">

            <!-- Üniversite adı -->
            <h3>
              <?php echo htmlspecialchars($row['name']); ?>
            </h3>

            <!-- Üniversite bilgileri -->
            <p>
              <strong>Şehir:</strong>

              <?php echo htmlspecialchars($row['city']); ?>
            </p>

            <p>
              <strong>Ülke:</strong>

              <?php echo htmlspecialchars($row['country']); ?>
            </p>

            <p>
              <strong>Tür:</strong>

              <?php echo htmlspecialchars($row['type']); ?>
            </p>

            <!-- Üniversite açıklaması -->
            <p>
              <?php echo htmlspecialchars($row['description']); ?>
            </p>

            <!-- Üniversite detay sayfası -->
            <a
                href="detail.php?id=<?php echo (int)$row['id']; ?>"
                class="detail-btn"
            >

              İncele

            </a>

          </div>

        </article>

      <?php endwhile; ?>

    <?php else: ?>

      <!-- Eğer sonuç bulunamazsa -->
      <p class="no-result">

        Aramana uygun üniversite bulunamadı.

      </p>

    <?php endif; ?>

  </section>

</main>

<!-- Footer dosyasını dahil eder -->
<?php include("includes/footer.php"); ?>