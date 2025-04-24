<?php
session_start();
require_once '../includes/conexion.php';

// Redirigir si ya está autenticado
if (isset($_SESSION['user_id'])) {
    header("Location: admin.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];

        // Validaciones básicas
        if (empty($email) || empty($password)) {
            throw new Exception("Por favor ingresa tu correo y contraseña.");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("El formato del correo electrónico no es válido.");
        }

        // Consulta preparada para mayor seguridad
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? LIMIT 1");
        if (!$stmt->execute([$email])) {
            throw new Exception("Error en la consulta a la base de datos.");
        }

        $user = $stmt->fetch();

        if (!$user) {
            throw new Exception("Correo o contraseña incorrectos.");
        }

        if (!password_verify($password, $user['password'])) {
            throw new Exception("Correo o contraseña incorrectos.");
        }

        // Autenticación exitosa
        session_regenerate_id(true);
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['nombre'];
        $_SESSION['last_login'] = time();
        
        header("Location: admin.php");
        exit();

    } catch (Exception $e) {
        $error = $e->getMessage();
        sleep(1); // Pequeño retraso para prevenir fuerza bruta
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Dirección de Investigación UBA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="../assets/css/admin.css" rel="stylesheet">
</head>
<body class="login-bg">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-5">
                <div class="card shadow-lg border-0 rounded-lg">
                    <div class="card-header bg-primary text-white text-center py-4">
                        <img src="../assets/imagenes/diuba-logo.png" alt="Logo" width="100" height="100" class="mb-3">
                        <h3 class="mb-0">Inicio Sesión</h3>
                        <p class="mb-0">Dirección de Investigación</p>
                    </div>
                    <div class="card-body p-4">
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" novalidate>
                            <div class="form-floating mb-3">
                                <input type="email" class="form-control" id="email" name="email" placeholder="nombre@ejemplo.com" required
                                       value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                                <label for="email"><i class="fas fa-envelope me-2"></i>Correo Electrónico</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña" required>
                                <label for="password"><i class="fas fa-lock me-2"></i>Contraseña</label>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                <a href="../index.php" class="text-decoration-none">
                                     <i class="fas fa-arrow-left me-1"></i> Volver al sitio</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
