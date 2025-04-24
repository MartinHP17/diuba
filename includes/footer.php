<!-- Footer -->
<footer class="footer">
  <div class="footer-top">
    <div class="container">
      <div class="row">
        <div class="col-lg-4 col-md-6 mb-5 mb-lg-0">
          <div class="footer-about">
            <div class="footer-logo">
              <img src="<?php echo $base_url; ?>assets/imagenes/logo-uba-white.png" alt="UBA Logo">
            </div>
            <p>La Dirección de Investigación de la Universidad Bicentenaria de Aragua está comprometida con el desarrollo científico y tecnológico a través de la investigación de calidad y la formación de investigadores.</p>
            <div class="footer-social">
              <a href="#"><i class="fab fa-facebook-f"></i></a>
              <a href="#"><i class="fab fa-twitter"></i></a>
              <a href="#"><i class="fab fa-instagram"></i></a>
              <a href="#"><i class="fab fa-youtube"></i></a>
              <a href="#"><i class="fab fa-telegram-plane"></i></a>
            </div>
          </div>
        </div>
        <div class="col-lg-2 col-md-6 mb-5 mb-lg-0">
          <h4 class="footer-title">Enlaces Rápidos</h4>
          <ul class="footer-links">
            <li><a href="index.php"><i class="fas fa-chevron-right"></i> Inicio</a></li>
            <li><a href="nosotros.php"><i class="fas fa-chevron-right"></i> Nosotros</a></li>
            <li><a href="programas.php"><i class="fas fa-chevron-right"></i> Programas</a></li>
            <li><a href="publicaciones.php"><i class="fas fa-chevron-right"></i> Publicaciones</a></li>
            <li><a href="eventos.php"><i class="fas fa-chevron-right"></i> Eventos</a></li>
            <li><a href="contacto.php"><i class="fas fa-chevron-right"></i> Contacto</a></li>
          </ul>
        </div>
        <div class="col-lg-3 col-md-6 mb-5 mb-md-0">
          <h4 class="footer-title">Información de Contacto</h4>
          <div class="footer-contact-item">
            <div class="footer-contact-icon">
              <i class="fas fa-map-marker-alt"></i>
            </div>
            <div class="footer-contact-text">
              <span>Dirección:</span>
              Universidad Bicentenaria de Aragua, Turmero, Venezuela
            </div>
          </div>
          <div class="footer-contact-item">
            <div class="footer-contact-icon">
              <i class="fas fa-phone-alt"></i>
            </div>
            <div class="footer-contact-text">
              <span>Teléfonos:</span>
              Decanato: 0243-2650198<br>
              Dirección: 0243-2650010
            </div>
          </div>
          <div class="footer-contact-item">
            <div class="footer-contact-icon">
              <i class="fas fa-envelope"></i>
            </div>
            <div class="footer-contact-text">
              <span>Email:</span>
              decanato.postgrado@uba.edu.ve<br>
              direccion.investigacion@uba.edu.ve
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-6">
          <h4 class="footer-title">Boletín Informativo</h4>
          <div class="footer-newsletter">
            <p>Suscríbete a nuestro boletín para recibir las últimas noticias y actualizaciones.</p>
            <form class="footer-newsletter-form">
              <input type="email" placeholder="Tu correo electrónico" required>
              <button type="submit">Enviar</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="footer-bottom">
    <div class="container">
      <p>&copy; <?php echo date('Y'); ?> Universidad Bicentenaria de Aragua. Todos los derechos reservados.</p>
    </div>
  </div>
</footer>

<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Main JS -->
<script src="<?php echo $base_url; ?>assets/js/main.js"></script>

<!-- Page Specific JS -->
<?php if ($current_page == 'contacto'): ?>
<script src="<?php echo $base_url; ?>assets/js/contact.js"></script>
<?php endif; ?>

<!-- Additional JS -->
<?php if (isset($additional_js)): ?>
  <?php foreach ($additional_js as $js): ?>
  <script src="<?php echo $base_url . $js; ?>"></script>
  <?php endforeach; ?>
<?php endif; ?>

</body>
</html>