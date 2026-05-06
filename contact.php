<?php include("includes/header.php"); ?>

<main class="contact-page">
  <section class="contact-hero">
    <h2>İletişim</h2>
    <p>Soruların, önerilerin veya geri bildirimlerin için bize ulaşabilirsin.</p>
  </section>

  <section class="contact-container">
    <div class="contact-info">
      <h3>Bize Ulaş</h3>
      <p><strong>E-posta:</strong> info@unirehber.com</p>
      <p><strong>Telefon:</strong> +90 534 365 62 22</p>
      <p><strong>Adres:</strong> İstanbul, Türkiye</p>

      <div class="contact-box">
        <h4>Neden yazmalısın?</h4>
        <p>
          Üniversiteler, yorumlar, öneriler veya iş birliği için bizimle iletişime geçebilirsin.
        </p>
      </div>
    </div>

    <div class="contact-form-area">
      <form class="contact-form" action="#" method="post">
        <div class="form-group">
          <label for="name">Ad Soyad</label>
          <input type="text" id="name" name="name" placeholder="Adınızı girin" required>
        </div>

        <div class="form-group">
          <label for="email">E-posta</label>
          <input type="email" id="email" name="email" placeholder="E-posta adresinizi girin" required>
        </div>

        <div class="form-group">
          <label for="subject">Konu</label>
          <input type="text" id="subject" name="subject" placeholder="Konu başlığı" required>
        </div>

        <div class="form-group">
          <label for="message">Mesaj</label>
          <textarea id="message" name="message" rows="6" placeholder="Mesajınızı yazın..." required></textarea>
        </div>

        <button type="submit" class="btn-contact">Gönder</button>
      </form>
    </div>
  </section>
</main>

<?php include("includes/footer.php"); ?>