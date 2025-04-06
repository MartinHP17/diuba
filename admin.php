<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración</title>
</head>
<body>
    <h1>Bienvenido al Panel de Administración</h1>
    <nav>
        <a href="gestionar_eventos.php">Gestionar Eventos</a> |
        <a href="editar_contenido.php">Editar Contenido</a> |
        <a href="logout.php">Cerrar Sesión</a>
    </nav>
</body>
</html>
