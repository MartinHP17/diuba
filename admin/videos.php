<?php
session_start();
require_once '../includes/conexion.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Procesar agregar video
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar_video'])) {
    try {
        $youtube_url = $_POST['youtube_url'];
        $titulo = $_POST['titulo'];
        $descripcion = $_POST['descripcion'];
        
        // Validar que sea URL de YouTube
        if (!preg_match('/^(https?\:\/\/)?(www\.)?(youtube\.com|youtu\.?be)\/.+$/', $youtube_url)) {
            throw new Exception("Por favor ingrese un enlace válido de YouTube");
        }
        
        $stmt = $pdo->prepare("INSERT INTO videos_youtube (youtube_url, titulo, descripcion) VALUES (?, ?, ?)");
        $stmt->execute([
            htmlspecialchars($youtube_url),
            htmlspecialchars($titulo),
            htmlspecialchars($descripcion)
        ]);
        
        $_SESSION['success'] = "Video agregado correctamente";
        header("Location: videos.php");
        exit();
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Procesar eliminar video
if (isset($_GET['eliminar'])) {
    try {
        $id = $_GET['eliminar'];
        
        $stmt = $pdo->prepare("DELETE FROM videos_youtube WHERE id = ?");
        $stmt->execute([$id]);
        
        $_SESSION['success'] = "Video eliminado correctamente";
        header("Location: videos.php");
        exit();
    } catch (PDOException $e) {
        $error = "Error al eliminar el video: " . $e->getMessage();
    }
}

$videos = $pdo->query("SELECT * FROM videos_youtube ORDER BY fecha_creacion DESC")->fetchAll();
$page_title = 'Gestión de Videos YouTube';
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="../assets/css/admin.css" rel="stylesheet">
</head>
<body>
    <div class="d-flex" id="wrapper">
            <!-- Sidebar -->
            <div class="bg-dark" id="sidebar-wrapper">
                <div class="sidebar-heading text-center py-4">
                    <img src="../assets/imagenes/diuba-logo.png" alt="Logo" width="80" height="80" class="mb-2">
                    <div class="text-white">Administración</div>
                </div>
                <div class="list-group list-group-flush my-3">
                    <a href="admin.php" class="list-group-item list-group-item-action bg-transparent text-white">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                    <a href="gestionar_eventos.php" class="list-group-item list-group-item-action bg-transparent text-white">
                        <i class="fas fa-calendar-alt me-2"></i>Gestionar Eventos
                    </a>
                    <a href="galeria.php" class="list-group-item list-group-item-action bg-transparent text-white">
                        <i class="fas fa-images me-2"></i>Gestionar Galería
                    </a>
                    <a href="videos.php" class="list-group-item list-group-item-action bg-transparent text-white active">
                         <i class="fas fa-video me-2"></i>Gestionar Videos
                    </a>
                    <a href="configuracion.php" class="list-group-item list-group-item-action bg-transparent text-white">
                        <i class="fas fa-cog me-2"></i>Configuración
                    </a>
                    <a href="../index.php" target="_blank" class="list-group-item list-group-item-action bg-transparent text-white">
                        <i class="fas fa-external-link-alt me-2"></i>Ver Sitio
                    </a>
                    <a href="logout.php" class="list-group-item list-group-item-action bg-transparent text-danger fw-bold">
                        <i class="fas fa-power-off me-2"></i>Cerrar Sesión
                    </a>
                </div>
            </div>
            <!-- /#sidebar-wrapper -->
            <!--navbar-->
            <div id="page-content-wrapper">
                <nav class="navbar navbar-expand-lg navbar-light bg-white py-4 px-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-align-left primary-text fs-4 me-3" id="menu-toggle"></i>
                        <h2 class="fs-2 m-0">Videos</h2>
                    </div>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle second-text fw-bold" href="#" id="navbarDropdown"
                                    role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-user me-2"></i><?= htmlspecialchars($_SESSION['user_name']) ?>
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <li><span class="dropdown-item-text"><?= htmlspecialchars($_SESSION['user_email']) ?></span></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="logout.php">Cerrar Sesión</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>
                <!-- Page Content -->
                <div id="page-content-wrapper">
                    <div class="youtube-alert">
                        <i class="fas fa-info-circle youtube-alert-icon"></i>
                        <div class="youtube-alert-content">
                            <strong>Importante:</strong> Todos los videos mostrados están alojados exclusivamente 
                            en la plataforma de YouTube. Solo se pueden agregar enlaces de videos públicos de YouTube.
                        </div>
                    </div>
                        <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
                            <?php unset($_SESSION['success']); ?>
                        <?php endif; ?>
                        
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <i class="fas fa-plus-circle"></i> Agregar Nuevo Video
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <div class="mb-3">
                                        <label for="youtube_url" class="form-label">URL de YouTube</label>
                                        <input type="url" class="form-control" id="youtube_url" name="youtube_url" 
                                            placeholder="https://www.youtube.com/watch?v=..." required>
                                        <div class="form-text">Solo se aceptan videos alojados en YouTube</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="titulo" class="form-label">Título del Video</label>
                                        <input type="text" class="form-control" id="titulo" name="titulo" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="descripcion" class="form-label">Descripción</label>
                                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                                    </div>
                                    
                                    <button type="submit" name="agregar_video" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Guardar Video
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                <i class="fas fa-list"></i> Videos Registrados
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Título</th>
                                                <th>URL</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($videos as $video): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($video['titulo']) ?></td>
                                                    <td class="text-truncate" style="max-width: 200px;">
                                                        <a href="<?= htmlspecialchars($video['youtube_url']) ?>" target="_blank">
                                                            <?= htmlspecialchars($video['youtube_url']) ?>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <a href="videos.php?eliminar=<?= $video['id'] ?>" 
                                                        class="btn btn-sm btn-danger"
                                                        onclick="return confirm('¿Eliminar este video?')">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal para reproducir videos -->
            <div class="modal fade" id="videoModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="videoModalTitle">Reproduciendo Video</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center p-0">
                            <div class="ratio ratio-16x9">
                                <iframe id="youtubePlayer" src="" allowfullscreen></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Control de videos de YouTube
    document.querySelectorAll('.play-video').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const videoId = this.getAttribute('data-id');
            
            document.getElementById('youtubePlayer').src = `https://www.youtube.com/embed/${videoId}?autoplay=1`;
            document.getElementById('videoModalTitle').textContent = this.closest('tr').querySelector('td:nth-child(3)').textContent;
            
            const videoModal = new bootstrap.Modal(document.getElementById('videoModal'));
            videoModal.show();
        });
    });

    // Limpiar el reproductor al cerrar el modal
    document.getElementById('videoModal')?.addEventListener('hidden.bs.modal', function() {
        document.getElementById('youtubePlayer').src = '';
    });
    </script>
</body>
</html>