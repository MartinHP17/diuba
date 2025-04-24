<?php
session_start();
require_once '../includes/conexion.php';

// Verificar autenticación
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$page_title = 'Panel de Administración - Dirección de Investigación UBA';

// Obtener estadísticas mejoradas
try {
    // Estadísticas básicas
    $total_eventos = $pdo->query("SELECT COUNT(*) FROM eventos")->fetchColumn();
    $eventos_proximos = $pdo->query("SELECT COUNT(*) FROM eventos WHERE fecha_evento >= CURDATE()")->fetchColumn();
    $total_usuarios = $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
    $total_fotos = $pdo->query("SELECT COUNT(*) FROM galeria_fotos")->fetchColumn();
    $total_videos = $pdo->query("SELECT COUNT(*) FROM videos_youtube")->fetchColumn();
    
    // Eventos por tipo (para el gráfico)
    $eventos_por_tipo = $pdo->query("
        SELECT tipo_evento, COUNT(*) as cantidad 
        FROM eventos 
        GROUP BY tipo_evento
        ORDER BY cantidad DESC
    ")->fetchAll(PDO::FETCH_KEY_PAIR);
    
    // Eventos próximos (para la tabla)
    $eventos_proximos_detalle = $pdo->query("
        SELECT id, titulo, fecha_evento, ubicacion 
        FROM eventos 
        WHERE fecha_evento >= CURDATE()
        ORDER BY fecha_evento ASC
        LIMIT 5
    ")->fetchAll();
    
    // Actividad reciente mejorada
    $actividad_reciente = $pdo->query("
        (SELECT 'evento' as tipo, id, titulo, fecha_creacion, usuario_id FROM eventos ORDER BY fecha_creacion DESC LIMIT 3)
        UNION
        (SELECT 'foto' as tipo, id, titulo, fecha_creacion, NULL as usuario_id FROM galeria_fotos ORDER BY fecha_creacion DESC LIMIT 3)
        UNION
        (SELECT 'video' as tipo, id, titulo, fecha_creacion, NULL as usuario_id FROM videos_youtube ORDER BY fecha_creacion DESC LIMIT 3)
        ORDER BY fecha_creacion DESC
        LIMIT 5
    ")->fetchAll();
    
} catch (PDOException $e) {
    // Manejo de errores
    $error_stats = "Error al cargar estadísticas: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="../assets/css/admin.css" rel="stylesheet">
    <style>
        /* Colores para fondos de iconos */
        .bg-light-blue { background-color: #e3f2fd; }
        .bg-light-green { background-color: #e8f5e9; }
        .bg-light-cyan { background-color: #e0f7fa; }
        .bg-light-red { background-color: #ffebee; }

        /* Mejoras para tarjetas */
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
            transform: translateY(-2px);
        }

        /* Estilos para la actividad reciente */
        .list-group-item {
            transition: all 0.2s ease;
        }

        .list-group-item:hover {
            background-color: #f8f9fa;
        }

        /* Ajustes para el gráfico */
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
    </style>
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
                <a href="admin.php" class="list-group-item list-group-item-action bg-transparent text-white active">
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
                <a href="configuracion.php" class="list-group-item list-group-item-action bg-transparent text-white">
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

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-white py-4 px-4">
                <div class="d-flex align-items-center">
                    <i class="fas fa-align-left primary-text fs-4 me-3" id="menu-toggle"></i>
                    <h2 class="fs-2 m-0">Dashboard</h2>
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

            <div class="container-fluid px-4">
                <?php if (isset($error_stats)): ?>
                    <div class="alert alert-danger alert-dismissible fade show mt-3">
                        <?= $error_stats ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="row g-3 my-2">
                    <!-- Tarjeta de Eventos -->
                    <div class="col-md-3">
                        <div class="p-3 bg-white shadow-sm d-flex justify-content-around align-items-center rounded h-100">
                            <div>
                                <h3 class="fs-2"><?= $total_eventos ?></h3>
                                <p class="fs-5">Eventos</p>
                                <small class="text-muted"><?= $eventos_proximos ?> próximos</small>
                            </div>
                            <i class="fas fa-calendar-check fs-1 text-primary bg-light-blue p-3 rounded-circle"></i>
                        </div>
                    </div>

                    <!-- Tarjeta de Usuarios -->
                    <div class="col-md-3">
                        <div class="p-3 bg-white shadow-sm d-flex justify-content-around align-items-center rounded h-100">
                            <div>
                                <h3 class="fs-2"><?= $total_usuarios ?></h3>
                                <p class="fs-5">Usuarios</p>
                                <small class="text-muted"><?= date('M Y') ?></small>
                            </div>
                            <i class="fas fa-users fs-1 text-success bg-light-green p-3 rounded-circle"></i>
                        </div>
                    </div>

                    <!-- Tarjeta de Galería -->
                    <div class="col-md-3">
                        <div class="p-3 bg-white shadow-sm d-flex justify-content-around align-items-center rounded h-100">
                            <div>
                                <h3 class="fs-2"><?= $total_fotos ?></h3>
                                <p class="fs-5">Fotos</p>
                                <small class="text-muted">En galería</small>
                            </div>
                            <i class="fas fa-images fs-1 text-info bg-light-cyan p-3 rounded-circle"></i>
                        </div>
                    </div>

                    <!-- Tarjeta de Videos -->
                    <div class="col-md-3">
                        <div class="p-3 bg-white shadow-sm d-flex justify-content-around align-items-center rounded h-100">
                            <div>
                                <h3 class="fs-2"><?= $total_videos ?></h3>
                                <p class="fs-5">Videos</p>
                                <small class="text-muted">Institucionales</small>
                            </div>
                            <i class="fas fa-video fs-1 text-danger bg-light-red p-3 rounded-circle"></i>
                        </div>
                    </div>
                </div>

                <div class="row my-5">
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fas fa-bolt me-2"></i> Acciones Rápidas</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <a href="gestionar_eventos.php?accion=nuevo" class="btn btn-primary w-100 p-3 d-flex flex-column align-items-center">
                                            <i class="fas fa-calendar-plus fs-4 mb-2"></i>
                                            <span>Nuevo Evento</span>
                                        </a>
                                    </div>
                                    <div class="col-md-6">
                                        <a href="galeria.php" class="btn btn-success w-100 p-3 d-flex flex-column align-items-center">
                                            <i class="fas fa-upload fs-4 mb-2"></i>
                                            <span>Subir Fotos</span>
                                        </a>
                                    </div>
                                    <div class="col-md-6">
                                        <a href="videos.php" class="btn btn-info text-white w-100 p-3 d-flex flex-column align-items-center">
                                            <i class="fab fa-youtube fs-4 mb-2"></i>
                                            <span>Agregar Video</span>
                                        </a>
                                    </div>
                                    <div class="col-md-6">
                                        <a href="configuracion.php" class="btn btn-secondary w-100 p-3 d-flex flex-column align-items-center">
                                            <i class="fas fa-user-cog fs-4 mb-2"></i>
                                            <span>Mi Cuenta</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="fas fa-history me-2"></i> Actividad Reciente</h5>
                            </div>
                            <div class="card-body p-0">
                                <?php if (count($actividad_reciente) > 0): ?>
                                    <ul class="list-group list-group-flush">
                                        <?php foreach ($actividad_reciente as $actividad): ?>
                                            <li class="list-group-item">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <?php if ($actividad['tipo'] == 'evento'): ?>
                                                            <span class="badge bg-primary rounded-circle p-2">
                                                                <i class="fas fa-calendar-alt"></i>
                                                            </span>
                                                        <?php elseif ($actividad['tipo'] == 'foto'): ?>
                                                            <span class="badge bg-info rounded-circle p-2">
                                                                <i class="fas fa-camera"></i>
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="badge bg-danger rounded-circle p-2">
                                                                <i class="fas fa-video"></i>
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1"><?= htmlspecialchars($actividad['titulo']) ?></h6>
                                                        <small class="text-muted">
                                                            <?= date('d M Y H:i', strtotime($actividad['fecha_creacion'])) ?>
                                                            <?php if ($actividad['tipo'] == 'evento'): ?>
                                                                • Evento
                                                            <?php elseif ($actividad['tipo'] == 'foto'): ?>
                                                                • Foto
                                                            <?php else: ?>
                                                                • Video
                                                            <?php endif; ?>
                                                        </small>
                                                    </div>
                                                    <div>
                                                        <a href="<?= $actividad['tipo'] == 'evento' ? 'gestionar_eventos.php?accion=editar&id='.$actividad['id'] : 
                                                                  ($actividad['tipo'] == 'foto' ? 'galeria.php' : 'videos.php') ?>" 
                                                           class="btn btn-sm btn-outline-secondary">
                                                            Ver
                                                        </a>
                                                    </div>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-inbox fs-1 text-muted mb-3"></i>
                                        <p class="text-muted">No hay actividad reciente</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0"><i class="fas fa-calendar-day me-2"></i> Próximos Eventos</h5>
                            </div>
                            <div class="card-body p-0">
                                <?php if (count($eventos_proximos_detalle) > 0): ?>
                                    <ul class="list-group list-group-flush">
                                        <?php foreach ($eventos_proximos_detalle as $evento): ?>
                                            <li class="list-group-item">
                                                <div class="d-flex w-100 justify-content-between">
                                                    <h6 class="mb-1"><?= htmlspecialchars($evento['titulo']) ?></h6>
                                                    <small><?= date('d/m/Y', strtotime($evento['fecha_evento'])) ?></small>
                                                </div>
                                                <small class="text-muted"><?= htmlspecialchars($evento['ubicacion']) ?></small>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-calendar-times fs-1 text-muted mb-3"></i>
                                        <p class="text-muted">No hay eventos próximos</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i> Distribución de Eventos</h5>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="eventosChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Toggle sidebar
        document.getElementById("menu-toggle").addEventListener("click", function(e) {
            e.preventDefault();
            document.getElementById("wrapper").classList.toggle("toggled");
        });

        // Gráfico con datos reales
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('eventosChart').getContext('2d');
            
            // Preparar datos para el gráfico
            const tiposEventos = <?= json_encode(array_keys($eventos_por_tipo)) ?>;
            const cantidades = <?= json_encode(array_values($eventos_por_tipo)) ?>;
            
            const backgroundColors = [
                'rgba(54, 162, 235, 0.7)',
                'rgba(255, 99, 132, 0.7)',
                'rgba(255, 206, 86, 0.7)',
                'rgba(75, 192, 192, 0.7)',
                'rgba(153, 102, 255, 0.7)',
                'rgba(255, 159, 64, 0.7)'
            ];
            
            const borderColors = [
                'rgba(54, 162, 235, 1)',
                'rgba(255, 99, 132, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ];
            
            const eventosChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: tiposEventos,
                    datasets: [{
                        data: cantidades,
                        backgroundColor: backgroundColors.slice(0, tiposEventos.length),
                        borderColor: borderColors.slice(0, tiposEventos.length),
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'right',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `${context.label}: ${context.raw} eventos`;
                                }
                            }
                        }
                    },
                    cutout: '70%',
                }
            });
        });
    </script>
</body>
</html>