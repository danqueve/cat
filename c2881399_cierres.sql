-- phpMyAdmin SQL Dump
-- version 4.9.11
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 07-02-2026 a las 23:31:30
-- Versión del servidor: 8.0.44-35.1
-- Versión de PHP: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `c2881399_cierres`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int NOT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `icono` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'fa-solid fa-tag',
  `color_bg` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'bg-slate-800',
  `color_text` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'text-white'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`, `icono`, `color_bg`, `color_text`) VALUES
(1, 'Aires Acondicionados', 'fa-solid fa-snowflake', 'bg-teal-500/10', 'text-teal-400'),
(2, 'Aspiradoras', 'fa-solid fa-wind', 'bg-purple-500/10', 'text-purple-400'),
(3, 'Balanzas', 'fa-solid fa-scale-balanced', 'bg-blue-500/10', 'text-blue-400'),
(4, 'Cafeteras', 'fa-solid fa-mug-hot', 'bg-yellow-500/10', 'text-yellow-400'),
(5, 'Cortapelos', 'fa-solid fa-scissors', 'bg-blue-500/10', 'text-blue-400'),
(6, 'Extractores de Cocina', 'fa-solid fa-fan', 'bg-orange-500/10', 'text-orange-400'),
(7, 'Freidoras', 'fa-solid fa-utensils', 'bg-orange-500/10', 'text-orange-400'),
(8, 'Grill y Vaporeras', 'fa-solid fa-burger', 'bg-orange-500/10', 'text-orange-400'),
(9, 'Heladeras Exhibidoras', 'fa-solid fa-box-archive', 'bg-cyan-500/10', 'text-cyan-400'),
(10, 'Jardineria', 'fa-solid fa-leaf', 'bg-green-500/10', 'text-green-400'),
(11, 'Jugueras y Extractores', 'fa-solid fa-lemon', 'bg-yellow-500/10', 'text-yellow-400'),
(12, 'Kit de Cocina', 'fa-solid fa-kitchen-set', 'bg-orange-500/10', 'text-orange-400'),
(13, 'Lavarropas', 'fa-solid fa-soap', 'bg-blue-500/10', 'text-blue-400'),
(14, 'Licuadoras', 'fa-solid fa-blender', 'bg-orange-500/10', 'text-orange-400'),
(15, 'Maquilladores', 'fa-solid fa-star', 'bg-pink-500/10', 'text-pink-400'),
(16, 'Minipimer', 'fa-solid fa-bolt', 'bg-orange-500/10', 'text-orange-400'),
(17, 'Mochilas y Valijas', 'fa-solid fa-suitcase', 'bg-slate-500/10', 'text-slate-400'),
(18, 'Modulos', 'fa-solid fa-cubes', 'bg-purple-500/10', 'text-purple-400'),
(19, 'Modulos Para Negocio', 'fa-solid fa-shop', 'bg-slate-500/10', 'text-slate-400'),
(20, 'Para tu Jardín', 'fa-solid fa-tree', 'bg-green-500/10', 'text-green-400'),
(21, 'Parlantes', 'fa-solid fa-volume-high', 'bg-indigo-500/10', 'text-indigo-400'),
(22, 'Parlantes BT', 'fa-brands fa-bluetooth', 'bg-indigo-500/10', 'text-indigo-400'),
(23, 'Placares', 'fa-solid fa-door-closed', 'bg-purple-500/10', 'text-purple-400'),
(24, 'Planchas', 'fa-solid fa-shirt', 'bg-blue-500/10', 'text-blue-400'),
(25, 'Planchitas', 'fa-solid fa-fire', 'bg-pink-500/10', 'text-pink-400'),
(26, 'Relojes Smart', 'fa-solid fa-clock', 'bg-slate-500/10', 'text-slate-400'),
(27, 'Somier Bari', 'fa-solid fa-bed', 'bg-purple-500/10', 'text-purple-400'),
(28, 'Tanques y Bombas', 'fa-solid fa-water', 'bg-cyan-500/10', 'text-cyan-400'),
(29, 'Termotanques ', 'fa-solid fa-temperature-full', 'bg-orange-500/10', 'text-orange-400'),
(30, 'Ventiladores Turbos', 'fa-solid fa-fan', 'bg-teal-500/10', 'text-teal-400'),
(31, 'Ventiladores de Pared', 'fa-solid fa-fan', 'bg-teal-500/10', 'text-teal-400'),
(32, 'Ventiladores de Pie', 'fa-solid fa-fan', 'bg-teal-500/10', 'text-teal-400'),
(33, 'Batidoras', 'fa-solid fa-blender', 'bg-orange-500/10', 'text-orange-400'),
(34, 'Celulares', 'fa-solid fa-mobile-screen', 'bg-blue-500/10', 'text-blue-400'),
(35, 'Cocinas y Otros', 'fa-solid fa-fire-burner', 'bg-orange-500/10', 'text-orange-400'),
(36, 'Freezer', 'fa-solid fa-temperature-low', 'bg-cyan-500/10', 'text-cyan-400'),
(37, 'Somier Zafiro', 'fa-solid fa-bed', 'bg-purple-500/10', 'text-purple-400');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cierres_semanales`
--

