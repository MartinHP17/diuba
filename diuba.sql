-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 24-04-2025 a las 20:04:43
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `diuba`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `eventos`
--

CREATE TABLE `eventos` (
  `id` int(11) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `descripcion` text NOT NULL,
  `imagen_ruta` varchar(255) DEFAULT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_evento` date DEFAULT NULL,
  `ubicacion` varchar(100) DEFAULT NULL,
  `tipo_evento` enum('Congreso','Jornada','Taller','Conferencia','Otro') DEFAULT 'Otro',
  `estado` enum('Activo','Inactivo') DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `eventos`
--

INSERT INTO `eventos` (`id`, `titulo`, `descripcion`, `imagen_ruta`, `usuario_id`, `fecha_creacion`, `fecha_evento`, `ubicacion`, `tipo_evento`, `estado`) VALUES
(6, 'Prueba', 'Tralaleiro Tralala', 'assets/imagenes/eventos/evento_680825b464c3d.jpeg', 1, '2025-04-22 23:26:44', '2025-06-08', 'En mi casa', 'Jornada', 'Activo'),
(7, 'Prueba', 'tt8t8t8t8', NULL, 1, '2025-04-22 23:27:34', '2025-09-07', 'La Delicias', 'Otro', 'Activo'),
(8, 'Prueba', 'chjoihdichihciohiochioc\r\nchuchoiuhcohcouhcouhouchuchuohcuiohcouhc\r\nchuichiouhcuiohcuihciuhuichuichuihciuhiuchiuchiuch\r\nchiucehhfcohuiochuchihcoihoischoshcos[pioc[sch[isc', 'assets/imagenes/eventos/evento_6808261c9f5c3.jpg', 1, '2025-04-22 23:28:28', '2025-05-07', 'La Delicias', 'Conferencia', 'Activo'),
(9, 'Prueba', 'hsdjhsdhksdfjhksdhf\r\nfisodjfisjdifjsd\r\nfiopsdfoijsdifoiwsjdiufhdsaopfos', 'assets/imagenes/eventos/evento_68083024180d0.jpg', 1, '2025-04-23 00:11:16', '2025-04-29', 'La Delicias', 'Jornada', 'Activo'),
(10, 'Evento de Investigacion ', 'hsifsoiuhusdhiufsdui\r\n0usdoifjiosdjoifdsoifjsijjifsdjfijsdpoifj\r\njfsdijfiposdjpiofjsdiojfoidjofjoisdjfiofdiosiodf\r\nhfisduhfuisduihf gsdifidsfidsgfi\r\nfhisuhfiushiofhishfiushuifhiusdf', 'assets/imagenes/eventos/evento_6808f40953745.png', 1, '2025-04-23 14:07:05', '2025-05-09', 'En cualquer lado', 'Taller', 'Activo'),
(11, 'Prueba', 'hcjhhcjhsdjkcs\r\nchsudhchsduicuchhscd\r\n', 'assets/imagenes/eventos/evento_68098ba2b3cb4.jpg', 1, '2025-04-24 00:53:54', '2025-05-02', 'En mi casa', 'Conferencia', 'Activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `galeria_fotos`
--

CREATE TABLE `galeria_fotos` (
  `id` int(11) NOT NULL,
  `imagen_ruta` varchar(255) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` enum('Activo','Inactivo') DEFAULT 'Activo',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `galeria_fotos`
--

INSERT INTO `galeria_fotos` (`id`, `imagen_ruta`, `titulo`, `descripcion`, `estado`, `fecha_creacion`) VALUES
(5, 'assets/imagenes/galeria/galeria_680a483c998f2.jpeg', 'Cualquier Cosa', 'fjjfoiwjfoiwofijf\r\nfihowofuhwoifohwfohwef\r\nfhiwuehouwehfouhopfouwhef\r\nfhwhfuwhfiuhweiufhiwuhfi', 'Activo', '2025-04-24 14:18:36'),
(6, 'assets/imagenes/galeria/galeria_680a485391e80.jpeg', 'Cualquier Cosa', 'bcsdbicbisdbcisbic\r\ncocoiscoijsdocijoidscjoijcoijsdc\r\ncioidscjoijsoicjoijcdsoijcoisjcodijoicdjsod', 'Activo', '2025-04-24 14:18:59'),
(7, 'assets/imagenes/galeria/galeria_680a520e28ffe.jpeg', 'Evento de Investigacion ', 'ihishdihsdiuf\r\nfjsodhfoshdfhsuidhf\r\nfhhfhsdifhsihfi', 'Activo', '2025-04-24 14:19:12');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sistema_config`
--

CREATE TABLE `sistema_config` (
  `id` int(11) NOT NULL,
  `clave` varchar(50) NOT NULL,
  `valor` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sistema_config`
--

INSERT INTO `sistema_config` (`id`, `clave`, `valor`) VALUES
(1, 'contacto_email', 'martinhermoso14@gmail.com');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `fecha_creacion`) VALUES
(1, 'Martin Hermoso', 'martin@diuba.com', '$2y$10$Ytw0vzGiAaI.ZzO.yYt4cOBorpotrWl1KQ.t9lyFmQBoq7HSafZRO', '2025-04-20 18:55:06');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `videos_youtube`
--

