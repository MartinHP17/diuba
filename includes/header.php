<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $page_title; ?></title>
  
  <!-- Favicon -->
  <link rel="shortcut icon" href="<?php echo $base_url; ?>assets/imagenes/favicon.ico" type="image/x-icon">
  
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  
  <!-- Main CSS -->
  <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/main.css">
  
  <!-- Additional CSS -->
  <?php if (isset($additional_css)): ?>
    <?php foreach ($additional_css as $css): ?>
    <link rel="stylesheet" href="<?php echo $base_url . $css; ?>">
    <?php endforeach; ?>
  <?php endif; ?>
</head>
<body>
