<?php
require 'includes/conexion.php';

// Obtener eventos activos de la base de datos
try {
    $eventos = $pdo->query("
        SELECT e.*, u.nombre as autor 
        FROM eventos e
        JOIN usuarios u ON e.usuario_id = u.id
        WHERE e.estado = 'Activo'
        ORDER BY e.fecha_evento DESC, e.fecha_creacion DESC
    ")->fetchAll();
} catch (PDOException $e) {
    $eventos = [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventos - Dirección de Investigación</title>
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
      <li class="navbar-item"><a href="eventos.php" class="navbar-link active">Eventos</a></li>
      <li class="navbar-item"><a href="lineas.php" class="navbar-link">Líneas De Investigación</a></li>
      <li class="navbar-item"><a href="contacto.php" class="navbar-link">Contacto</a></li>
      <li class="navbar-item"><a href="admin/admin.php" class="navbar-link admin">DIED Admin</a></li>
    </ul>
  </div>
</nav>

<!-- Hero Section -->
<section class="hero">
  <div class="container">
    <h1>Eventos Académicos</h1>
    <p>Conoce y participa en nuestros eventos de investigación y desarrollo académico</p>
  </div>
</section>

<!-- Próximos Eventos -->
<section class="section events-section">
  <div class="container">
    <h2 class="section-title">Próximos Eventos</h2>
    
    <?php if (count($eventos) > 0): ?>
      <div class="accordion-container">
        <?php foreach ($eventos as $index => $evento): ?>
          <div class="accordion-item">
            <button class="accordion-button" id="accordion-btn-<?= $index ?>">
              <?= htmlspecialchars($evento['titulo']) ?>
              <span class="badge"><?= $evento['tipo_evento'] ?></span>
              <?php if ($evento['fecha_evento']): ?>
                <span class="event-date"><?= date('d/m/Y', strtotime($evento['fecha_evento'])) ?></span>
              <?php endif; ?>
            </button>
            <div class="accordion-content" id="accordion-content-<?= $index ?>">
              <div class="accordion-body">
                <?php if ($evento['imagen_ruta']): ?>
                  <img src="<?= htmlspecialchars($evento['imagen_ruta']) ?>" 
                       class="event-image" 
                       alt="<?= htmlspecialchars($evento['titulo']) ?>">
                <?php else: ?>
                  <div class="event-image-placeholder">
                    <i class="fas fa-calendar-alt"></i>
                  </div>
                <?php endif; ?>
                
                <?php if ($evento['ubicacion']): ?>
                  <div class="event-location">
                    <i class="fas fa-map-marker-alt"></i>
                    <span><?= htmlspecialchars($evento['ubicacion']) ?></span>
                  </div>
                <?php endif; ?>
                
                <div class="event-description">
                  <?= nl2br(htmlspecialchars($evento['descripcion'])) ?>
                </div>  
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="alert-info">No hay eventos programados actualmente.</div>
    <?php endif; ?>
  </div>
</section>

<!--Formulario de Inscripción-->
<section class="section" style="text-align: center;">
  <div class="container">
    <h2 class="section-title">¡Inscríbete en la jornada de investigación!</h2>
    <p>A continuación, puede completar el formulario de inscripción para participar en nuestra jornada de investigación. Toda la información proporcionada será utilizada únicamente con fines organizativos.</p>
    <div class="ratio ratio-16x9" style="display: inline-block;">
      <iframe src="https://forms.gle/LJjd2VBqMiFpBEML6" width="640" height="480" frameborder="0" marginheight="0" marginwidth="0">Cargando…</iframe>
    </div>
  </div>  
</section>

<!-- Galería de Fotos -->
<section class="gallery-section">
    <div class="container">
        <h2 class="section-title">Galería de Fotos</h2>
        <p class="text-center mb-4">Revive los momentos especiales de nuestros eventos pasados.</p>
        
        <?php
        $fotos = []; // Inicializar variable
        try {
            $fotos = $pdo->query("SELECT * FROM galeria_fotos WHERE estado = 'Activo' ORDER BY fecha_creacion DESC LIMIT 6")->fetchAll();
        } catch (PDOException $e) {
            echo '<div class="col-12"><div class="alert alert-danger">Error al cargar la galería: '.htmlspecialchars($e->getMessage()).'</div></div>';
        }
        
        if (count($fotos) > 0): ?>
            <div class="carousel" id="galleryCarousel">
                <div class="carousel-inner">
                    <?php foreach ($fotos as $index => $foto): ?>
                        <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                            <img src="<?= htmlspecialchars($foto['imagen_ruta']) ?>" 
                                 alt="<?= htmlspecialchars($foto['titulo']) ?>">
                            <div class="carousel-caption">
                                <h5><?= htmlspecialchars($foto['titulo']) ?></h5>
                                <p><?= htmlspecialchars($foto['descripcion']) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <button class="carousel-control prev" onclick="moveSlide(-1)">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="carousel-control next" onclick="moveSlide(1)">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center py-4">
                <i class="fas fa-image fa-3x mb-3"></i>
                <p>No hay imágenes en la galería todavía.</p>
            </div>
        <?php endif; ?>
    </div>
</section>
<!-- Sección de Videos Institucionales -->
<section class="youtube-section">
    <div class="container">
        
        <h2 class="section-title youtube-title">
            <i class="fab fa-youtube youtube-icon"></i>Videos Institucionales
        </h2>
        
        <?php
        try {
            $videos = $pdo->query("SELECT * FROM videos_youtube ORDER BY fecha_creacion ")->fetchAll();
            
            if (count($videos) > 0): ?>
                <div class="youtube-grid">
                    <?php foreach ($videos as $video): 
                        // Extraer el ID del video
                        $video_id = '';
                        $url = $video['youtube_url'];
                        if (preg_match('/[\\?\\&]v=([^\\?\\&]+)/', $url, $matches)) {
                            $video_id = $matches[1];
                        } elseif (preg_match('/youtu\\.be\\/([^\\?\\&]+)/', $url, $matches)) {
                            $video_id = $matches[1];
                        }
                    ?>
                        <?php if ($video_id): ?>
                            <div class="youtube-card">
                                <div class="youtube-video-container">
                                    <iframe src="https://www.youtube.com/embed/<?= $video_id ?>?rel=0&modestbranding=1" 
                                            title="<?= htmlspecialchars($video['titulo']) ?>" 
                                            class="youtube-iframe"
                                            allowfullscreen></iframe>
                                </div>
                                <div class="youtube-card-body">
                                    <h3 class="youtube-card-title"><?= htmlspecialchars($video['titulo']) ?></h3>
                                    <p class="youtube-card-description"><?= htmlspecialchars($video['descripcion']) ?></p>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                
                <?php if (count($videos) > 3): ?>
                    <div class="youtube-more-container">
                        <a href="videos.php" class="youtube-more-button">
                            <i class="fab fa-youtube youtube-button-icon"></i> Ver todos los videos
                        </a>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="youtube-empty">
                    <i class="fas fa-video-slash youtube-empty-icon"></i>
                    <p class="youtube-empty-text">Próximamente agregaremos nuestros videos institucionales.</p>
                </div>
            <?php endif;
        } catch (PDOException $e) {
            echo '<div class="youtube-error">Error al cargar los videos: '.htmlspecialchars($e->getMessage()).'</div>';
        }
        ?>
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
  document.addEventListener('DOMContentLoaded', function() {
    // ==================== NAVBAR ====================
    // Toggle del menú móvil
    const navbarToggle = document.getElementById('navbar-toggle');
    const navbarMenu = document.getElementById('navbar-menu');
    
    if (navbarToggle && navbarMenu) {
        navbarToggle.addEventListener('click', () => {
            navbarMenu.classList.toggle('active');
        });
    }

    // Enlace activo según la página actual
    const currentLocation = location.href;
    const navLinks = document.querySelectorAll('.navbar-link');
    
    navLinks.forEach(link => {
        if (link.href === currentLocation) {
            link.classList.add('active');
        }
    });

    // Efecto de scroll para el navbar
    const navbar = document.querySelector('.navbar');
    if (navbar) {
        window.addEventListener('scroll', function() {
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
    }

    // Índices para animación del menú móvil
    const navItems = document.querySelectorAll('.navbar-menu .navbar-item');
    navItems.forEach((item, index) => {
        item.style.setProperty('--item-index', index);
    });

    // ==================== BOTÓN VOLVER ARRIBA ====================
    const backToTopButton = document.getElementById('backToTop');
    if (backToTopButton) {
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
    }

    // ==================== ACORDEÓN ====================
    const accordionButtons = document.querySelectorAll('.accordion-button');
    accordionButtons.forEach((button, index) => {
        button.addEventListener('click', () => {
            const content = document.getElementById(`accordion-content-${index}`);
            
            // Alternar clase active en el botón
            button.classList.toggle('active');
            
            // Alternar visibilidad del contenido
            if (content.style.maxHeight) {
                content.style.maxHeight = null;
            } else {
                content.style.maxHeight = content.scrollHeight + 'px';
            }
        });
    });

    // ==================== CARRUSEL PERSONALIZADO ====================
    function initCustomCarousel() {
        let currentIndex = 0;
        const carousel = document.querySelector('.carousel');
        const items = document.querySelectorAll('.carousel-item');
        const totalItems = items.length;

        if (items.length === 0) return;

        function updateCarousel() {
            const inner = document.querySelector('.carousel-inner');
            inner.style.transform = `translateX(-${currentIndex * 100}%)`;
            
            // Actualizar clases active
            items.forEach((item, index) => {
                item.classList.toggle('active', index === currentIndex);
            });
        }

        function moveSlide(direction) {
            currentIndex = (currentIndex + direction + totalItems) % totalItems;
            updateCarousel();
        }

        // Event listeners para controles
        document.querySelector('.carousel-control.prev')?.addEventListener('click', () => moveSlide(-1));
        document.querySelector('.carousel-control.next')?.addEventListener('click', () => moveSlide(1));

        // Auto-avance cada 5 segundos
        let autoSlide = setInterval(() => moveSlide(1), 5000);

        // Pausar al hacer hover
        carousel?.addEventListener('mouseenter', () => clearInterval(autoSlide));
        carousel?.addEventListener('mouseleave', () => {
            autoSlide = setInterval(() => moveSlide(1), 5000);
        });

        // Inicializar
        updateCarousel();
    }

    initCustomCarousel();

    // ==================== REPRODUCTOR DE VIDEO ====================
    const videoButtons = document.querySelectorAll('.watch-video, .video-play');
    const videoPlayer = document.getElementById('videoPlayer');
    const youtubeVideo = document.getElementById('youtubeVideo');
    const closeVideo = document.getElementById('closeVideo');
    
    if (videoButtons.length && videoPlayer && youtubeVideo && closeVideo) {
        videoButtons.forEach(button => {
            button.addEventListener('click', () => {
                const videoId = button.getAttribute('data-video-id');
                youtubeVideo.src = `https://www.youtube.com/embed/${videoId}?autoplay=1`;
                videoPlayer.classList.add('active');
                
                // Scroll al reproductor de video
                videoPlayer.scrollIntoView({ behavior: 'smooth', block: 'center' });
            });
        });
        
        closeVideo.addEventListener('click', () => {
            youtubeVideo.src = '';
            videoPlayer.classList.remove('active');
        });
    }

    // ==================== ANIMACIONES AL SCROLL ====================
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
    
    // Inicializar estilos
    revealElements.forEach(element => {
        element.style.opacity = 0;
        element.style.transform = 'translateY(50px)';
        element.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
    });
    
    window.addEventListener('scroll', reveal);
    window.addEventListener('load', reveal);

    // ==================== MODAL DE GALERÍA ====================
    const galleryModal = document.getElementById('galleryModal');
    if (galleryModal) {
        galleryModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const title = button.getAttribute('data-title');
            const desc = button.getAttribute('data-desc');
            const imgSrc = button.getAttribute('data-img');
            
            document.getElementById('galleryModalTitle').textContent = title;
            document.getElementById('galleryModalImage').src = imgSrc;
            document.getElementById('galleryModalDesc').textContent = desc;
        });
    }
});
</script>



</body>
</html>
