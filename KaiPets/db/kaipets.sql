SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

START TRANSACTION;
SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS `kaipets` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `kaipets`;


CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(200) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `telefono` int(9) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `terminos` tinyint(1) NOT NULL DEFAULT 0,
  `admin` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Usuario mio
INSERT INTO `usuarios` 
(`id`, `nombre`, `apellido`, `email`, `password`, `telefono`, `direccion`, `registro`, `terminos`, `admin`) VALUES
(1, 'Santiago', 'Enfedaque', 'sanenfcor@gmail.com', '$2y$10$6bEisPQ85XNd4DqJRBIEou7uln5ifpEWMauA6OVlcHz.pVaLxhovq', 123456789, 'Cuarteles 37', NOW(), 1, 1);

CREATE TABLE `mascota` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `edad` int(3) NOT NULL,
  `duenio` int(11) NOT NULL,
  `animal` varchar(255) NOT NULL,
  `raza` varchar(255) NOT NULL,
  `rating` decimal(10,0) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `duenio` (`duenio`),
  CONSTRAINT `mascota_ibfk_1` FOREIGN KEY (`duenio`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `servicios` (
  `id_servicio` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_servicio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `servicios` (`nombre`, `descripcion`, `precio`) VALUES
('Baño', 'Baño completo para la mascota', 20.00),
('Corte de pelo', 'Corte de pelo profesional', 25.00),
('Consulta veterinaria', 'Revisión completa con un veterinario', 30.00);

CREATE TABLE `pedidos` (
  `id_pedido` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `fecha_pedido` datetime NOT NULL DEFAULT current_timestamp(),
  `estado_pedido` ENUM('pendiente','pagado','cancelado','completado') NOT NULL DEFAULT 'pendiente',
  `metodo_pago` varchar(50) DEFAULT NULL,
  `total` decimal(10,2) NOT NULL DEFAULT 0,
  `notas` text DEFAULT NULL,
  PRIMARY KEY (`id_pedido`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `pedidos_usuario_fk` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `pedido_detalles` (
  `id_detalle` int(11) NOT NULL AUTO_INCREMENT,
  `id_pedido` int(11) NOT NULL,
  `id_servicio` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 1,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_detalle`),
  KEY `id_pedido` (`id_pedido`),
  KEY `id_servicio` (`id_servicio`),
  CONSTRAINT `detalle_pedido_fk` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id_pedido`) ON DELETE CASCADE,
  CONSTRAINT `detalle_servicio_fk` FOREIGN KEY (`id_servicio`) REFERENCES `servicios` (`id_servicio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `reservas` (
  `id_reserva` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `id_mascota` int(11) NOT NULL,
  `id_servicio` int(11) NOT NULL,
  `fecha_reserva` datetime NOT NULL,
  `estado_reserva` ENUM('pendiente','confirmada','cancelada','realizada') NOT NULL DEFAULT 'pendiente',
  `precio_final` decimal(10,2) DEFAULT NULL,
  `notas` text DEFAULT NULL,
  PRIMARY KEY (`id_reserva`),
  KEY `reserva_usuario_fk` (`id_usuario`),
  KEY `reserva_mascota_fk` (`id_mascota`),
  KEY `reserva_servicio_fk` (`id_servicio`),
  CONSTRAINT `reserva_usuario_fk` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `reserva_mascota_fk` FOREIGN KEY (`id_mascota`) REFERENCES `mascota` (`id`),
  CONSTRAINT `reserva_servicio_fk` FOREIGN KEY (`id_servicio`) REFERENCES `servicios` (`id_servicio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

COMMIT;
