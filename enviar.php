<?php
// Configuración para enviar el correo
$destinatario = "jose_leo2009@hotmail.com";
$asunto = "Mensaje desde el formulario de contacto de la Dirección de Investigación";

// Recoger los datos del formulario
$nombre = $_POST['nombre'];
$email = $_POST['email'];
$mensaje = $_POST['mensaje'];

// Construir el cuerpo del mensaje
$cuerpo = "Has recibido un nuevo mensaje desde el formulario de contacto:\n\n";
$cuerpo .= "Nombre: " . $nombre . "\n";
$cuerpo .= "Email: " . $email . "\n";
$cuerpo .= "Mensaje: \n" . $mensaje . "\n";

// Cabeceras del correo
$cabeceras = "From: xdarkvaderxx@gmail.com" . "\r\n" .
             "Reply-To: " . $email . "\r\n" .
             "X-Mailer: PHP/" . phpversion();

// Configuración adicional para servidores que requieren autenticación
ini_set("SMTP", "smtp.gmail.com");
ini_set("smtp_port", "587");
ini_set("sendmail_from", "xdarkvaderxx@gmail.com");

// Habilitar reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Intentar enviar el correo
try {
    $resultado = mail($destinatario, $asunto, $cuerpo, $cabeceras);
    
    if ($resultado) {
        // Redireccionar con mensaje de éxito
        header('Location: contacto.html?envio=exito');
    } else {
        // Obtener el último error
        $error = error_get_last();
        $errorMessage = $error ? $error['message'] : 'Error desconocido al enviar el correo';
        
        // Redireccionar con mensaje de error detallado
        header('Location: contacto.html?envio=error&detalle=' . urlencode($errorMessage));
    }
} catch (Exception $e) {
    // Redireccionar con mensaje de excepción
    header('Location: contacto.html?envio=error&detalle=' . urlencode($e->getMessage()));
}
exit;
?>