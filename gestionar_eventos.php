<?php
require 'admin.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $stmt = $pdo->prepare("INSERT INTO eventos (titulo, descripcion) VALUES (?, ?)");
    $stmt->execute([$titulo, $descripcion]);
}

$eventos = $pdo->query("SELECT * FROM eventos")->fetchAll();
?>

<h2>Eventos</h2>
<form method="POST">
    <input type="text" name="titulo" placeholder="Título del evento" required>
    <textarea name="descripcion" placeholder="Descripción" required></textarea>
    <button type="submit">Agregar Evento</button>
</form>

<ul>
    <?php foreach ($eventos as $evento): ?>
        <li><strong><?= $evento['titulo'] ?></strong>: <?= $evento['descripcion'] ?></li>
    <?php endforeach; ?>
</ul>
