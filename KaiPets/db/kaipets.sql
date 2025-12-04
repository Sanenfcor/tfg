-- phpMyAdmin SQL Dump
-- version 5.2.1deb1+deb12u1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 04-12-2025 a las 22:01:03
-- Versión del servidor: 10.11.14-MariaDB-0+deb12u2
-- Versión de PHP: 8.2.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `kaipets`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `barrios`
--

CREATE TABLE `barrios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `id_ciudad` int(11) NOT NULL,
  `lat` decimal(10,7) NOT NULL,
  `lon` decimal(10,7) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `barrios`
--

INSERT INTO `barrios` (`id`, `nombre`, `id_ciudad`, `lat`, `lon`) VALUES
(1, 'Centro Histórico', 1, 36.7212750, -4.4213990),
(2, 'La Malagueta', 1, 36.7194380, -4.4064200),
(3, 'El Palo', 1, 36.7199370, -4.3473850),
(4, 'Pedregalejo', 1, 36.7208620, -4.3755060),
(5, 'Teatinos', 1, 36.7097850, -4.4829970),
(6, 'Ciudad Jardín', 1, 36.7424630, -4.4312370),
(7, 'Puerto de la Torre', 1, 36.7402840, -4.4985430),
(8, 'Churriana', 1, 36.6623340, -4.4975930),
(9, 'Campanillas', 1, 36.7369060, -4.4987410),
(10, 'Cruz de Humilladero', 1, 36.7154500, -4.4492670),
(11, 'Carretera de Cádiz', 1, 36.7047040, -4.4348170),
(12, 'Bailén-Miraflores', 1, 36.7331060, -4.4424780),
(13, 'El Perchel', 1, 36.7159690, -4.4298890),
(14, 'La Goleta', 1, 36.7271400, -4.4219000),
(15, 'El Limonar', 1, 36.7332620, -4.4069390),
(16, 'Monte Sancha', 1, 36.7299200, -4.4060100),
(17, 'La Victoria', 1, 36.7263600, -4.4216000),
(18, 'Miraflores del Palo', 1, 36.7362990, -4.3576000),
(19, 'Centro de Fuengirola', 3, 36.5397440, -4.6242430),
(20, 'Los Boliches', 3, 36.5478720, -4.6121520),
(21, 'Torreblanca', 3, 36.5662900, -4.6043000),
(22, 'Los Pacos', 3, 36.5646990, -4.6099760),
(23, 'El Boquetillo', 3, 36.5419000, -4.6279000),
(24, 'Los Cordobeses / Parque Miramar', 3, 36.5352000, -4.6229000),
(25, 'Castillo Sohail', 3, 36.5293000, -4.6225000),
(26, 'Carvajal', 3, 36.5683400, -4.5929000),
(27, 'San Cayetano', 3, 36.5459000, -4.6331000),
(28, 'Las Cañadas', 3, 36.5573000, -4.6095000),
(29, 'Marbella Centro', 2, 36.5099400, -4.8857500),
(30, 'Casco Antiguo', 2, 36.5092380, -4.8865930),
(31, 'La Bajadilla', 2, 36.5065700, -4.8762000),
(32, 'Elviria', 2, 36.4914200, -4.7716000),
(33, 'Las Chapas', 2, 36.4856000, -4.7802000),
(34, 'Nueva Andalucía', 2, 36.5039000, -4.9522000),
(35, 'Puerto Banús', 2, 36.4844000, -4.9521000),
(36, 'San Pedro de Alcántara', 2, 36.4887810, -4.9895840),
(37, 'Guadalmina', 2, 36.4882000, -5.0195000),
(38, 'Nagüeles', 2, 36.5317000, -4.9122000),
(39, 'La Milla de Oro', 2, 36.5156000, -4.9110000),
(40, 'Cerro del Colorado', 2, 36.5217000, -4.9049000),
(41, 'Río Real', 2, 36.5081000, -4.8412000),
(42, 'Marbella Este', 2, 36.5009000, -4.8240000),
(43, 'Artola / Cabopino', 2, 36.4820000, -4.7420000);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ciudades`
--

CREATE TABLE `ciudades` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ciudades`
--

INSERT INTO `ciudades` (`id`, `nombre`) VALUES
(3, 'Fuengirola'),
(1, 'Málaga'),
(2, 'Marbella');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuidadores`
--

