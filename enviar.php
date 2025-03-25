<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger los datos del formulario
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $mensaje = $_POST['mensaje'];
    
    // REEMPLAZA ESTA LÍNEA CON TU CORREO:
    $destinatario = "martinhermoso14@gmail.com";  // ← Aquí va tu dirección
    
    // Asunto del correo
    $asunto = "Nuevo mensaje de contacto desde tu sitio web";
    
    // Cuerpo del mensaje
    $cuerpo = "Nombre: $nombre\n";
    $cuerpo .= "Email: $email\n\n";
    $cuerpo .= "Mensaje:\n$mensaje";
    
    // Cabeceras del correo
    $cabeceras = "From: $email";
    
    // Enviar el correo
    if (mail($destinatario, $asunto, $cuerpo, $cabeceras)) {
        header('Location: gracias.html');
    } else {
        header('Location: error.html');
    }
}
?>