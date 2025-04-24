<?php
require 'includes/conexion.php';

// Obtener correo de contacto desde la configuración
try {
    $stmt = $pdo->query("SELECT valor FROM sistema_config WHERE clave = 'contacto_email'");
    $contacto_email = $stmt->fetchColumn();
} catch (PDOException $e) {
    $contacto_email = 'contacto@universidad.edu'; // Valor por defecto
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = htmlspecialchars($_POST['nombre']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $mensaje = htmlspecialchars($_POST['mensaje']);
    
    // Validar datos
    if (empty($nombre) || empty($email) || empty($mensaje)) {
        $error = "Todos los campos son obligatorios";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "El correo electrónico no es válido";
    } else {
        // Enviar correo (esto es un ejemplo, necesitarás configurar tu servidor de correo)
        $asunto = "Mensaje de contacto desde el sitio web";
        $cuerpo = "Nombre: $nombre\n";
        $cuerpo .= "Email: $email\n\n";
        $cuerpo .= "Mensaje:\n$mensaje";
        $headers = "From: $email";
        
        if (mail($contacto_email, $asunto, $cuerpo, $headers)) {
            $success = "Tu mensaje ha sido enviado correctamente";
        } else {
            $error = "Hubo un error al enviar el mensaje. Por favor intenta nuevamente.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contacto - Dirección de Investigación</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
  <div class="container navbar-container">
    <div class="navbar-logo">
      <img src="assets/imagenes/diuba-logo.png" alt="Logo">
      <a href="index.php" class="navbar-brand">Dirección de Investigación</a>
    </div>
    <button class="navbar-toggle" id="navbar-toggle">
      <i class="fas fa-bars"></i>
    </button>
    <ul class="navbar-menu" id="navbar-menu">
      <li class="navbar-item"><a href="index.php" class="navbar-link">Inicio</a></li>
      <li class="navbar-item"><a href="eventos.php" class="navbar-link">Eventos</a></li>
      <li class="navbar-item"><a href="lineas.php" class="navbar-link">Líneas De Investigación</a></li>
      <li class="navbar-item"><a href="contacto.php" class="navbar-link active">Contacto</a></li>
      <li class="navbar-item"><a href="admin/admin.php" class="navbar-link admin">DIED Admin</a></li>
    </ul>
  </div>
</nav>

<!-- Hero Section -->
<section class="hero">
  <div class="container">
    <h1>Contáctanos</h1>
    <p>Estamos aquí para responder tus preguntas y recibir tus comentarios</p>
  </div>
</section>

<!-- Formulario de Contacto -->
<section class="section contact-section">
  <div class="container">
    <h2 class="section-title">Envíanos un Mensaje</h2>
    
    <!-- Mensajes de retroalimentación -->
    <div id="mensaje-alerta" class="alert" style="display: none;">
      <span id="mensaje-texto"></span>
      <button type="button" class="alert-close" id="alertClose">×</button>
    </div>
    
    <div class="contact-form">
      <form action="enviar.php" method="POST" id="contactForm">
        <div class="form-group">
          <label for="nombre" class="form-label">Nombre</label>
          <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>
        <div class="form-group">
          <label for="email" class="form-label">Correo Electrónico</label>
          <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group">
          <label for="mensaje" class="form-label">Mensaje</label>
          <textarea class="form-control" id="mensaje" name="mensaje" rows="5" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Enviar Mensaje</button>
      </form>
    </div>
    
    <!-- Información de Contacto -->
    <div class="contact-info">
      <div class="row">
        <div class="col">
          <div class="contact-card">
            <div class="contact-icon">
              <i class="fas fa-building"></i>
            </div>
            <h3>Decanato de Investigación, Extensión y Postgrado</h3>
            <p>Teléfono: 0243-2650198</p>
            <p>E-mail: decanato.postgrado@uba.edu.ve</p>
          </div>
        </div>
        <div class="col">
          <div class="contact-card">
            <div class="contact-icon">
              <i class="fas fa-flask"></i>
            </div>
            <h3>Dirección de Investigación</h3>
            <p>Teléfonos: 0243-2650010</p>
            <p>E-mail: direccion.investigacion@uba.edu.ve</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Redes Sociales -->
<section class="section social-section">
  <div class="container">
    <h3 class="social-title">¡Visita Nuestras Redes Sociales!</h3>
    <div class="social-icons">
      <a href="https://www.instagram.com/ubapostgrados" target="_blank" class="social-icon instagram">
        <i class="fab fa-instagram"></i>
        <span class="social-tooltip">Instagram</span>
      </a>
      <a href="https://t.me/ColoquioLIIUBA" target="_blank" class="social-icon telegram">
        <i class="fab fa-telegram-plane"></i>
        <span class="social-tooltip">Telegram</span>
      </a>
      <a href="https://www.youtube.com/@direcciondeinvestigacionub8921" target="_blank" class="social-icon youtube">
        <i class="fab fa-youtube"></i>
        <span class="social-tooltip">Youtube</span>
      </a>
    </div>
  </div>
</section>

<!-- Back to Top Button -->
<div class="back-to-top" id="backToTop">
  <i class="fas fa-arrow-up"></i>
</div>

<!-- Footer -->
<footer class="footer">
  <div class="container">
    <p>Dirección de Investigación - Universidad Bicentenaria de Aragua &copy; 2025. Todos los derechos reservados.</p>
  </div>
</footer>

<script>
  // Navbar Toggle
  const navbarToggle = document.getElementById('navbar-toggle');
  const navbarMenu = document.getElementById('navbar-menu');
  
  navbarToggle.addEventListener('click', () => {
    navbarMenu.classList.toggle('active');
  });
  
  // Active Link
  const currentLocation = location.href;
  const navLinks = document.querySelectorAll('.navbar-link');
  
  navLinks.forEach(link => {
    if (link.href === currentLocation) {
      link.classList.add('active');
    }
  });
  
  // Back to Top Button
  const backToTopButton = document.getElementById('backToTop');
  
  window.addEventListener('scroll', () => {
    if (window.scrollY > 300) {
      backToTopButton.classList.add('show');
    } else {
      backToTopButton.classList.remove('show');
    }
  });
  
  backToTopButton.addEventListener('click', () => {
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
  });
  
  // Form Submission Feedback
  document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const envio = urlParams.get('envio');
    const detalleError = urlParams.get('detalle');
    
    const mensajeAlerta = document.getElementById('mensaje-alerta');
    const mensajeTexto = document.getElementById('mensaje-texto');
    const alertClose = document.getElementById('alertClose');
    
    if (envio === 'exito') {
      mensajeAlerta.style.display = 'block';
      mensajeAlerta.className = 'alert alert-success';
      mensajeTexto.textContent = '¡Mensaje enviado con éxito! Nos pondremos en contacto contigo pronto.';
    } else if (envio === 'error') {
      mensajeAlerta.style.display = 'block';
      mensajeAlerta.className = 'alert alert-danger';
      
      let mensajeError = 'Hubo un error al enviar el mensaje. Por favor, inténtalo de nuevo más tarde.';
      
      if (detalleError) {
        mensajeError += ' Detalle técnico: ' + decodeURIComponent(detalleError);
      }
      
      mensajeTexto.textContent = mensajeError;
    }
    
    // Close alert
    if (alertClose) {
      alertClose.addEventListener('click', () => {
        mensajeAlerta.style.display = 'none';
      });
    }
  });
  
  // Form Validation
  const contactForm = document.getElementById('contactForm');
  
  if (contactForm) {
    contactForm.addEventListener('submit', (e) => {
      let isValid = true;
      const nombre = document.getElementById('nombre');
      const email = document.getElementById('email');
      const mensaje = document.getElementById('mensaje');
      
      // Simple validation
      if (nombre.value.trim() === '') {
        isValid = false;
        nombre.classList.add('error');
      } else {
        nombre.classList.remove('error');
      }
      
      if (email.value.trim() === '' || !isValidEmail(email.value)) {
        isValid = false;
        email.classList.add('error');
      } else {
        email.classList.remove('error');
      }
      
      if (mensaje.value.trim() === '') {
        isValid = false;
        mensaje.classList.add('error');
      } else {
        mensaje.classList.remove('error');
      }
      
      if (!isValid) {
        e.preventDefault();
      }
    });
  }
  
  // Email validation helper
  function isValidEmail(email) {
    const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
  }
  
  // Animation on scroll
  const revealElements = document.querySelectorAll('.section');
  
  function reveal() {
    revealElements.forEach(element => {
      const elementTop = element.getBoundingClientRect().top;
      const windowHeight = window.innerHeight;
      
      if (elementTop < windowHeight - 100) {
        element.style.opacity = 1;
        element.style.transform = 'translateY(0)';
      }
    });
  }
  
  window.addEventListener('scroll', reveal);
  window.addEventListener('load', reveal);
  
  // Initialize with styles
  revealElements.forEach(element => {
    element.style.opacity = 0;
    element.style.transform = 'translateY(50px)';
    element.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
  });
  document.addEventListener('DOMContentLoaded', function() {
  // Añadir índices para la animación del menú móvil
  const navItems = document.querySelectorAll('.navbar-menu .navbar-item');
  navItems.forEach((item, index) => {
    item.style.setProperty('--item-index', index);
  });
  
  // Efecto de scroll para el navbar
  window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.navbar');
    if (window.scrollY > 50) {
      navbar.style.padding = '0.25rem 0';
      navbar.style.backgroundColor = 'rgba(27, 53, 93, 0.98)';
      navbar.style.boxShadow = '0 4px 10px rgba(0, 0, 0, 0.2)';
    } else {
      navbar.style.padding = '0.5rem 0';
      navbar.style.backgroundColor = 'rgba(27, 53, 93, 0.95)';
      navbar.style.boxShadow = '0 4px 6px rgba(0, 0, 0, 0.1)';
    }
  });
});
</script>

</body>
</html>