CREATE TABLE `cierres_semanales` (
  `id` int NOT NULL,
  `zona` varchar(50) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `saldo_favor` decimal(10,2) DEFAULT '0.00',
  `saldo_concepto` varchar(255) DEFAULT NULL,
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `descuento_creditos` decimal(10,2) DEFAULT '0.00',
  `descuento_creditos_concepto` varchar(255) DEFAULT NULL,
  `valor_hora` decimal(10,2) DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `cierres_semanales`
--

INSERT INTO `cierres_semanales` (`id`, `zona`, `fecha_inicio`, `saldo_favor`, `saldo_concepto`, `fecha_creacion`, `descuento_creditos`, `descuento_creditos_concepto`, `valor_hora`) VALUES
(1, 'Zona 4a6', '2025-12-29', '74000.00', 'varios', '2026-01-09 15:39:11', '120000.00', 'Vales', '0.00'),
(2, 'Zona 3', '2025-12-01', '158000.00', 'ventas y entregas', '2026-01-09 15:54:43', '308100.00', 'Vale', '0.00'),
(3, 'Zona 3', '2025-12-08', '0.00', '', '2026-01-09 15:58:59', '0.00', '', '0.00'),
(4, 'Zona 3', '2025-12-22', '0.00', '', '2026-01-09 16:06:58', '25000.00', 'a cuenta de los creditos', '0.00'),
(5, 'Zona 3', '2025-12-15', '83200.00', 'Ventas y extras', '2026-01-09 16:17:31', '110800.00', 'Adelantos y creditos ', '0.00'),
(6, 'Zona 2', '2025-12-22', '50000.00', 'extras', '2026-01-09 16:21:03', '53000.00', 'creditos', '0.00'),
(7, 'Zona 1', '2025-12-01', '112000.00', 'Ventas', '2026-01-09 16:23:08', '127500.00', 'Creditos y Vale', '0.00'),
(8, 'Zona 1', '2025-12-08', '131100.00', 'Ventas y entregas', '2026-01-09 16:26:36', '97500.00', 'Creditos', '0.00'),
(9, 'Zona 1', '2025-12-15', '227750.00', 'Extras Gral', '2026-01-09 16:29:52', '97500.00', 'Creditos', '0.00'),
(10, 'Zona 1', '2025-12-22', '25000.00', 'extras', '2026-01-09 16:32:05', '0.00', '', '0.00'),
(11, 'Zona 2', '2025-12-15', '221500.00', 'varios', '2026-01-09 16:37:36', '82000.00', 'creditos', '0.00'),
(12, 'Zona 2', '2025-12-08', '40000.00', 'ventas', '2026-01-09 16:39:41', '34500.00', 'vales', '0.00'),
(13, 'Zona 2', '2025-12-01', '39000.00', 'ventas', '2026-01-09 16:41:15', '0.00', '', '0.00'),
(14, 'Zona 1', '2026-01-05', '146750.00', 'Ventas', '2026-01-10 14:58:34', '97500.00', '', '0.00'),
(17, 'Zona 2', '2026-01-05', '34200.00', 'ventas + verificaciones', '2026-01-10 15:06:01', '91000.00', 'Creditos', '0.00'),
(20, 'Zona 3', '2026-01-05', '19200.00', 'Ventas - Gonzalez Nicolas', '2026-01-10 15:11:21', '27500.00', 'Creditos', '0.00'),
(24, 'Zona 4a6', '2026-01-05', '165000.00', 'SANTIAGO Y FAMAILLA', '2026-01-12 13:39:00', '0.00', '', '0.00'),
(29, 'Zona 2', '2026-01-12', '0.00', '', '2026-01-13 12:24:00', '64000.00', 'Adlenatos', '0.00'),
(32, 'Zona 1', '2026-01-12', '0.00', '', '2026-01-13 12:32:28', '64000.00', 'Adelantos', '0.00'),
(34, 'Zona 1', '2026-01-13', '0.00', '', '2026-01-19 12:59:23', '0.00', '', '0.00'),
(37, 'Zona 1', '2026-01-14', '0.00', '', '2026-01-19 12:59:55', '0.00', '', '0.00'),
(41, 'Zona 3', '2026-01-12', '32500.00', 'Venta Aire', '2026-01-19 13:31:17', '0.00', '', '0.00'),
(43, 'Zona 2', '2026-01-19', '0.00', '', '2026-01-26 14:10:29', '0.00', '', '0.00'),
(46, 'Zona 1', '2026-01-26', '62500.00', 'Ventas', '2026-01-26 14:10:49', '0.00', '', '0.00'),
(49, 'Zona 1', '2026-01-19', '178100.00', 'venta+intereses', '2026-01-26 14:17:48', '0.00', '', '0.00'),
(52, 'Zona 1', '2026-09-19', '0.00', '', '2026-01-26 14:19:59', '0.00', '', '0.00'),
(55, 'Zona 3', '2026-01-19', '94300.00', 'VENTAS', '2026-01-26 14:32:14', '0.00', '', '0.00'),
(58, 'Zona 3', '2026-01-26', '175600.00', 'Comisiones', '2026-01-26 14:37:34', '0.00', '', '0.00'),
(60, 'Zona 2', '2026-01-26', '0.00', '', '2026-02-02 13:20:33', '0.00', '', '0.00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalles_diarios`
--

CREATE TABLE `detalles_diarios` (
  `id` int NOT NULL,
  `cierre_id` int NOT NULL,
  `dia_semana` enum('LUNES','MARTES','MIERCOLES','JUEVES','VIERNES','SABADO') NOT NULL,
  `efectivo` decimal(10,2) DEFAULT '0.00',
  `transferencia` decimal(10,2) DEFAULT '0.00',
  `gasto_monto` decimal(10,2) DEFAULT '0.00',
  `gasto_concepto` varchar(255) DEFAULT NULL,
  `hora_entrada` time DEFAULT NULL,
  `hora_salida` time DEFAULT NULL,
  `hora_entrada_tarde` time DEFAULT NULL,
  `hora_salida_tarde` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `detalles_diarios`
--

INSERT INTO `detalles_diarios` (`id`, `cierre_id`, `dia_semana`, `efectivo`, `transferencia`, `gasto_monto`, `gasto_concepto`, `hora_entrada`, `hora_salida`, `hora_entrada_tarde`, `hora_salida_tarde`) VALUES
(1, 1, 'SABADO', '327400.00', '491000.00', '0.00', '', NULL, NULL, NULL, NULL),
(3, 3, 'LUNES', '178000.00', '325000.00', '0.00', '', NULL, NULL, NULL, NULL),
(4, 3, 'MARTES', '463000.00', '95000.00', '6000.00', 'Nafta', NULL, NULL, NULL, NULL),
(5, 3, 'MIERCOLES', '263500.00', '107000.00', '0.00', '', NULL, NULL, NULL, NULL),
(6, 3, 'JUEVES', '199000.00', '153000.00', '57500.00', 'nafta y vale', NULL, NULL, NULL, NULL),
(7, 3, 'VIERNES', '370200.00', '317500.00', '0.00', '', NULL, NULL, NULL, NULL),
(8, 3, 'SABADO', '1804400.00', '523000.00', '0.00', '', NULL, NULL, NULL, NULL),
(9, 4, 'LUNES', '628500.00', '570000.00', '17300.00', '4 de nafta y comida', NULL, NULL, NULL, NULL),
(10, 4, 'MARTES', '132000.00', '395000.00', '5000.00', 'nafta', NULL, NULL, NULL, NULL),
(11, 4, 'MIERCOLES', '0.00', '0.00', '0.00', '', NULL, NULL, NULL, NULL),
(12, 4, 'JUEVES', '0.00', '0.00', '0.00', '', NULL, NULL, NULL, NULL),
(13, 4, 'VIERNES', '471500.00', '368000.00', '600.00', 'faltan', NULL, NULL, NULL, NULL),
(14, 4, 'SABADO', '1414000.00', '541700.00', '15500.00', 'vale', NULL, NULL, NULL, NULL),
(15, 5, 'LUNES', '587500.00', '390000.00', '0.00', '', NULL, NULL, NULL, NULL),
(16, 5, 'MARTES', '252000.00', '143000.00', '0.00', '', NULL, NULL, NULL, NULL),
(17, 5, 'MIERCOLES', '72000.00', '205000.00', '0.00', '', NULL, NULL, NULL, NULL),
(18, 5, 'JUEVES', '142000.00', '366000.00', '0.00', '', NULL, NULL, NULL, NULL),
(19, 5, 'VIERNES', '388500.00', '40000.00', '0.00', '', NULL, NULL, NULL, NULL),
(20, 5, 'SABADO', '886000.00', '919200.00', '0.00', '', NULL, NULL, NULL, NULL),
(21, 6, 'LUNES', '304100.00', '567000.00', '0.00', '', NULL, NULL, NULL, NULL),
(22, 6, 'MARTES', '200000.00', '200000.00', '0.00', '', NULL, NULL, NULL, NULL),
(23, 6, 'MIERCOLES', '0.00', '0.00', '0.00', '', NULL, NULL, NULL, NULL),
(24, 6, 'JUEVES', '0.00', '0.00', '0.00', '', NULL, NULL, NULL, NULL),
(25, 6, 'VIERNES', '351000.00', '286000.00', '0.00', '', NULL, NULL, NULL, NULL),
(26, 6, 'SABADO', '1020100.00', '886200.00', '0.00', '', NULL, NULL, NULL, NULL),
(27, 7, 'LUNES', '624000.00', '250680.00', '0.00', '', NULL, NULL, NULL, NULL),
(28, 7, 'MARTES', '337500.00', '648450.00', '0.00', '', NULL, NULL, NULL, NULL),
(29, 7, 'MIERCOLES', '246700.00', '295800.00', '0.00', '', NULL, NULL, NULL, NULL),
(30, 7, 'JUEVES', '44000.00', '0.00', '0.00', '', NULL, NULL, NULL, NULL),
(31, 7, 'VIERNES', '154000.00', '441000.00', '0.00', '', NULL, NULL, NULL, NULL),
(32, 7, 'SABADO', '1154000.00', '1136400.00', '0.00', '', NULL, NULL, NULL, NULL),
(33, 8, 'LUNES', '469200.00', '511000.00', '0.00', '', NULL, NULL, NULL, NULL),
(34, 8, 'MARTES', '297000.00', '1064900.00', '0.00', '', NULL, NULL, NULL, NULL),
(35, 8, 'MIERCOLES', '34000.00', '313000.00', '0.00', '', NULL, NULL, NULL, NULL),
(36, 8, 'JUEVES', '180900.00', '67000.00', '0.00', '', NULL, NULL, NULL, NULL),
(37, 8, 'VIERNES', '262000.00', '401400.00', '0.00', '', NULL, NULL, NULL, NULL),
(38, 8, 'SABADO', '1056800.00', '1196880.00', '0.00', '', NULL, NULL, NULL, NULL),
(39, 9, 'LUNES', '388100.00', '292760.00', '0.00', '', NULL, NULL, NULL, NULL),
(40, 9, 'MARTES', '288200.00', '298000.00', '0.00', '', NULL, NULL, NULL, NULL),
(41, 9, 'MIERCOLES', '129500.00', '256000.00', '0.00', '', NULL, NULL, NULL, NULL),
(42, 9, 'JUEVES', '123000.00', '279500.00', '0.00', '', NULL, NULL, NULL, NULL),
(43, 9, 'VIERNES', '140000.00', '450000.00', '0.00', '', NULL, NULL, NULL, NULL),
(44, 9, 'SABADO', '1190725.00', '1475000.00', '0.00', '', NULL, NULL, NULL, NULL),
(45, 10, 'LUNES', '255000.00', '465000.00', '0.00', '', NULL, NULL, NULL, NULL),
(46, 10, 'MARTES', '116000.00', '134000.00', '0.00', '', NULL, NULL, NULL, NULL),
(47, 10, 'MIERCOLES', '510000.00', '531700.00', '0.00', '', NULL, NULL, NULL, NULL),
(48, 10, 'JUEVES', '0.00', '0.00', '0.00', '', NULL, NULL, NULL, NULL),
(49, 10, 'VIERNES', '0.00', '0.00', '0.00', '', NULL, NULL, NULL, NULL),
(50, 10, 'SABADO', '1268000.00', '1319000.00', '0.00', '', NULL, NULL, NULL, NULL),
(51, 2, 'LUNES', '454500.00', '442000.00', '0.00', '', NULL, NULL, NULL, NULL),
(52, 2, 'MARTES', '330000.00', '90000.00', '0.00', '', NULL, NULL, NULL, NULL),
(53, 2, 'MIERCOLES', '135500.00', '176000.00', '0.00', '', NULL, NULL, NULL, NULL),
(54, 2, 'JUEVES', '180000.00', '146000.00', '0.00', '', NULL, NULL, NULL, NULL),
(55, 2, 'VIERNES', '454300.00', '359860.00', '0.00', '', NULL, NULL, NULL, NULL),
(56, 2, 'SABADO', '1214100.00', '916600.00', '0.00', '', NULL, NULL, NULL, NULL),
(57, 11, 'LUNES', '253500.00', '132200.00', '0.00', '', NULL, NULL, NULL, NULL),
(58, 11, 'MARTES', '110000.00', '273000.00', '0.00', '', NULL, NULL, NULL, NULL),
(59, 11, 'MIERCOLES', '39000.00', '120000.00', '0.00', '', NULL, NULL, NULL, NULL),
(60, 11, 'JUEVES', '39000.00', '163000.00', '0.00', '', NULL, NULL, NULL, NULL),
(61, 11, 'VIERNES', '387000.00', '644500.00', '0.00', '', NULL, NULL, NULL, NULL),
(62, 11, 'SABADO', '650500.00', '525200.00', '0.00', '', NULL, NULL, NULL, NULL),
(63, 12, 'LUNES', '303000.00', '38500.00', '0.00', '', NULL, NULL, NULL, NULL),
(64, 12, 'MARTES', '225000.00', '233000.00', '0.00', '', NULL, NULL, NULL, NULL),
(65, 12, 'MIERCOLES', '322000.00', '15000.00', '0.00', '', NULL, NULL, NULL, NULL),
(66, 12, 'JUEVES', '0.00', '98000.00', '0.00', '', NULL, NULL, NULL, NULL),
(67, 12, 'VIERNES', '209000.00', '384500.00', '0.00', '', NULL, NULL, NULL, NULL),
(68, 12, 'SABADO', '774500.00', '669700.00', '0.00', '', NULL, NULL, NULL, NULL),
(69, 13, 'LUNES', '181500.00', '37700.00', '0.00', '', NULL, NULL, NULL, NULL),
(70, 13, 'MARTES', '259300.00', '169500.00', '0.00', '', NULL, NULL, NULL, NULL),
(71, 13, 'MIERCOLES', '103000.00', '220000.00', '0.00', '', NULL, NULL, NULL, NULL),
(72, 13, 'JUEVES', '113000.00', '0.00', '0.00', '', NULL, NULL, NULL, NULL),
(73, 13, 'VIERNES', '441000.00', '608500.00', '0.00', '', NULL, NULL, NULL, NULL),
(74, 13, 'SABADO', '730700.00', '558700.00', '0.00', '', NULL, NULL, NULL, NULL),
(77, 14, 'LUNES', '172000.00', '427000.00', '5000.00', 'Faltante de el ', NULL, NULL, NULL, NULL),
(80, 14, 'MARTES', '489000.00', '275260.00', '69000.00', 'Diaz Veronica no rendido', NULL, NULL, NULL, NULL),
(83, 14, 'MIERCOLES', '679000.00', '247300.00', '50000.00', 'DENTISTA', NULL, NULL, NULL, NULL),
(86, 14, 'JUEVES', '102000.00', '198225.00', '0.00', '', NULL, NULL, NULL, NULL),
(89, 14, 'VIERNES', '140800.00', '308000.00', '0.00', '', NULL, NULL, NULL, NULL),
(95, 17, 'LUNES', '559900.00', '295000.00', '0.00', '', NULL, NULL, NULL, NULL),
(98, 17, 'MARTES', '218500.00', '158500.00', '0.00', '', NULL, NULL, NULL, NULL),
(101, 17, 'MIERCOLES', '150000.00', '430500.00', '0.00', '', NULL, NULL, NULL, NULL),
(104, 17, 'JUEVES', '0.00', '104000.00', '0.00', '', NULL, NULL, NULL, NULL),
(107, 17, 'VIERNES', '432000.00', '780000.00', '0.00', '', NULL, NULL, NULL, NULL),
(110, 17, 'SABADO', '767000.00', '573000.00', '0.00', '', NULL, NULL, NULL, NULL),
(113, 20, 'LUNES', '798500.00', '285500.00', '27500.00', 'adelanto comida y nafta ', NULL, NULL, NULL, NULL),
(116, 20, 'MARTES', '586000.00', '370500.00', '3000.00', 'nafta', NULL, NULL, NULL, NULL),
(119, 20, 'MIERCOLES', '60000.00', '43000.00', '30000.00', 'Gastos', NULL, NULL, NULL, NULL),
(122, 20, 'JUEVES', '282000.00', '282000.00', '3000.00', 'Gastos', NULL, NULL, NULL, NULL),
(125, 20, 'VIERNES', '532000.00', '596400.00', '7000.00', 'adelanto ', NULL, NULL, NULL, NULL),
(128, 20, 'SABADO', '1555500.00', '1114700.00', '275000.00', 'Gastos', NULL, NULL, NULL, NULL),
(130, 14, 'SABADO', '1863100.00', '2378100.00', '0.00', '', NULL, NULL, NULL, NULL),
(135, 24, 'SABADO', '543000.00', '973500.00', '0.00', '', NULL, NULL, NULL, NULL),
(140, 29, 'LUNES', '359500.00', '226000.00', '64000.00', 'Adelanto', NULL, NULL, NULL, NULL),
(143, 32, 'LUNES', '80000.00', '768500.00', '0.00', '', NULL, NULL, NULL, NULL),
(144, 32, 'MARTES', '254000.00', '165000.00', '0.00', '', NULL, NULL, NULL, NULL),
(145, 29, 'MARTES', '388000.00', '139000.00', '0.00', '', NULL, NULL, NULL, NULL),
(146, 29, 'MIERCOLES', '119000.00', '630000.00', '0.00', '', NULL, NULL, NULL, NULL),
(148, 34, 'MARTES', '183000.00', '612700.00', '0.00', '', NULL, NULL, NULL, NULL),
(151, 37, 'MIERCOLES', '317000.00', '700800.00', '0.00', '', NULL, NULL, NULL, NULL),
(155, 32, 'MIERCOLES', '435000.00', '168000.00', '30000.00', 'GASTOS', NULL, NULL, NULL, NULL),
(157, 32, 'JUEVES', '129000.00', '338500.00', '0.00', '', NULL, NULL, NULL, NULL),
(159, 32, 'VIERNES', '458500.00', '469500.00', '0.00', '', NULL, NULL, NULL, NULL),
(161, 32, 'SABADO', '1243430.00', '1060700.00', '0.00', '', NULL, NULL, NULL, NULL),
(163, 41, 'LUNES', '326000.00', '409500.00', '0.00', '', NULL, NULL, NULL, NULL),
(165, 41, 'MARTES', '254000.00', '165000.00', '0.00', '', NULL, NULL, NULL, NULL),
(167, 41, 'VIERNES', '390900.00', '449000.00', '0.00', '', NULL, NULL, NULL, NULL),
(169, 41, 'MIERCOLES', '435000.00', '168000.00', '30000.00', 'Gastos', NULL, NULL, NULL, NULL),
(171, 41, 'SABADO', '2221000.00', '596000.00', '48300.00', 'gastos varios', NULL, NULL, NULL, NULL),
(173, 29, 'VIERNES', '458500.00', '469500.00', '0.00', '', NULL, NULL, NULL, NULL),
(175, 29, 'JUEVES', '194000.00', '30000.00', '0.00', '', NULL, NULL, NULL, NULL),
(177, 29, 'SABADO', '796100.00', '873470.00', '0.00', '', NULL, NULL, NULL, NULL),
(178, 43, 'LUNES', '0.00', '0.00', '0.00', '', NULL, NULL, NULL, NULL),
(181, 43, 'MARTES', '0.00', '0.00', '0.00', '', NULL, NULL, NULL, NULL),
(184, 43, 'MIERCOLES', '279000.00', '78000.00', '2000.00', 'NAFTA', NULL, NULL, NULL, NULL),
(187, 43, 'JUEVES', '163000.00', '0.00', '0.00', '', NULL, NULL, NULL, NULL),
(190, 43, 'VIERNES', '600200.00', '399500.00', '5000.00', 'parche', NULL, NULL, NULL, NULL),
(193, 43, 'SABADO', '800500.00', '569200.00', '0.00', '', NULL, NULL, NULL, NULL),
(199, 49, 'LUNES', '353000.00', '335400.00', '0.00', '', NULL, NULL, NULL, NULL),
(202, 49, 'MARTES', '558000.00', '384000.00', '43000.00', 'RESPUESTO DE MOTO', NULL, NULL, NULL, NULL),
(205, 49, 'MIERCOLES', '40000.00', '326350.00', '0.00', '', NULL, NULL, NULL, NULL),
(208, 49, 'JUEVES', '286000.00', '160500.00', '0.00', '', NULL, NULL, NULL, NULL),
(211, 49, 'VIERNES', '643700.00', '390000.00', '5000.00', 'nafta', NULL, NULL, NULL, NULL),
(214, 52, 'SABADO', '1239650.00', '1193500.00', '0.00', '', NULL, NULL, NULL, NULL),
(217, 49, 'SABADO', '1239650.00', '1193500.00', '0.00', '', NULL, NULL, NULL, NULL),
(220, 55, 'LUNES', '377500.00', '386400.00', '0.00', '', NULL, NULL, NULL, NULL),
(223, 52, 'MIERCOLES', '279000.00', '78000.00', '2000.00', 'NAFTA', NULL, NULL, NULL, NULL),
(226, 55, 'MARTES', '558000.00', '384000.00', '43000.00', 'RESPUESTO DE MOTO', NULL, NULL, NULL, NULL),
(229, 55, 'MIERCOLES', '279000.00', '78000.00', '2000.00', 'NAFTA', NULL, NULL, NULL, NULL),
(235, 55, 'SABADO', '1316200.00', '1127300.00', '90000.00', 'FINDE', NULL, NULL, NULL, NULL),
(238, 55, 'VIERNES', '643700.00', '390000.00', '5000.00', 'NAFTA', NULL, NULL, NULL, NULL),
(240, 58, 'LUNES', '364500.00', '302000.00', '2000.00', 'gasto', NULL, NULL, NULL, NULL),
(243, 58, 'MARTES', '196000.00', '206200.00', '6000.00', 'nafta', NULL, NULL, NULL, NULL),
(246, 58, 'MIERCOLES', '315000.00', '138000.00', '11000.00', 'nafta + respuestos', NULL, NULL, NULL, NULL),
(249, 58, 'JUEVES', '191000.00', '116000.00', '0.00', '', NULL, NULL, NULL, NULL),
(252, 58, 'VIERNES', '425500.00', '380600.00', '28000.00', 'gastos', NULL, NULL, NULL, NULL),
(255, 58, 'SABADO', '1486000.00', '1048000.00', '75400.00', 'gastos', NULL, NULL, NULL, NULL),
(258, 60, 'LUNES', '388000.00', '300700.00', '0.00', '', NULL, NULL, NULL, NULL),
(260, 60, 'MARTES', '148000.00', '294000.00', '0.00', '', NULL, NULL, NULL, NULL),
(262, 60, 'MIERCOLES', '153000.00', '593000.00', '0.00', '', NULL, NULL, NULL, NULL),
(264, 60, 'JUEVES', '225000.00', '243350.00', '20000.00', 'gastos', NULL, NULL, NULL, NULL),
(266, 60, 'VIERNES', '387200.00', '426510.00', '0.00', '', NULL, NULL, NULL, NULL),
(268, 60, 'SABADO', '694600.00', '301700.00', '0.00', '', NULL, NULL, NULL, NULL),
(270, 46, 'LUNES', '309400.00', '266000.00', '0.00', '', NULL, NULL, NULL, NULL),
(272, 46, 'MARTES', '251000.00', '206000.00', '0.00', '', NULL, NULL, NULL, NULL),
(274, 46, 'MIERCOLES', '45000.00', '312200.00', '0.00', '', NULL, NULL, NULL, NULL),
(276, 46, 'JUEVES', '387200.00', '240000.00', '0.00', '', NULL, NULL, NULL, NULL),
(278, 46, 'VIERNES', '302000.00', '249500.00', '0.00', '', NULL, NULL, NULL, NULL),
(280, 46, 'SABADO', '1466735.00', '1475300.00', '0.00', '', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `flyers`
--

CREATE TABLE `flyers` (
  `id` int NOT NULL,
  `categoria_id` int DEFAULT NULL,
  `titulo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `imagen_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_subida` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `flyers`
--

INSERT INTO `flyers` (`id`, `categoria_id`, `titulo`, `imagen_url`, `fecha_subida`) VALUES
(2, 1, 'Aire Electra Ventana', 'uploads/698762f1d6d7b.png', '2026-02-07 16:06:11'),
(3, 1, 'Aire Split 3000 FG', 'uploads/6987630b501c0.png', '2026-02-07 16:06:35'),
(4, 2, 'B&D Portatil', 'uploads/6987633b68811.png', '2026-02-07 16:07:23'),
(5, 2, 'Philco 1.2', 'uploads/6987634ce26ee.png', '2026-02-07 16:07:40'),
(6, 2, 'B&D 1800W', 'uploads/6987635b46c74.png', '2026-02-07 16:07:55'),
(7, 2, 'Atma Inalambrica 2 en 1', 'uploads/6987636bf31a7.png', '2026-02-07 16:08:11'),
(8, 2, 'Ultracomb AS-6061 Robot', 'uploads/6987637f74508.png', '2026-02-07 16:08:31'),
(9, 2, 'Atma Robot', 'uploads/6987638dd15e3.png', '2026-02-07 16:08:45'),
(10, 2, 'Atma ATAR21C1 Robot', 'uploads/698763a25dbcb.png', '2026-02-07 16:09:06'),
(11, 3, '1', 'uploads/698763c1a307e.png', '2026-02-07 16:09:37'),
(12, 3, '2', 'uploads/698763c74061e.png', '2026-02-07 16:09:43'),
(13, 3, '3', 'uploads/698763ced8d59.png', '2026-02-07 16:09:50'),
(14, 3, '4', 'uploads/698763d829f30.png', '2026-02-07 16:10:00'),
(15, 3, '5', 'uploads/698763e1ac8ad.png', '2026-02-07 16:10:09'),
(16, 33, 'B&D 500w', 'uploads/698764317d6a4.jpg', '2026-02-07 16:11:29'),
(17, 33, 'Peabody PE-BM 1200w', 'uploads/698764429c437.jpg', '2026-02-07 16:11:46'),
(18, 33, 'Planetaria Liliana', 'uploads/69876451b79d4.jpg', '2026-02-07 16:12:01'),
(19, 33, 'Planetaria Liliana 800W', 'uploads/698764635ee1a.jpg', '2026-02-07 16:12:19'),
(20, 33, 'B&D MX 500w', 'uploads/6987647277299.jpg', '2026-02-07 16:12:34'),
(21, 33, 'Peabody 300W Con Base', 'uploads/6987648676980.jpg', '2026-02-07 16:12:54'),
(22, 33, 'Peabody 1000W', 'uploads/6987650ada739.jpg', '2026-02-07 16:15:06'),
(23, 33, 'Peabody 1000W', 'uploads/6987651bbc0dd.jpg', '2026-02-07 16:15:23'),
(24, 33, 'Peabody 1000W', 'uploads/6987652648fe9.jpg', '2026-02-07 16:15:34'),
(25, 33, 'Planetaria LQ 1000W', 'uploads/6987654c22bd8.png', '2026-02-07 16:16:12'),
(26, 33, 'Planetaria LQ 1000W', 'uploads/6987655398ef8.png', '2026-02-07 16:16:19'),
(27, 33, 'Planetaria LQ 1000W', 'uploads/6987655a11a8c.png', '2026-02-07 16:16:26'),
(28, 4, 'ATMA FILTRO', 'uploads/6987684922743.png', '2026-02-07 16:28:57'),
(29, 4, 'LILIANA FILTRO', 'uploads/69876856e9603.png', '2026-02-07 16:29:10'),
(30, 4, 'LILIANA AC964 FILTRO', 'uploads/6987686abb3a5.png', '2026-02-07 16:29:30'),
(31, 4, 'ATMA DE FILTRO', 'uploads/6987687b02408.png', '2026-02-07 16:29:47'),
(32, 4, 'Peabody PE-CT4207 Filtro', 'uploads/69876a581e501.png', '2026-02-07 16:37:44'),
(33, 4, 'Peabody MK01 FILTRO', 'uploads/69876a6a5c86a.png', '2026-02-07 16:38:02'),
(34, 4, 'Atma Digital Express', 'uploads/69876a7e0a47b.png', '2026-02-07 16:38:22'),
(35, 34, 'Samsung A16', 'uploads/69876ab6d565e.png', '2026-02-07 16:39:18'),
(36, 34, 'Samsung A07', 'uploads/69876ac4abe9d.png', '2026-02-07 16:39:32'),
(37, 34, 'Redmi 15C', 'uploads/69876ad136bbd.png', '2026-02-07 16:39:45'),
(38, 35, 'Anafe Electrico', 'uploads/69876b1948040.png', '2026-02-07 16:40:57'),
(39, 35, 'Anafe 1 Hornalla', 'uploads/69876b2ad3b3a.png', '2026-02-07 16:41:14'),
(40, 35, 'Anafe Electrico  2 hornallas', 'uploads/69876d0a61bce.png', '2026-02-07 16:49:14'),
(41, 35, 'Anafe 5 Hornallas ', 'uploads/69876d1bcd08a.png', '2026-02-07 16:49:31'),
(42, 35, 'Cocina Philco Electica', 'uploads/69876d31db1fe.png', '2026-02-07 16:49:53'),
(43, 35, 'Cocina Drean Multigas', 'uploads/69876d420bbb4.png', '2026-02-07 16:50:10'),
(44, 35, 'Eslabon de Lujo Gas Natural', 'uploads/69876d57c4650.png', '2026-02-07 16:50:31'),
(45, 35, 'Whirlpool Multigas', 'uploads/69876d6b4d521.png', '2026-02-07 16:50:51'),
(46, 5, 'Cortapelos', 'uploads/69876e7b820cb_0.png', '2026-02-07 16:55:23'),
(47, 5, 'Cortapelos', 'uploads/69876e7b83bae_1.png', '2026-02-07 16:55:23'),
(48, 5, 'Cortapelos', 'uploads/69876e7b84077_2.png', '2026-02-07 16:55:23'),
(49, 5, 'Cortapelos', 'uploads/69876e7b84571_3.png', '2026-02-07 16:55:23'),
(50, 5, 'Cortapelos', 'uploads/69876e7b84c35_4.png', '2026-02-07 16:55:23'),
(51, 5, 'Cortapelos', 'uploads/69876e7b850df_5.png', '2026-02-07 16:55:23'),
(52, 5, 'Cortapelos', 'uploads/69876e7b855b1_6.png', '2026-02-07 16:55:23'),
(53, 5, 'Cortapelos', 'uploads/69876e7b85aa7_7.png', '2026-02-07 16:55:23'),
(54, 5, 'Cortapelos', 'uploads/69876e7b85f63_8.png', '2026-02-07 16:55:23'),
(55, 5, 'Cortapelos', 'uploads/69876e7b863e9_9.png', '2026-02-07 16:55:23'),
(56, 5, 'Cortapelos', 'uploads/69876e7b868cf_10.png', '2026-02-07 16:55:23'),
(57, 5, 'Cortapelos', 'uploads/69876e7b86d62_11.png', '2026-02-07 16:55:23'),
(58, 5, 'Cortapelos', 'uploads/69876e7b872b6_12.png', '2026-02-07 16:55:23'),
(59, 5, 'Cortapelos', 'uploads/69876e7b877ed_13.png', '2026-02-07 16:55:23'),
(60, 5, 'Cortapelos', 'uploads/69876e7b87c49_14.png', '2026-02-07 16:55:23'),
(61, 5, 'Cortapelos', 'uploads/69876e7b8809a_15.png', '2026-02-07 16:55:23'),
(62, 5, 'Cortapelos', 'uploads/69876e7b88695_16.png', '2026-02-07 16:55:23'),
(63, 5, 'Cortapelos', 'uploads/69876e7b88ae2_17.png', '2026-02-07 16:55:23'),
(64, 5, 'Cortapelos', 'uploads/69876e7b88f24_18.png', '2026-02-07 16:55:23'),
(65, 5, 'Cortapelos', 'uploads/69876e7b893fc_19.png', '2026-02-07 16:55:23'),
(66, 6, 'Extractores', 'uploads/69876ead2396e_0.png', '2026-02-07 16:56:13'),
(67, 6, 'Extractores', 'uploads/69876ead24200_1.png', '2026-02-07 16:56:13'),
(68, 6, 'Extractores', 'uploads/69876ead246a1_2.png', '2026-02-07 16:56:13'),
(69, 6, 'Extractores', 'uploads/69876ead24c04_3.png', '2026-02-07 16:56:13'),
(70, 6, 'Extractores', 'uploads/69876ead2532b_4.png', '2026-02-07 16:56:13'),
(71, 6, 'Extractores', 'uploads/69876ead259f6_5.png', '2026-02-07 16:56:13'),
(72, 36, 'Freezer de Pozo', 'uploads/69876edcdf347_0.png', '2026-02-07 16:57:00'),
(73, 36, 'Freezer de Pozo', 'uploads/69876edcdfe67_1.png', '2026-02-07 16:57:00'),
(74, 36, 'Freezer de Pozo', 'uploads/69876edce0415_2.png', '2026-02-07 16:57:00'),
(75, 36, 'Freezer de Pozo', 'uploads/69876edce0b88_3.png', '2026-02-07 16:57:00'),
(76, 36, 'Freezer de Pozo', 'uploads/69876edce1004_4.png', '2026-02-07 16:57:00'),
(77, 36, 'Freezer de Pozo', 'uploads/69876edce1438_5.png', '2026-02-07 16:57:00'),
(78, 36, 'Freezer de Pozo', 'uploads/69876edce182c_6.png', '2026-02-07 16:57:00'),
(79, 7, 'Freidoras', 'uploads/69876f1c68aa7_0.png', '2026-02-07 16:58:04'),
(80, 7, 'Freidoras', 'uploads/69876f1c69555_1.png', '2026-02-07 16:58:04'),
(81, 7, 'Freidoras', 'uploads/69876f1c69d68_2.png', '2026-02-07 16:58:04'),
(82, 7, 'Freidoras', 'uploads/69876f1c6a3db_3.png', '2026-02-07 16:58:04'),
(83, 7, 'Freidoras', 'uploads/69876f1c6a867_4.png', '2026-02-07 16:58:04'),
(84, 7, 'Freidoras', 'uploads/69876f1c6ad26_5.png', '2026-02-07 16:58:04'),
(85, 7, 'Freidoras', 'uploads/69876f1c6b20b_6.png', '2026-02-07 16:58:04'),
(86, 8, 'Varias', 'uploads/69876f353ddf2_0.png', '2026-02-07 16:58:29'),
(87, 8, 'Varias', 'uploads/69876f353e3a1_1.png', '2026-02-07 16:58:29'),
(88, 8, 'Varias', 'uploads/69876f353ea61_2.png', '2026-02-07 16:58:29'),
(89, 8, 'Varias', 'uploads/69876f353f563_3.png', '2026-02-07 16:58:29'),
(90, 8, 'Varias', 'uploads/69876f353fa19_4.png', '2026-02-07 16:58:29'),
(91, 8, 'Varias', 'uploads/69876f353feec_5.png', '2026-02-07 16:58:29'),
(92, 9, 'Exhibidoras', 'uploads/69876f5d8cdce_0.png', '2026-02-07 16:59:09'),
(93, 9, 'Exhibidoras', 'uploads/69876f5d8d33d_1.png', '2026-02-07 16:59:09'),
(94, 9, 'Exhibidoras', 'uploads/69876f5d8d91c_2.png', '2026-02-07 16:59:09'),
(95, 10, 'Herramientas de Jardineria', 'uploads/69876f9324470_0.png', '2026-02-07 17:00:03'),
(96, 10, 'Herramientas de Jardineria', 'uploads/69876f9325677_1.png', '2026-02-07 17:00:03'),
(97, 10, 'Herramientas de Jardineria', 'uploads/69876f9325be9_2.png', '2026-02-07 17:00:03'),
(98, 10, 'Herramientas de Jardineria', 'uploads/69876f9326147_3.png', '2026-02-07 17:00:03'),
(99, 10, 'Herramientas de Jardineria', 'uploads/69876f9326659_4.png', '2026-02-07 17:00:03'),
(100, 10, 'Herramientas de Jardineria', 'uploads/69876f9326b99_5.png', '2026-02-07 17:00:03'),
(101, 10, 'Herramientas de Jardineria', 'uploads/69876f9327146_6.png', '2026-02-07 17:00:03'),
(102, 11, 'Jugueras', 'uploads/6987700fda8ee_0.png', '2026-02-07 17:02:07'),
(103, 11, 'Jugueras', 'uploads/6987700fdb427_1.png', '2026-02-07 17:02:07'),
(104, 11, 'Jugueras', 'uploads/6987700fdb967_2.png', '2026-02-07 17:02:07'),
(105, 11, 'Jugueras', 'uploads/6987700fdbf63_3.png', '2026-02-07 17:02:07'),
(106, 11, 'Jugueras', 'uploads/6987700fdc389_4.png', '2026-02-07 17:02:07'),
(107, 11, 'Jugueras', 'uploads/6987700fdc7ba_5.png', '2026-02-07 17:02:07'),
(108, 11, 'Jugueras', 'uploads/6987700fdcbbc_6.png', '2026-02-07 17:02:07'),
(109, 13, '-', 'uploads/69877030a4fd7_0.png', '2026-02-07 17:02:40'),
(110, 13, '-', 'uploads/69877030a58eb_1.png', '2026-02-07 17:02:40'),
(111, 13, '-', 'uploads/69877030a5dcc_2.png', '2026-02-07 17:02:40'),
(112, 13, '-', 'uploads/69877030a627d_3.png', '2026-02-07 17:02:40'),
(113, 13, '-', 'uploads/69877030a6793_4.png', '2026-02-07 17:02:40'),
(114, 13, '-', 'uploads/69877030a6c00_5.png', '2026-02-07 17:02:40'),
(115, 13, '-', 'uploads/69877030a70c3_6.png', '2026-02-07 17:02:40'),
(116, 13, '-', 'uploads/69877030a75ff_7.png', '2026-02-07 17:02:40'),
(117, 13, '-', 'uploads/69877030a7a88_8.png', '2026-02-07 17:02:40'),
(118, 13, '-', 'uploads/69877030a7f13_9.png', '2026-02-07 17:02:40'),
(119, 13, '-', 'uploads/69877030a83e5_10.png', '2026-02-07 17:02:40'),
(120, 13, '-', 'uploads/69877030a8ef1_11.png', '2026-02-07 17:02:40'),
(121, 13, '-', 'uploads/69877030a93a3_12.png', '2026-02-07 17:02:40'),
(122, 13, '-', 'uploads/69877030a9862_13.png', '2026-02-07 17:02:40'),
(123, 13, '-', 'uploads/69877030a9cf4_14.png', '2026-02-07 17:02:40'),
(124, 13, '-', 'uploads/69877030aa1f3_15.png', '2026-02-07 17:02:40'),
(125, 14, 'Licuadoras', 'uploads/6987704ec80cc_0.png', '2026-02-07 17:03:10'),
(126, 14, 'Licuadoras', 'uploads/6987704ec9449_1.png', '2026-02-07 17:03:10'),
(127, 14, 'Licuadoras', 'uploads/6987704ec9c42_2.png', '2026-02-07 17:03:10'),
(128, 14, 'Licuadoras', 'uploads/6987704eccde4_3.png', '2026-02-07 17:03:10'),
(129, 14, 'Licuadoras', 'uploads/6987704ecd2bd_4.png', '2026-02-07 17:03:10'),
(130, 14, 'Licuadoras', 'uploads/6987704ece784_5.png', '2026-02-07 17:03:10'),
(131, 14, 'Licuadoras', 'uploads/6987704eceee8_6.png', '2026-02-07 17:03:10'),
(132, 14, 'Licuadoras', 'uploads/6987704ecf7de_7.png', '2026-02-07 17:03:10'),
(133, 14, 'Licuadoras', 'uploads/6987704ecfcdf_8.png', '2026-02-07 17:03:10'),
(134, 14, 'Licuadoras', 'uploads/6987704ed0246_9.png', '2026-02-07 17:03:10'),
(135, 14, 'Licuadoras', 'uploads/6987704ed0bfe_10.png', '2026-02-07 17:03:10'),
(136, 14, 'Licuadoras', 'uploads/6987704ed194c_11.png', '2026-02-07 17:03:10'),
(137, 14, 'Licuadoras', 'uploads/6987704ed1e5c_12.png', '2026-02-07 17:03:10'),
(138, 14, 'Licuadoras', 'uploads/6987704ed2360_13.png', '2026-02-07 17:03:10'),
(139, 14, 'Licuadoras', 'uploads/6987704ed286a_14.png', '2026-02-07 17:03:10'),
(140, 15, '-', 'uploads/69877171c8699_0.png', '2026-02-07 17:08:01'),
(141, 15, '-', 'uploads/69877171c93e9_1.png', '2026-02-07 17:08:01'),
(142, 15, '-', 'uploads/69877171c9941_2.png', '2026-02-07 17:08:01'),
(143, 15, '-', 'uploads/69877171ca1ff_3.png', '2026-02-07 17:08:01'),
(144, 15, '-', 'uploads/69877171ca8aa_4.png', '2026-02-07 17:08:01'),
(145, 15, '-', 'uploads/69877171cad28_5.png', '2026-02-07 17:08:01'),
(146, 15, '-', 'uploads/69877171cb117_6.png', '2026-02-07 17:08:01'),
(147, 19, '-', 'uploads/6987718b4c42e_0.png', '2026-02-07 17:08:27'),
(148, 19, '-', 'uploads/6987718b4ca3c_1.png', '2026-02-07 17:08:27'),
(149, 19, '-', 'uploads/6987718b4cf17_2.png', '2026-02-07 17:08:27'),
(150, 19, '-', 'uploads/6987718b4d3e2_3.png', '2026-02-07 17:08:27'),
(151, 19, '-', 'uploads/6987718b4d882_4.png', '2026-02-07 17:08:27'),
(152, 19, '-', 'uploads/6987718b4e3bd_5.png', '2026-02-07 17:08:27'),
(153, 19, '-', 'uploads/6987718b4ea32_6.png', '2026-02-07 17:08:27'),
(154, 19, '-', 'uploads/6987718b4ef50_7.png', '2026-02-07 17:08:27'),
(155, 19, '-', 'uploads/6987718b4f50e_8.png', '2026-02-07 17:08:27'),
(156, 19, '-', 'uploads/6987718b4fa1d_9.png', '2026-02-07 17:08:27'),
(157, 19, '-', 'uploads/6987718b4ff14_10.png', '2026-02-07 17:08:27'),
(158, 19, '-', 'uploads/6987718b50385_11.png', '2026-02-07 17:08:27'),
(159, 18, '-', 'uploads/698771c67ff40_0.png', '2026-02-07 17:09:26'),
(160, 18, '-', 'uploads/698771c68045a_1.png', '2026-02-07 17:09:26'),
(161, 18, '-', 'uploads/698771c68088a_2.png', '2026-02-07 17:09:26'),
(162, 18, '-', 'uploads/698771c681092_3.png', '2026-02-07 17:09:26'),
(163, 18, '-', 'uploads/698771c68161b_4.png', '2026-02-07 17:09:26'),
(164, 18, '-', 'uploads/698771c681a7f_5.png', '2026-02-07 17:09:26'),
(165, 18, '-', 'uploads/698771c681ed9_6.png', '2026-02-07 17:09:26'),
(166, 18, '-', 'uploads/698771c682349_7.png', '2026-02-07 17:09:26'),
(167, 18, '-', 'uploads/698771c682765_8.png', '2026-02-07 17:09:26'),
(168, 18, '-', 'uploads/698771c682bde_9.png', '2026-02-07 17:09:26'),
(169, 18, '-', 'uploads/698771c683029_10.png', '2026-02-07 17:09:26'),
(170, 18, '-', 'uploads/698771c6834bb_11.png', '2026-02-07 17:09:26'),
(171, 16, '-', 'uploads/698771f818a42_0.png', '2026-02-07 17:10:16'),
(172, 16, '-', 'uploads/698771f81901f_1.png', '2026-02-07 17:10:16'),
(173, 16, '-', 'uploads/698771f81955f_2.png', '2026-02-07 17:10:16'),
(174, 16, '-', 'uploads/698771f819a5e_3.png', '2026-02-07 17:10:16'),
(175, 16, '-', 'uploads/698771f819feb_4.png', '2026-02-07 17:10:16'),
(176, 16, '-', 'uploads/698771f81a53e_5.png', '2026-02-07 17:10:16'),
(177, 16, '-', 'uploads/698771f81aa62_6.png', '2026-02-07 17:10:16'),
(178, 20, '|', 'uploads/69877269abbd6_0.png', '2026-02-07 17:12:09'),
(179, 20, '|', 'uploads/69877269ac256_1.png', '2026-02-07 17:12:09'),
(180, 20, '|', 'uploads/69877269ac6c7_2.png', '2026-02-07 17:12:09'),
(181, 20, '|', 'uploads/69877269acc88_3.png', '2026-02-07 17:12:09'),
(182, 20, '|', 'uploads/69877269ad10f_4.png', '2026-02-07 17:12:09'),
(183, 20, '|', 'uploads/69877269ad5b8_5.png', '2026-02-07 17:12:09'),
(184, 22, '|', 'uploads/6987729748280_0.png', '2026-02-07 17:12:55'),
(185, 22, '|', 'uploads/69877297487fd_1.png', '2026-02-07 17:12:55'),
(186, 22, '|', 'uploads/6987729748dba_2.png', '2026-02-07 17:12:55'),
(187, 22, '|', 'uploads/6987729749277_3.png', '2026-02-07 17:12:55'),
(188, 22, '|', 'uploads/6987729749796_4.png', '2026-02-07 17:12:55'),
(189, 22, '|', 'uploads/6987729749deb_5.png', '2026-02-07 17:12:55'),
(190, 22, '|', 'uploads/698772974a7a1_6.png', '2026-02-07 17:12:55'),
(191, 22, '|', 'uploads/698772974b05a_7.png', '2026-02-07 17:12:55'),
(192, 22, '|', 'uploads/698772974b62a_8.png', '2026-02-07 17:12:55'),
(193, 22, '|', 'uploads/698772974bae1_9.png', '2026-02-07 17:12:55'),
(194, 22, '|', 'uploads/698772974bfe7_10.png', '2026-02-07 17:12:55'),
(195, 22, '|', 'uploads/698772974c45a_11.png', '2026-02-07 17:12:55'),
(196, 22, '|', 'uploads/698772974c964_12.png', '2026-02-07 17:12:55'),
(197, 22, '|', 'uploads/698772974ce4f_13.png', '2026-02-07 17:12:55'),
(198, 22, '|', 'uploads/698772974d31a_14.png', '2026-02-07 17:12:55'),
(199, 23, '|', 'uploads/698772b7138c6_0.png', '2026-02-07 17:13:27'),
(200, 23, '|', 'uploads/698772b713e78_1.png', '2026-02-07 17:13:27'),
(201, 23, '|', 'uploads/698772b714327_2.png', '2026-02-07 17:13:27'),
(202, 23, '|', 'uploads/698772b714874_3.png', '2026-02-07 17:13:27'),
(203, 23, '|', 'uploads/698772b71512e_4.png', '2026-02-07 17:13:27'),
(204, 23, '|', 'uploads/698772b7157ea_5.png', '2026-02-07 17:13:27'),
(205, 23, '|', 'uploads/698772b715e75_6.png', '2026-02-07 17:13:27'),
(206, 23, '|', 'uploads/698772b716576_7.png', '2026-02-07 17:13:27'),
(207, 23, '|', 'uploads/698772b7169c3_8.png', '2026-02-07 17:13:27'),
(208, 23, '-', 'uploads/698772c6c72eb_0.png', '2026-02-07 17:13:42'),
(209, 24, '-', 'uploads/698772ed58327_0.png', '2026-02-07 17:14:21'),
(210, 24, '-', 'uploads/698772ed5893e_1.png', '2026-02-07 17:14:21'),
(211, 25, '|', 'uploads/698773113d38e_0.png', '2026-02-07 17:14:57'),
(212, 25, '|', 'uploads/698773113d87e_1.png', '2026-02-07 17:14:57'),
(213, 25, '|', 'uploads/698773113dd5b_2.png', '2026-02-07 17:14:57'),
(214, 25, '|', 'uploads/698773113e1ee_3.png', '2026-02-07 17:14:57'),
(215, 25, '|', 'uploads/698773113e6d1_4.png', '2026-02-07 17:14:57'),
(216, 25, '|', 'uploads/698773113ee82_5.png', '2026-02-07 17:14:57'),
(217, 25, '|', 'uploads/698773113f893_6.png', '2026-02-07 17:14:57'),
(218, 25, '|', 'uploads/698773113fe56_7.png', '2026-02-07 17:14:57'),
(219, 25, '|', 'uploads/698773114040a_8.png', '2026-02-07 17:14:57'),
(220, 25, '|', 'uploads/69877311408d5_9.png', '2026-02-07 17:14:57'),
(221, 25, '|', 'uploads/698773114106d_10.png', '2026-02-07 17:14:57'),
(222, 27, '|', 'uploads/698773459cb7e_0.png', '2026-02-07 17:15:49'),
(223, 27, '|', 'uploads/698773459d49b_1.png', '2026-02-07 17:15:49'),
(224, 27, '|', 'uploads/698773459d962_2.png', '2026-02-07 17:15:49'),
(225, 27, '|', 'uploads/698773459de29_3.png', '2026-02-07 17:15:49'),
(226, 27, '|', 'uploads/698773459e277_4.png', '2026-02-07 17:15:49'),
(227, 27, '|', 'uploads/698773459e741_5.png', '2026-02-07 17:15:49'),
(228, 27, '|', 'uploads/698773459ebea_6.png', '2026-02-07 17:15:49'),
(229, 27, '|', 'uploads/698773459f10a_7.png', '2026-02-07 17:15:49'),
(230, 37, '/', 'uploads/69877365027d9_0.jpg', '2026-02-07 17:16:21'),
(231, 37, '/', 'uploads/6987736502cdc_1.png', '2026-02-07 17:16:21'),
(232, 37, '/', 'uploads/698773650314c_2.png', '2026-02-07 17:16:21'),
(233, 37, '/', 'uploads/698773650356f_3.png', '2026-02-07 17:16:21'),
(234, 37, '/', 'uploads/6987736503993_4.png', '2026-02-07 17:16:21'),
(235, 37, '/', 'uploads/6987736503dee_5.png', '2026-02-07 17:16:21'),
(236, 37, '/', 'uploads/6987736504227_6.png', '2026-02-07 17:16:21'),
(237, 28, '|', 'uploads/6987738a82246_0.jpeg', '2026-02-07 17:16:58'),
(238, 28, '|', 'uploads/6987738a82a06_1.jpeg', '2026-02-07 17:16:58'),
(239, 28, '|', 'uploads/6987738a82edf_2.jpeg', '2026-02-07 17:16:58'),
(240, 28, '|', 'uploads/6987738a83419_3.png', '2026-02-07 17:16:58'),
(241, 28, '|', 'uploads/6987738a8391f_4.jpg', '2026-02-07 17:16:58'),
(242, 29, '|', 'uploads/698773a7b3497_0.png', '2026-02-07 17:17:27'),
(243, 29, '|', 'uploads/698773a7b39b6_1.png', '2026-02-07 17:17:27'),
(244, 29, '|', 'uploads/698773a7b3e84_2.png', '2026-02-07 17:17:27'),
(245, 29, '|', 'uploads/698773a7b55f7_3.png', '2026-02-07 17:17:27'),
(246, 29, '|', 'uploads/698773a7b5c4c_4.png', '2026-02-07 17:17:27'),
(247, 29, '|', 'uploads/698773a7b6295_5.png', '2026-02-07 17:17:27'),
(248, 29, '|', 'uploads/698773a7b6773_6.png', '2026-02-07 17:17:27'),
(249, 29, '|', 'uploads/698773a7b6d03_7.png', '2026-02-07 17:17:27'),
(250, 29, '|', 'uploads/698773a7b7387_8.png', '2026-02-07 17:17:27'),
(251, 29, '|', 'uploads/698773a7b7828_9.png', '2026-02-07 17:17:27'),
(252, 29, '|', 'uploads/698773a7b7cd6_10.png', '2026-02-07 17:17:27'),
(253, 29, '|', 'uploads/698773a7b82cc_11.png', '2026-02-07 17:17:27'),
(254, 29, '|', 'uploads/698773a7b87f1_12.png', '2026-02-07 17:17:27'),
(255, 29, '|', 'uploads/698773a7b8f05_13.png', '2026-02-07 17:17:27'),
(256, 29, '|', 'uploads/698773a7b9c2a_14.png', '2026-02-07 17:17:27'),
(257, 31, '|', 'uploads/698773d31b87a_0.png', '2026-02-07 17:18:11'),
(258, 31, '|', 'uploads/698773d31be6d_1.png', '2026-02-07 17:18:11'),
(259, 31, '|', 'uploads/698773d31c3c4_2.png', '2026-02-07 17:18:11'),
(260, 31, '|', 'uploads/698773d31c9f7_3.png', '2026-02-07 17:18:11'),
(261, 31, '|', 'uploads/698773d31d6cc_4.png', '2026-02-07 17:18:11'),
(262, 26, 'Smart', 'uploads/698777ce1c641_0.png', '2026-02-07 17:35:10'),
(263, 26, 'Smart', 'uploads/698777ce22075_1.png', '2026-02-07 17:35:10'),
(264, 26, 'Smart', 'uploads/698777ce226cf_2.png', '2026-02-07 17:35:10'),
(265, 26, 'Smart', 'uploads/698777ce22d0a_3.png', '2026-02-07 17:35:10'),
(266, 26, 'Smart', 'uploads/698777ce2330e_4.png', '2026-02-07 17:35:10'),
(267, 26, 'Smart', 'uploads/698777ce239d6_5.png', '2026-02-07 17:35:10'),
(268, 26, 'Smart', 'uploads/698777e3bc58e_0.png', '2026-02-07 17:35:31'),
(269, 26, 'Smart', 'uploads/698777e3bd37a_1.png', '2026-02-07 17:35:31'),
(270, 26, 'Smart', 'uploads/698777e3bdb24_2.png', '2026-02-07 17:35:31'),
(271, 26, 'Smart', 'uploads/698777e3be283_3.png', '2026-02-07 17:35:31'),
(272, 26, 'Smart', 'uploads/698777e3be85d_4.png', '2026-02-07 17:35:31'),
(273, 26, 'Smart', 'uploads/698777f1c283a_0.png', '2026-02-07 17:35:45'),
(274, 26, 'Smart', 'uploads/698777f1c2f81_1.png', '2026-02-07 17:35:45'),
(275, 26, 'Smart', 'uploads/698777f1c356b_2.png', '2026-02-07 17:35:45'),
(276, 26, 'Smart', 'uploads/698777f1c41ca_3.png', '2026-02-07 17:35:45'),
(277, 32, 'V. Pie', 'uploads/6987784a06a4b_0.png', '2026-02-07 17:37:14'),
(278, 32, 'V. Pie', 'uploads/6987784a07845_1.png', '2026-02-07 17:37:14'),
(279, 32, 'V. Pie', 'uploads/6987784a07ea8_2.png', '2026-02-07 17:37:14'),
(280, 32, 'V. Pie', 'uploads/6987784a08559_3.png', '2026-02-07 17:37:14'),
(281, 32, 'V. Pie', 'uploads/6987784a08bbc_4.png', '2026-02-07 17:37:14'),
(282, 32, 'V. Pie', 'uploads/69877863d8586_0.png', '2026-02-07 17:37:39'),
(283, 32, 'V. Pie', 'uploads/69877863d957e_1.png', '2026-02-07 17:37:39'),
(284, 32, 'V. Pie', 'uploads/69877863d9ab8_2.png', '2026-02-07 17:37:39'),
(285, 32, 'V. Pie', 'uploads/69877863d9ff7_3.png', '2026-02-07 17:37:39'),
(286, 32, 'V. Pie', 'uploads/69877863da591_4.png', '2026-02-07 17:37:39'),
(287, 32, 'V. Pie', 'uploads/6987787a7924b_0.png', '2026-02-07 17:38:02'),
(288, 32, 'V. Pie', 'uploads/6987787a79db6_1.png', '2026-02-07 17:38:02'),
(289, 32, 'V. Pie', 'uploads/6987787a7a3a4_2.png', '2026-02-07 17:38:02'),
(290, 32, 'V. Pie', 'uploads/6987787a7a948_3.png', '2026-02-07 17:38:02'),
(291, 30, 'V. Turbo', 'uploads/698778a717664_0.png', '2026-02-07 17:38:47'),
(292, 30, 'V. Turbo', 'uploads/698778a71849e_1.png', '2026-02-07 17:38:47'),
(293, 30, 'V. Turbo', 'uploads/698778a718c71_2.png', '2026-02-07 17:38:47'),
(294, 30, 'V. Turbo', 'uploads/698778a71938f_3.png', '2026-02-07 17:38:47'),
(295, 30, 'V. Turbo', 'uploads/698778a71996f_4.png', '2026-02-07 17:38:47'),
(296, 30, 'V. Turbo', 'uploads/698778b780484_0.png', '2026-02-07 17:39:03'),
(297, 30, 'V. Turbo', 'uploads/698778b7813c4_1.png', '2026-02-07 17:39:03'),
(298, 30, 'V. Turbo', 'uploads/698778b781b57_2.png', '2026-02-07 17:39:03'),
(299, 30, 'V. Turbo', 'uploads/698778b7824de_3.png', '2026-02-07 17:39:03');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','supervisor') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `username`, `password`, `rol`) VALUES
(3, 'danqueve', '$2y$10$5wwtwVEiYVEcF1sQJOied.Aaf3tOc4tyv0de901vHvrkS5DfpqGve', 'admin'),
(5, 'Agustina', '$2y$10$aXvhwkYfJsobVUDc7mq3DeV3xU0MV7LrzvDdZ/8xKBfhAFnvT93KW', 'admin');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cierres_semanales`
--
ALTER TABLE `cierres_semanales`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_cierre` (`zona`,`fecha_inicio`);

--
-- Indices de la tabla `detalles_diarios`
--
ALTER TABLE `detalles_diarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cierre_id` (`cierre_id`);

--
-- Indices de la tabla `flyers`
--
ALTER TABLE `flyers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categoria_id` (`categoria_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT de la tabla `cierres_semanales`
--
ALTER TABLE `cierres_semanales`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT de la tabla `detalles_diarios`
--
ALTER TABLE `detalles_diarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=281;

--
-- AUTO_INCREMENT de la tabla `flyers`
--
ALTER TABLE `flyers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=300;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detalles_diarios`
--
ALTER TABLE `detalles_diarios`
  ADD CONSTRAINT `detalles_diarios_ibfk_1` FOREIGN KEY (`cierre_id`) REFERENCES `cierres_semanales` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
