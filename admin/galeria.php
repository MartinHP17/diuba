<?php
session_start();
require_once '../includes/conexion.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Configuración para subida de imágenes
$uploadDir = '../assets/imagenes/galeria/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
$maxSize = 5 * 1024 * 1024; // 5MB

// Procesar subida de imágenes
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['imagenes'])) {
    try {
        foreach ($_FILES['imagenes']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['imagenes']['error'][$key] === UPLOAD_ERR_OK) {
                $fileType = mime_content_type($tmp_name);
                if (in_array($fileType, $allowedTypes) && $_FILES['imagenes']['size'][$key] <= $maxSize) {
                    $extension = pathinfo($_FILES['imagenes']['name'][$key], PATHINFO_EXTENSION);
                    $nombreArchivo = uniqid('galeria_') . '.' . $extension;
                    $rutaDestino = $uploadDir . $nombreArchivo;
                    
                    if (move_uploaded_file($tmp_name, $rutaDestino)) {
                        $titulo = $_POST['titulos'][$key] ?? 'Imagen de galería';
                        $descripcion = $_POST['descripciones'][$key] ?? '';
                        
                        $stmt = $pdo->prepare("INSERT INTO galeria_fotos (imagen_ruta, titulo, descripcion, estado) VALUES (?, ?, ?, ?)");
                        $stmt->execute([
                            'assets/imagenes/galeria/' . $nombreArchivo,
                            htmlspecialchars($titulo),
                            htmlspecialchars($descripcion),
                            'Activo'
                        ]);
                    }
                }
            }
        }
        $_SESSION['success'] = "Imágenes subidas correctamente";
        header("Location: galeria.php");
        exit();
    } catch (PDOException $e) {
        $error = "Error al subir imágenes: " . $e->getMessage();
    }
}


// Procesar eliminación de imagen
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    
    try {
        $stmt = $pdo->prepare("SELECT imagen_ruta FROM galeria_fotos WHERE id = ?");
        $stmt->execute([$id]);
        $imagen = $stmt->fetch();
        
        if ($imagen && file_exists('../' . $imagen['imagen_ruta'])) {
            unlink('../' . $imagen['imagen_ruta']);
        }
        
        $stmt = $pdo->prepare("DELETE FROM galeria_fotos WHERE id = ?");
        $stmt->execute([$id]);
        
        $_SESSION['success'] = "Imagen eliminada correctamente";
        header("Location: galeria.php");
        exit();
    } catch (PDOException $e) {
        $error = "Error al eliminar la imagen: " . $e->getMessage();
    }
}
// Procesar edición de imagen
if (isset($_GET['editar'])) {
    $id = $_GET['editar'];
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM galeria_fotos WHERE id = ?");
        $stmt->execute([$id]);
        $imagen = $stmt->fetch();
        
        if ($imagen) {
            // Mostrar formulario de edición
            $editar_imagen = $imagen;
        }
    } catch (PDOException $e) {
        $error = "Error al cargar la imagen para editar: " . $e->getMessage();
    }
}

// Procesar actualización de imagen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar_imagen'])) {
    try {
        $id = $_POST['id'];
        $titulo = $_POST['titulo'];
        $descripcion = $_POST['descripcion'];
        
        // Obtener la imagen actual primero
        $stmt = $pdo->prepare("SELECT imagen_ruta FROM galeria_fotos WHERE id = ?");
        $stmt->execute([$id]);
        $imagen_actual = $stmt->fetch();
        
        $nueva_ruta = $imagen_actual['imagen_ruta'];
        
        // Procesar nueva imagen si se subió
        if (!empty($_FILES['nueva_imagen']['tmp_name'])) {
            $fileType = mime_content_type($_FILES['nueva_imagen']['tmp_name']);
            if (in_array($fileType, $allowedTypes) && $_FILES['nueva_imagen']['size'] <= $maxSize) {
                // Eliminar la imagen anterior
                if (file_exists('../' . $imagen_actual['imagen_ruta'])) {
                    unlink('../' . $imagen_actual['imagen_ruta']);
                }
                
                // Subir la nueva imagen
                $extension = pathinfo($_FILES['nueva_imagen']['name'], PATHINFO_EXTENSION);
                $nombreArchivo = uniqid('galeria_') . '.' . $extension;
                $rutaDestino = $uploadDir . $nombreArchivo;
                
                if (move_uploaded_file($_FILES['nueva_imagen']['tmp_name'], $rutaDestino)) {
                    $nueva_ruta = 'assets/imagenes/galeria/' . $nombreArchivo;
                }
            }
        }
        
        // Actualizar en la base de datos
        $stmt = $pdo->prepare("UPDATE galeria_fotos SET imagen_ruta = ?, titulo = ?, descripcion = ? WHERE id = ?");
        $stmt->execute([
            $nueva_ruta,
            htmlspecialchars($titulo),
            htmlspecialchars($descripcion),
            $id
        ]);
        
        $_SESSION['success'] = "Imagen actualizada correctamente";
        header("Location: galeria.php");
        exit();
    } catch (PDOException $e) {
        $error = "Error al actualizar la imagen: " . $e->getMessage();
    }
}
// Obtener imágenes de la galería
try {
    $stmt = $pdo->query("SELECT * FROM galeria_fotos ORDER BY fecha_creacion DESC");
    $imagenes = $stmt->fetchAll();
} catch (PDOException $e) {
    $imagenes = [];
    $error = "Error al cargar la galería: " . $e->getMessage();
}

