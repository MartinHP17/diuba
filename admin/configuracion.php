<?php
session_start();
require_once '../includes/conexion.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}


// Procesar actualización de perfil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar_perfil'])) {
    $nombre = htmlspecialchars($_POST['nombre']);
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];
    
    try {
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE usuarios SET nombre = ?, email = ?, password = ? WHERE id = ?");
            $stmt->execute([$nombre, $email, $hashed_password, $_SESSION['user_id']]);
        } else {
            $stmt = $pdo->prepare("UPDATE usuarios SET nombre = ?, email = ? WHERE id = ?");
            $stmt->execute([$nombre, $email, $_SESSION['user_id']]);
        }
        
        $_SESSION['user_name'] = $nombre;
        $_SESSION['user_email'] = $email;
        $_SESSION['success'] = "Perfil actualizado correctamente";
    } catch (PDOException $e) {
        $error = "Error al actualizar el perfil: " . $e->getMessage();
    }
}

// Procesar actualización de configuración
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar_config'])) {
    $contacto_email = filter_var($_POST['contacto_email'], FILTER_SANITIZE_EMAIL);
    
    try {
        $stmt = $pdo->prepare("INSERT INTO sistema_config (clave, valor) VALUES ('contacto_email', ?) 
                              ON DUPLICATE KEY UPDATE valor = ?");
        $stmt->execute([$contacto_email, $contacto_email]);
        
        $_SESSION['success'] = "Configuración actualizada correctamente";
    } catch (PDOException $e) {
        $error = "Error al actualizar la configuración: " . $e->getMessage();
    }
}

// Obtener datos del usuario
try {
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $usuario = $stmt->fetch();
} catch (PDOException $e) {
    die("Error al obtener datos del usuario: " . $e->getMessage());
}

// Obtener configuración del sistema
try {
    $stmt = $pdo->query("SELECT * FROM sistema_config");
    $configs = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
} catch (PDOException $e) {
    $configs = [];
}

$page_title = 'Configuración - Dirección de Investigación UBA';
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
                <a href="videos.php" class="list-group-item list-group-item-action bg-transparent text-white">
                    <i class="fas fa-video me-2"></i>Gestionar Videos
                </a>
                <a href="configuracion.php" class="list-group-item list-group-item-action bg-transparent text-white active">
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
                    <h2 class="fs-2 m-0">Configuración</h2>
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
                
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card mb-4 shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h5><i class="fas fa-user-cog me-2"></i> Configuración de Perfil</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <div class="mb-3">
                                        <label for="nombre" class="form-label">Nombre</label>
                                        <input type="text" class="form-control" id="nombre" name="nombre" 
                                               value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Correo Electrónico</label>
                                        <input type="email" class="form-control" id="email" name="email" 
                                               value="<?= htmlspecialchars($usuario['email']) ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Nueva Contraseña </label>
                                        <input type="password" class="form-control" id="password" name="password">
                                    </div>
                                    
                                    <button type="submit" name="actualizar_perfil" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> Actualizar Perfil
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card mb-4 shadow-sm">
                            <div class="card-header bg-success text-white">
                                <h5><i class="fas fa-cog me-2"></i> Configuración del Sistema</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <div class="mb-3">
                                        <label for="contacto_email" class="form-label">Correo para Contacto</label>
                                        <input type="email" class="form-control" id="contacto_email" name="contacto_email" 
                                               value="<?= htmlspecialchars($configs['contacto_email'] ?? '') ?>" required>
                                        <small class="text-muted">Este correo recibirá los mensajes del formulario de contacto</small>
                                    </div>
                                    
                                    <button type="submit" name="actualizar_config" class="btn btn-success">
                                        <i class="fas fa-save me-1"></i> Guardar Configuración
                                    </button>
                                </form>
                            </div>
                        </div>
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