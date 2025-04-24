<?php
session_start();
require_once '../includes/conexion.php';

// Verificar autenticación
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Configuración para subida de imágenes
$uploadDir = '../assets/imagenes/eventos/';
$uploadDirGaleria = '../assets/imagenes/galeria/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}
if (!file_exists($uploadDirGaleria)) {
    mkdir($uploadDirGaleria, 0777, true);
}

$allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
$maxSize = 5 * 1024 * 1024; // 5MB

// Procesar formulario principal de eventos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['titulo'])) {
    $id = $_POST['id'] ?? null;
    $titulo = htmlspecialchars($_POST['titulo'] ?? '');
    $descripcion = htmlspecialchars($_POST['descripcion'] ?? '');
    $fecha_evento = $_POST['fecha_evento'] ?? '';
    $ubicacion = htmlspecialchars($_POST['ubicacion'] ?? '');
    $tipo_evento = $_POST['tipo_evento'] ?? 'Otro';
    $estado = $_POST['estado'] ?? 'Activo';
    
    // Procesar imagen principal
    $imagen_ruta = null;
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $fileType = mime_content_type($_FILES['imagen']['tmp_name']);
        if (!in_array($fileType, $allowedTypes)) {
            $error = "Solo se permiten imágenes JPEG, PNG o GIF.";
        } elseif ($_FILES['imagen']['size'] > $maxSize) {
            $error = "La imagen es demasiado grande (máximo 5MB).";
        } else {
            $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
            $nombreArchivo = uniqid('evento_') . '.' . $extension;
            $rutaDestino = $uploadDir . $nombreArchivo;
            $imagen_ruta = 'assets/imagenes/eventos/' . $nombreArchivo;

            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino)) {
                if ($id) {
                    $stmt_img = $pdo->prepare("SELECT imagen_ruta FROM eventos WHERE id = ?");
                    $stmt_img->execute([$id]);
                    $evento_anterior = $stmt_img->fetch();

                    if ($evento_anterior && $evento_anterior['imagen_ruta'] && file_exists('../' . $evento_anterior['imagen_ruta'])) {
                        unlink('../' . $evento_anterior['imagen_ruta']);
                    }
                }
            } else {
                $error = "Error al subir la imagen.";
            }
        }
    }

    try {
        if ($id) {
            if ($imagen_ruta) {
                $stmt = $pdo->prepare("UPDATE eventos SET titulo=?, descripcion=?, imagen_ruta=?, fecha_evento=?, ubicacion=?, tipo_evento=?, estado=? WHERE id=?");
                $stmt->execute([$titulo, $descripcion, $imagen_ruta, $fecha_evento, $ubicacion, $tipo_evento, $estado, $id]);
            } else {
                $stmt = $pdo->prepare("UPDATE eventos SET titulo=?, descripcion=?, fecha_evento=?, ubicacion=?, tipo_evento=?, estado=? WHERE id=?");
                $stmt->execute([$titulo, $descripcion, $fecha_evento, $ubicacion, $tipo_evento, $estado, $id]);
            }
            $_SESSION['success'] = "Evento actualizado correctamente";
        } else {
            $stmt = $pdo->prepare("INSERT INTO eventos (titulo, descripcion, imagen_ruta, usuario_id, fecha_evento, ubicacion, tipo_evento, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$titulo, $descripcion, $imagen_ruta, $_SESSION['user_id'], $fecha_evento, $ubicacion, $tipo_evento, $estado]);
            $id = $pdo->lastInsertId();
            $_SESSION['success'] = "Evento creado correctamente";
        }
        
        // Procesar imágenes de galería
        if (isset($_FILES['galeria']) && $id) {
            foreach ($_FILES['galeria']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['galeria']['error'][$key] === UPLOAD_ERR_OK) {
                    $fileType = mime_content_type($tmp_name);
                    if (in_array($fileType, $allowedTypes)) {
                        $extension = pathinfo($_FILES['galeria']['name'][$key], PATHINFO_EXTENSION);
                        $nombreArchivo = uniqid('galeria_') . '.' . $extension;
                        $rutaDestino = $uploadDirGaleria . $nombreArchivo;
                        
                        if (move_uploaded_file($tmp_name, $rutaDestino)) {
                            $imagen_galeria_ruta = 'assets/imagenes/galeria/' . $nombreArchivo;
                            $stmt = $pdo->prepare("INSERT INTO galeria_eventos (evento_id, imagen_ruta) VALUES (?, ?)");
                            $stmt->execute([$id, $imagen_galeria_ruta]);
                        }
                    }
                }
            }
        }
        
        header("Location: gestionar_eventos.php?editar=" . $id);
        exit();
    } catch (PDOException $e) {
        $error = "Error al guardar el evento: " . $e->getMessage();
    }
}