CREATE TABLE `cuidadores` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `ciudad_id` int(11) NOT NULL,
  `barrio` varchar(100) NOT NULL,
  `descripcion` text NOT NULL,
  `experiencia` varchar(255) DEFAULT NULL,
  `dni_foto` varchar(255) NOT NULL,
  `foto_perfil` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cuidadores`
--

INSERT INTO `cuidadores` (`id`, `usuario_id`, `ciudad_id`, `barrio`, `descripcion`, `experiencia`, `dni_foto`, `foto_perfil`) VALUES
(1, 1, 1, 'El Perchel', 'Chico responsable de 22 años', 'Cuidador de perros durante 3 años', '/KaiPets/uploads/predeterminado/pred_dni.jpg', '/KaiPets/uploads/predeterminado/pred_usu.png'),
(2, 2, 1, 'Teatinos', 'Cuidador disponible para servicios.', 'Sin experiencia registrada.', '/KaiPets/uploads/predeterminado/pred_dni.jpg', '/KaiPets/uploads/predeterminado/pred_usu.png'),
(3, 3, 1, 'La Goleta', 'Cuidador disponible para servicios.', 'Sin experiencia registrada.', '/KaiPets/uploads/predeterminado/pred_dni.jpg', '/KaiPets/uploads/predeterminado/pred_usu.png'),
(4, 4, 1, 'Pedregalejo', 'Cuidador disponible para servicios.', 'Sin experiencia registrada.', '/KaiPets/uploads/predeterminado/pred_dni.jpg', '/KaiPets/uploads/predeterminado/pred_usu.png'),
(5, 5, 1, 'Carretera de Cádiz', 'Cuidador disponible para servicios.', 'Sin experiencia registrada.', '/KaiPets/uploads/predeterminado/pred_dni.jpg', '/KaiPets/uploads/predeterminado/pred_usu.png'),
(6, 6, 3, 'Barrio 6', 'Cuidador disponible para servicios.', 'Sin experiencia registrada.', '/KaiPets/uploads/predeterminado/pred_dni.jpg', '/KaiPets/uploads/predeterminado/pred_usu.png'),
(7, 7, 3, 'Barrio 7', 'Cuidador disponible para servicios.', 'Sin experiencia registrada.', '/KaiPets/uploads/predeterminado/pred_dni.jpg', '/KaiPets/uploads/predeterminado/pred_usu.png'),
(8, 8, 3, 'Barrio 8', 'Cuidador disponible para servicios.', 'Sin experiencia registrada.', '/KaiPets/uploads/predeterminado/pred_dni.jpg', '/KaiPets/uploads/predeterminado/pred_usu.png'),
(9, 9, 3, 'Barrio 9', 'Cuidador disponible para servicios.', 'Sin experiencia registrada.', '/KaiPets/uploads/predeterminado/pred_dni.jpg', '/KaiPets/uploads/predeterminado/pred_usu.png'),
(10, 10, 3, 'Barrio 10', 'Cuidador disponible para servicios.', 'Sin experiencia registrada.', '/KaiPets/uploads/predeterminado/pred_dni.jpg', '/KaiPets/uploads/predeterminado/pred_usu.png'),
(11, 11, 2, 'Barrio 11', 'Cuidador disponible para servicios.', 'Sin experiencia registrada.', '/KaiPets/uploads/predeterminado/pred_dni.jpg', '/KaiPets/uploads/predeterminado/pred_usu.png'),
(12, 12, 2, 'Barrio 12', 'Cuidador disponible para servicios.', 'Sin experiencia registrada.', '/KaiPets/uploads/predeterminado/pred_dni.jpg', '/KaiPets/uploads/predeterminado/pred_usu.png'),
(13, 13, 2, 'Barrio 13', 'Cuidador disponible para servicios.', 'Sin experiencia registrada.', '/KaiPets/uploads/predeterminado/pred_dni.jpg', '/KaiPets/uploads/predeterminado/pred_usu.png'),
(14, 14, 2, 'Barrio 14', 'Cuidador disponible para servicios.', 'Sin experiencia registrada.', '/KaiPets/uploads/predeterminado/pred_dni.jpg', '/KaiPets/uploads/predeterminado/pred_usu.png'),
(15, 15, 2, 'Barrio 15', 'Cuidador disponible para servicios.', 'Sin experiencia registrada.', '/KaiPets/uploads/predeterminado/pred_dni.jpg', '/KaiPets/uploads/predeterminado/pred_usu.png'),
(16, 2, 1, 'El Palo', 'Cuidador promedio', '', '/KaiPets/uploads/dni/dni_6932031c9a1ac_pred_dni.jpg', '/KaiPets/uploads/perfiles/foto_6932031c9a1b5_pred_usu.jpg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuidador_servicio`
--