CREATE TABLE `videos_youtube` (
  `id` int(11) NOT NULL,
  `youtube_url` varchar(255) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `videos_youtube`
--

INSERT INTO `videos_youtube` (`id`, `youtube_url`, `titulo`, `descripcion`, `fecha_creacion`) VALUES
(1, 'https://youtu.be/6mvnmSUgmdc', 'Palabras de Bienvenida', '', '2025-04-24 16:34:49'),
(2, 'https://youtu.be/Bt3I90yy2V8', 'JORNADA DE INVESTIGACIÓN NACIONAL', '', '2025-04-24 16:35:39'),
(3, 'https://youtu.be/WN1928IAdvY', 'JORNADA DE INVESTIGACIÓN NACIONAL 2\r\n', '', '2025-04-24 16:36:27'),
(4, 'https://youtu.be/2sLTeCcqoJo', 'SALA ZOOM 1 14-11-2024\r\n', '', '2025-04-24 16:39:23'),
(5, 'https://youtu.be/F5Lm2fZdE4I', 'Evento UBA 2024 SALA ZOOM 2 13-11-2024', '', '2025-04-24 17:06:10'),
(8, 'https://youtu.be/cnynDucQhiQ', 'Evento UBA 2024 SALA ZOOM 3 13-11-2024', '', '2025-04-24 17:13:14'),
(9, 'https://youtu.be/GVVwFpJ6EiA', 'Evento UBA 2024 SALA ZOOM 2 14-11-2024', '', '2025-04-24 17:15:12'),
(10, 'https://youtu.be/OpzB9XnSIeE', 'PRESENCIAL SALA ZOOM 1 14-11-2024', '', '2025-04-24 17:15:43'),
(11, 'https://youtu.be/dCqldMYvFMQ', 'IV Congreso Iberoamericano de Estudiantes Universitarios 03 07 24', '', '2025-04-24 17:16:22');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `eventos`
--
ALTER TABLE `eventos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `galeria_fotos`
--
ALTER TABLE `galeria_fotos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sistema_config`
--
ALTER TABLE `sistema_config`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `clave` (`clave`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `videos_youtube`
--
ALTER TABLE `videos_youtube`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `eventos`
--
ALTER TABLE `eventos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `galeria_fotos`
--
ALTER TABLE `galeria_fotos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `sistema_config`
--
ALTER TABLE `sistema_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `videos_youtube`
--
ALTER TABLE `videos_youtube`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `eventos`
--
ALTER TABLE `eventos`
  ADD CONSTRAINT `eventos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