// Procesar eliminación de evento
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    
    try {
        // Eliminar imagen principal
        $stmt_img = $pdo->prepare("SELECT imagen_ruta FROM eventos WHERE id = ?");
        $stmt_img->execute([$id]);
        $evento_img = $stmt_img->fetch();
        
        if ($evento_img && $evento_img['imagen_ruta'] && file_exists('../' . $evento_img['imagen_ruta'])) {
            unlink('../' . $evento_img['imagen_ruta']);
        }
        
        // Eliminar imágenes de galería
        $stmt_galeria = $pdo->prepare("SELECT imagen_ruta FROM galeria_eventos WHERE evento_id = ?");
        $stmt_galeria->execute([$id]);
        $imagenes_galeria = $stmt_galeria->fetchAll();
        
        foreach ($imagenes_galeria as $imagen) {
            if (file_exists('../' . $imagen['imagen_ruta'])) {
                unlink('../' . $imagen['imagen_ruta']);
            }
        }
        
        // Eliminar registros de la base de datos
        $stmt = $pdo->prepare("DELETE FROM eventos WHERE id = ?");
        $stmt->execute([$id]);
        
        $_SESSION['success'] = "Evento eliminado correctamente";
        header("Location: gestionar_eventos.php");
        exit();
    } catch (PDOException $e) {
        $error = "Error al eliminar el evento: " . $e->getMessage();
    }
}

// Procesar eliminación de imagen de galería
if (isset($_GET['eliminar_imagen'])) {
    $imagen_id = $_GET['eliminar_imagen'];
    $evento_id = $_GET['evento_id'] ?? 0;
    
    try {
        $stmt = $pdo->prepare("SELECT imagen_ruta FROM galeria_eventos WHERE id = ?");
        $stmt->execute([$imagen_id]);
        $imagen = $stmt->fetch();
        
        if ($imagen && file_exists('../' . $imagen['imagen_ruta'])) {
            unlink('../' . $imagen['imagen_ruta']);
        }
        
        $stmt = $pdo->prepare("DELETE FROM galeria_eventos WHERE id = ?");
        $stmt->execute([$imagen_id]);
        
        $_SESSION['success'] = "Imagen eliminada correctamente";
        header("Location: gestionar_eventos.php?editar=" . $evento_id);
        exit();
    } catch (PDOException $e) {
        $error = "Error al eliminar la imagen: " . $e->getMessage();
    }
}

