<?php
// Verificar si las clases ya están cargadas
if (!class_exists('PHPMailer\PHPMailer\Exception')) {
    require 'PHPMailer/src/Exception.php';
    require 'PHPMailer/src/PHPMailer.php';
    require 'PHPMailer/src/SMTP.php';
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Recoger datos del formulario
$nombre = $_POST['nombre'] ?? '';
$email = $_POST['email'] ?? '';
$mensaje = $_POST['mensaje'] ?? '';

// Validaciones básicas
if (empty($nombre) || empty($email) || empty($mensaje)) {
    header('Location: contacto.php?envio=error&detalle=' . urlencode('Todos los campos son requeridos'));
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: contacto.php?envio=error&detalle=' . urlencode('Correo electrónico no válido'));
    exit;
}

// Configurar PHPMailer
$mail = new PHPMailer(true);

try {
    // Configuración del servidor SMTP (para Gmail)
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'rau30ly@gmail.com'; // Reemplaza con tu correo Gmail
    $mail->Password = 'zeis socm nuxd opub'; // Reemplaza con tu contraseña de aplicación
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Remitente y destinatario
    $mail->setFrom($email, $nombre); // Usar el correo y nombre del formulario
    $mail->addAddress('correo_destino@dominio.com', 'Nombre Destinatario'); // Correo destino desde configuración
    
    // Obtener el correo de destino desde la base de datos
    require 'includes/conexion.php';
    $stmt = $pdo->query("SELECT valor FROM sistema_config WHERE clave = 'contacto_email'");
    $contacto_email = $stmt->fetchColumn();
    
    if ($contacto_email) {
        $mail->clearAddresses();
        $mail->addAddress($contacto_email, 'Dirección de Investigación');
    }

    // Contenido del mensaje
    $mail->isHTML(false);
    $mail->Subject = 'Mensaje de contacto desde la web';
    $mail->Body = "Nuevo mensaje de contacto:\n\n"
                . "Nombre: $nombre\n"
                . "Email: $email\n"
                . "Mensaje: $mensaje\n";

    $mail->send();
    header('Location: contacto.php?envio=exito');
} catch (Exception $e) {
    header('Location: contacto.php?envio=error&detalle=' . urlencode('Error al enviar: ' . $e->getMessage()));
}
exit;
?>