$page_title = 'Galería de Imágenes - Dirección de Investigación UBA';
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
    <style>
        .gallery-img {
            height: 200px;
            width: 100%;
            object-fit: cover;
            cursor: pointer;
            transition: transform 0.3s;
        }
        .gallery-img:hover {
            transform: scale(1.03);
        }
        .image-card {
            position: relative;
            margin-bottom: 20px;
        }
        .image-actions {
            position: absolute;
            top: 10px;
            right: 10px;
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
                <a href="gestionar_eventos.php" class="list-group-item list-group-item-action bg-transparent text-white">
                    <i class="fas fa-calendar-alt me-2"></i>Gestionar Eventos
                </a>
                <a href="galeria.php" class="list-group-item list-group-item-action bg-transparent text-white active">
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
        <!--navbar-->
        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-white py-4 px-4">
                <div class="d-flex align-items-center">
                    <i class="fas fa-align-left primary-text fs-4 me-3" id="menu-toggle"></i>
                    <h2 class="fs-2 m-0">Galería</h2>
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
            <div class="container-fluid px-4">
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show mt-3">
                        <?= $_SESSION['success'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show mt-3">
                        <?= $error ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <div class="card mb-4 mt-3">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-upload me-2"></i> Subir Nuevas Imágenes</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data" id="uploadForm">
                            <div class="mb-3">
                                <label for="imagenes" class="form-label">Seleccionar imágenes (Múltiples)</label>
                                <input type="file" class="form-control" id="imagenes" name="imagenes[]" multiple accept="image/*" required>
                            </div>
                            
                            <div id="previewContainer" class="row mb-3 d-none">
                                <h6>Vista previa de las imágenes:</h6>
                                <!-- Las miniaturas y campos de texto se agregarán aquí dinámicamente -->
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Subir Imágenes
                            </button>
                        </form>
                    </div>
                </div>
            
                
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-images me-2"></i> Galería de Imágenes</h5>
                    </div>
                    <div class="card-body">
                        <?php if (count($imagenes) > 0): ?>
                            <div class="row">
                                <?php foreach ($imagenes as $imagen): ?>
                                    <div class="col-md-4 col-lg-3 mb-4">
                                        <div class="card image-card h-100">
                                            <img src="../<?= htmlspecialchars($imagen['imagen_ruta']) ?>" 
                                                 class="card-img-top gallery-img" 
                                                 alt="<?= htmlspecialchars($imagen['titulo']) ?>">
                                            <div class="card-body">
                                                <h6 class="card-title"><?= htmlspecialchars($imagen['titulo']) ?></h6>
                                                <p class="card-text small text-muted"><?= htmlspecialchars($imagen['descripcion']) ?></p>
                                            </div>   
                                            <div class="image-actions">
                                                <button class="btn btn-sm btn-primary me-1" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editarModal"
                                                        data-id="<?= $imagen['id'] ?>"
                                                        data-titulo="<?= htmlspecialchars($imagen['titulo']) ?>"
                                                        data-descripcion="<?= htmlspecialchars($imagen['descripcion']) ?>"
                                                        data-imagen="<?= htmlspecialchars($imagen['imagen_ruta']) ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <a href="galeria.php?eliminar=<?= $imagen['id'] ?>" 
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirm('¿Estás seguro de eliminar esta imagen?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info text-center py-4">
                                <i class="fas fa-image fa-3x mb-3"></i>
                                <p>No hay imágenes en la galería todavía.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal de edición -->
        <div class="modal fade" id="editarModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Imagen</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="galeria.php" enctype="multipart/form-data">
                        <div class="modal-body">
                            <input type="hidden" name="id" id="editar_id">
                            
                            <div class="mb-3 text-center">
                                <img id="editar_imagen_preview" 
                                    class="img-fluid mb-3 rounded" 
                                    style="max-height: 200px; object-fit: contain;">
                            </div>
                            
                            <div class="mb-3">
                                <label for="editar_imagen" class="form-label">Cambiar imagen (opcional)</label>
                                <input type="file" class="form-control" id="editar_imagen" name="nueva_imagen" accept="image/*">
                                <div class="form-text">Formatos: JPG, PNG, GIF. Máx. 5MB</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="editar_titulo" class="form-label">Título</label>
                                <input type="text" class="form-control" id="editar_titulo" name="titulo" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="editar_descripcion" class="form-label">Descripción</label>
                                <textarea class="form-control" id="editar_descripcion" name="descripcion" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" name="actualizar_imagen" class="btn btn-primary">Guardar Cambios</button>
                        </div>
                    </form>
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
    document.getElementById('imagenes').addEventListener('change', function(e) {
        const previewContainer = document.getElementById('previewContainer');
        previewContainer.innerHTML = '<h6 class="mb-3">Vista previa de las imágenes:</h6>';
        previewContainer.classList.remove('d-none');
        
        const files = e.target.files;
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            if (file.type.match('image.*')) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const col = document.createElement('div');
                    col.className = 'col-12 col-md-6 col-lg-4 mb-4';
                    
                    const card = document.createElement('div');
                    card.className = 'card h-100';
                    
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'card-img-top';
                    img.style.height = '150px';
                    img.style.objectFit = 'cover';
                    
                    const cardBody = document.createElement('div');
                    cardBody.className = 'card-body';
                    
                    // Campo para título
                    const titleGroup = document.createElement('div');
                    titleGroup.className = 'mb-3';
                    
                    const titleLabel = document.createElement('label');
                    titleLabel.className = 'form-label small';
                    titleLabel.textContent = 'Título';
                    titleLabel.htmlFor = `titulo-${i}`;
                    
                    const titleInput = document.createElement('input');
                    titleInput.type = 'text';
                    titleInput.className = 'form-control form-control-sm';
                    titleInput.id = `titulo-${i}`;
                    titleInput.name = 'titulos[]';
                    titleInput.placeholder = 'Ingrese un título';
                    titleInput.required = true;
                    
                    titleGroup.appendChild(titleLabel);
                    titleGroup.appendChild(titleInput);
                    
                    // Campo para descripción
                    const descGroup = document.createElement('div');
                    descGroup.className = 'mb-2';
                    
                    const descLabel = document.createElement('label');
                    descLabel.className = 'form-label small';
                    descLabel.textContent = 'Descripción';
                    descLabel.htmlFor = `descripcion-${i}`;
                    
                    const descInput = document.createElement('textarea');
                    descInput.className = 'form-control form-control-sm';
                    descInput.id = `descripcion-${i}`;
                    descInput.name = 'descripciones[]';
                    descInput.placeholder = 'Ingrese una descripción';
                    descInput.rows = 2;
                    
                    descGroup.appendChild(descLabel);
                    descGroup.appendChild(descInput);
                    
                    // Info del archivo
                    const fileInfo = document.createElement('div');
                    fileInfo.className = 'small text-muted mt-2';
                    fileInfo.innerHTML = `
                        <div>Nombre: <span class="text-truncate d-inline-block" style="max-width: 150px;">${file.name}</span></div>
                        <div>Tamaño: ${(file.size / 1024).toFixed(2)} KB</div>
                    `;
                    
                    cardBody.appendChild(titleGroup);
                    cardBody.appendChild(descGroup);
                    cardBody.appendChild(fileInfo);
                    
                    card.appendChild(img);
                    card.appendChild(cardBody);
                    col.appendChild(card);
                    previewContainer.appendChild(col);
                }
                
                reader.readAsDataURL(file);
            }
        }
    });
    document.addEventListener('DOMContentLoaded', function() {
        const editarModal = document.getElementById('editarModal');
        if (editarModal) {
            editarModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                
                // Extraer datos de los atributos data-*
                const id = button.getAttribute('data-id');
                const titulo = button.getAttribute('data-titulo');
                const descripcion = button.getAttribute('data-descripcion');
                const imagen = button.getAttribute('data-imagen');
                
                // Actualizar el contenido del modal
                document.getElementById('editar_id').value = id;
                document.getElementById('editar_titulo').value = titulo;
                document.getElementById('editar_descripcion').value = descripcion;
                document.getElementById('editar_imagen_preview').src = '../' + imagen;
            });
        }
    });
    </script>
</body>
</html>