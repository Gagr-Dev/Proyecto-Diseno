-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 27-06-2026 a las 05:10:57
-- Versión del servidor: 8.0.46-0ubuntu0.24.04.3
-- Versión de PHP: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `gestion_licoreria`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Bitacora_Sistema`
--

CREATE TABLE `Bitacora_Sistema` (
  `id` int NOT NULL,
  `usuario_id` int DEFAULT NULL,
  `accion` varchar(100) NOT NULL,
  `tabla_afectada` varchar(50) DEFAULT NULL,
  `registro_id` int DEFAULT NULL,
  `detalles` json DEFAULT NULL,
  `ip_direccion` varchar(45) DEFAULT NULL,
  `fecha_hora` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Categorias`
--

CREATE TABLE `Categorias` (
  `id` int NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Combos`
--

CREATE TABLE `Combos` (
  `id` int NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `stock_reservado` int NOT NULL DEFAULT '0',
  `estado` enum('Activo','Inactivo') DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Combo_Receta`
--

CREATE TABLE `Combo_Receta` (
  `id` int NOT NULL,
  `combo_id` int NOT NULL,
  `producto_id` int NOT NULL,
  `cantidad_necesaria` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Cuentas_Por_Cobrar`
--

CREATE TABLE `Cuentas_Por_Cobrar` (
  `id` int NOT NULL,
  `deudor_id` int NOT NULL,
  `venta_id` int DEFAULT NULL,
  `monto` decimal(10,2) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `estado` enum('Pendiente','Pagado') DEFAULT 'Pendiente',
  `fecha_deuda` datetime DEFAULT CURRENT_TIMESTAMP,
  `fecha_pago` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Detalle_Venta`
--

CREATE TABLE `Detalle_Venta` (
  `id` int NOT NULL,
  `venta_id` int NOT NULL,
  `producto_id` int DEFAULT NULL,
  `combo_id` int DEFAULT NULL,
  `formato_venta` enum('Unidad','Caja') NOT NULL,
  `cantidad` int NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Deudores`
--

CREATE TABLE `Deudores` (
  `id` int NOT NULL,
  `nombre_completo` varchar(100) NOT NULL,
  `cedula` varchar(20) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `limite_credito` decimal(10,2) DEFAULT '0.00',
  `fecha_registro` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Evento`
--

CREATE TABLE `Evento` (
  `id` int NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `fecha_inicio` datetime NOT NULL,
  `fecha_fin` datetime NOT NULL,
  `descripcion` text,
  `estado` enum('Programado','En Curso','Finalizado','Cancelado') DEFAULT 'Programado'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Movimiento_Inventario`
--

CREATE TABLE `Movimiento_Inventario` (
  `id` int NOT NULL,
  `producto_id` int DEFAULT NULL,
  `combo_id` int DEFAULT NULL,
  `cantidad_unidades` int NOT NULL,
  `costo_unitario` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tipo_movimiento` enum('Compra_Proveedor','Venta','Ajuste_Merma','Armado_Combo','Desarmado_Combo') NOT NULL,
  `referencia_id` int DEFAULT NULL,
  `usuario_id` int NOT NULL,
  `fecha_hora` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Productos`
--

CREATE TABLE `Productos` (
  `id` int NOT NULL,
  `categoria_id` int NOT NULL,
  `codigo_barras` varchar(50) DEFAULT NULL,
  `nombre` varchar(100) NOT NULL,
  `unidades_por_caja` int NOT NULL DEFAULT '1',
  `stock_unidades_total` int NOT NULL DEFAULT '0',
  `precio_unidad` decimal(10,2) NOT NULL,
  `precio_combo_5` decimal(10,2) DEFAULT NULL,
  `precio_caja_36` decimal(10,2) DEFAULT NULL,
  `estado` enum('Activo','Inactivo') DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Rol`
--

CREATE TABLE `Rol` (
  `id` int NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `Rol`
--

INSERT INTO `Rol` (`id`, `nombre`, `descripcion`) VALUES
(1, 'Administrador', 'Acceso total al sistema, inventario y reportes'),
(2, 'Cajero', 'Solo puede registrar ventas y ver el stock actual');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Torneo`
--

CREATE TABLE `Torneo` (
  `id` int NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `estado` enum('Pendiente','En Curso','Terminado') DEFAULT 'Pendiente',
  `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Torneo_Equipo`
--

CREATE TABLE `Torneo_Equipo` (
  `id` int NOT NULL,
  `torneo_id` int NOT NULL,
  `nombre_equipo` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Torneo_Partido`
--

CREATE TABLE `Torneo_Partido` (
  `id` int NOT NULL,
  `torneo_id` int NOT NULL,
  `equipo_local_id` int NOT NULL,
  `equipo_visitante_id` int NOT NULL,
  `fecha_hora` datetime NOT NULL,
  `ganador_id` int DEFAULT NULL,
  `estado` enum('Pendiente','Jugado','Cancelado') DEFAULT 'Pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Usuario`
--

CREATE TABLE `Usuario` (
  `id` int NOT NULL,
  `primer_nombre` varchar(50) NOT NULL,
  `segundo_nombre` varchar(50) DEFAULT NULL,
  `primer_apellido` varchar(50) NOT NULL,
  `segundo_apellido` varchar(50) DEFAULT NULL,
  `cedula` varchar(20) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `rol_id` int NOT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `Usuario`
--

INSERT INTO `Usuario` (`id`, `primer_nombre`, `segundo_nombre`, `primer_apellido`, `segundo_apellido`, `cedula`, `username`, `password_hash`, `rol_id`, `activo`, `fecha_creacion`) VALUES
(7, 'Gabriel', NULL, 'Gonzalez', NULL, 'V-31445815', 'gagr1', '$2y$10$/BN8.zYGVO4AhuUw9WP.DOKvMELwTzL2HGXXg15TKibsG7GF/qQ0a', 1, 1, '2026-05-30 14:15:28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Venta`
--

CREATE TABLE `Venta` (
  `id` int NOT NULL,
  `fecha_hora` datetime DEFAULT CURRENT_TIMESTAMP,
  `tipo_venta` enum('Normal','Evento') NOT NULL DEFAULT 'Normal',
  `evento_id` int DEFAULT NULL,
  `usuario_id` int NOT NULL,
  `total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `Bitacora_Sistema`
--
ALTER TABLE `Bitacora_Sistema`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `Categorias`
--
ALTER TABLE `Categorias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `Combos`
--
ALTER TABLE `Combos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `Combo_Receta`
--
ALTER TABLE `Combo_Receta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `combo_id` (`combo_id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indices de la tabla `Cuentas_Por_Cobrar`
--
ALTER TABLE `Cuentas_Por_Cobrar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `deudor_id` (`deudor_id`),
  ADD KEY `venta_id` (`venta_id`);

--
-- Indices de la tabla `Detalle_Venta`
--
ALTER TABLE `Detalle_Venta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `venta_id` (`venta_id`),
  ADD KEY `FK_DetalleVenta_Productos` (`producto_id`),
  ADD KEY `FK_Detalle_Combo` (`combo_id`);

--
-- Indices de la tabla `Deudores`
--
ALTER TABLE `Deudores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cedula` (`cedula`);

--
-- Indices de la tabla `Evento`
--
ALTER TABLE `Evento`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `Movimiento_Inventario`
--
ALTER TABLE `Movimiento_Inventario`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_movimiento_usuario` (`usuario_id`),
  ADD KEY `FK_Movimiento_Productos` (`producto_id`),
  ADD KEY `FK_Movimiento_Combo` (`combo_id`);

--
-- Indices de la tabla `Productos`
--
ALTER TABLE `Productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categoria_id` (`categoria_id`);

--
-- Indices de la tabla `Rol`
--
ALTER TABLE `Rol`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `Torneo`
--
ALTER TABLE `Torneo`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `Torneo_Equipo`
--
ALTER TABLE `Torneo_Equipo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `torneo_id` (`torneo_id`);

--
-- Indices de la tabla `Torneo_Partido`
--
ALTER TABLE `Torneo_Partido`
  ADD PRIMARY KEY (`id`),
  ADD KEY `torneo_id` (`torneo_id`),
  ADD KEY `equipo_local_id` (`equipo_local_id`),
  ADD KEY `equipo_visitante_id` (`equipo_visitante_id`),
  ADD KEY `ganador_id` (`ganador_id`);

--
-- Indices de la tabla `Usuario`
--
ALTER TABLE `Usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `cedula` (`cedula`),
  ADD KEY `rol_id` (`rol_id`);

--
-- Indices de la tabla `Venta`
--
ALTER TABLE `Venta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `evento_id` (`evento_id`),
  ADD KEY `fk_venta_usuario` (`usuario_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `Bitacora_Sistema`
--
ALTER TABLE `Bitacora_Sistema`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Categorias`
--
ALTER TABLE `Categorias`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Combos`
--
ALTER TABLE `Combos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Combo_Receta`
--
ALTER TABLE `Combo_Receta`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Cuentas_Por_Cobrar`
--
ALTER TABLE `Cuentas_Por_Cobrar`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Detalle_Venta`
--
ALTER TABLE `Detalle_Venta`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Deudores`
--
ALTER TABLE `Deudores`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Evento`
--
ALTER TABLE `Evento`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Movimiento_Inventario`
--
ALTER TABLE `Movimiento_Inventario`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Productos`
--
ALTER TABLE `Productos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Rol`
--
ALTER TABLE `Rol`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `Torneo`
--
ALTER TABLE `Torneo`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Torneo_Equipo`
--
ALTER TABLE `Torneo_Equipo`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Torneo_Partido`
--
ALTER TABLE `Torneo_Partido`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Usuario`
--
ALTER TABLE `Usuario`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `Venta`
--
ALTER TABLE `Venta`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `Bitacora_Sistema`
--
ALTER TABLE `Bitacora_Sistema`
  ADD CONSTRAINT `Bitacora_Sistema_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `Usuario` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `Combo_Receta`
--
ALTER TABLE `Combo_Receta`
  ADD CONSTRAINT `Combo_Receta_ibfk_1` FOREIGN KEY (`combo_id`) REFERENCES `Combos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `Combo_Receta_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `Productos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `Cuentas_Por_Cobrar`
--
ALTER TABLE `Cuentas_Por_Cobrar`
  ADD CONSTRAINT `Cuentas_Por_Cobrar_ibfk_1` FOREIGN KEY (`deudor_id`) REFERENCES `Deudores` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `Cuentas_Por_Cobrar_ibfk_2` FOREIGN KEY (`venta_id`) REFERENCES `Venta` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `Detalle_Venta`
--
ALTER TABLE `Detalle_Venta`
  ADD CONSTRAINT `Detalle_Venta_ibfk_1` FOREIGN KEY (`venta_id`) REFERENCES `Venta` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_Detalle_Combo` FOREIGN KEY (`combo_id`) REFERENCES `Combos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_DetalleVenta_Productos` FOREIGN KEY (`producto_id`) REFERENCES `Productos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `Movimiento_Inventario`
--
ALTER TABLE `Movimiento_Inventario`
  ADD CONSTRAINT `FK_Movimiento_Combo` FOREIGN KEY (`combo_id`) REFERENCES `Combos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_Movimiento_Productos` FOREIGN KEY (`producto_id`) REFERENCES `Productos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_movimiento_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `Usuario` (`id`);

--
-- Filtros para la tabla `Productos`
--
ALTER TABLE `Productos`
  ADD CONSTRAINT `Productos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `Categorias` (`id`);

--
-- Filtros para la tabla `Torneo_Equipo`
--
ALTER TABLE `Torneo_Equipo`
  ADD CONSTRAINT `Torneo_Equipo_ibfk_1` FOREIGN KEY (`torneo_id`) REFERENCES `Torneo` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `Torneo_Partido`
--
ALTER TABLE `Torneo_Partido`
  ADD CONSTRAINT `Torneo_Partido_ibfk_1` FOREIGN KEY (`torneo_id`) REFERENCES `Torneo` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `Torneo_Partido_ibfk_2` FOREIGN KEY (`equipo_local_id`) REFERENCES `Torneo_Equipo` (`id`),
  ADD CONSTRAINT `Torneo_Partido_ibfk_3` FOREIGN KEY (`equipo_visitante_id`) REFERENCES `Torneo_Equipo` (`id`),
  ADD CONSTRAINT `Torneo_Partido_ibfk_4` FOREIGN KEY (`ganador_id`) REFERENCES `Torneo_Equipo` (`id`);

--
-- Filtros para la tabla `Usuario`
--
ALTER TABLE `Usuario`
  ADD CONSTRAINT `Usuario_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `Rol` (`id`);

--
-- Filtros para la tabla `Venta`
--
ALTER TABLE `Venta`
  ADD CONSTRAINT `fk_venta_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `Usuario` (`id`),
  ADD CONSTRAINT `Venta_ibfk_1` FOREIGN KEY (`evento_id`) REFERENCES `Evento` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