// Obtener todos los eventos
try {
    $eventos = $pdo->query("
        SELECT e.*, u.nombre as autor 
        FROM eventos e
        JOIN usuarios u ON e.usuario_id = u.id
        ORDER BY e.fecha_evento DESC, e.fecha_creacion DESC
    ")->fetchAll();
} catch (PDOException $e) {
    $error = "Error al cargar los eventos: " . $e->getMessage();
    $eventos = [];
}

// Obtener evento para editar
$evento_editar = null;
$galeria = [];
if (isset($_GET['editar'])) {
    $id = $_GET['editar'];
    try {
        $stmt = $pdo->prepare("SELECT * FROM eventos WHERE id = ?");
        $stmt->execute([$id]);
        $evento_editar = $stmt->fetch();
        
        // Obtener imágenes de la galería
        $stmt_galeria = $pdo->prepare("SELECT * FROM galeria_eventos WHERE evento_id = ?");
        $stmt_galeria->execute([$id]);
        $galeria = $stmt_galeria->fetchAll();
    } catch (PDOException $e) {
        $error = "Error al cargar el evento: " . $e->getMessage();
    }
}

$page_title = 'Gestionar Eventos - Dirección de Investigación UBA';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="../assets/css/admin.css" rel="stylesheet">
    <style>
        .gallery-thumbnail {
            height: 150px;
            width: 100%;
            object-fit: cover;
        }
        .gallery-item {
            position: relative;
            margin-bottom: 15px;
        }
        .gallery-actions {
            position: absolute;
            top: 5px;
            right: 5px;
        }
    </style>
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
                <a href="gestionar_eventos.php" class="list-group-item list-group-item-action bg-transparent text-white active">
                    <i class="fas fa-calendar-alt me-2"></i>Gestionar Eventos
                </a>
                <a href="galeria.php" class="list-group-item list-group-item-action bg-transparent text-white">
                    <i class="fas fa-images me-2"></i>Gestionar Galería
                </a>
                <a href="videos.php" class="list-group-item list-group-item-action bg-transparent text-white">
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

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-white py-4 px-4">
                <div class="d-flex align-items-center">
                    <i class="fas fa-align-left primary-text fs-4 me-3" id="menu-toggle"></i>
                    <h2 class="fs-2 m-0">Gestionar Eventos</h2>
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

            <div class="container-fluid px-4">
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show mt-3">
                        <i class="fas fa-check-circle me-2"></i> <?= $_SESSION['success'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show mt-3">
                        <i class="fas fa-exclamation-circle me-2"></i> <?= $error ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <div class="card mb-4 shadow-sm mt-3">
                    <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-calendar-plus me-2"></i> <?= $evento_editar ? 'Editar' : 'Agregar' ?> Evento</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <?php if ($evento_editar): ?>
                                <input type="hidden" name="id" value="<?= $evento_editar['id'] ?>">
                            <?php endif; ?>
                            
                            <div class="mb-3">
                                <label for="titulo" class="form-label">Título*</label>
                                <input type="text" class="form-control" id="titulo" name="titulo" 
                                       value="<?= $evento_editar ? htmlspecialchars($evento_editar['titulo']) : '' ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="descripcion" class="form-label">Descripción*</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" rows="5" required><?= 
                                    $evento_editar ? htmlspecialchars($evento_editar['descripcion']) : '' 
                                ?></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="fecha_evento" class="form-label">Fecha del Evento</label>
                                    <input type="date" class="form-control" id="fecha_evento" name="fecha_evento"
                                           value="<?= $evento_editar ? htmlspecialchars($evento_editar['fecha_evento']) : '' ?>">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="ubicacion" class="form-label">Ubicación</label>
                                    <input type="text" class="form-control" id="ubicacion" name="ubicacion"
                                           value="<?= $evento_editar ? htmlspecialchars($evento_editar['ubicacion']) : '' ?>">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="tipo_evento" class="form-label">Tipo de Evento</label>
                                    <select class="form-select" id="tipo_evento" name="tipo_evento">
                                        <option value="Congreso" <?= $evento_editar && $evento_editar['tipo_evento'] === 'Congreso' ? 'selected' : '' ?>>Congreso</option>
                                        <option value="Jornada" <?= $evento_editar && $evento_editar['tipo_evento'] === 'Jornada' ? 'selected' : '' ?>>Jornada</option>
                                        <option value="Taller" <?= $evento_editar && $evento_editar['tipo_evento'] === 'Taller' ? 'selected' : '' ?>>Taller</option>
                                        <option value="Conferencia" <?= $evento_editar && $evento_editar['tipo_evento'] === 'Conferencia' ? 'selected' : '' ?>>Conferencia</option>
                                        <option value="Otro" <?= !$evento_editar || $evento_editar['tipo_evento'] === 'Otro' ? 'selected' : '' ?>>Otro</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="estado" class="form-label">Estado</label>
                                    <select class="form-select" id="estado" name="estado">
                                        <option value="Activo" <?= $evento_editar && $evento_editar['estado'] === 'Activo' ? 'selected' : '' ?>>Activo</option>
                                        <option value="Inactivo" <?= $evento_editar && $evento_editar['estado'] === 'Inactivo' ? 'selected' : '' ?>>Inactivo</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="imagen" class="form-label">Imagen Principal</label>
                                    <input type="file" class="form-control" id="imagen" name="imagen" accept="image/*">
                                    <?php if ($evento_editar && $evento_editar['imagen_ruta']): ?>
                                        <div class="mt-2">
                                            <img src="../<?= htmlspecialchars($evento_editar['imagen_ruta']) ?>" 
                                                 class="img-thumbnail" style="max-height: 100px;">
                                            <small class="d-block text-muted">Imagen actual: <?= basename($evento_editar['imagen_ruta']) ?></small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Sección de Galería -->
                            <?php if ($evento_editar): ?>
                                <div class="card mb-3 mt-4">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0"><i class="fas fa-images me-2"></i> Galería del Evento</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="galeria" class="form-label">Agregar imágenes a la galería (Múltiples)</label>
                                            <input type="file" class="form-control" id="galeria" name="galeria[]" multiple accept="image/*">
                                        </div>
                                        
                                        <?php if (count($galeria) > 0): ?>
                                            <div class="row">
                                                <?php foreach ($galeria as $imagen): ?>
                                                    <div class="col-md-3 gallery-item">
                                                        <img src="../<?= htmlspecialchars($imagen['imagen_ruta']) ?>" class="img-thumbnail gallery-thumbnail">
                                                        <div class="gallery-actions">
                                                            <a href="gestionar_eventos.php?eliminar_imagen=<?= $imagen['id'] ?>&evento_id=<?= $evento_editar['id'] ?>" 
                                                               class="btn btn-sm btn-danger"
                                                               onclick="return confirm('¿Eliminar esta imagen?')">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="alert alert-info">No hay imágenes en la galería para este evento.</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <div class="d-flex justify-content-between mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> <?= $evento_editar ? 'Actualizar' : 'Guardar' ?> Evento
                                </button>
                                
                                <?php if ($evento_editar): ?>
                                    <a href="gestionar_eventos.php" class="btn btn-secondary">
                                        <i class="fas fa-times me-1"></i> Cancelar
                                    </a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-list me-2"></i> Eventos Existentes</h5>
                    </div>
                    <div class="card-body">
                        <?php if (count($eventos) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Título</th>
                                            <th>Tipo</th>
                                            <th>Fecha</th>
                                            <th>Estado</th>
                                            <th>Autor</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($eventos as $evento): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($evento['titulo']) ?></td>
                                                <td><span class="badge bg-info"><?= $evento['tipo_evento'] ?></span></td>
                                                <td><?= $evento['fecha_evento'] ? date('d/m/Y', strtotime($evento['fecha_evento'])) : 'No definida' ?></td>
                                                <td>
                                                    <?php if ($evento['estado'] === 'Activo'): ?>
                                                        <span class="badge bg-success">Activo</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">Inactivo</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= htmlspecialchars($evento['autor']) ?></td>
                                                <td>
                                                    <a href="gestionar_eventos.php?editar=<?= $evento['id'] ?>" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i> Editar
                                                    </a>
                                                    <a href="gestionar_eventos.php?eliminar=<?= $evento['id'] ?>" 
                                                       class="btn btn-sm btn-danger" 
                                                       onclick="return confirm('¿Estás seguro de eliminar este evento?')">
                                                        <i class="fas fa-trash"></i> Eliminar
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">No hay eventos registrados.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle sidebar
        document.getElementById("menu-toggle").addEventListener("click", function(e) {
            e.preventDefault();
            document.getElementById("wrapper").classList.toggle("toggled");
        });
    </script>
</body>
</html>