CREATE TABLE `cuidador_servicio` (
  `id` int(11) NOT NULL,
  `cuidador_id` int(11) NOT NULL,
  `servicio_id` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cuidador_servicio`
--

INSERT INTO `cuidador_servicio` (`id`, `cuidador_id`, `servicio_id`, `precio`) VALUES
(4, 1, 4, 15.00),
(5, 1, 1, 10.00),
(6, 1, 2, 25.00),
(7, 1, 3, 20.00),
(8, 1, 5, 20.00),
(9, 2, 1, 10.00),
(10, 2, 2, 25.00),
(11, 2, 3, 20.00),
(12, 2, 4, 15.00),
(13, 2, 5, 20.00),
(14, 3, 1, 10.00),
(15, 3, 2, 25.00),
(16, 3, 3, 20.00),
(17, 3, 4, 15.00),
(18, 3, 5, 20.00),
(19, 4, 1, 10.00),
(20, 4, 2, 25.00),
(21, 4, 3, 20.00),
(22, 4, 4, 15.00),
(23, 4, 5, 20.00),
(24, 5, 1, 10.00),
(25, 5, 2, 25.00),
(26, 5, 3, 20.00),
(27, 5, 4, 15.00),
(28, 5, 5, 20.00),
(29, 6, 1, 10.00),
(30, 6, 2, 25.00),
(31, 6, 3, 20.00),
(32, 6, 4, 15.00),
(33, 6, 5, 20.00),
(34, 7, 1, 10.00),
(35, 7, 2, 25.00),
(36, 7, 3, 20.00),
(37, 7, 4, 15.00),
(38, 7, 5, 20.00),
(39, 8, 1, 10.00),
(40, 8, 2, 25.00),
(41, 8, 3, 20.00),
(42, 8, 4, 15.00),
(43, 8, 5, 20.00),
(44, 9, 1, 10.00),
(45, 9, 2, 25.00),
(46, 9, 3, 20.00),
(47, 9, 4, 15.00),
(48, 9, 5, 20.00),
(49, 10, 1, 10.00),
(50, 10, 2, 25.00),
(51, 10, 3, 20.00),
(52, 10, 4, 15.00),
(53, 10, 5, 20.00),
(54, 11, 1, 10.00),
(55, 11, 2, 25.00),
(56, 11, 3, 20.00),
(57, 11, 4, 15.00),
(58, 11, 5, 20.00),
(59, 12, 1, 10.00),
(60, 12, 2, 25.00),
(61, 12, 3, 20.00),
(62, 12, 4, 15.00),
(63, 12, 5, 20.00),
(64, 13, 1, 10.00),
(65, 13, 2, 25.00),
(66, 13, 3, 20.00),
(67, 13, 4, 15.00),
(68, 13, 5, 20.00),
(69, 14, 1, 10.00),
(70, 14, 2, 25.00),
(71, 14, 3, 20.00),
(72, 14, 4, 15.00),
(73, 14, 5, 20.00),
(74, 15, 1, 10.00),
(75, 15, 2, 25.00),
(76, 15, 3, 20.00),
(77, 15, 4, 15.00),
(78, 15, 5, 20.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_cuidados`
--

CREATE TABLE `historial_cuidados` (
  `id` int(11) NOT NULL,
  `cuidador_id` int(11) NOT NULL,
  `mascota_id` int(11) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `notas` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mascotas`
--

CREATE TABLE `mascotas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `edad` int(3) NOT NULL,
  `peso` decimal(5,2) DEFAULT NULL,
  `usuario_id` int(11) NOT NULL,
  `especie` varchar(255) NOT NULL,
  `raza` varchar(255) NOT NULL,
  `rating` decimal(10,0) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `mascotas`
--

INSERT INTO `mascotas` (`id`, `nombre`, `edad`, `peso`, `usuario_id`, `especie`, `raza`, `rating`) VALUES
(1, 'Arthur', 8, 40.00, 1, 'Perro', 'Labrador', 0),
(2, 'Cleo', 4, 6.00, 2, 'Gato', 'Persa', 0),
(3, 'Pepo', 2, 5.00, 4, 'Conejo', 'Blanco', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `opiniones`
--

CREATE TABLE `opiniones` (
  `id` int(11) NOT NULL,
  `cuidador_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `puntuacion` int(11) NOT NULL CHECK (`puntuacion` between 1 and 5),
  `comentario` text DEFAULT NULL,
  `fecha` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `opiniones`
--

INSERT INTO `opiniones` (`id`, `cuidador_id`, `usuario_id`, `puntuacion`, `comentario`, `fecha`) VALUES
(1, 1, 4, 5, '', '2025-12-04 19:26:30');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `imagen` varchar(255) DEFAULT NULL,
  `categoria` varchar(100) DEFAULT NULL,
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservas`
--

CREATE TABLE `reservas` (
  `id_reserva` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_cuidador` int(11) NOT NULL,
  `id_mascota` int(11) NOT NULL,
  `id_servicio` int(11) NOT NULL,
  `tipo_reserva` enum('dias','horas') NOT NULL,
  `fecha_reserva` datetime NOT NULL,
  `fecha_inicio` datetime NOT NULL,
  `fecha_fin` datetime NOT NULL,
  `horas` int(11) DEFAULT NULL,
  `estado_reserva` enum('pendiente','confirmada','cancelada','realizada') NOT NULL DEFAULT 'pendiente',
  `precio_final` decimal(10,2) DEFAULT NULL,
  `notas` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `reservas`
--

INSERT INTO `reservas` (`id_reserva`, `id_usuario`, `id_cuidador`, `id_mascota`, `id_servicio`, `tipo_reserva`, `fecha_reserva`, `fecha_inicio`, `fecha_fin`, `horas`, `estado_reserva`, `precio_final`, `notas`) VALUES
(1, 2, 1, 2, 1, 'horas', '2025-12-03 22:07:55', '2025-12-05 00:00:00', '2025-12-05 23:59:59', 2, 'confirmada', NULL, 'Suelta mucho pelo'),
(2, 4, 1, 3, 1, 'horas', '2025-12-04 01:38:30', '2025-12-04 00:00:00', '2025-12-04 23:59:59', 6, 'confirmada', NULL, ''),
(3, 4, 1, 3, 4, 'dias', '2025-12-04 01:40:39', '2025-12-06 00:00:00', '2025-12-07 23:59:59', NULL, 'cancelada', NULL, ''),
(4, 1, 3, 1, 1, 'dias', '2025-12-04 22:52:19', '2025-12-04 00:00:00', '2025-12-05 23:59:59', NULL, 'confirmada', 30.00, ''),
(5, 1, 2, 1, 1, 'dias', '2025-12-04 22:53:18', '2025-12-09 00:00:00', '2025-12-11 23:59:59', NULL, 'pendiente', 40.00, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios`
--

CREATE TABLE `servicios` (
  `id_servicio` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `servicios`
--

INSERT INTO `servicios` (`id_servicio`, `nombre`, `descripcion`, `precio`) VALUES
(1, 'Paseo', 'Paseo de la mascota por las inmediaciones del domicilio del dueño.', 10.00),
(2, 'Canguro', 'Cuidado de la mascota en horario nocturno en el domicilio del dueño', 25.00),
(3, 'Alojamiento', 'Cuidado de la mascota en el domicilio del cuidador', 20.00),
(4, 'Cuidado matinal', 'Cuidado durante la mañana de la mascota en casa del cuidador', 15.00),
(5, 'Cuidados adicionales', 'Variedad de cuidados extras que pueden incluir desde peluquería a cuidados de recuperación acuática.', 20.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(200) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `telefono` int(9) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `terminos` tinyint(1) NOT NULL DEFAULT 0,
  `admin` tinyint(1) NOT NULL DEFAULT 0,
  `cuidador` tinyint(1) NOT NULL DEFAULT 0,
  `ciudad_id` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellido`, `email`, `password`, `telefono`, `direccion`, `registro`, `terminos`, `admin`, `cuidador`, `ciudad_id`) VALUES
(1, 'Santiago', 'Enfedaque', 'sanenfcor@gmail.com', '$2y$10$6bEisPQ85XNd4DqJRBIEou7uln5ifpEWMauA6OVlcHz.pVaLxhovq', 123456789, 'Cuarteles 37', '2025-11-26 10:58:09', 1, 1, 1, 1),
(2, 'Cuidador', '1', 'cuidador1@gmail.com', '$2y$10$f53V5SRLAFqAqxhRyPkwweRnkIMQ0XTi.r4AOqjvejQVZaTp0FVgm', 123456789, 'Alameda', '2025-11-29 18:40:46', 1, 0, 1, 1),
(3, 'Cuidador', '2', 'cuidador2@gmail.com', '$2y$10$J9Ttx7bbdrxiJQXTb2Vl1OSAhJVhzS/Hr5an0tIE5GNpF0bK.mWwq', 123456789, 'Larios', '2025-11-29 18:41:27', 1, 0, 1, 1),
(4, 'Cuidador', '3', 'cuidador3@gmail.com', '$2y$10$eucaOf2LyhiWkuEk6wJwee./75RQ5DpqwirHnVaddHXuwYdf7zdgu', 123456789, 'Carranque', '2025-12-01 13:03:51', 1, 0, 1, 1),
(5, 'Cuidador', '4', 'cuidador4@gmail.com', '$2y$10$xNEVfPmlALshY3SvXQ9wCOVLFFto7S2kaCQgVyDxWa4t6xp7ePaki', 123456789, 'El Palo', '2025-12-01 13:06:02', 1, 0, 1, 1),
(6, 'Cuidador', '5', 'cuidador5@gmail.com', '$2y$10$B9wVSnSTbi0ifsRx8iCeeu.LdpDyNh/lgLPlsH/hGXL.zneaRxSNG', 60000005, 'Andalucia', '2025-12-04 01:56:05', 1, 0, 1, 3),
(7, 'Cuidador', '6', 'cuidador6@gmail.com', '$2y$10$xw8HNUb4CJBEpwrWiMOPxeoh70NQwctE1KpGRzgJju1MuKBkm2D7i', 60000006, 'Andalucia', '2025-12-04 01:56:06', 1, 0, 1, 3),
(8, 'Cuidador', '7', 'cuidador7@gmail.com', '$2y$10$6xuulC.EIwfTy3oSx88VFeVgzw9BJgkxgbtmsP0RpWjeglExKwYAy', 60000007, 'Andalucia', '2025-12-04 01:56:06', 1, 0, 1, 3),
(9, 'Cuidador', '8', 'cuidador8@gmail.com', '$2y$10$dc8dFNL27EHIWqkBAr9.k.HmHBZFyS8w5GapT4Z6jZV9NPG/M9hw2', 60000008, 'Andalucia', '2025-12-04 01:56:06', 1, 0, 1, 3),
(10, 'Cuidador', '9', 'cuidador9@gmail.com', '$2y$10$sFJITzhuSbEWfTR9VhxQ0e2GegYJ6ZTB.dLAniJmE4Lk8GxZuQnrm', 60000009, 'Andalucia', '2025-12-04 01:56:07', 1, 0, 1, 3),
(11, 'Cuidador', '10', 'cuidador10@gmail.com', '$2y$10$Gbu2LgIx5N77qmWp1FWoi.C.qt2ocEocZAJ/Go9bYdYl3j.lK/zkO', 60000010, 'Andalucia', '2025-12-04 01:59:45', 1, 0, 1, 2),
(12, 'Cuidador', '11', 'cuidador11@gmail.com', '$2y$10$8K2Ey5NWqQypzKw.mR2TwuFSmTUgppWm2ueehWX77phWiMEQRDG9m', 60000011, 'Andalucia', '2025-12-04 01:59:46', 1, 0, 1, 2),
(13, 'Cuidador', '12', 'cuidador12@gmail.com', '$2y$10$0rGM.kK6S8gsqnX73ZU0fub5USdJzfNDuLzVb9Q5L/vdWF3IaadIG', 60000012, 'Andalucia', '2025-12-04 01:59:46', 1, 0, 1, 2),
(14, 'Cuidador', '13', 'cuidador13@gmail.com', '$2y$10$LArxuV76ay7ryMEk3DIwZubxnWH7uRHTbAtY/xx3WEjor39mKeKlO', 60000013, 'Andalucia', '2025-12-04 01:59:46', 1, 0, 1, 2),
(15, 'Cuidador', '14', 'cuidador14@gmail.com', '$2y$10$qPeGQYRqKlvL8DREWx4nu.siyktYFy516glyXa5qHGIki6rzu5Hz6', 60000014, 'Andalucia', '2025-12-04 01:59:46', 1, 0, 1, 2);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `barrios`
--
ALTER TABLE `barrios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`),
  ADD KEY `fk_barrios_ciudad` (`id_ciudad`);

--
-- Indices de la tabla `ciudades`
--
ALTER TABLE `ciudades`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `cuidadores`
--
ALTER TABLE `cuidadores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `ciudad_id` (`ciudad_id`);

--
-- Indices de la tabla `cuidador_servicio`
--
ALTER TABLE `cuidador_servicio`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cuidador_id` (`cuidador_id`),
  ADD KEY `servicio_id` (`servicio_id`);

--
-- Indices de la tabla `historial_cuidados`
--
ALTER TABLE `historial_cuidados`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cuidador_id` (`cuidador_id`),
  ADD KEY `mascota_id` (`mascota_id`);

--
-- Indices de la tabla `mascotas`
--
ALTER TABLE `mascotas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `duenio` (`usuario_id`);

--
-- Indices de la tabla `opiniones`
--
ALTER TABLE `opiniones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cuidador_id` (`cuidador_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD PRIMARY KEY (`id_reserva`),
  ADD KEY `reserva_usuario_fk` (`id_usuario`),
  ADD KEY `reserva_mascota_fk` (`id_mascota`),
  ADD KEY `reserva_servicio_fk` (`id_servicio`);

--
-- Indices de la tabla `servicios`
--
ALTER TABLE `servicios`
  ADD PRIMARY KEY (`id_servicio`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `usuarios_ciudad_fk` (`ciudad_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `barrios`
--
ALTER TABLE `barrios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT de la tabla `ciudades`
--
ALTER TABLE `ciudades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `cuidadores`
--
ALTER TABLE `cuidadores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `cuidador_servicio`
--
ALTER TABLE `cuidador_servicio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT de la tabla `historial_cuidados`
--
ALTER TABLE `historial_cuidados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `mascotas`
--
ALTER TABLE `mascotas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `opiniones`
--
ALTER TABLE `opiniones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `reservas`
--
ALTER TABLE `reservas`
  MODIFY `id_reserva` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `servicios`
--
ALTER TABLE `servicios`
  MODIFY `id_servicio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `barrios`
--
ALTER TABLE `barrios`
  ADD CONSTRAINT `fk_barrios_ciudad` FOREIGN KEY (`id_ciudad`) REFERENCES `ciudades` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `cuidadores`
--
ALTER TABLE `cuidadores`
  ADD CONSTRAINT `cuidadores_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cuidadores_ibfk_2` FOREIGN KEY (`ciudad_id`) REFERENCES `ciudades` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `cuidador_servicio`
--
ALTER TABLE `cuidador_servicio`
  ADD CONSTRAINT `cuidador_servicio_ibfk_1` FOREIGN KEY (`cuidador_id`) REFERENCES `cuidadores` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cuidador_servicio_ibfk_2` FOREIGN KEY (`servicio_id`) REFERENCES `servicios` (`id_servicio`) ON DELETE CASCADE;

--
-- Filtros para la tabla `historial_cuidados`
--
ALTER TABLE `historial_cuidados`
  ADD CONSTRAINT `historial_cuidados_ibfk_1` FOREIGN KEY (`cuidador_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `historial_cuidados_ibfk_2` FOREIGN KEY (`mascota_id`) REFERENCES `mascotas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `mascotas`
--
ALTER TABLE `mascotas`
  ADD CONSTRAINT `mascotas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `opiniones`
--
ALTER TABLE `opiniones`
  ADD CONSTRAINT `opiniones_ibfk_1` FOREIGN KEY (`cuidador_id`) REFERENCES `cuidadores` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `opiniones_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD CONSTRAINT `reserva_mascota_fk` FOREIGN KEY (`id_mascota`) REFERENCES `mascotas` (`id`),
  ADD CONSTRAINT `reserva_servicio_fk` FOREIGN KEY (`id_servicio`) REFERENCES `servicios` (`id_servicio`),
  ADD CONSTRAINT `reserva_usuario_fk` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ciudad_fk` FOREIGN KEY (`ciudad_id`) REFERENCES `ciudades` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
