-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 07-06-2026 a las 19:34:54
-- Versión del servidor: 8.0.45-0ubuntu0.24.04.1
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

--
-- Volcado de datos para la tabla `Torneo_Partido`
--

INSERT INTO `Torneo_Partido` (`id`, `torneo_id`, `equipo_local_id`, `equipo_visitante_id`, `fecha_hora`, `ganador_id`, `estado`) VALUES
(1, 1, 1, 4, '2026-06-12 14:41:00', 4, 'Jugado'),
(2, 1, 2, 5, '2026-06-20 14:41:00', NULL, 'Pendiente'),
(3, 1, 3, 6, '2026-06-04 14:41:00', 3, 'Jugado'),
(4, 1, 1, 5, '2026-06-11 14:42:00', 5, 'Jugado'),
(5, 1, 4, 6, '2026-06-17 14:42:00', 4, 'Jugado'),
(6, 1, 2, 3, '2026-06-17 14:42:00', 2, 'Jugado'),
(7, 1, 1, 6, '2026-06-25 14:42:00', NULL, 'Pendiente'),
(8, 1, 5, 3, '2026-06-18 14:42:00', 3, 'Jugado'),
(9, 1, 4, 2, '2026-06-20 14:42:00', NULL, 'Pendiente'),
(10, 1, 1, 3, '2026-06-20 14:42:00', NULL, 'Pendiente'),
(11, 1, 6, 2, '2026-06-21 14:42:00', NULL, 'Pendiente'),
(12, 1, 5, 4, '2026-06-23 14:42:00', NULL, 'Pendiente'),
(13, 1, 1, 2, '2026-07-10 14:42:00', NULL, 'Pendiente'),
(14, 1, 3, 4, '2026-06-27 14:42:00', NULL, 'Pendiente'),
(15, 1, 6, 5, '2026-06-27 14:42:00', NULL, 'Pendiente'),
(16, 2, 7, 8, '2026-06-07 14:57:00', 7, 'Jugado'),
(17, 3, 9, 10, '2026-06-07 15:03:00', 9, 'Jugado');

--
-- Índices para tablas volcadas
--

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
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `Torneo_Partido`
--
ALTER TABLE `Torneo_Partido`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `Torneo_Partido`
--
ALTER TABLE `Torneo_Partido`
  ADD CONSTRAINT `Torneo_Partido_ibfk_1` FOREIGN KEY (`torneo_id`) REFERENCES `Torneo` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `Torneo_Partido_ibfk_2` FOREIGN KEY (`equipo_local_id`) REFERENCES `Torneo_Equipo` (`id`),
  ADD CONSTRAINT `Torneo_Partido_ibfk_3` FOREIGN KEY (`equipo_visitante_id`) REFERENCES `Torneo_Equipo` (`id`),
  ADD CONSTRAINT `Torneo_Partido_ibfk_4` FOREIGN KEY (`ganador_id`) REFERENCES `Torneo_Equipo` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
