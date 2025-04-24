<!-- Header -->
<header class="main-header">
  <nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
      <a class="navbar-brand" href="index.php">
        <img src="<?php echo $base_url; ?>assets/imagenes/logo-uba.png" alt="UBA Logo">
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
        <div class="navbar-toggler-icon">
          <span></span>
          <span></span>
          <span></span>
          <span></span>
        </div>
      </button>
      <div class="collapse navbar-collapse" id="navbarMain">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link <?php echo ($current_page == 'inicio') ? 'active' : ''; ?>" href="index.php">Inicio</a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle <?php echo (in_array($current_page, ['nosotros', 'historia', 'equipo'])) ? 'active' : ''; ?>" href="#" id="navbarDropdownNosotros" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Nosotros
            </a>
            <ul class="dropdown-menu" aria-labelledby="navbarDropdownNosotros">
              <li><a class="dropdown-item <?php echo ($current_page == 'nosotros') ? 'active' : ''; ?>" href="nosotros.php">Quiénes Somos</a></li>
              <li><a class="dropdown-item <?php echo ($current_page == 'historia') ? 'active' : ''; ?>" href="historia.php">Historia</a></li>
              <li><a class="dropdown-item <?php echo ($current_page == 'equipo') ? 'active' : ''; ?>" href="equipo.php">Nuestro Equipo</a></li>
            </ul>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle <?php echo (in_array($current_page, ['investigacion', 'lineas', 'proyectos'])) ? 'active' : ''; ?>" href="#" id="navbarDropdownInvestigacion" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Investigación
            </a>
            <ul class="dropdown-menu" aria-labelledby="navbarDropdownInvestigacion">
              <li><a class="dropdown-item <?php echo ($current_page == 'lineas') ? 'active' : ''; ?>" href="lineas.php">Líneas de Investigación</a></li>
              <li><a class="dropdown-item <?php echo ($current_page == 'proyectos') ? 'active' : ''; ?>" href="proyectos.php">Proyectos</a></li>
              <li><a class="dropdown-item <?php echo ($current_page == 'grupos') ? 'active' : ''; ?>" href="grupos.php">Grupos de Investigación</a></li>
            </ul>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle <?php echo (in_array($current_page, ['programas', 'postgrado', 'formacion'])) ? 'active' : ''; ?>" href="#" id="navbarDropdownProgramas" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Programas
            </a>
            <ul class="dropdown-menu" aria-labelledby="navbarDropdownProgramas">
              <li><a class="dropdown-item <?php echo ($current_page == 'programas') ? 'active' : ''; ?>" href="programas.php">Todos los Programas</a></li>
              <li><a class="dropdown-item <?php echo ($current_page == 'postgrado') ? 'active' : ''; ?>" href="postgrado.php">Postgrado</a></li>
              <li><a class="dropdown-item <?php echo ($current_page == 'formacion') ? 'active' : ''; ?>" href="formacion.php">Formación Continua</a></li>
            </ul>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo ($current_page == 'publicaciones') ? 'active' : ''; ?>" href="publicaciones.php">Publicaciones</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo ($current_page == 'eventos') ? 'active' : ''; ?>" href="eventos.php">Eventos</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo ($current_page == 'contacto') ? 'active' : ''; ?>" href="contacto.php">Contacto</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</header>
