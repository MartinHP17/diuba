<!DOCTYPE php>
<php lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contacto - Dirección de Investigación</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="styles.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
  integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
  crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>

  <nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
      <a class="navbar-brand d-flex align-items-center" href="#">
        <img src="imagenes/Logo diuba.png" alt="Logo" width="200" height="200" class="me-2">
      </a>
      <a class="navbar-brand" href="index.php">Dirección de Investigación</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link" href="index.php">Inicio</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="eventos.php">Eventos</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="lineas.php">Líneas De Investigación</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="contacto.php">Contacto</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <section class="container py-5">
    <h2>Contáctanos</h2>
      <!-- Mensajes de retroalimentación -->
    <div id="mensaje-alerta" class="alert alert-dismissible fade show" style="display: none;">
      <span id="mensaje-texto"></span>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <form action="https://formspree.io/f/mrbpakbz" method="POST">
      <div class="mb-3">
        <label for="nombre" class="form-label">Nombre</label>
        <input type="text" class="form-control" id="nombre" name="nombre" required>
      </div>
      <div class="mb-3">
        <label for="email" class="form-label">Correo Electrónico</label>
        <input type="email" class="form-control" id="email" name="email" required>
      </div>
      <div class="mb-3">
        <label for="mensaje" class="form-label">Mensaje</label>
        <textarea class="form-control" id="mensaje" name="mensaje" rows="4" required></textarea>
      </div>
      <button type="submit" class="btn btn-primary">Enviar</button>
    </form>
  </section>
  
<!-- Sección de contactos -->
<div class="row">
  <div class="col-12 text-center mb-4">
    <h2>Contactos</h2>
  </div>
  
  <div class="col-md-6">
    <div class="contact-info text-center">
      <i class="far fa-building"></i>
      <h3>Decanato de Investigación, Extensión y Postgrado</h3>
      <p>Teléfono: 0243-2650198<br>
      E-mail: decanato.postgrado@uba.edu.ve</p>
    </div>
  </div>
  
  <div class="col-md-6">
    <div class="contact-info text-center">
      <i class="far fa-building"></i>
      <h3>Dirección de Investigación</h3>
      <p>Teléfonos: 0243-2650010<br>
      E-mail: direccion.investigacion@uba.edu.ve</p>
    </div>
  </div>
</div>
<section class="container-fluid">
<h3 class="text-center">¡Visita Nuestras Redes Sociales!</h3>
<ul class="wrapper">
  
  <li class="icon instagram">
    <a href="https://www.instagram.com/ubapostgrados" target="_blank">
      <span class="tooltip">Instagram</span>
      <span><i class="fab fa-instagram"></i></span></a>
  </li>
  <li class="icon telegram"> 
    <a href="https://t.me/ColoquioLIIUBA" target="_blank">
    <span class="tooltip">Telegram</span>
    <span><i class="fab fa-telegram-plane"></i></span></a>
</li>
  <li class="icon youtube">
    <a href="https://www.youtube.com/@direcciondeinvestigacionub8921" target="_blank" >
      <span class="tooltip">Youtube</span>
      <span><i class="fab fa-youtube"></i></span></a>
  </li>
</ul>
</section>
<footer class="bg-dark text-white text-center py-3">
  <p>Dirección de Investigación - Universidad Bicentenaria de Aragua &copy; 2025. Todos los derechos reservados.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Verificar parámetros de URL al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const envio = urlParams.get('envio');
    const detalleError = urlParams.get('detalle');
    
    const mensajeAlerta = document.getElementById('mensaje-alerta');
    const mensajeTexto = document.getElementById('mensaje-texto');
    
    if (envio === 'exito') {
        mensajeAlerta.style.display = 'block';
        mensajeAlerta.className = 'alert alert-success alert-dismissible fade show';
        mensajeTexto.textContent = '¡Mensaje enviado con éxito! Nos pondremos en contacto contigo pronto.';
    } else if (envio === 'error') {
        mensajeAlerta.style.display = 'block';
        mensajeAlerta.className = 'alert alert-danger alert-dismissible fade show';
        
        let mensajeError = 'Hubo un error al enviar el mensaje. Por favor, inténtalo de nuevo más tarde.';
        
        if (detalleError) {
            mensajeError += ' Detalle técnico: ' + decodeURIComponent(detalleError);
        }
        
        mensajeTexto.textContent = mensajeError;
    }
});
</script>
</body>
</php>
