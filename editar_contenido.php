<?php
require 'admin.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contenido = $_POST['contenido'];
    $stmt = $pdo->prepare("UPDATE contenido SET texto = ? WHERE seccion = 'principal'");
    $stmt->execute([$contenido]);
}

$stmt = $pdo->query("SELECT texto FROM contenido WHERE seccion = 'principal'");
$contenido = $stmt->fetchColumn();
?>

<h2>Editar Contenido Principal</h2>
<form method="POST">
    <textarea name="contenido" rows="10" cols="60"><?= htmlspecialchars($contenido) ?></textarea>
    <button type="submit">Guardar</button>
</form>
