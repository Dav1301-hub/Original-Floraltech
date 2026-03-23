-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 18-03-2026 a las 16:23:43
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
-- Base de datos: `flores`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria_empleados`
--

CREATE TABLE `auditoria_empleados` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `empleado_id` int(11) DEFAULT NULL,
  `accion` varchar(20) NOT NULL,
  `datos_anteriores` text DEFAULT NULL,
  `datos_nuevos` text DEFAULT NULL,
  `fecha` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria_inventario`
--

CREATE TABLE `auditoria_inventario` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `producto_id` int(11) DEFAULT NULL,
  `accion` varchar(50) DEFAULT NULL,
  `detalles` text DEFAULT NULL,
  `valor_anterior` text DEFAULT NULL,
  `valor_nuevo` text DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `auditoria_inventario`
--

INSERT INTO `auditoria_inventario` (`id`, `usuario_id`, `producto_id`, `accion`, `detalles`, `valor_anterior`, `valor_nuevo`, `fecha`) VALUES
(1, 43, 28, 'CREAR_PRODUCTO', 'Producto nuevo: dwdwdwdw', NULL, '{\"accion\":\"nuevo_producto\",\"nuevo_producto_proveedor_id\":\"\",\"tipo_producto\":\"chocolate\",\"nombre_producto\":\"dwdwdwdw\",\"tflor_idtflor\":\"\",\"categoria\":\"Comestible\",\"color\":\"Multicolor\",\"stock\":\"10\",\"precio_compra\":\"3500\",\"precio\":\"5000\",\"descripcion\":\"\"}', '2026-03-11 01:15:05'),
(2, 43, 28, 'AJUSTE_STOCK', 'Agregado de stock manual: 10', '{\"stock\":10}', '{\"stock\":20}', '2026-03-11 01:15:51'),
(3, 43, 28, 'EDITAR_PRODUCTO', 'Cambio en propiedades del producto', '{\"idinv\":28,\"alimentacion\":\"Ambiente fresco y seco\",\"tflor_idtflor\":34,\"stock\":20,\"precio\":\"5000.00\",\"precio_compra\":\"3500.00\",\"fecha_actualizacion\":\"2026-03-10 20:15:51\",\"empleado_id\":43,\"motivo\":\"Producto nuevo agregado al inventario\",\"cantidad_disponible\":10,\"nombre\":\"dwdwdwdw\",\"naturaleza\":\"Comestible\",\"color\":\"Multicolor\"}', '{\"accion\":\"editar_producto\",\"producto_id\":\"28\",\"nombre_producto\":\"dwdwdwdw\",\"tipo_producto\":\"chocolate\",\"stock\":\"20\",\"precio_compra\":\"3500.00\",\"precio\":\"5000.00\",\"color\":\"Multicolor\",\"estado\":\"Disponible\",\"naturaleza\":null}', '2026-03-11 01:16:15'),
(4, 43, 28, 'EDITAR_PRODUCTO', 'Cambio en propiedades del producto', '{\"idinv\":28,\"alimentacion\":\"Ambiente fresco y seco\",\"tflor_idtflor\":34,\"stock\":20,\"precio\":\"5000.00\",\"precio_compra\":\"3500.00\",\"fecha_actualizacion\":\"2026-03-10 20:16:15\",\"empleado_id\":43,\"motivo\":\"Producto nuevo agregado al inventario\",\"cantidad_disponible\":10,\"nombre\":\"dwdwdwdw\",\"naturaleza\":null,\"color\":\"Multicolor\"}', '{\"accion\":\"editar_producto\",\"producto_id\":\"28\",\"nombre_producto\":\"dwdwdwdw\",\"tipo_producto\":\"flor\",\"stock\":\"20\",\"precio_compra\":\"3500.00\",\"precio\":\"5000.00\",\"color\":\"Multicolor\",\"estado\":\"Disponible\",\"naturaleza\":null}', '2026-03-11 01:16:47'),
(5, 43, 28, 'EDITAR_PRODUCTO', 'Cambio en propiedades del producto', '{\"idinv\":28,\"alimentacion\":\"Agua y nutrientes\",\"tflor_idtflor\":34,\"stock\":20,\"precio\":\"5000.00\",\"precio_compra\":\"3500.00\",\"fecha_actualizacion\":\"2026-03-10 20:16:47\",\"empleado_id\":43,\"motivo\":\"Producto nuevo agregado al inventario\",\"cantidad_disponible\":10,\"nombre\":\"dwdwdwdw\",\"naturaleza\":null,\"color\":\"Multicolor\"}', '{\"accion\":\"editar_producto\",\"producto_id\":\"28\",\"nombre_producto\":\"dwdwdwdw\",\"tipo_producto\":\"chocolate\",\"stock\":\"20\",\"precio_compra\":\"3500.00\",\"precio\":\"5000.00\",\"color\":\"Multicolor\",\"estado\":\"Disponible\",\"naturaleza\":null}', '2026-03-11 01:16:54'),
(6, 43, 28, 'EDITAR_PRODUCTO', 'Cambio en propiedades del producto', '{\"idinv\":28,\"alimentacion\":\"Ambiente fresco y seco\",\"tflor_idtflor\":34,\"stock\":15,\"precio\":\"5000.00\",\"precio_compra\":\"3500.00\",\"fecha_actualizacion\":\"2026-03-10 20:22:18\",\"empleado_id\":43,\"motivo\":\"Producto nuevo agregado al inventario\",\"cantidad_disponible\":5,\"nombre\":\"dwdwdwdw\",\"naturaleza\":null,\"color\":\"Multicolor\"}', '{\"accion\":\"editar_producto\",\"producto_id\":\"28\",\"nombre_producto\":\"dwdwdwdw\",\"tipo_producto\":\"otro\",\"stock\":\"0\",\"precio_compra\":\"3500.00\",\"precio\":\"5000.00\",\"color\":\"Multicolor\",\"estado\":\"Disponible\",\"naturaleza\":null}', '2026-03-11 12:12:05'),
(7, 43, 29, 'CREAR_PRODUCTO', 'Producto nuevo: Rosa Amarilla', NULL, '{\"accion\":\"nuevo_producto\",\"nuevo_producto_proveedor_id\":\"\",\"tipo_producto\":\"flor\",\"nombre_producto\":\"Rosa Amarilla\",\"tflor_idtflor\":\"\",\"categoria\":\"Natural\",\"color\":\"Multicolor\",\"stock\":\"0\",\"precio_compra\":\"2000\",\"precio\":\"2500\",\"descripcion\":\"\"}', '2026-03-12 02:36:58'),
(8, 43, 28, 'EDITAR_PRODUCTO', 'Cambio en propiedades del producto', '{\"idinv\":28,\"alimentacion\":\"Seg\\u00fan especificaciones\",\"tflor_idtflor\":34,\"stock\":0,\"precio\":\"5000.00\",\"precio_compra\":\"3500.00\",\"fecha_actualizacion\":\"2026-03-11 07:12:05\",\"empleado_id\":43,\"motivo\":\"Producto nuevo agregado al inventario\",\"cantidad_disponible\":5,\"nombre\":\"dwdwdwdw\",\"naturaleza\":null,\"color\":\"Multicolor\"}', '{\"accion\":\"editar_producto\",\"producto_id\":\"28\",\"nombre_producto\":\"dwdwdwdw\",\"tipo_producto\":\"chocolate\",\"stock\":\"70\",\"precio_compra\":\"3500.00\",\"precio\":\"5000.00\",\"color\":\"Multicolor\",\"estado\":\"Disponible\",\"naturaleza\":null}', '2026-03-12 02:54:21'),
(9, 43, 30, 'CREAR_PRODUCTO', 'Producto nuevo: Rosa Amarilla', NULL, '{\"accion\":\"nuevo_producto\",\"nuevo_producto_proveedor_id\":\"\",\"tipo_producto\":\"flor\",\"nombre_producto\":\"Rosa Amarilla\",\"tflor_idtflor\":\"\",\"categoria\":\"Natural\",\"color\":\"Multicolor\",\"stock\":\"0\",\"precio_compra\":\"2000\",\"precio\":\"2500\",\"descripcion\":\"\"}', '2026-03-12 13:27:49'),
(10, 43, 28, 'EDITAR_PRODUCTO', 'Cambio en propiedades del producto', '{\"idinv\":28,\"alimentacion\":\"Ambiente fresco y seco\",\"tflor_idtflor\":34,\"stock\":70,\"precio\":\"5000.00\",\"precio_compra\":\"3500.00\",\"fecha_actualizacion\":\"2026-03-11 21:54:21\",\"empleado_id\":43,\"motivo\":\"Producto nuevo agregado al inventario\",\"cantidad_disponible\":5,\"nombre\":\"dwdwdwdw\",\"naturaleza\":null,\"color\":\"Multicolor\"}', '{\"accion\":\"editar_producto\",\"producto_id\":\"28\",\"nombre_producto\":\"dwdwdwdw\",\"tipo_producto\":\"otro\",\"stock\":\"0\",\"precio_compra\":\"3500.00\",\"precio\":\"5000.00\",\"color\":\"Multicolor\",\"estado\":\"Disponible\",\"naturaleza\":null}', '2026-03-13 00:10:43'),
(11, 43, 28, 'EDITAR_PRODUCTO', 'Cambio en propiedades del producto', '{\"idinv\":28,\"alimentacion\":\"Seg\\u00fan especificaciones\",\"tflor_idtflor\":34,\"stock\":0,\"precio\":\"5000.00\",\"precio_compra\":\"3500.00\",\"fecha_actualizacion\":\"2026-03-12 19:10:43\",\"empleado_id\":43,\"motivo\":\"Producto nuevo agregado al inventario\",\"cantidad_disponible\":5,\"nombre\":\"dwdwdwdw\",\"naturaleza\":null,\"color\":\"Multicolor\"}', '{\"accion\":\"editar_producto\",\"producto_id\":\"28\",\"nombre_producto\":\"dwdwdwdw\",\"tipo_producto\":\"otro\",\"stock\":\"100\",\"precio_compra\":\"3500.00\",\"precio\":\"5000.00\",\"color\":\"Multicolor\",\"estado\":\"Disponible\",\"naturaleza\":null}', '2026-03-13 01:39:04'),
(12, 43, 31, 'CREAR_PRODUCTO', 'Producto nuevo: Rosa Amarilla', NULL, '{\"accion\":\"nuevo_producto\",\"nuevo_producto_proveedor_id\":\"\",\"tipo_producto\":\"flor\",\"nombre_producto\":\"Rosa Amarilla\",\"tflor_idtflor\":\"\",\"categoria\":\"Natural\",\"color\":\"Amarillo\",\"stock\":\"0\",\"precio_compra\":\"2000\",\"precio\":\"2500\",\"descripcion\":\"\"}', '2026-03-13 02:07:07'),
(13, 43, 31, 'EDITAR_PRODUCTO', 'Cambio en propiedades del producto', '{\"idinv\":31,\"alimentacion\":\"Agua y nutrientes\",\"tflor_idtflor\":37,\"stock\":0,\"precio\":\"2500.00\",\"precio_compra\":\"2000.00\",\"fecha_actualizacion\":\"2026-03-12 21:07:07\",\"empleado_id\":43,\"motivo\":\"Producto nuevo agregado al inventario\",\"cantidad_disponible\":0,\"nombre\":\"Rosa Amarilla\",\"naturaleza\":\"Natural\",\"color\":\"Amarillo\"}', '{\"accion\":\"editar_producto\",\"producto_id\":\"31\",\"nombre_producto\":\"Rosa Amarilla\",\"tipo_producto\":\"flor\",\"stock\":\"50\",\"precio_compra\":\"2000.00\",\"precio\":\"2500.00\",\"color\":\"Amarillo\",\"naturaleza\":\"Natural\",\"estado\":\"Disponible\"}', '2026-03-13 02:07:25'),
(14, 43, 32, 'CREAR_PRODUCTO', 'Producto nuevo: Jet', NULL, '{\"accion\":\"nuevo_producto\",\"nuevo_producto_proveedor_id\":\"\",\"tipo_producto\":\"chocolate\",\"nombre_producto\":\"Jet\",\"tflor_idtflor\":\"\",\"categoria\":\"Comestible\",\"color\":\"Blanco\",\"stock\":\"100\",\"precio_compra\":\"500\",\"precio\":\"3000\",\"descripcion\":\"\"}', '2026-03-14 14:35:22'),
(15, 43, 31, 'EDITAR_PRODUCTO', 'Cambio en propiedades del producto', '{\"idinv\":31,\"alimentacion\":\"Agua y nutrientes\",\"tflor_idtflor\":37,\"stock\":48,\"precio\":\"2500.00\",\"precio_compra\":\"2000.00\",\"fecha_actualizacion\":\"2026-03-15 08:55:26\",\"empleado_id\":43,\"motivo\":\"Producto nuevo agregado al inventario\",\"cantidad_disponible\":-2,\"nombre\":\"Rosa Amarilla\",\"naturaleza\":\"Natural\",\"color\":\"Amarillo\"}', '{\"accion\":\"editar_producto\",\"producto_id\":\"31\",\"nombre_producto\":\"Rosa Amarilla\",\"tipo_producto\":\"flor\",\"stock\":\"0\",\"precio_compra\":\"2000.00\",\"precio\":\"2500.00\",\"color\":\"Amarillo\",\"naturaleza\":\"Natural\",\"estado\":\"Disponible\"}', '2026-03-15 15:09:13'),
(16, 43, 31, 'EDITAR_PRODUCTO', 'Cambio en propiedades del producto', '{\"idinv\":31,\"alimentacion\":\"Agua y nutrientes\",\"tflor_idtflor\":37,\"stock\":100,\"precio\":\"2500.00\",\"precio_compra\":\"2000.00\",\"fecha_actualizacion\":\"2026-03-15 10:19:10\",\"empleado_id\":43,\"motivo\":\"Producto nuevo agregado al inventario\",\"cantidad_disponible\":-2,\"nombre\":\"Rosa Amarilla\",\"naturaleza\":\"Natural\",\"color\":\"Amarillo\"}', '{\"accion\":\"editar_producto\",\"producto_id\":\"31\",\"nombre_producto\":\"Rosa Amarilla\",\"tipo_producto\":\"flor\",\"stock\":\"100\",\"precio_compra\":\"2000.00\",\"precio\":\"2500.00\",\"color\":\"Amarillo\",\"naturaleza\":\"Natural\",\"estado\":\"Descontinuado\"}', '2026-03-15 15:19:37'),
(17, 43, 31, 'EDITAR_PRODUCTO', 'Cambio en propiedades del producto', '{\"idinv\":31,\"alimentacion\":\"Agua y nutrientes\",\"tflor_idtflor\":37,\"stock\":100,\"precio\":\"2500.00\",\"precio_compra\":\"2000.00\",\"fecha_actualizacion\":\"2026-03-15 10:19:37\",\"empleado_id\":43,\"motivo\":\"Producto nuevo agregado al inventario\",\"cantidad_disponible\":-2,\"nombre\":\"Rosa Amarilla\",\"naturaleza\":\"Natural\",\"color\":\"Amarillo\"}', '{\"accion\":\"editar_producto\",\"producto_id\":\"31\",\"nombre_producto\":\"Rosa Amarilla\",\"tipo_producto\":\"flor\",\"stock\":\"100\",\"precio_compra\":\"2000.00\",\"precio\":\"2500.00\",\"color\":\"Amarillo\",\"naturaleza\":\"Natural\",\"estado\":\"Descontinuado\"}', '2026-03-15 15:19:54'),
(18, 43, 33, 'CREAR_PRODUCTO', 'Producto nuevo: Chocolatina jet', NULL, '{\"accion\":\"nuevo_producto\",\"nuevo_producto_proveedor_id\":\"\",\"tipo_producto\":\"chocolate\",\"nombre_producto\":\"Chocolatina jet\",\"tflor_idtflor\":\"\",\"categoria\":\"Comestible\",\"color\":\"Blanco\",\"stock\":\"50\",\"precio_compra\":\"1500\",\"precio\":\"2500\",\"descripcion\":\"\"}', '2026-03-15 15:45:12'),
(19, 43, 33, 'EDITAR_PRODUCTO', 'Cambio en propiedades del producto', '{\"idinv\":33,\"alimentacion\":\"Ambiente fresco y seco\",\"tflor_idtflor\":39,\"stock\":50,\"precio\":\"2500.00\",\"precio_compra\":\"1500.00\",\"fecha_actualizacion\":\"2026-03-15 10:45:12\",\"empleado_id\":43,\"motivo\":\"Producto nuevo agregado al inventario\",\"cantidad_disponible\":50,\"nombre\":\"Chocolatina jet\",\"naturaleza\":\"Comestible\",\"color\":\"Blanco\"}', '{\"accion\":\"editar_producto\",\"producto_id\":\"33\",\"nombre_producto\":\"Chocolatina jet\",\"tipo_producto\":\"otro\",\"stock\":\"50\",\"precio_compra\":\"1500.00\",\"precio\":\"2500.00\",\"color\":\"Blanco\",\"estado\":\"Descontinuado\"}', '2026-03-15 15:45:21'),
(20, 43, 33, 'EDITAR_PRODUCTO', 'Cambio en propiedades del producto', '{\"idinv\":33,\"alimentacion\":\"Seg\\u00fan especificaciones\",\"tflor_idtflor\":39,\"stock\":50,\"precio\":\"2500.00\",\"precio_compra\":\"1500.00\",\"fecha_actualizacion\":\"2026-03-15 10:45:21\",\"empleado_id\":43,\"motivo\":\"Producto nuevo agregado al inventario\",\"cantidad_disponible\":50,\"nombre\":\"Chocolatina jet\",\"naturaleza\":\"\",\"color\":\"Blanco\"}', '{\"accion\":\"editar_producto\",\"producto_id\":\"33\",\"nombre_producto\":\"Chocolatina jet\",\"tipo_producto\":\"otro\",\"stock\":\"50\",\"precio_compra\":\"1500.00\",\"precio\":\"2500.00\",\"color\":\"Blanco\",\"naturaleza\":\"\",\"estado\":\"Agotado\"}', '2026-03-15 15:46:25'),
(21, 43, 33, 'EDITAR_PRODUCTO', 'Cambio en propiedades del producto', '{\"idinv\":33,\"alimentacion\":\"Seg\\u00fan especificaciones\",\"tflor_idtflor\":39,\"stock\":50,\"precio\":\"2500.00\",\"precio_compra\":\"1500.00\",\"fecha_actualizacion\":\"2026-03-15 10:46:25\",\"empleado_id\":43,\"motivo\":\"Producto nuevo agregado al inventario\",\"cantidad_disponible\":50,\"nombre\":\"Chocolatina jet\",\"naturaleza\":\"\",\"color\":\"Blanco\"}', '{\"accion\":\"editar_producto\",\"producto_id\":\"33\",\"nombre_producto\":\"Chocolatina jet\",\"tipo_producto\":\"otro\",\"stock\":\"50\",\"precio_compra\":\"1500.00\",\"precio\":\"2500.00\",\"color\":\"Blanco\",\"naturaleza\":\"\",\"estado\":\"desactivado\"}', '2026-03-15 15:47:53'),
(22, 43, 33, 'EDITAR_PRODUCTO', 'Cambio en propiedades del producto', '{\"idinv\":33,\"alimentacion\":\"Seg\\u00fan especificaciones\",\"tflor_idtflor\":39,\"stock\":50,\"precio\":\"2500.00\",\"precio_compra\":\"1500.00\",\"fecha_actualizacion\":\"2026-03-15 10:47:53\",\"empleado_id\":43,\"motivo\":\"Producto nuevo agregado al inventario\",\"cantidad_disponible\":50,\"nombre\":\"Chocolatina jet\",\"naturaleza\":\"\",\"color\":\"Blanco\"}', '{\"accion\":\"editar_producto\",\"producto_id\":\"33\",\"nombre_producto\":\"Chocolatina jet\",\"tipo_producto\":\"otro\",\"stock\":\"50\",\"precio_compra\":\"1500.00\",\"precio\":\"2500.00\",\"color\":\"Blanco\",\"naturaleza\":\"\",\"estado\":\"desactivado\"}', '2026-03-15 15:48:12'),
(23, 43, 33, 'EDITAR_PRODUCTO', 'Cambio en propiedades del producto', '{\"idinv\":33,\"alimentacion\":\"Seg\\u00fan especificaciones\",\"tflor_idtflor\":39,\"stock\":50,\"precio\":\"2500.00\",\"precio_compra\":\"1500.00\",\"fecha_actualizacion\":\"2026-03-15 10:48:12\",\"empleado_id\":43,\"motivo\":\"Producto nuevo agregado al inventario\",\"cantidad_disponible\":50,\"nombre\":\"Chocolatina jet\",\"naturaleza\":\"\",\"color\":\"Blanco\"}', '{\"accion\":\"editar_producto\",\"producto_id\":\"33\",\"nombre_producto\":\"Chocolatina jet\",\"tipo_producto\":\"otro\",\"stock\":\"50\",\"precio_compra\":\"1500.00\",\"precio\":\"2500.00\",\"color\":\"Blanco\",\"naturaleza\":\"\",\"estado\":\"desactivado\"}', '2026-03-15 15:50:25'),
(24, 43, 33, 'EDITAR_PRODUCTO', 'Cambio en propiedades del producto', '{\"idinv\":33,\"alimentacion\":\"Seg\\u00fan especificaciones\",\"tflor_idtflor\":39,\"stock\":50,\"precio\":\"2500.00\",\"precio_compra\":\"1500.00\",\"fecha_actualizacion\":\"2026-03-15 10:50:25\",\"empleado_id\":43,\"motivo\":\"Producto nuevo agregado al inventario\",\"cantidad_disponible\":50,\"nombre\":\"Chocolatina jet\",\"naturaleza\":\"\",\"color\":\"Blanco\"}', '{\"accion\":\"editar_producto\",\"producto_id\":\"33\",\"nombre_producto\":\"Chocolatina jet\",\"tipo_producto\":\"otro\",\"stock\":\"50\",\"precio_compra\":\"1500.00\",\"precio\":\"2500.00\",\"color\":\"Blanco\",\"naturaleza\":\"\",\"estado\":\"desactivado\"}', '2026-03-15 15:51:14'),
(25, 43, 33, 'EDITAR_PRODUCTO', 'Cambio en propiedades del producto', '{\"idinv\":33,\"alimentacion\":\"Seg\\u00fan especificaciones\",\"tflor_idtflor\":39,\"stock\":50,\"precio\":\"2500.00\",\"precio_compra\":\"1500.00\",\"fecha_actualizacion\":\"2026-03-15 10:51:14\",\"empleado_id\":43,\"motivo\":\"Producto nuevo agregado al inventario\",\"cantidad_disponible\":50,\"nombre\":\"Chocolatina jet\",\"naturaleza\":\"\",\"color\":\"Blanco\"}', '{\"accion\":\"editar_producto\",\"producto_id\":\"33\",\"nombre_producto\":\"Chocolatina jet\",\"tipo_producto\":\"otro\",\"stock\":\"50\",\"precio_compra\":\"1500.00\",\"precio\":\"2500.00\",\"color\":\"Blanco\",\"naturaleza\":\"\",\"estado\":\"desactivado\"}', '2026-03-15 15:52:44'),
(26, 43, 33, 'EDITAR_PRODUCTO', 'Cambio en propiedades del producto', '{\"idinv\":33,\"alimentacion\":\"Seg\\u00fan especificaciones\",\"tflor_idtflor\":39,\"stock\":50,\"precio\":\"2500.00\",\"precio_compra\":\"1500.00\",\"fecha_actualizacion\":\"2026-03-15 10:52:44\",\"empleado_id\":43,\"motivo\":\"Producto nuevo agregado al inventario\",\"cantidad_disponible\":50,\"nombre\":\"Chocolatina jet\",\"naturaleza\":\"\",\"color\":\"Blanco\"}', '{\"accion\":\"editar_producto\",\"producto_id\":\"33\",\"nombre_producto\":\"Chocolatina jet\",\"tipo_producto\":\"otro\",\"stock\":\"50\",\"precio_compra\":\"1500.00\",\"precio\":\"2500.00\",\"color\":\"Blanco\",\"naturaleza\":\"\",\"estado\":\"activo\"}', '2026-03-15 15:57:55'),
(27, 43, 34, 'CREAR_PRODUCTO', 'Producto nuevo: Chocolatina jet', NULL, '{\"accion\":\"nuevo_producto\",\"nuevo_producto_proveedor_id\":\"\",\"tipo_producto\":\"chocolate\",\"nombre_producto\":\"Chocolatina jet\",\"tflor_idtflor\":\"\",\"categoria\":\"Comestible\",\"color\":\"Multicolor\",\"stock\":\"50\",\"precio_compra\":\"1500\",\"precio\":\"2500\",\"descripcion\":\"\"}', '2026-03-15 16:04:43'),
(28, 43, 35, 'CREAR_PRODUCTO', 'Producto nuevo: Rosa Amarilla', NULL, '{\"accion\":\"nuevo_producto\",\"nuevo_producto_proveedor_id\":\"\",\"tipo_producto\":\"flor\",\"nombre_producto\":\"Rosa Amarilla\",\"tflor_idtflor\":\"\",\"categoria\":\"Natural\",\"color\":\"Amarillo\",\"stock\":\"0\",\"precio_compra\":\"500\",\"precio\":\"1000\",\"descripcion\":\"\"}', '2026-03-15 16:14:44'),
(29, 43, 35, 'EDITAR_PRODUCTO', 'Cambio en propiedades del producto', '{\"idinv\":35,\"alimentacion\":\"Agua y nutrientes\",\"tflor_idtflor\":41,\"stock\":100,\"precio\":\"1000.00\",\"precio_compra\":\"500.00\",\"fecha_actualizacion\":\"2026-03-15 11:15:31\",\"empleado_id\":43,\"motivo\":\"Producto nuevo agregado al inventario\",\"cantidad_disponible\":100,\"nombre\":\"Rosa Amarilla\",\"naturaleza\":\"Natural\",\"color\":\"Amarillo\"}', '{\"accion\":\"editar_producto\",\"producto_id\":\"35\",\"nombre_producto\":\"Rosa Amarilla\",\"tipo_producto\":\"flor\",\"stock\":\"101\",\"precio_compra\":\"500.00\",\"precio\":\"1000.00\",\"color\":\"Amarillo\",\"naturaleza\":\"Natural\",\"estado\":\"activo\"}', '2026-03-15 16:15:43'),
(30, 43, 35, 'EDITAR_PRODUCTO', 'Cambio en propiedades del producto', '{\"idinv\":35,\"alimentacion\":\"Agua y nutrientes\",\"tflor_idtflor\":41,\"stock\":100,\"precio\":\"1000.00\",\"precio_compra\":\"500.00\",\"fecha_actualizacion\":\"2026-03-15 11:20:08\",\"empleado_id\":43,\"motivo\":\"Producto nuevo agregado al inventario\",\"cantidad_disponible\":100,\"nombre\":\"Rosa Amarilla\",\"naturaleza\":\"Natural\",\"color\":\"Amarillo\"}', '{\"accion\":\"editar_producto\",\"producto_id\":\"35\",\"nombre_producto\":\"Rosa Amarilla\",\"tipo_producto\":\"flor\",\"stock\":\"101\",\"precio_compra\":\"500.00\",\"precio\":\"1000.00\",\"color\":\"Amarillo\",\"naturaleza\":\"Natural\",\"estado\":\"activo\"}', '2026-03-15 16:20:20'),
(31, 43, 35, 'EDITAR_PRODUCTO', 'Cambio en propiedades del producto', '{\"idinv\":35,\"alimentacion\":\"Agua y nutrientes\",\"tflor_idtflor\":41,\"stock\":1,\"precio\":\"1000.00\",\"precio_compra\":\"500.00\",\"fecha_actualizacion\":\"2026-03-15 11:30:39\",\"empleado_id\":43,\"motivo\":\"Producto nuevo agregado al inventario\",\"cantidad_disponible\":1,\"nombre\":\"Rosa Amarilla\",\"naturaleza\":\"Natural\",\"color\":\"Amarillo\"}', '{\"accion\":\"editar_producto\",\"producto_id\":\"35\",\"nombre_producto\":\"Rosa Amarilla\",\"tipo_producto\":\"flor\",\"stock\":\"12\",\"precio_compra\":\"500.00\",\"precio\":\"1000.00\",\"color\":\"Amarillo\",\"naturaleza\":\"Natural\",\"estado\":\"activo\"}', '2026-03-15 16:30:48'),
(32, 43, 35, 'EDITAR_PRODUCTO', 'Cambio en propiedades del producto', '{\"idinv\":35,\"alimentacion\":\"Agua y nutrientes\",\"tflor_idtflor\":41,\"stock\":12,\"precio\":\"1000.00\",\"precio_compra\":\"500.00\",\"fecha_actualizacion\":\"2026-03-15 11:30:48\",\"empleado_id\":43,\"motivo\":\"Producto nuevo agregado al inventario\",\"cantidad_disponible\":1,\"nombre\":\"Rosa Amarilla\",\"naturaleza\":\"Natural\",\"color\":\"Amarillo\"}', '{\"accion\":\"editar_producto\",\"producto_id\":\"35\",\"nombre_producto\":\"Rosa Amarilla\",\"tipo_producto\":\"flor\",\"stock\":\"121\",\"precio_compra\":\"500.00\",\"precio\":\"1000.00\",\"color\":\"Amarillo\",\"naturaleza\":\"Natural\",\"estado\":\"activo\"}', '2026-03-15 16:31:04'),
(33, 43, 35, 'EDITAR_PRODUCTO', 'Cambio en propiedades del producto', '{\"idinv\":35,\"alimentacion\":\"Agua y nutrientes\",\"tflor_idtflor\":41,\"stock\":121,\"precio\":\"1000.00\",\"precio_compra\":\"500.00\",\"fecha_actualizacion\":\"2026-03-15 11:34:39\",\"empleado_id\":43,\"motivo\":\"Producto nuevo agregado al inventario\",\"cantidad_disponible\":121,\"nombre\":\"Rosa Amarilla\",\"naturaleza\":\"Natural\",\"color\":\"Amarillo\"}', '{\"accion\":\"editar_producto\",\"producto_id\":\"35\",\"nombre_producto\":\"Rosa Amarilla\",\"tipo_producto\":\"flor\",\"stock\":\"121\",\"precio_compra\":\"500.00\",\"precio\":\"1000.00\",\"color\":\"Amarillo\",\"naturaleza\":\"Natural\",\"estado\":\"desactivado\"}', '2026-03-15 16:34:50'),
(34, 43, 35, 'EDITAR_PRODUCTO', 'Cambio en propiedades del producto', '{\"idinv\":35,\"alimentacion\":\"Agua y nutrientes\",\"tflor_idtflor\":41,\"stock\":121,\"precio\":\"1000.00\",\"precio_compra\":\"500.00\",\"fecha_actualizacion\":\"2026-03-15 11:34:50\",\"empleado_id\":43,\"motivo\":\"Producto nuevo agregado al inventario\",\"cantidad_disponible\":121,\"nombre\":\"Rosa Amarilla\",\"naturaleza\":\"Natural\",\"color\":\"Amarillo\"}', '{\"accion\":\"editar_producto\",\"producto_id\":\"35\",\"nombre_producto\":\"Rosa Amarilla\",\"tipo_producto\":\"flor\",\"stock\":\"121\",\"precio_compra\":\"500.00\",\"precio\":\"1000.00\",\"color\":\"Amarillo\",\"naturaleza\":\"Natural\",\"estado\":\"desactivado\"}', '2026-03-15 16:34:51'),
(35, 43, 35, 'EDITAR_PRODUCTO', 'Cambio en propiedades del producto', '{\"idinv\":35,\"alimentacion\":\"Agua y nutrientes\",\"tflor_idtflor\":41,\"stock\":121,\"precio\":\"1000.00\",\"precio_compra\":\"500.00\",\"fecha_actualizacion\":\"2026-03-15 11:34:51\",\"empleado_id\":43,\"motivo\":\"Producto nuevo agregado al inventario\",\"cantidad_disponible\":121,\"nombre\":\"Rosa Amarilla\",\"naturaleza\":\"Natural\",\"color\":\"Amarillo\"}', '{\"accion\":\"editar_producto\",\"producto_id\":\"35\",\"nombre_producto\":\"Rosa Amarilla\",\"tipo_producto\":\"flor\",\"stock\":\"121\",\"precio_compra\":\"500.00\",\"precio\":\"1000.00\",\"color\":\"Amarillo\",\"naturaleza\":\"Natural\",\"estado\":\"desactivado\"}', '2026-03-15 16:35:08'),
(36, 43, 35, 'EDITAR_PRODUCTO', 'Cambio en propiedades del producto', '{\"idinv\":35,\"alimentacion\":\"Agua y nutrientes\",\"tflor_idtflor\":41,\"stock\":121,\"precio\":\"1000.00\",\"precio_compra\":\"500.00\",\"fecha_actualizacion\":\"2026-03-15 11:35:08\",\"empleado_id\":43,\"motivo\":\"Producto nuevo agregado al inventario\",\"cantidad_disponible\":121,\"nombre\":\"Rosa Amarilla\",\"naturaleza\":\"Natural\",\"color\":\"Amarillo\"}', '{\"accion\":\"editar_producto\",\"producto_id\":\"35\",\"nombre_producto\":\"Rosa Amarilla\",\"tipo_producto\":\"flor\",\"stock\":\"121\",\"precio_compra\":\"500.00\",\"precio\":\"1000.00\",\"color\":\"Amarillo\",\"naturaleza\":\"Natural\",\"estado\":\"activo\"}', '2026-03-15 16:35:17'),
(37, 43, 34, 'EDITAR_PRODUCTO', 'Cambio en propiedades del producto', '{\"idinv\":34,\"alimentacion\":\"Ambiente fresco y seco\",\"tflor_idtflor\":40,\"stock\":50,\"precio\":\"2500.00\",\"precio_compra\":\"1500.00\",\"fecha_actualizacion\":\"2026-03-15 11:34:39\",\"empleado_id\":43,\"motivo\":\"Producto nuevo agregado al inventario\",\"cantidad_disponible\":50,\"nombre\":\"Chocolatina jet\",\"naturaleza\":\"Comestible\",\"color\":\"Multicolor\"}', '{\"accion\":\"editar_producto\",\"producto_id\":\"34\",\"nombre_producto\":\"Chocolatina jet\",\"tipo_producto\":\"chocolate\",\"stock\":\"50\",\"precio_compra\":\"1500.00\",\"precio\":\"2500.00\",\"color\":\"Multicolor\",\"naturaleza\":\"Comestible\",\"estado\":\"activo\"}', '2026-03-15 16:43:48'),
(38, 43, 34, 'EDITAR_PRODUCTO', 'Cambio en propiedades del producto', '{\"idinv\":34,\"alimentacion\":\"Ambiente fresco y seco\",\"tflor_idtflor\":40,\"stock\":50,\"precio\":\"2500.00\",\"precio_compra\":\"1500.00\",\"fecha_actualizacion\":\"2026-03-15 11:43:48\",\"empleado_id\":43,\"motivo\":\"Producto nuevo agregado al inventario\",\"cantidad_disponible\":50,\"nombre\":\"Chocolatina jet\",\"naturaleza\":\"Comestible\",\"color\":\"Multicolor\"}', '{\"accion\":\"editar_producto\",\"producto_id\":\"34\",\"nombre_producto\":\"Chocolatina jet\",\"tipo_producto\":\"chocolate\",\"stock\":\"50\",\"precio_compra\":\"1500.00\",\"precio\":\"2500.00\",\"color\":\"Multicolor\",\"naturaleza\":\"Comestible\",\"estado\":\"desactivado\"}', '2026-03-15 16:44:01'),
(39, 43, 34, 'EDITAR_PRODUCTO', 'Cambio en propiedades del producto', '{\"idinv\":34,\"alimentacion\":\"Ambiente fresco y seco\",\"tflor_idtflor\":40,\"stock\":50,\"precio\":\"2500.00\",\"precio_compra\":\"1500.00\",\"fecha_actualizacion\":\"2026-03-15 11:44:01\",\"empleado_id\":43,\"motivo\":\"Producto nuevo agregado al inventario\",\"cantidad_disponible\":50,\"nombre\":\"Chocolatina jet\",\"naturaleza\":\"Comestible\",\"color\":\"Multicolor\"}', '{\"accion\":\"editar_producto\",\"producto_id\":\"34\",\"nombre_producto\":\"Chocolatina jet\",\"tipo_producto\":\"chocolate\",\"stock\":\"50\",\"precio_compra\":\"1500.00\",\"precio\":\"2500.00\",\"color\":\"Multicolor\",\"naturaleza\":\"Comestible\",\"estado\":\"activo\"}', '2026-03-15 16:53:25'),
(40, 43, 35, 'EDITAR_PRODUCTO', 'Cambio en propiedades del producto', '{\"idinv\":35,\"alimentacion\":\"Agua y nutrientes\",\"tflor_idtflor\":41,\"stock\":121,\"precio\":\"1000.00\",\"precio_compra\":\"500.00\",\"fecha_actualizacion\":\"2026-03-15 11:35:17\",\"empleado_id\":43,\"motivo\":\"Producto nuevo agregado al inventario\",\"cantidad_disponible\":121,\"nombre\":\"Rosa Amarilla\",\"naturaleza\":\"Natural\",\"color\":\"Amarillo\"}', '{\"accion\":\"editar_producto\",\"producto_id\":\"35\",\"nombre_producto\":\"Rosa Amarilla\",\"tipo_producto\":\"flor\",\"stock\":\"121\",\"precio_compra\":\"500.00\",\"precio\":\"1000.00\",\"color\":\"Amarillo\",\"naturaleza\":\"Natural\",\"estado\":\"activo\"}', '2026-03-15 16:53:31'),
(41, 43, 34, 'EDITAR_PRODUCTO', 'Cambio en propiedades del producto', '{\"idinv\":34,\"alimentacion\":\"Ambiente fresco y seco\",\"tflor_idtflor\":40,\"stock\":50,\"precio\":\"2500.00\",\"precio_compra\":\"1500.00\",\"fecha_actualizacion\":\"2026-03-15 11:53:25\",\"empleado_id\":43,\"motivo\":\"Producto nuevo agregado al inventario\",\"cantidad_disponible\":50,\"nombre\":\"Chocolatina jet\",\"naturaleza\":\"Comestible\",\"color\":\"Multicolor\"}', '{\"accion\":\"editar_producto\",\"producto_id\":\"34\",\"nombre_producto\":\"Chocolatina jet\",\"tipo_producto\":\"chocolate\",\"stock\":\"50\",\"precio_compra\":\"1500.00\",\"precio\":\"2500.00\",\"color\":\"Multicolor\",\"naturaleza\":\"Comestible\",\"estado\":\"desactivado\"}', '2026-03-15 16:54:48'),
(42, 43, 34, 'EDITAR_PRODUCTO', 'Cambio en propiedades del producto', '{\"idinv\":34,\"alimentacion\":\"Ambiente fresco y seco\",\"tflor_idtflor\":40,\"stock\":50,\"precio\":\"2500.00\",\"precio_compra\":\"1500.00\",\"fecha_actualizacion\":\"2026-03-15 11:54:48\",\"empleado_id\":43,\"motivo\":\"Producto nuevo agregado al inventario\",\"cantidad_disponible\":50,\"nombre\":\"Chocolatina jet\",\"naturaleza\":\"Comestible\",\"color\":\"Multicolor\"}', '{\"accion\":\"editar_producto\",\"producto_id\":\"34\",\"nombre_producto\":\"Chocolatina jet\",\"tipo_producto\":\"chocolate\",\"stock\":\"50\",\"precio_compra\":\"1500.00\",\"precio\":\"2500.00\",\"color\":\"Multicolor\",\"naturaleza\":\"Comestible\",\"estado\":\"activo\"}', '2026-03-15 17:12:42'),
(43, 70, 34, 'AJUSTE_STOCK', 'Agregado de stock manual: 1', '{\"stock\":50}', '{\"stock\":51}', '2026-03-15 17:25:06'),
(44, 70, 34, 'AJUSTE_STOCK', 'Agregado de stock manual: 1', '{\"stock\":51}', '{\"stock\":52}', '2026-03-15 17:25:13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cfg_sis`
--

CREATE TABLE `cfg_sis` (
  `id_cfg` int(11) NOT NULL,
  `moneda` varchar(50) NOT NULL DEFAULT 'COP',
  `idioma` varchar(50) NOT NULL DEFAULT 'Español',
  `zona_hor` varchar(100) NOT NULL DEFAULT 'America/Bogota',
  `fmt_fecha` varchar(50) NOT NULL DEFAULT 'dd/mm/yyyy',
  `estilo_ui` varchar(50) NOT NULL DEFAULT 'Claro',
  `act_auto` tinyint(1) NOT NULL DEFAULT 1,
  `notif_act` tinyint(1) NOT NULL DEFAULT 1,
  `act_prog` varchar(50) DEFAULT NULL,
  `auth_2fa` tinyint(1) NOT NULL DEFAULT 0,
  `intentos_max` int(11) NOT NULL DEFAULT 3,
  `bloqueo_min` int(11) NOT NULL DEFAULT 30,
  `log_cambios` tinyint(1) NOT NULL DEFAULT 1,
  `retencion_log` int(11) NOT NULL DEFAULT 365,
  `id_usu_mod` int(11) DEFAULT NULL,
  `fch_ult_mod` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `horario_atencion` varchar(100) DEFAULT '',
  `dias_festivos` varchar(255) DEFAULT '',
  `whatsapp` varchar(100) DEFAULT '',
  `instagram` varchar(100) DEFAULT '',
  `facebook` varchar(100) DEFAULT '',
  `mensaje_confirmacion` text DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cfg_sis`
--

INSERT INTO `cfg_sis` (`id_cfg`, `moneda`, `idioma`, `zona_hor`, `fmt_fecha`, `estilo_ui`, `act_auto`, `notif_act`, `act_prog`, `auth_2fa`, `intentos_max`, `bloqueo_min`, `log_cambios`, `retencion_log`, `id_usu_mod`, `fch_ult_mod`, `horario_atencion`, `dias_festivos`, `whatsapp`, `instagram`, `facebook`, `mensaje_confirmacion`) VALUES
(1, 'USD', 'Inglés', 'America/New_York', 'dd/mm/yyyy', 'Oscuro', 0, 0, '', 0, 3, 30, 1, 365, NULL, '2025-12-01 17:30:53', 'Lunes a Sábado 8am-6pm', '25/12, 01/01', '573001234567', '@floraltech', 'facebook.com/floraltech', '¡Gracias por tu pedido! Pronto recibirás confirmación.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cli`
--

CREATE TABLE `cli` (
  `idcli` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `telefono` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL COMMENT 'Ruta de la foto de perfil del cliente',
  `avatar_data` mediumblob DEFAULT NULL COMMENT 'Imagen de perfil en binario',
  `avatar_tipo` varchar(30) DEFAULT NULL COMMENT 'MIME type ej. image/jpeg, image/png',
  `fecha_registro` date DEFAULT curdate(),
  `fecha_actualizacion` datetime DEFAULT NULL COMMENT 'Fecha de última actualización',
  `empleado_registro` int(11) DEFAULT NULL COMMENT 'ID del empleado que registró al cliente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cli`
--

INSERT INTO `cli` (`idcli`, `nombre`, `direccion`, `telefono`, `email`, `avatar`, `avatar_data`, `avatar_tipo`, `fecha_registro`, `fecha_actualizacion`, `empleado_registro`) VALUES
(20, 'Maury Yesid Echeverria Silva', 'Calle 7 #1C-84', '3137970263', 'mauryecheverria948@gmail.com', 'uploads/avatars/cliente/avatar_cli_20_1773511767.png', NULL, NULL, '2026-03-12', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion_inventario`
--

CREATE TABLE `configuracion_inventario` (
  `id` int(11) NOT NULL,
  `stock_minimo` int(11) DEFAULT 20,
  `dias_vencimiento` int(11) DEFAULT 30,
  `alertas_email` tinyint(1) DEFAULT 1,
  `moneda` varchar(10) DEFAULT 'USD',
  `iva_porcentaje` decimal(5,2) DEFAULT 13.00,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `configuracion_inventario`
--

INSERT INTO `configuracion_inventario` (`id`, `stock_minimo`, `dias_vencimiento`, `alertas_email`, `moneda`, `iva_porcentaje`, `fecha_actualizacion`) VALUES
(1, 20, 7, 1, 'COP', 19.00, '2026-03-15 15:30:03');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detped`
--

CREATE TABLE `detped` (
  `iddetped` int(11) NOT NULL,
  `idped` int(11) NOT NULL,
  `idtflor` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) GENERATED ALWAYS AS (`cantidad` * `precio_unitario`) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detped`
--

INSERT INTO `detped` (`iddetped`, `idped`, `idtflor`, `cantidad`, `precio_unitario`) VALUES
(1, 1, 1, 5, 5.99),
(2, 1, 3, 2, 4.75),
(3, 1, 8, 3, 2.99),
(4, 2, 2, 4, 7.50),
(5, 2, 5, 1, 6.25),
(6, 3, 4, 5, 12.99),
(7, 3, 7, 2, 8.99),
(8, 4, 6, 5, 3.50),
(9, 4, 1, 2, 5.99),
(10, 5, 3, 10, 4.75),
(11, 5, 5, 2, 6.25),
(12, 6, 8, 6, 2.99),
(14, 8, 3, 1, 4.75),
(15, 8, 6, 1, 3.50),
(16, 9, 1, 6, 25.99),
(17, 9, 2, 3, 18.50),
(18, 10, 3, 2, 35.00),
(19, 10, 4, 1, 22.75),
(20, 11, 5, 3, 85.00),
(21, 12, 1, 2, 25.99),
(22, 12, 4, 1, 22.75),
(23, 13, 1, 5, 25.99),
(24, 13, 3, 3, 35.00),
(25, 13, 5, 2, 85.00),
(26, 14, 10, 1, 15.00),
(27, 15, 12, 1, 15.00),
(28, 16, 12, 1, 15.00),
(29, 16, 11, 1, 15.00),
(30, 17, 12, 1, 15.00),
(31, 18, 12, 7, 15.00),
(32, 18, 11, 7, 15.00),
(33, 19, 6, 189, 3.50),
(34, 19, 5, 1, 6.25),
(35, 19, 4, 1, 12.99),
(36, 19, 7, 1, 8.99),
(37, 19, 1, 1, 5.99),
(38, 19, 8, 1, 2.99),
(39, 19, 2, 1, 7.50),
(40, 20, 3, 1, 4.75),
(41, 21, 6, 1, 10000.00),
(42, 22, 6, 180, 10000.00),
(57, 26, 3, 6, 6000.00),
(58, 26, 25, 1, 15000.00),
(61, 23, 6, 1, 10000.00),
(62, 29, 3, 12, 4.75),
(63, 30, 34, 5, 5000.00),
(64, 31, 34, 2, 5000.00),
(65, 32, 37, 1, 2500.00),
(66, 33, 38, 15, 3000.00),
(67, 34, 38, 20, 3000.00),
(68, 35, 38, 1, 3000.00),
(69, 36, 38, 1, 3000.00),
(70, 36, 37, 1, 2500.00),
(71, 37, 38, 1, 3000.00),
(72, 38, 38, 1, 3000.00),
(73, 38, 37, 1, 2500.00),
(74, 39, 38, 1, 3000.00),
(75, 40, 38, 1, 3000.00),
(76, 41, 38, 1, 3000.00),
(77, 42, 37, 2, 2500.00),
(78, 43, 41, 3, 1000.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresa`
--

CREATE TABLE `empresa` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `email_contacto` varchar(255) DEFAULT NULL,
  `horarios_apertura` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL COMMENT 'Ruta del logo de la empresa',
  `facebook` varchar(255) DEFAULT NULL COMMENT 'URL de Facebook',
  `instagram` varchar(255) DEFAULT NULL COMMENT 'URL de Instagram',
  `whatsapp` varchar(50) DEFAULT NULL COMMENT 'N??mero de WhatsApp',
  `moneda` varchar(10) DEFAULT 'CRC' COMMENT 'C??digo de moneda (CRC, USD, etc)',
  `iva_porcentaje` decimal(5,2) DEFAULT 13.00 COMMENT 'Porcentaje de IVA',
  `zona_horaria` varchar(50) DEFAULT 'America/Costa_Rica' COMMENT 'Zona horaria',
  `formato_fecha` varchar(20) DEFAULT 'd/m/Y' COMMENT 'Formato de fecha',
  `footer_activo` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=mostrar footer en zona cliente, 0=ocultar',
  `nequi_numero` varchar(50) DEFAULT NULL COMMENT 'Número Nequi (muestra debajo del QR)',
  `nequi_qr_imagen` longblob DEFAULT NULL COMMENT 'Imagen del QR Nequi en BD',
  `nequi_qr_tipo` varchar(100) DEFAULT NULL COMMENT 'MIME type ej. image/jpeg, image/png',
  `nequi_qr` varchar(255) DEFAULT NULL COMMENT 'Ruta legacy del QR',
  `cobrar_envio` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1=activar cobro envío, 0=desactivar',
  `precio_envio` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Precio del envío a domicilio'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empresa`
--

INSERT INTO `empresa` (`id`, `nombre`, `direccion`, `telefono`, `email_contacto`, `horarios_apertura`, `logo`, `facebook`, `instagram`, `whatsapp`, `moneda`, `iva_porcentaje`, `zona_horaria`, `formato_fecha`, `footer_activo`, `nequi_numero`, `nequi_qr_imagen`, `nequi_qr_tipo`, `nequi_qr`, `cobrar_envio`, `precio_envio`) VALUES
(1, 'FloralTech', 'dig 82 g 73 a 72', '3137970263', 'flsfssds@gmail.com', 'Lunes a viernes de 8 am a 5pm', NULL, 'https://facebook.com/floraltech', 'https://instagram.com/floraltech', '3137970263', 'COP', 19.00, 'America/Bogota', 'd/m/Y', 1, '3137970263', 0x89504e470d0a1a0a0000000d4948445200000274000002760806000000d6a11fc4000000017352474200aece1ce90000000467414d410000b18f0bfc6105000000097048597300001d8700001d87018fe5f165000074ed49444154785eeddc7fb01e5779d8f1e7bded049931585169fc63d2f807c5b1a030f2552635090a46367f48a64d254f27e06b1aa81c32012604fba650d46486a2e232b20732c8651aae3d10215a3cb6e8782ca7a5aa841bb04333121912c9148f102ed88626914930d8e9bcfb6effb87b769ff39cb3fbee9e5de97d7df97e66ce9cebe3fb9cbbbbefbef73e7aced9779465593e5a5810c973e922cf7391d168356e349291fd8629f23c97d16854ced3375e8aff6eab0a53e721d27a8e5c4446f6380a6de6688a971673c4e347c5ffe9121fbf0e32650efdd3dc71d8786998a339be3a0fa999a331bec5797489979a39a443bcd4ccd1255e2273e878fb7ea863e790731c2f9139aab0f8fb2146cf11bd0e2db83952e3a59863b8f8ea3ad8f741a3e27768f47e6a21179105731e5de347a28ea3637c291adffe3a94e7a1e33b1c47791eb27a2c5de39d3ccfa5fcbb9a1aefeeef19c67be7d18117afdea76d55972d6d1e7d1febf763db39e2f12369191e8f2f5e8736c7a0effaf0fd507f1ea32ccbf2bebf9044f41bb17a213a89fd424a7923d75c80695c5c751e69f1e57178f1ddcea37ca95c7cc27188f48bcf137f21d9ebefae69db7847df87bd7fa1f48c2fcfa383eab2757b233bdeeb30d82fa4556de688c7afdec7dde2c3eb202d8ea1fa69f5efe7a6399ae3ddffa99fa3313ee538ce62bc34cca1e3f5fd6ce3a5610e69192f0d730c195fdd8f71b139aa1f3b3d5e227344afe3147a8e947851730c175f5d07fd3e68321aa9bfabea389a5ec740ecef7b97787b1c09f122ea38bcf876d741a426bee37104ef838ef14e308fa8846e9af80d519936473cbeba90ade31b5ed0a639dac44bc31c6de3a5668ee6f8ea3a38b139a42e3e721c7de3a5668e2ef15233877488979a39f2967f2064807889ccd1355ecc1c5558df5fd0dde2a598231e1fde87317e7c751e6daf838888f4fc059f9fd57fc0b5bb0ea28f43c7773c8ebef1e55dd5233ecf73595858e87c1d1d771f89a8e3e860a878ef3c3aa84e5b1d4707fa3abae3e8324f2c5e8af75a1b557c751e9212afdf4fc5489b39a2f1ea756c338734c44bcb399ae2a5c51cf178776689f129e731e5ef4bdd1c4142d7e68062dc1cb38a177392d32e489df23812e3a598a30aeb1ebf6ab5bc9b1caffe5095e7d141aefed054f1d58d3d8dfbeed4f3d03f2df883d7c290f1e55d55c64fbf0e363ecf6753f1b4f1a28fa305eff8ddfdd021deb1efa7ea9dda8e8d97ae7f30d5fb51bf1fdace11bb0e4e9b39ce66bcb498a38aafae83d5344719afefa7c8fba06e8e687ce43eee1b2f0d73486d7cc279e8f80ec7d1255e6ae69081e2cbfba8215e6ae6e8122f9139bac68b9943c797f3b4e0e6a87e6c62bc7a1dbbc44b31479f78111189fe7d1f15095d875fd0753764f892c7f58d776cbc74fe051fbfa1dace51172f2de7d037948d9716734cbb21d2e247c5ff59d534479b786998a38a6fbe0e523387fe69ee38868b9f7e1e8df129c7d1225e2273748d1733c790f175ef8798e03c3ac68b3d8e847851735461f1f7539351cf5fd07de3454424fa0bbebdbcfc874f751d62ef833aeebbf57174791dfac63bd579a4c797775519dffd3ae4f96cfe01e7b8fb40a45fbc771e1d78f761e2df77fb7eb6bf339a78c7afde0f6de7a87e6c627cc3fbb9cd1cf1f8ea3eac9b23dc43a76fa80e3772f946343764eb1ba1677cdeb80493701e3a3ee5387ac4972fd50ce29d7c4e9660f2e45f28eec726c6abfba8eecdd3c4c6779d27162f499b72abebe05e8736c751c647de4f7de3db9c47637cca7144e24564ea1cd3e265ca1cd1d7b143bcd3142f2de698162f53e668132f0d735461e11f38ad363e761d6bc4e6e8122f9139bac68b99a38aafae437587d77373e8ef76c7d1f43a387de3a5f63cdac74bec38bcf8e9d7418a39a2f129c731407c791f748817771e3de29d587cb0e45a67da0d316d8eb31d2f53e6688eaf6ea8a639a42e3ee5381ae2a5618ebef1ceb478993247db1bb26e8eb6f1523347ff7817d6ee17b49d43bf0e6de2c5cc118fafeec33a6e8e2abe3a8f69d7c119e95f8cb1fb698abef12222d2f80fb8e9d7216ffa075cca7124c697c731407cdbfbb94e9ff87cc07fc0b57d3f68eec74a6abcba0f92f6d0a978fb5e6fa38a7797afdb3c65bcbe9f5bfcc3c789c677a88cd5c54bea7998fba8ed1cf1f8d5236b3347343ee5380688af7b3fb699a3295e1ae60812ba6917a48efd01d30ec8ea1b2f668e2aac7dbca83952e3a598435fc7f23cba882ea1acded8ad94f169e79137fdc16cc1c5e9f348892f8fc38b9f7e1d1ae3138e4364f55c52e3f3bcff124c797f778877f47d9454b1ec1defdf87d53bb51d7b1dbcebd1422c5e445acf51172f2de7a8e2d57528748a57c7a1df07d3e688c69bfba8698e36f132650e395bf129e7d1102f53e69016f132650eef3eaa89978639ce55bcd4cca1e3cb791ad839aa1fdb2e5ecc1cfa756c1b2f6a8eb3133f2afe4fb3d1e8ec3d54d6790f9de31d40ca2f687343f58d97aebfe06b2e64db39e22fe8aa367334c54b8b39e2f1d50dd53e3e7e1d64ca1cfaa7e91bcade47757334c757e721357334c6b7388f2ef15233877488979a39bac44b640e1d6fdf0f75ec1c728ee325324715167f3fc4e839a2d7a10537476abc14730c175f5d07fb3e6854f30b7edaebe0e433fe075c291adffe3a94e7a1e33b1c47791eb27a2c5de39d3c4ffb079ce3ee2311751c1d0c15ef9d47075ebc7a9fb6555db6b479f47dacdf8f6de788c727544c23bf17db1c83beebc3f743fd79847be8127e2189e83762f5427412fb8594f246aeb900d3b8b8ea3cd2e2cbe3f0e2bb9d47f952b9f884e310e9179f27fe42b2d7df5dd3b6f18ebe0f7bff42e9195f9e4707d565ebf64676bcd761b05f48abdacc118f5fbd8fbbc587d7415a1c43f5d3eadfcf4d7334c7bbff533f47637cca719cc578699843c7ebfbd9c64bc31cd2325e1ae61832beba1fe36273543f767abc44e6885ec729f41c29f1a2e6182ebeba0efa7dd064343adb5b2cdaf18e23215e441d8717dfee3a88d4c4773c8ee07dd031de09e691c8926b9df80d519936473cbeba90ade31b5ed0a639dac44bc31c6de3a5668ee6f8ea3a38b139a42e3e721c7de3a5668e2ef15233877488979a39f2967f2064807889ccd1355ecc1c5558df5fd0dde2a598231e1fde87317e7c751e6daf838888f4fc059f9fd57fc0b5bb0ea28f43c7773c8ebef1e55dd5233e1f700fdd2ce3bdf3e8a03a6d751c1de8ebe88ea3cb3cb17829de6b6d54f1d579484abc7e3f15236de688c6abd7b1cd1cd2102f2de7688a971673c4e3dd9925c6a79cc794bf2f757304095d9b038a7173cc2a5ecc494ebb2075cae3488c97628e2aac7bfcaab4cf6f2ba93f54e5797490c7366bab1b7b1af7dda9e7a17f5af007af8521e3cbbbaa8c9f7e1d6c7c9ecfa6e269e3451f470bdef1bbfba143bc63df4fd53bb51d1b2f5dff60aaf7a37e3fb49d23761d9c36739ccd78693147155f5d07ab698e325edf4f91f741dd1cd1f8c87ddc375e1ae690daf884f3d0f11d8ea34bbcd4cc2103c597f75143bcd4ccd1255e2273748d1733878e2fe769c1cd51fdd8c478f53a768997628e3ef1222212fdfb9eb087aeee860c5ff2b8bef18e8d97cebfe0e33754db39eae2a5e51cfa86b2f1d2628e6937445afca8f83fab9ae668132f0d7354f1cdd7416ae6d03fcd1dc770f1d3cfa3313ee5385ac44b648eaef162e61832beeefd10139c47c778b1c791102f6a8e2a2cfe7e6a32eaf90bba6fbc8888447fc1b79797fff0a9ae43ec7d50c77db73e8e2eaf43df78a73a8ff4f8f2ae2ae3bb5f873c9fcd3fe01c771f88f48bf7cea303ef3e4cfcfb6edfcff6774613eff8d5fba1ed1cd58f4d8c6f783fb799231e5fdd877573847be8f40dd5e1462edf88e6866c7d23f48ccf1b976012ce43c7a71c478ff8f2a59a41bc93cfc9124c9efc0bc5fdd8c478751fd5bd799ad8f8aef3c4e22569536e751ddcebd0e638caf8c8fba96f7c9bf3688c4f398e48bc884c9d635abc4c9923fa3a7688779ae2a5c51cd3e265ca1c6de2a5618e2a2cfc03a7d5c6c7ae638dd81c5de2253247d778317354f1d575a8eef07a6e0efdddee389a5e07a76fbcd49e47fb78891d87173ffd3a483147343ee53806882fef830ef1e2cea347bc138b0f965ceb4cbb21a6cd71b6e365ca1ccdf1d50dd53487d4c5a71c4743bc34ccd137de99162f53e6687b43d6cdd1365e6ae6e81fefc2dafd82b673e8d7a14dbc9839e2f1d57d58c7cd51c557e731ed3a3823fd8b31763f4dd1375e44441aff0137fd3ae44dff804b398ec4f8f23806886f7b3fd7e9139f0ff80fb8b6ef07cdfd58498d57f741d21e3a156fdfeb6d54f1eef2759ba78cd7f7738b7ff838d1f80e95b1ba78493d0f731fb59d231ebf7a646de688c6a71cc700f175efc7367334c54bc31c414237ed82d4b13f60da01597de3c5cc5185b58f1735476abc1473e8eb589e4717d12594d51bbb95323eed3cf2a63f982db8387d1e29f1e57178f1d3af43637cc27188ac9e4b6a7c9ef75f8229efef0ef18ebe8f922a96bde3fdfbb07aa7b663af83773d5a88c58b48eb39eae2a5e51c55bcba0e854ef1ea38f4fb60da1cd178731f35cdd1265ea6cc21672b3ee53c1ae265ca1cd2225ea6cce1dd4735f1d230c7b98a979a39747c394f033b47f563dbc58b9943bf8e6de345cd7176e247c5ff69361a9dbd87ca3aefa173bc0348f9056d6ea8bef1d2f5177ccd856c3b47fc055dd5668ea6786931473cbebaa1dac7c7af834c9943ff347d43d9fba86e8ee6f8ea3ca4668ec6f816e7d1255e6ae6900ef1523347977889cca1e3edfba18e9d43ce71bc44e6a8c2e2ef87183d47f43ab4e0e6488d97628ee1e2abeb60df078d6a7ec14f7b1d9c7cc6ff802b45e3db5f87f23c747c87e328cf43568fa56bbc93e769ff8073dc7d24a28ea383a1e2bdf3e8c08b57efd3b6aacb96368fbe8ff5fbb1ed1cf1f8848a69e4f7629b63d0777df87ea83f8f700f5dc22f2411fd46ac5e884e62bf9052dec83517601a17579d475a7c791c5e7cb7f3285f2a179f701c22fde2f3c45f48f6fabb6bda36ded1f761ef5f283de3cbf3e8a0ba6cdddec88ef73a0cf60b69559b39e2f1abf771b7f8f03a488b63a87e5afdfbb9698ee678f77feae7688c4f398eb3182f0d73e8787d3fdb786998435ac64bc31c43c657f7635c6c8eeac74e8f97c81cd1eb38859e23255ed41cc3c557d741bf0f9a8c46677b8b453bde7124c48ba8e3f0e2db5d07919af88ec711bc0f3ac63bc13c125972ad13bf212ad3e688c75717b2757cc30bda34479b786998a36dbcd4ccd11c5f5d07273687d4c5478ea36fbcd4ccd1255e6ae6900ef1523347def20f840c102f9139bac68b99a30aebfb0bba5bbc1473c4e3c3fb30c68fafcea3ed751011919ebfe0f3b3fa0fb876d741f471e8f88ec7d137bebcab7ac4e703eea19b65bc771e1d54a7ad8ea3037d1ddd71749927162fc57bad8d2abe3a0f4989d7efa762a4cd1cd178f53ab699431ae2a5e51c4df1d2628e78bc3bb3c4f894f398f2f7a56e8e20a16b7340316e8e59c58b39c96917a44e791c89f152cc5185758f5f95f6f96d25f587aa3c8f0ef2d8666d75634fe3be3bf53cf44f0bfee0b530647c795795f1d3af838dcff3d9543c6dbce8e368c13b7e773f748877ecfba97aa7b663e3a5eb1f4cf57ed4ef87b673c4ae83d3668eb3192f2de6a8e2abeb6035cd51c6ebfb29f23ea89b231a1fb98ffbc64bc31c521b9f701e3abec3717489979a3964a0f8f23e6a88979a39bac44b648eaef162e6d0f1e53c2db839aa1f9b18af5ec72ef152ccd1275e4444a27fdf13f6d0d5dd90e14b1ed737deb1f1d2f9177cfc866a3b475dbcb49c43df50365e5acc31ed86488b1f15ff6755d31c6de2a5618e2abef93a48cd1cfaa7b9e3182e7efa7934c6a71c478b7889ccd1355ecc1c43c6d7bd1f6282f3e8182ff63812e245cd5185c5df4f4d463d7f41f78d171191e82ff8f6f2f21f3ed57588bd0feab8efd6c7d1e575e81bef54e7911e5fde55657cf7eb90e7b3f9079ce3ee03917ef1de7974e0dd87897fdfedfbd9fece68e21dbf7a3fb49da3fab189f10defe73673c4e3abfbb06e8e700f9dbea13adcc8e51bd1dc90ad6f849ef179e3124cc279e8f894e3e8115fbe54338877f2395982c9937fa1b81f9b18afeea3ba374f131bdf759e58bc246dcaadae837b1dda1c47191f793ff58d6f731e8df129c711891791a9734c8b972973445fc70ef14e53bcb498635abc4c99a34dbc34cc5185857fe0b4daf8d875ac119ba34bbc44e6e81a2f668e2abeba0ed51d5ecfcda1bfdb1d47d3ebe0f48d97daf3681f2fb1e3f0e2a75f0729e688c6a71cc700f1e57dd0215edc79f4887762f1c1926b9d6937c4b439ce76bc4c99a339bebaa19ae690baf894e36888978639fac63bd3e265ca1c6d6fc8ba39dac64bcd1cfde35d58bb5fd0760efd3ab4891733473cbeba0febb839aaf8ea3ca65d0767a47f31c6eea729fac68b8848e33fe0a65f87bce91f7029c791185f1ec700f16defe73a7de2f301ff01d7f6fda0b91f2ba9f1ea3e48da43a7e2ed7bbd8d2ade5dbe6ef394f1fa7e6ef10f1f271adfa13256172fa9e761eea3b673c4e3578faccd1cd1f894e31820beeefdd8668ea67869982348e8a65d10676161c1fb6f000000b437994c6a13b75a35ff20eebc87cea9cb1001000030dd6432b1438d155369c8bfc23d742d9790a8d0010000a4cb2693ce05352992ba20d1b34bae75aa846f35be4d0c000000e2a215bac43d744199cd85e5792eb9ea25cf8baa9deb010000d0475ee457aeb9b1e20bff9b4d9ee6fa3c7715ba0e7be85ca5ae2e43040000c074d10a5d740b9cdb55579f7f857be8f4263c3581c51e3a00008074b3dd4347850e0000a0b76885ee9ceda1536bbc00000048e7e55b5df6d0b9be88efbc87cea9cb10010000305db44217db02c7e7d0010000cca7d9eea1e373e80000007a8b56e8ced91e3a3e870e000060106eef5ce73d74ea7b733e870e00006036a215bae81638b7abae3eff0af7d0e94d787c0e1d0000c05931db3d7454e80000007a8b56e8ced91e3a3e870e000060105ebed5650f9debf91c3a000080d98956e8625be0f81c3a000080f934db3d7467e173e8366fde6c8700d9ba75abecddbbd70eaf396f7ef39be5bbdffdae1d5e535efbdad7caedb7df6e87e7c2ce9d3be5cc99337678e67eedd77e4d969696ecf0cc3dfdf4d3f296b7bcc50ecf858f7dec63b269d3263b9c647979598e1e3d6a87013976ec981d4a16add025eea1932ccbf2c96452b6ccf5599667b6576d488b8b8b799133d268655b5e5eb6b7ca9a74e9a59706e7bed6daf6eddbed69cf8d0b2fbc3038de79687bf6ecb1873a174e9d3a151cebbcb423478ed8c34d76db6db705f3d3688b8b8bf656e925cbb27c3c1e776b451e361e8f557c96afae9baa6ccf7d351a8dcaa759cb7eb534579f1d020000a035975fc5f2ac2adf8ae769ae1f8ddc53aeee49d622055d1d5afde461f7d445f9f4054fb9020000f49697f9563ccfaaf2ad2aefd2ff4fc7974f36d81cd0657c41c648850e0000a037bd025ae65bba3217c9b7bcca9c8a0f1e55f52a74aae773e800000086652b6d6eacf8c2ff669da7b9be88efbc874e67900000004867f3ace88a682c4f737d111feea1d36bb30d0d000000e97465aeccaf6c2f7ea5cecbd3547cb887ce7b6a4255e4ca4c910a1d0000405f752ba0b1ca9ce3e5692abefb1e3aa1420700003084a042a7aa708d7be8bc15d5843d743ce50a0000308ca04217abd495d959dd8a6a6c0f9deb8b8c2fc81cf59a2e00000092e83d70b13cabcab7aabccbabcca9f8700f9deb8b8c2fc818a9d0010000f4a65740cb7c4b57e622f996579953f1ddf7d051a10300001884adb4b9b1e20bff9b759ee6fa22bef31e3a9d41020000209dcdb3a22ba2b13ccdf5457cb887ce7b6aa2be010000209daecc95f995edc5afd479799a8a0ff7d0794f4da88a5c992952a1030000e8ab6e0534569973bc3c4dc577df43c7e7d00100000c22a8d0a92a5ce31e3a6f4535610f1d4fb90200000c23a8d0c52a75657656b7a22a32cab22c1f8d469217df54f6b91e092d2c04c5bd649b376f96e3c78fdbe1242b2b2b7608e7d0d1a347e5c081037638c9f2f2b2ecddbbd70eaf39975d76993cf1c41376784db9e1861be4c1071fb4c373e18e3bee90679f7dd60ecfdc75d75d275bb66cb1c333f7cd6f7e535efef297dbe164ef7bdffb64ddba757638c9dbdef636b9ecb2cbec7092e5e565b9f3ce3bed7092a5a52579c31bde6087710edd72cb2d7628c9e2e2a21c3b76cc0e27cb269368156e9ad168b49aa78d46557c9665f9643299da32d30f69717171752d7780b6b2b262a7c739b4b4b414bc26a96d7979d94ebf265d7ae9a5c1b9afb5b67dfb767bda78813a75ea54f0faf669cf3cf38cfd1173e1b6db6e0b8e35b52d2d2dd9e9710eadacac04af496a5b5c5cb4d3f7926559d0c6e371d98f5daf9afd7ed782325beefaba3d74faa90b00000024f3f2ad2e7be85c5fc477de4357aef1020000a0179b67f139740000002f20ba3257e657b617bf52e7e5692a9ecfa10300009881ba15d05865cef1f23415df7d0f1d9f430700003088a042a7aa708d7be8bc15d5843d747c0e1d0000c030820a5dac52576667752baab13d74ae2f32be2073d46bba00000048a2f7c0c5f2ac2adfaaf22eaf32a7e2c33d74ae2f32be2063a442070000d09b5e012df32d5d998be45b5e654ec577df4347850e00006010b6d2e6c68a2ffc6fd6799aeb8bf8ce7be87406090000807436cf8aae88c6f234d717f1e11e3aefa989fa0600008074ba3257e657b617bf52e7e5692a3edc43e73d35a12a7265a648850e0000a0afba15d05865cef1f23415df7d0f1d9f430700003088a042a7aa708d7be8bc15d5843d743ce50a0000308ca04217abd495d959dd8a6a6c0f9deb8b8c2fc81cf59a2e00000092e83d70b13cabcab7aabccbabcca9f8700f9deb8b8c2fc818a9d0010000f4a65740cb7c4b57e622f996579953f1ddf7d051a10300001884adb4b9b1e20bff9b759ee6fa227e9465593e5a5888063519b24ab779f366397efcb81d4eb2b2b222bb76edb2c349eebaeb2e3bb4266dddba55366edc688793dc7cf3cd72e0c0013b9c64797959f6eedd6b87939c3e7d5a3efde94fdbe124e79d779ebcef7defb3c3c92ebbec3279e28927ecf0cc6dd9b245b66edd6a87935c79e59572d34d37d9e124cf3efbacdc71c71d7638d9f2f2b29c7ffef97638c9810307e4f1c71fb7c349aebbee3ad9b2658b1d9eb96f7ef39bf2f297bfdc0e277be6996764fdfaf57678e6969797e5ce3befb4c349969696e4339ff98c1d4e72f2e449397af4a81d5e93def5ae77d9a12477df7db7dc72cb2d7638c9e2e2a21c3b76cc0e279b4c2676683539d37d9eaf56ea8a3cad36ffcab22c9f4c26793699acf6eebfb3acb10d697171312f8ebd775b5959b1d327dbb76f5f30ff5a6c274f9eb4a79e6c696929983fb52d2f2fdbe9931d3e7c38983fb5ad5fbfde4edfcba5975e1afc8c7968bb77efb6873a179e7aeaa9e058fbb4a79f7edafe8864dbb66d0be64f6d7bf6ecb1d3cf8553a74e05c7daa73df3cc33f647cc85db6ebb2d38d6d4b6b4b464a74f76e2c48960feb5d8f6eddb674f3dd9caca4a307f6a5b5c5cb4d3f732ceb27c3c1e776e998b53f1e11e3aefa989d85317eca1030000e86bb67be8f81c3a00008041b8bd739df7d0e9a75df91c3a000080d9092a74b14a1d9f43070000309fca95d09a3cabcab7aabccbabcca9f8700f9deb8b8c2fc818a9d0010000f4a65740cb7c4b57e622f996579953f1ddf7d051a10300001884adb4b9b1e20bff9b759ee6fa22bef31e3a9d41020000209dcdb3a22ba2b13ccdf5457cb887ce7b6aa2be010000209daecc95f995edc5afd479799a8a0ff7d0794f4da88a5c992952a1030000e8ab6e0534569973bc3c4dc577df43c7e7d00100000c22a8d0a92a5ce31e3a6f4535610f1d4fb90200000c23a8d0c52a75657656b7a21adb43e7fa22e30b3247bda60b000080247a0f5c2ccfaaf2ad2aeff22a732a3edc43e7fa22e30b32462a74000000bde915d032dfd295b948bee555e6547cf73d7454e800000006612b6d6eacf8c2ff669da7b9be88efbc874e67900000004867f3ace88a682c4f737d111feea1f39e9aa86f00000048a72b73657e657bf12b755e9ea6e2c33d74de5313aa2257668a54e8000000faaa5b018d55e61c2f4fd3f15996e53a41cb8b1f90e7f9ea44ba57161682ed77c9366fde2cc78f1fb7c34956565664d7ae5d7638c95d77dd25ef7ef7bbedf09a73f2e449d9b871a31d4e72f3cd37cb810307ec7092e5e565d9bb77af1d4e72f4e851d9ba75ab1d4ef29297bc441e78e0013b9ceccd6f7eb37cef7bdfb3c3495ef5aa57c9cb5ef6323b9ce4ad6f7deb60efa5219d39734676eedc6987931d3c7850366cd8608793dc70c30df2d0430fd9e1241ffef087e55fffeb7f6d8793fce55ffea5fcf99fffb91d4ef2fdef7f5f3ef6b18fd9e164ef7ffffb65ddba757638c9a64d9b64fdfaf57638c9f2f2b2dc79e79d7638c9d2d2927ce6339fb1c3494e9e3c29af7ad5abecf09ab36fdf3e79d7bbde658793dc7df7dd72cb2db7d8e1248b8b8b72ecd8313b9c6c3299745ff52c12b93ccf65341a15f123912ccbf2499ee793c9a455cb8a7e488b8b8bb9ab3cf66d2b2b2b76fa64fbf6ed0be65f8bede4c993f6d4932d2d2d05f3a7b6e5e5653b7db2c3870f07f3afc576fffdf7db53c739b46ddbb6e035496d7bf6ecb1d327bbefbefb82f953dbe5975f6ea7efe5820b2e087e466a3b72e4889d3ed96db7dd16cc9fda969696ecf4c94e9c3811ccbf16dbbe7dfbeca9275b595909e64f6d8b8b8b76fa5eb22c0bdad8f5e371d98fc7ae5f1d8bb5700f9debf39ccfa1030000384bf232df8ae75955be55e55dfaffe9f8700f9deb47233e870e0000e02c19720f5db011ceabd0a93efad40500000092d94a9b1b2bbef0bf59e769ae2fe2f91c3a00008019b1795674453496a7b9be880ff7d0e9b5d9860600008074ba3257e657b617bf52e7e5692a3edc43a7d7667545aecc14a9d0010000f455b7021aabcc395e9ea6e2bbefa1132a7400000043082a74aa0ad7b887ce5b514dd843c753ae000000c3082a74b14a5d999dd5ada8c6f6d0b9bec8f882cc51afe90200002089de0317cbb3aa7cabcabbbcca9c8a0ff7d0b9bec8f8828c910a1d0000406f7a05b4ccb774652e926f79953915df7d0f1d153a00008041d84a9b1b2bbef0bf59e769ae2fe23befa1d31924000000d2d93c2bba221acbd35c5fc4877be8bca726ea1b000000d2e9ca5c995fd95efc4a9d97a7a9f8700f9df7d484aac8959922153a000080beea5640639539c7cbd3547cf73d747c0e1d0000c020820a9daac235eea1f3565413f6d0f1942b0000c030820a5dac52576667752baab13d74ae2f32be2073d46bba00000048a2f7c0c5f2ac2adfaaf22eaf32a7e2c33d74ae2f32be2063a442070000d09b5e012df32d5d998be45b5e654ec577df4347850ef8b1b067cf9ee0170dadb9bdfbddef0efeb59dda76efde6d5f12006b509ef339740000002f6836cf8aae88c6f234d717f1e11e3aefa989fa0600008074ba3257e657b617bf52e7e5692a3edc43e73d35a12a7265a648850e0000a0afba15d05865cef1f23415df7d0f1d9f430700003088a042a7aa708d7be8bc15d5843d743ce50a0000308ca04217abd495d959dd8a6a6c0f9deb8b8c2fc81cf59a2e00000092e83d70b13cabcab7aabccbabcca9f8700f9deb8b8c2fc818a9d0010000f4a65740cb7c4b57e622f996579953f1ddf7d051a10300001884adb4b9b1e20bff9b759ee6fa22bef31e3a9d41020000209dcdb3a22ba2b13ccdf5457cb887ce7b6aa2be010000209daecc95f995edc5afd479799a8a0ff7d0794f4da88a5c992952a1030000e8ab6e0534569973bc3c4dc577df43c7e7d00100000c22a8d0a92a5ce31e3a6f4535610f1d4fb90200000c23a8d0c52a75657656b7a21adb43e7fa22e30b3247bda60b000080247a0f5c2ccfaaf2ad2aeff22a732a3edc43e7fa22e30b32462a74000000bde915d032dfd295b948bee555e6547cf73d7454e800000006612b6d6eacf8c2ff669da7b9be88efbc874e67900000004867f3ace88a682c4f737d111feea1f39e9aa86f00000048a72b73657e657bf12b755e9ea6e2475996e56d2a6e79910de6f96aa2d826a6adcd9b37cbf1e3c7ed7092959515d9b56b971d4ef2d8638fd9a1356be3c68d7628c9cd37df2c070e1cb0c34996979765efdebd7638c9d1a34765ebd6ad7638c94b5ef21279e08107ecf05cb8f7de7be5e4c9937638c9134f3c21dffad6b7ec301abcfad5af960d1b36d8e1997bd5ab5e25fffc9fff733b9ce4bcf3ce937ffc8fffb11d4ef6a52f7d49c6e3b11d4eb269d32659bf7ebd1d4eb2bcbc2c77de79a71d4eb2b4b4249ff9cc67ec7012fe2e7577f7dd77cb2db7dc6287932c2e2ecab163c7ec70b2c96462875693b3d16835711b8d8265d7dafc2bcbb27c3299942d737d96e599ed551bd2e2e262ee12d5be6d6565c54e8f7368696929784d52dbf2f2b29d3ed9e1c38783f953dbfaf5ebedf47363c78e1dc1f1d2683b77eeb4b70aa6b8edb6db82eb98da969696ecf4388756565682d724b52d2e2edae97bc9b22c1f8fc7dd5a91878dc763159fe59df7d0f1942b0000c0305c7e15cbb3aa7c2b9ea7b97ec4e7d0010000cc465ee65bf13cabcab7aabc4bff3f1dcfe7d0010000cc805e012df32d5d998be45b5e654ec5f3397400000033622b6d6eacf8c2ff669da7b9be88efbc874e67900000004867f3ace88a682c4f737d111feea1d36bb30d0d000000e97465aeccaf6c2f7ea5cecbd3547cb887ce7b6a4255e4ca4c910a1d0000405f752ba0b1ca9ce3e5692abefb1e3aa1420700003084a042a7aa708d7be8bc15d5843d743ce50a0000308ca04217abd495d959dd8a6a6c0f9deb8b8c2fc81cf59a2e00000092e83d70b13cabcab7aabccbabcca9f8700f9deb8b8c2fc818a9d0010000f4a65740cb7c4b57e622f996579953f1ddf7d051a10300001884adb4b9b1e20bff9b759ee6fa223e48e8bc0a9dea79ca1500006058b6d2e6c68a2ffc6fd6799aeb8bf820a1b3195f50a1e3295700008041b8ca5ce70a9dde4bc753ae000000b3e3f2ab589e55e55bf13ccdf5239e72050000988d7225b426cfaaf2ad2aeff22a732a9ea75c0100006640af8096f996aecc45f22daf32a7e3b32ccb758296173f20cff3d58974af2c2c04dbef926ddebc598e1f3f6e87932c2d2dd9219c63070e1cb0434996979765efdebd7638c9d1a34765ebd6ad7638c9860d1be4affeeaafec70b237bff9cdf2ddef7ed70e2779f39bdf2c575d75951d4eb27fff7eb9e79e7bec70926baeb9466ebffd763b9ce4cc993372e38d37dae164f7df7fbf6cd8b0c10e2779fffbdf2f5ff9ca57ec70925b6eb965b0df675ffffad7e53fffe7ff6c87935c72c925f2d9cf7ed60e277bd39bde24cf3efbac1d4ef2b18f7d4c366dda6487932c2f2fcb9d77de6987930cf53a22dd507f97161717e5d8b1637638d96432f1f3ad368a442ec8d7b22ccb27799e4f26934e6d488b8b8bb9ab3cd268ae2d2f2fdb5b25d9e1c38783f953dbfaf5ebedf4bd5c7ae9a5c1cf486df7df7fbf9d3ed9873ef4a160fed4b67dfb763b7db2a79e7a2a98bf4f7bfae9a7ed8f48b66ddbb660fed4b667cf1e3b7db2fbeebb2f983fb55d7ef9e576fa5e2eb8e082e067a4b623478ed8e993dd76db6dc1fc34dae2e2a2bd557ac9b22c6863db8fc7f9d8f5e371f0fdae857be8bca726ea1b000000d2e5917c2bfab9bf2aeff2f234151feea1f39e9a883d75c11e3a000080be86dc43176c84d395ba32f35b1d28b2422a7400000043082a74aa0a17db57175f51e573e80000006626a8d0c52a757c0e1d0000c07cd27be0627956956f557997579953f1e11e3ad717195f903152a1030000e84daf8096f996aecc45f22daf32a7e2bbefa1a34207000030085b697363c517fe37eb3ccdf5457ce73d743a83040000403a9b6745574463799aeb8bf8700f9df7d4447d030000403a5d992bf32bdb8b5fa9f3f234151feea1f39e9a5015b93253a442070000d057dd0a68ac32e778799a8aefbe878ecfa1030000184450a15355b8c63d74de8a6ac21e3a9e72050000184650a18b55eacaecac6e4535b687cef545c617648e7a4d1700000049f41eb8589e55e55b55dee555e6547cb887cef545c617648c54e80000007ad32ba065bea52b73917ccbabcca9f8ee7be8a8d00100000cc256dadc58f185ffcd3a4f737d11df790f9dce2001000090cee659d115d1589ee6fa223edc43e73d3551df000000904e57e6cafccaf6e257eabc3c4dc58fb22ccbdb54dcf2221bccf3d544b14d4c5bbffddbbf6d87001111d9bb77af1d4a72e4c811b9eebaebec70929ffcc99f943367ced8e164975d76993cf1c4137638c9fdf7df2f3b77eeb4c349f6ecd923bff33bbf638793bce215af909b6ebac90e27198d463f16ffa81cf23c1f7bec31b9f7de7bed7092f5ebd7cb7bdef31e3b9cec831ffca01d4a76f4e851b9f6da6bed7012fe2ea1ce507f9744442693891d5a4dcedcfb7f340a965debf2af20a1ab1237355164c2858560fb1d30b7fec7fff81f72fdf5d7dbe124ebd7af97679e79c60e27fb7148e88674d14517c9d34f3f6d87935d74d145f2bdef7dcf0e273974e8906cdfbedd0e27d9bd7bb77cf8c31fb6c36870e4c81179c31bde608781b935994cbaffc3ad5862f5123f710f45c4d6666bf6d0f1942b0000c0305c7e15cbb3aa7c2b9ea7b97ec4e7d0010000cc46e31e3ab55f8ecfa103000098537a05b4ccb774652e926f799539151f6c84f32a74aa8f3e750100008064b6d2e6c68a2ffc6fd6799aeb8bf8ce7be87406090000807436cf8aae88c6f234d717f1e11e3abd36dbd0000000904e57e6cafccaf6e257eabc3c4dc5877be8bca7265445aecc14a9d0010000f455b7021aabcc395e9ea6e2bbefa1132a7400000043082a74aa0ad7b887ce5b514dd843c753ae000000c3082a74b14a5d999dd5ada8c6f6d0b9bec8f882cc51afe90200002089de0317cbb3aa7cabcabbbcca9c8a0ff7d0b9bec8f8828c910a1d0000406f7a05b4ccb774652e926f79953915df7d0f1d153a00008041d84a9b1b2bbef0bf59e769ae2fe23befa1d31924000000d2d93c2bba221acbd35c5fc4877be8bca726ea1b000000d2e9ca5c995fd95efc4a9d97a7a9f8700f9df7d484aac8959922153a000080beea5640639539c7cbd3547cf73d747c0e1d0000c020820a9daac235eea1f3565413f6d0f1942b0000c030820a5dac52576667752baa22a32ccbf2d1682479f14d659feb91d0c24250dc4b76ecd831f9c10f7e6087935c75d55572d14517d9e1244f3ef9a43cfef8e37618e7c83ff807ff405efef297dbe1245ffdea57e5bdef7daf1d4ef2d297be541e78e0013b9cecb2cb2e93279e78c20e27f9b7fff6dfca962d5bec7092fdfbf7cb3df7dc6387936cd8b0415efdea57dbe1242f7de94be5d65b6fb5c3c976eedc29cf3cf38c1d4ef2d0430fc9b66ddbec7092bbefbe5bf6efdf6f87d79c871f7ed80e253b7af4a85c7bedb57638c9a953a7e4dbdffeb61dc63972e59557ca25975c6287d79c6c328956e1a6198d46ab79da6854c56759964f2693a92d33fd90366ddab4ba963b405b5959b1d327fbf8c73f1ecc4f3b77edb6db6eb32fc99a74e9a59706e7bed6daf6eddbed69277beaa9a782f9e7a51d3a74c81e2ea6b8e0820b82eb98da8e1c3962a74f76ebadb706f3d3ce5ddbb76f9f7d49d6a42ccb82361e8fcb7eec7ad5ecf7bb1694d972d7d7eda1d34f5d0000002099976f75d943e7fa22bef31eba728d17000000bdd83c8bcfa10300007801d195b932bfb2bdf8953a2f4f53f17c0e1d0000c00cd4ad80c62a738e97a7a9f8ee7be8f81c3a0000804104153a55856bdc43e7ada826eca1e373e8000000861154e86295ba323bab5b518deda1737d91f10599a35ed30500004012bd072e966755f9569577799539151feea1737d91f1051923153a000080def40a68996fe9ca5c24dff22a732abefb1e3a2a7400000083b0953637567ce17fb3ced35c5fc477de43a73348000000a4b3795674453496a7b9be880ff7d0794f4dd437000000a4d395b932bfb2bdf8953a2f4f53f1e11e3aefa90955912b33452a740000007dd5ad80c62a738e97a7a9f8ee7be8f81c3a0000804104153a55856bdc43e7ada826eca1e32957000080610415ba58a5aeccceea5654637be85c5f647c41e6a8d7740100009044ef818be55955be55e55d5e654ec5877be85c5f647c41c648850e0000a037bd025ae65bba3217c9b7bcca9c8aefbe878e0a1d0000c0206ca5cd8d155ff8dfacf334d717f19df7d0e90c12000000e96c9e155d118de569ae2fe2c33d74de5313f56d485ffdea5783f953dba38f3e1a249fa9edeb5fff7a307f6a7be49147ec69cf8dc71e7b2c38de796877dc71873dd464478f1e0d5edfd4f6f7fededfb3d3cf8d83070f06d7711edaa14387eca126bbf8e28b83f9fbb40b2fbcd0fe886437dc704370bfa4b6db6fbfdd4e3f174e9f3e1d1c6b9ff6d77ffdd7f647241b0d586cb8f3ce3b837b651edac99327eda1ce8d471f7d3438ded4f6d8638f05f74a6abbe5965beca1ce0d5d99732dba22aaf22e3796e7d3f6d0e9b559d55e289f43579efc9c99d7e392393fb6a14c26133b946cc8b986f6e3f05afeb898d7fb6c9eefb1793eb6a1ccf3390e796c43ce35cfea5640cb3c2b926f79799a8aefbe878ecfa10300001884abb0b9e6c68a2ffc6f36799aeb733e870e00006076820a5dac52576667752baab13d74ae2f32be2073d46bba0000004852ae84d6e45955be55e55d5e654ec5877be85c5f647c41c648850e0000a037bd025ae65bba3217c9b7bcca9c8aefbe878e0a1d0000c0206ca5cd8d155ff8dfacf334d717f19df7d0e90c12000000e96c9e155d118de569ae2fe2c33d74de5313f50d000000e97465aeccaf6c2f7ea5cecbd3547cb887ce7b6a4255e4ca4c910a1d0000405f752ba0b1ca9ce3e5692abefb1e3a3e870e0000601041854e55e11af7d0792baa097be878ca150000601841852e56a92bb3b3ba15d5d81e3ad717195f9039ea355d00000024d17be0627956956f557997579953f1e11e3ad717195f903152a1030000e84daf8096f996aecc45f22daf32a7e2bbefa1a34207000030085b697363c517fe37eb3ccdf5457ce73d743a83040000403a9b6745574463799aeb8bf8700f9df7d4447d030000403a5d992bf32bdb8b5fa9f3f234151feea1f39e9a5015b93253a442070000d057dd0a68ac32e778799a8aefbe878ecfa1030000184450a15355b8c63d74de8aaac828cbb27cb4b0100d8ac95546399477bce31df28d6f7cc30e27f9dffffb7fcb77bffb5d3b9ce4924b2e9157bce2157638c9dffccddfc857bffa553b9cece8d1a37628d97ff80fff41feeffffdbf76784d79f9cb5f2e6f7deb5bed7092bffddbbf95db6fbfdd0e277bf8e187ed50b283070fca8e1d3bec7092fdfbf7cbdd77df6d8793bcf6b5af1df49a0de991471e91fff7fffe9f1d9eb9a3478f0e766fbcfef5af970f7ef0837638c9f3cf3f2f7ffcc77f6c87e7c2a64d9b64fdfaf576784df9d18f7e24ffeb7ffd2f3b9cec0d6f78831d4a76f5d557cb4b5ffa523b9ce44d6f7a93fcdccffd9c1d4e72f1c517cbcffeeccfdae1b930994cec509967e5792ea3d1a848dc46652a579b7f6559964f26933c9b4cfc3ecbf22c737dd886b469d3a6d5d21fad555bb76e9dbd84bd5c75d555c1cf586b6debd6adf6b4939d397326987f5edafdf7df6f0f37d9873ef4a160fed4b67dfb763b3da6f8c0073e105cc7d4b673e74e3b3d90e7799ebfe8452f0aee977968fbf6edb387ba268db32c1f8fc79d5be6e2547cb887cef5a3119f430700007096cc760f9d7eea02000000c9bc7cabcb1e3ad717f17c0e1d0000c08cd83c2bba221acbd35c5fc4f3397400000033a02b73657e657bf12b755e9ea6e2c33d747a6d5657e4ca4c910a1d0000405f752ba0b1ca9ce3e5692abefb1e3a3e870e0000601041854e55e11af7d0792baa097be878ca150000601841852e56a92bb3b3ba15d5d81e3ad717195f9039ea355d00000024d17be0627956956f557997579953f1e11e3ad717195f903152a1030000e84daf8096f996aecc45f22daf32a7e2bbefa1a34207000030085b697363c517fe37eb3ccdf5457ce73d743a83040000403a9b6745574463799aeb8bf8700f9df7d4447d030000403a5d992bf32bdb8b5fa9f3f234151feea1f39e9a5015b93253a442070000d057dd0a68ac32e778799a8aefbe878ecfa1030000184450a15355b8c63d74de8a6ac21e3a9e72050000184650a18b55eacaecac6e4535b687cef545c617648e7a4d1700000049f41eb8589e55e55b55dee555e6547cb887cef545c617648c54e80000007ad32ba065bea52b73917ccbabcce9f82ccb729da0e5c50fc8f37c7522dd2b0b0bc1f6bb64c78e1d931ffce0077638c9debd7be5a1871eb2c349fed93ffb67f29ef7bcc70e273971e284bcfbddefb6c3c95efffad7dba1647ff2277f223ffad18fec70927ff36ffe8d5c77dd757678e6bef5ad6fc9a73ef5293b9ce4c52f7eb1fcab7ff5afecf05cb8f7de7be5e4c9937638c9134f3c21dffad6b7ec70921b6eb8411e7cf0413b9ce4cc9933b273e74e3b9cece0c183b261c3063b9ce4fdef7fbffcf11fffb11d4e72faf469f93fffe7ffd8e1242f7bd9cbe455af7a951d06e4e1871fb643c9eebaeb2e79e52b5f6987935c79e59572c92597d8e124870e1d92bd7bf7dae124575e79a5fcfeefffbe1d4e36994cfc7cab8d22910bf2b52ccbf2499ee793c9a4539b57fff25ffecbdc5531fbb677bdeb5d76fa645ffef29783f9d7623b70e0803df5b970f8f0e1e05853dbfaf5ebedf47363c78e1dc1f1ce43dbbe7dbb3dd4644f3df554307f9ff6f4d34fdb1f916cdbb66dc1fc34da8f4b7be49147ec5b622e7cf2939f0c8e35b55d7df5d576fa5eb22c0bdad8f6e3713e76fd781c7cbf6be11e3aefa989fa06000080747924dfe273e80000005e4086dc43176c84d395ba32f35b1d28b2422a7400000043082a74aa0a17db57175f51e573e80000006626a8d0c52a757c0e1d0000c07cd27be0627956956f557997579953f1e11e3ad717195f903152a1030000e84daf8096f996aecc45f22daf32a7e2bbefa1a34207000030085b697363c517fe37eb3ccdf5457ce73d743a83040000403a9b6745574463799aeb8bf8700f9df7d4447d030000403a5d992bf32bdb8b5fa9f3f234151feea1f39e9a5015b93253a442070000d057dd0a68ac32e778799a8aefbe878ecfa1030000184450a15355b8c63d74de8a6ac21e3a9e72050000184650a18b55eacaecac6e4535b687cef545c617648e7a4d1700000049f41eb8589e55e55b55dee555e6547cb887cef545c617648c54e80000007ad32ba065bea52b73917ccbabcca9f8ee7be8a8d00100000cc256dadc58f185ffcd3a4f737d11df790f9dce2001000090cee659d115d1589ee6fa223edc43e73d3551df000000904e57e6cafccaf6e257eabc3c4dc58fb22ccbdb54dcf2221bccf3d544b14d4c5bfff13ffe4779fae9a7ed7092d1683458c2f9f33ffff3b27dfb763b9ce4dbdffeb6dc7df7dd7618e7c890f7c579e79d27ef7bdffbecf05cd8b973a77cfef39fb7c349b66cd9225bb76eb5c349aebcf24ab9e9a69bec7092679f7d56eeb8e30e3b9c6c797959ce3fff7c3b9c64fbf6edf2877ff8877638c9d6ad5b65cb962d7678cdf9e0073f688792fdeaaffeaa5c76d9657638c917bef00579f4d147edf0ccbdec652f9377bdeb5d76782edc72cb2df2d33ffdd37678e68e1f3f2e0f3cf0801d4e72f1c517cbaffffaafdbe16493c9c40ead2667ee6fd668142cbbd6e65f5996e593c9a46c99ebb32ccf6cafda90366dda94bb44b56f5b5959b1d3e31c7acb5bde12bc26f3d0b66edd6a0f754ddab1634770eea96df7eedd767a4cb16ddbb6e03aa6b63d7bf6d8e9d7a40b2eb82038f7d476e4c8113b7db25b6fbd35987f1edac68d1beda1e2052ccbb27c3c1e776b451e361e8f557c9677de43c753ae000000c370f9552ccfaaf2ad789ee6fa119f43070000301b79996fc5f3ac2adfaaf22efdff743c9f4307000030037a05b4ccb774652e926f79953915cfe7d0010000cc88adb4b9b1e20bff9b759ee6fa22bef31e3a9d41020000209dcdb3a22ba2b13ccdf5457cb8874eafcd3634000000a4d395b932bfb2bdf8953a2f4f53f1e11e3aefa90955912b33452a740000007dd5ad80c62a738e97a7a9f8ee7be8840a1d0000c010820a9daac235eea1f3565413f6d0f1942b0000c030820a5dac52576667752baab13d74ae2f32be2073d46bba00000048a2f7c0c5f2ac2adfaaf22eaf32a7e2c33d74ae2f32be2063a442070000d09b5e012df32d5d998be45b5e654ec577df4347850e00006010b6d2e6c68a2ffc6fd6799aeb8bf8ce7be87406090000807436cf8aae88c6f234d717f1e11e3aefa989fa0600008074ba3257e657b617bf52e7e5692a3edc43e73d35a12a7265a648850e0000a0afba15d05865cef1f23415df7d0f1d9f430700003088a042a7aa708d7be8bc15d5843d743ce50a0000308ca04217abd495d959dd8aaac828cbb27c341a495e7c53d9e77a24b4b01014f792bde31def906f7ce31b7638c9fbdef73ed9b66d9b1d4ef2e4934fcae38f3f6e8767eeeffc9dbf235bb66cb1c3c9fee44ffe447ef8c31fdae124070f1e94af7ded6b7638c9b7bffd6df9e637bf6987935c77dd7572f8f0613b9cec8b5ffca21d4a76cd35d7c8ba75ebec7092dffddddf95fff93fffa71d4ef24bbff44bb275eb563b8c069ffdec6707fb5df66bbff66bb2b4b46487d79c37bde94df2ecb3cfdae1246f7ffbdbe5d24b2fb5c3493ef1894fc8bdf7de6b8793fcd44ffd946cdcb8d10e27f9a99ffa2979e73bdf6987e7c2e2e2a2bcf4a52fb5c3491e7ffc7179f2c927ed70928b2fbe587ef6677fd60ecf856c328956e1a6198d46ab79da6854c56759964f2693a92d33fd8f838f7ffce3ab6bcc73d6d6ad5b670fb597abaeba2af819a9edc0810376fa647bf7ee0de64f6d5bb76eb5d3273b73e64c307f9f76faf469fb23e6c2873ef4a1e05869cdedd0a143f632e21cbaf6da6b83d7641edad2d2923dd464274e9c08e69f97f6c8238fd8c34df6ce77be33983fb5eddab5cb4e3f37b22c0bda783c2efbb1eb55b3dfef5a5066cb5d5fb7874e3f7501000080645ebed5650f9deb8bf8ce7be8ca355e000000f462f32c3e870e0000e0054457e6cafccaf6e257eabc3c4dc5f339740000003350b7021aabcc395e9ea6e2bbefa1e373e8000000061154e85415ae710f9db7a29ab0878ecfa1030000184650a18b55eacaecac6e4535b687cef545c617648e7a4d1700000049f41eb8589e55e55b55dee555e6547cb887cef545c617648c54e80000007ad32ba065bea52b73917ccbabcca9f8ee7be8a8d00100000cc256dadc58f185ffcd3a4f737d11df790f9dce2001000090cee659d115d1589ee6fa223edc43e73d3551df000000904e57e6cafccaf6e257eabc3c4dc5877be8bca7265445aecc14a9d0010000f455b7021aabcc395e9ea6e2bbefa1e373e8000000061154e85415ae710f9db7a29ab0878ea75c010000861154e86295ba323bab5b518deda1737d91f10599a35ed30500004012bd072e966755f9569577799539151feea1737d91f1051923153a000080def40a68996fe9ca5c24dff22a732abefb1e3a2a7400000083b0953637567ce17fb3ced35c5fc477de43a73348000000a4b3795674453496a7b9be88972ccbf2c964926793c96aeffe3bcb1adb90366dda9417c966efb6b2b262a75f737ef4a31f05e7dda79d3c79d2fe88646f79cb5b82f953db6db7dd66a74f76f8f0e1607e5a73dbbd7bb7bd8cc91e7cf0c1607eda0bb35d7ef9e5f6e5c50bd88b5ef4a2e035a6d5b7abafbeda5ec25ec659968fc7e3ce2d73712a3edc43e73d35a12a7265a648850e0000a0afba15d05865cef1f23415df7d0f1d9f430700003008b777aef31e3afdb42b9f43070000303b41852e56a92bb3b3ba15553e870e00006026ca95d09a3cabcab7aabccbabcca9f8700f9deb8b8c2fc818a9d0010000f4a65740cb7c4b57e622f996579953f1ddf7d051a10300001884adb4b9b1e20bff9b759ee6fa22bef31e3a9d41020000209dcdb3a22ba2b13ccdf5457cb887ce7b6aa2be010000209daecc95f995edc5afd479799a8a0ff7d0794f4da88a5c992952a1030000e8ab6e0534569973bc3c4dc577df43c7e7d00100000c22a8d0a92a5ce31e3a6f4535610f1d4fb90200000c23a8d0c52a75657656b7a21adb43e7fa22e30b3247bda60b000080247a0f5c2ccfaaf2ad2aeff22a732a3edc43e7fa22e30b32462a74000000bde915d032dfd295b948bee555e6547cf73d7454e800000006612b6d6eacf8c2ff669da7b9be88efbc874e67900000004867f3ace88a682c4f737d111feea1f39e9aa86f00000048a72b73657e657bf12b755e9ea6e2c33d74de5313aa2257668a54e8000000faaa5b018d55e61c2f4f53f1ddf7d0f1397400000083082a74aa0ad7b887ce5b514dd843f7e3f494ebbe7dfb82cc39b5fdc22ffc829d3ed979e79d17dc007ddac68d1bed8f48f6d9cf7e36983fb5dd71c71d76fa64d75d775d307f6a3b73e68c9dbe97d3a74f073f23b5edd8b1c34ebfe65c74d145c179f769175e78a1fd11c90e1d3a14ccbfd6dae1c38783df6fb4176efbfef7bf1fbcc6f3d0def9ce77dab7d79a655f133e870e0000e00544ef818be55955be55e55dfaffe9f8700f9deb8b8c2fc81855e608000080347a05b4ccb774652e926f79953915df7d0f1d153a00008041d84a9b1b2bbef0bf59e769ae2fe23befa1d31924000000d2d93c2bba221acbd35c5fc4877be8bca726ea1b000000d2e9ca5c995fd95efc4a9d97a7a9f8700f9df7d484aac8959922153a000080beea5640639539c7cbd3547cf73d747c0e1d0000c020820a9daac235eea1f3565413f6d0f1942b0000c030820a5dac52576667752baab13d74ae2f32be2073d46bba00000048a2f7c0c5f2ac2adfaaf22eaf32a7e2c33d74ae2f32be2063a442070000d09b5e012df32d5d998be45b5e654ec577df4347850e00006010b6d2e6c68a2ffc6fd6799aeb8bf8ce7be87406090000807436cf8aae88c6f234d717f1e11e3aefa989fa0600008074ba3257e657b617bf52e7e5692a3edc43e73d35a12a7265a648850e0000a0afba15d05865cef1f23415df7d0f1d9f430700003088a042a7aa708d7be8bc15d5843d743ce50a0000308ca04217abd495d959dd8a6a6c0f9deb8b8c2fc81cf59a2e00000092e83d70b13cabcab7aabccbabcca9f8700f9deb8b8c2fc818a9d0010000f4a65740cb7c4b57e622f9965799d3f15996e53a41cb8b1f90e7f9ea44ba57161682ed77c9366fde2cc78f1fb7c349eeb9e71e79fbdbdf6e87933cf9e493f2f8e38fdbe124dff9ce77646565c50e2759b76e9dfcd7fffa5fedf05cd8b3678f1c3e7cd80e27f9955ff915f98ddff80d3b3c73dffffef7e5277ff227ed70b2d3a74fcb65975d668793dc78e38d72f0e0413b9c64f7eeddb267cf1e3b9ce4cc9933f2b5af7dcd0e27f9e10f7f287bf7eeb5c3c91e7ef8613b94eca1871e926ddbb6d9e12477df7db7ecdfbfdf0ecfdc860d1be4377ff337edf09af3894f7c42eebdf75e3b3c73975e7aa97cea539fb2c3c9aebdf65a3b34171e7ffc7179f2c927edf0ccbde4252f91cd9b37dbe16493c9c4cfb7da2812b9205fcbb22c9fe4793e994c3ab5216ddab4297795c7be6d6565c54e3f17befce52f07c79adad6ad5b67a79f1b6f79cb5b82e34d6db7dd769b9d7e2e9c39732638d63eedf4e9d3f64724dbb16347307f6adbbd7bb79d7e2e3cf5d453c1b1ce4b3b74e8903ddc641ff8c00782f9e7a15d7ef9e5f650d7a45b6fbd3538f779681b376eb4878a17b02ccb8236b6fd789c8f5d3f1e07dfef5ab887ce7b6aa2be010000205d1ec9b7f81c3a000080179021f7d0051be174a5aeccfc56078aac900a1d0000c010820a9daac2c5f6d5c55754f91c3a00008099092a74b14a1d9f43070000309ff41eb8589e55e55b55dee555e6547cb887cef545c617648c54e80000007ad32ba065bea52b73917ccbabcca9f8ee7be8a8d00100000cc256dadc58f185ffcd3a4f737d11df790f9dce2001000090cee659d115d1589ee6fa223edc43e73d3551df000000904e57e6cafccaf6e257eabc3c4dc5877be8bca7265445aecc14a9d0010000f455b7021aabcc395e9ea6e2bbefa1e373e8000000061154e85415ae710f9db7a29ab0878ea75c010000861154e86295ba323bab5b518deda1737d91f10599a35ed30500004012bd072e966755f9569577799539151feea1737d91f1051923153a000080def40a68996fe9ca5c24dff22a732abefb1e3a2a7400000083b0953637567ce17fb3ced35c5fc477de43a73348000000a4b3795674453496a7b9be880ff7d0794f4dd437000000a4d395b932bfb2bdf8953a2f4f53f1a32ccbf23615b7bcc806f37c35516c13d3d6e6cd9be5f8f1e37638c93df7dc236f7ffbdbedf0cc3dfae8a3f20bbff00b7638c98b5ffc62f9e10f7f6887e7c2fdf7df2f7ff6677f668767ee8a2bae907ff12ffe851d4ef2fcf3cfcbbffff7ffde0e27fbaddffa2d59bf7ebd1d4e72e38d37cac18307ed70922d5bb6c8d6ad5bed70922bafbc526ebae9263b9ce4d9679f953beeb8c30e27bbe38e3b067b3fdd74d34df28a57bcc20e2759585890c9646287937cfdeb5f97cf7dee737638c915575c21a74e9db2c373e10ffee00fe49bdffca61d4ef2852f7c411e7df4513b9ce4d5af7eb5ecdcb9d30e27198d46735b54b9e5965be4a77ffaa7edf0cc1d3f7e5c1e78e0013b9ce4924b2e9177bce31d763859ec3d9ee779f53a8f46c1b26b6dfe9565593e994cca96b93ecbf2ccf6aa0d69d3a64db94b54fbb69595153bfd5cf8f297bf1c1c6b6a5bb76e9d9d7e4ddabb776f70eea96debd6ad76fa3569c78e1dc1b9cf43dbbe7dbb3dd4b971e1851706c73b0f6dcf9e3df65093dd77df7dc1fca9edf2cb2fb7d3cf8d6bafbd3638de79684b4b4bf650939d387122987f5eda238f3c620f772e7cf2939f0c8e35b55d7df5d576fa5eb22ccbc7e371b756e461e3f158c56779e73d743ce50a0000300c975fc5f2ac2adf8ae769ae1ff13974000000b39197f9563ccfaaf2ad2aefd2ff4fc7f3397400000033a05740cb7c4b57e622f996579953f17c0e1d0000c08cd84a9b1b2bbef0bf59e769ae2fe23befa1d31924000000d2d93c2bba221acbd35c5fc4877be8f4da6c43030000403a5d992bf32bdb8b5fa9f3f234151feea1f39e9a5015b93253a442070000d057dd0a68ac32e778799a8aefbe874ea8d00100000c21a8d0a92a5ce31e3a6f4535610f1d4fb90200000c23a8d0c52a75657656b7a21adb43e7fa22e30b3247bda60b000080247a0f5c2ccfaaf2ad2aeff22a732a3edc43e7fa22e30b32462a74000000bde915d032dfd295b948bee555e6547cf73d7454e800000006612b6d6eacf8c2ff669da7b9be88efbc874e67900000004867f3ace88a682c4f737d111feea1f39e9aa86f00000048a72b73657e657bf12b755e9ea6e2c33d74de5313aa2257668a54e8000000faaa5b018d55e61c2f4f53f1ddf7d0f1397400000083082a74aa0ad7b887ce5b514dd843c753ae000000c3082a74b14a5d999dd5ada88a8cb22ccb47a391e4c537957dae47420b0b41712fd9e6cd9be5f8f1e37638c93df7dc236f7ffbdbed7092279f7c521e7ffc713b9ce43bdff98eacacacd8e1243ff1133f211ff8c007ec70b29ffff99f9717bff8c57638c9638f3d26dffbdef7ec7092471e7944bef0852fd8e1249b376f963befbcd30e27fbe217bf6887925d73cd35b26edd3a3b9ce4c61b6f9483070fdae199bbe1861be4c1071fb4c37361e7ce9d72e6cc193b3c73d75f7fbdbcee75afb3c349fee88ffe487ef7777fd70e27b9e28a2be4d4a9537638d997bef425198fc77638c91ffcc11fc837bff94d3b3c73af79cd6b64e7ce9d7638c95ffee55fcabe7dfbec70b2871f7ed80e25bbebaebbe495af7ca51d9eb943870ec91d77dc6187932c2e2ecab163c7ec70b26c328956e1a6198d46ab79da6854c56759964f2693a92d33fd90366ddab4ba963b405b5959b1d327fbf8c73f1ecc9fda5efbdad7dae993fde8473f0ae6efd34e9e3c697f44b2b7bce52dc1fca9edb6db6eb3d3cf853367ce04c7daa79d3e7ddafe88643b76ec08e69f87b67dfb767ba898e2031ff840701de7a15d7ef9e5f6507bb9e0820b829f91da8e1c3962a79f0bfbf7ef0f8e35b56ddcb8d14edfcb8b5ef4a2e067d0eadbd5575f6d2f612f5996056d3c1e97fdd8f5aad9ef772d28b3e5aeafdb43a79fba00000040322fdfeab287cef5457ce73d74e51a2f0000007ab179169f43070000f002a22b73657e657bf12b755e9ea6e2f91c3a00008019a85b018d55e61c2f4f53f1ddf7d0f1397400000083082a74aa0ad7b887ce5b514dd843c7e7d00100000c23a8d0c52a75657656b7a21adb43e7fa22e30b3247bda60b000080247a0f5c2ccfaaf2ad2aeff22a732a3edc43e7fa22e30b32462a74000000bde915d032dfd295b948bee555e6547cf73d7454e800000006612b6d6eacf8c2ff669da7b9be88efbc874e67900000004867f3ace88a682c4f737d111feea1f39e9aa86f00000048a72b73657e657bf12b755e9ea6e2c33d74de5313aa2257668a54e8000000faaa5b018d55e61c2f4f53f1ddf7d0f1397400000083082a74aa0ad7b887ce5b514dd843c753ae000000c3082a74b14a5d999dd5ada8c6f6d0b9bec8f882cc51afe90200002089de0317cbb3aa7cabcabbbcca9c8a0ff7d0b9bec8f8828c910a1d0000406f7a05b4ccb774652e926f79953915df7d0f1d153a00008041d84a9b1b2bbef0bf59e769ae2fe2475996e5a3858568509321ab749b376f96e3c78fdbe124f7dc738fbcfded6fb7c3493efff9cfcbeffddeefd9e124ffe81ffd23d9b76f9f1d4ef2fcf3cfcb79e79d6787933df6d86372d55557d9e1247bf6ec91c3870fdbe124bff22bbf22bff11bbf6187937cfffbdf973ffdd33fb5c3499e7df659f927ffe49fd8e164a74f9f96cb2ebbcc0e27b9f1c61be5e0c1837678e66eb8e10679f0c107ed70b22f7ef18b7628d9b5d75e6b8792fdd99ffd99fcd55ffd951d4e72f7dd77cb673ef3193b9ce4652f7b99bcea55afb2c349366cd820bff99bbf698793bde94d6f921ffef0877638c9473ffa51d9b469931d9eb9fffedfffbb7cf8c31fb6c3492ebdf452f9d4a73e658793edd9b347c6e3b11d468d2bafbc527efff77fdf0e279b4c2676683539d37d9eaf56ea8a3cad36ffcab22c9f4c26793699acf6eebfb3acb10d69d3a64d7971ecbddbcaca8a9d7ecdf9d18f7e149c779f76f2e449fb23d69cc3870f07e73d2fedf4e9d3f67093edd8b123987f1edaf6eddbeda1267beaa9a782f9fbb4a79f7edafe8864dbb66d0be69f87b673e74e7ba8c94e9d3a15cc4f7be1b6e79e7bcebec43887c659968fc7e3ce2d73712a3edc43e73d35117bea823d740000007dcd760f1d9f430700003008b777aef31e3afdb42b9f43070000303b41852e56a9e373e8000000e653b9125a936755f9569577799539151feea1737d91f1051923153a000080def40a68996fe9ca5c24dff22a732abefb1e3a2a7400000083b0953637567ce17fb3ced35c5fc477de43a73348000000a4b3795674453496a7b9be880ff7d0794f4dd437000000a4d395b932bfb2bdf8953a2f4f53f1e11e3aefa90955912b33452a740000007dd5ad80c62a738e97a7a9f8ee7be8f81c3a0000804104153a55856bdc43e7ada826eca1e32957000080610415ba58a5aeccceea5654637be85c5f647c41e6a8d7740100009044ef818be55955be55e55d5e654ec5877be85c5f647c41c648850e0000a037bd025ae65bba3217c9b7bcca9c8aefbe878e0a1d0000c0206ca5cd8d155ff8dfacf334d717f19df7d0e90c12000000e96c9e155d118de569ae2fe2c33d74de5313f50d000000e97465aeccaf6c2f7ea5cecbd3547cb887ce7b6a4255e4ca4c910a1d0000405f752ba0b1ca9ce3e5692abefb1e3a3e870e0000601041854e55e11af7d0792baa097be8cec653ae0b0b415e996cc8b9f6eddb1764cea9ed177ff117edf4c9ce3befbce006e8d3366edc687f44b2a5a5a5e0dce7a1dd7efbedc179a7b6679e79c69e762f975f7e7970bca9edad6f7d6b70bcf3d00e1d3a644f3bd9c5175f1cccdfa75d74d145f647241b0df8bbf1c31ffe7070aca9ede69b6f0eee95d4f6c637be3198bf4fbbe0820beca9e31c3aefbcf382d778adb55b6eb9c59ef65cb1c7fb82fe1cbac9646287920d39d79086be66f36a5ecf73c8e31a72aea1cdf3b1fd3818f2fa0ff9bb6cc8b9863c47e0c75d5ee65bf13cab7abf55ef3bfdff747cb887cef545c617648c2a73040000401abd025ae65bba3217c9b7bcca9c8a0fd627bd0a9deaa34f5d0000002099adb4b9b1e20bff9b759ee6fa22bef31e3a9d41020000209dcdb3a22ba2b13ccdf5457cb8874eafcd3634000000a4d395b932bfb2bdf8953a2f4f53f1e11e3aefa90955912b33452a740000007dd5ad80c62a738e97a7a9f8ee7be8f81c3a0000804104153a55856bdc43e7ada826eca1e32957000080610415ba58a5aeccceea5654637be85c5f647c41e6a8d7740100009044ef818be55955be55e55d5e654ec5877be85c5f647c41c648850e0000a037bd025ae65bba3217c9b7bcca9c8aefbe878e0a1d0000c0206ca5cd8d155ff8dfacf334d717f19df7d0e90c12000000e96c9e155d118de569ae2fe2c33d74de5313f50d000000e97465aeccaf6c2f7ea5cecbd3547cb887ce7b6a4255e4ca4c910a1d0000405f752ba0b1ca9ce3e5692abefb1e3a3e870e0000601041854e55e11af7d0792baa097be878ca150000601841852e56a92bb3b3ba15d5d81e3ad717195f9039ea355d00000024d17be0627956956f557997579953f1e11e3ad717195f903152a1030000e84daf8096f996aecc45f22daf32a7e3b32ccb758296173f20cff3d58974af2c2c04dbef921d3b764c7ef0831fd8e124575d75955c74d1457638c9934f3e298f3ffeb81d4e72c10517c8d5575f6d87d79cc71e7b4cbef7bdefd9e199fbd6b7be259ffad4a7ec7092f1782c5ffef297ed70b2fff49ffed360f7ecbdf7de2b274f9eb4c349defad6b7caae5dbbec7092af7ce52bf2bef7bdcf0eaf3937df7cb3fcc37ff80fed70922baeb8427ee6677ec60e27f9fce73f2f3b77eeb4c349aeb8e20a3975ea941d4eb67efd7af9ebbffe6b3b9ce4a31ffda86cdab4c90eaf294f3cf184bced6d6fb3c3c9fedb7ffb6ff2133ff1137678e67eeff77e4ffecb7ff92f7638c9ae5dbb646565c50ecf85c964e2e75b6d14895c90af6559964ff23c9f4c269d1af04272f8f0e1dc55b7e7ad9d3e7dda1e6eb21d3b7604f3a7b6ddbb77dbe9933df8e083c1fc6bb11d3a74c89efa5cb8efbefb82634d6d975f7eb99dbe970b2eb820f819a9edc8912376fa35e7c48913c179f769cf3df79cfd1173e19def7c6770aca96dd7ae5d76fab9916559d0c6b61f8ff3b1ebc7e3e0fb5d0bf7d0794f4dd437000000a4cb23f9169f43070000f00232e41eba60239caed49599dfea40911552a1030000184250a15355b8d8bebaf88a2a9f43070000303341852e56a9e373e8000000e693de0317cbb3aa7cabcabbbcca9c8a0ff7d0b9bec8f8828c910a1d0000406f7a05b4ccb774652e926f79953915df7d0f1d153a00008041d84a9b1b2bbef0bf59e769ae2fe23befa1d31924000000d2d93c2bba221acbd35c5fc4877be8bca726ea1b000000d2e9ca5c995fd95efc4a9d97a7a9f8700f9df7d484aac8959922153a000080beea5640639539c7cbd3547cf73d747c0e1d0000c020820a9daac235eea1f3565413f6d0f1942b0000c030820a5dac52576667752baab13d74ae2f32be2073d46bba00000048a2f7c0c5f2ac2adfaaf22eaf32a7e2c33d74ae2f32be2063a442070000d09b5e012df32d5d998be45b5e654ec577df4347850e00006010b6d2e6c68a2ffc6fd6799aeb8bf8ce7be87406090000807436cf8aae88c6f234d717f1e11e3aefa989fa0600008074ba3257e657b617bf52e7e5692a7e946559dea6e29617d9609eaf268a6d62da5a5e5eb643808c4623d9bb77af1d4e72faf469f9f4a73f6d87933cfffcf3f2918f7cc40e277bcf7bde23ebd7afb7c3493ef7b9cfc9d7bffe753b9c64f7eeddb267cf1e3b9ce40ffff00f65fbf6ed7678cdb9e9a69be415af78851d4e72fdf5d7cbeb5ef73a3b9ce4b1c71e93cf7dee737638c968341af41ff51ffce007ed50b2a3478fcab5d75e6b87937ce10b5f90471e79c40e2779cd6b5e233b77eeb4c3491e7bec3179e52b5f698793eddebd5bfeeedffdbb7678e6161616643299d8e1248b8b8bf24fffe93fb5c37321768e799e57efb3d1285876adcdbfb22ccb279349d932d767599ed95eb5212d2e2ee62e51a5d15c5b5e5eb6b7ca5c3873e64c70ac6bb1eddebddb9e7ab2071f7c30989fd6dcf6ecd9632fe35c3875ea5470acf3d28e1c39620f37d9adb7de1acc9fda969696ecf4c94e9c3811ccbf16dbbe7dfbeca9af495996e5e3f1b85b2bf2b0f178ace2b3bcf31e3a9e720500001886cbaf627956956fc5f334d78ff81c3a000080d9c8cb7c2b9e6755f9569577e9ffa7e3f91c3a00008019d02ba065bea52b73917ccbabcca9783e870e000060466ca5cd8d155ff8dfacf334d717f19df7d0e90c12000000e96c9e155d118de569ae2fe2c33d747a6db6a1010000209daecc95f995edc5afd479799a8a0ff7d0794f4da88a5c992952a1030000e8ab6e0534569973bc3c4dc577df432754e8000000861054e85415ae710f9db7a29ab0878ea75c010000861154e86295ba323bab5b518deda1737d91f10599a35ed30500004012bd072e966755f9569577799539151feea1737d91f1051923153a000080def40a68996fe9ca5c24dff22a732abefb1e3a2a7400000083b0953637567ce17fb3ced35c5fc477de43a73348000000a4b3795674453496a7b9be880ff7d0794f4dd437000000a4d395b932bfb2bdf8953a2f4f53f1e11e3aefa90955912b33452a740000007dd5ad80c62a738e97a7a9f8ee7be8f81c3a0000804104153a55856bdc43e7ada826eca1e32957000080610415ba58a5aeccceea565445465996e5a3d148f2e29bca3ed723a18585a0b8976cf3e6cd72fcf8713b9c646969c90ee11c3b70e0801d4ab2bcbc2c7bf7eeb5c349befffdefcb9ffee99fdae1247ffbb77f2bb7df7ebb1d5e737ee9977e49b66edd6a87937ce52b5f91f7bffffd7638d9eb5fff7a3b94ece1871fb643c95efdea57cb860d1bec7092ebafbf5e5ef7bad7d9e1997bfae9a7e5a69b6eb2c373e1e8d1a372edb5d7dae1249ff8c427e4739ffb9c1d4ef2c637be5176efde6d87933cf6d863f2ca57bed20e27dbb265cb607fcfbffad5afcadffccddfd8e124bff55bbf25bffccbbf6c8767ee252f79896cdebcd90e27cb269368156e9ad168b49aa78d46557c9665f9643299da32d30f69717171752d7780b6b2b262a7c739b4b4b414bc26a96d7979d94e9fecf0e1c3c1fca96dfdfaf576fa35e9431ffa5070eef3d02ebae8227ba8bd5c78e185c1cf486d870e1db2d327fbc0073e10cc4f6b6e478e1cb19771cd3971e24470de7dda73cf3d677f44b26baeb926987fadb5abafbeda9e762f5996056d3c1e97fdd8f5aad9ef772d48cb73d7d7eda1d34f5d0000002099976f75d943e7fa22bef31eba728d17000000bdd83c8bcfa10300007801d195b932bfb2bdf8953a2f4f53f17c0e1d0000c00cd4ad80c62a738e97a7a9f8ee7be8f81c3a0000804104153a55856bdc43e7ada826eca1e373e8000000861154e86295ba323bab5b518deda1737d91f10599a35ed30500004012bd072e966755f9569577799539151feea1737d91f1051923153a000080def40a68996fe9ca5c24dff22a732abefb1e3a2a7400000083b0953637567ce17fb3ced35c5fc477de43a73348000000a4b3795674453496a7b9be880ff7d0794f4dd437000000a4d395b932bfb2bdf8953a2f4f53f1e11e3aefa90955912b33452a740000007dd5ad80c62a738e97a7a9f8ee7be8f81c3a0000804104153a55856bdc43e7ada826eca1e32957000080610415ba58a5aeccceea5654637be85c5f647c41e6a8d7740100009044ef818be55955be55e55d5e654ec5877be85c5f647c41c648850e0000a037bd025ae65bba3217c9b7bcca9c8aefbe878e0a1d0000c0206ca5cd8d155ff8dfacf334d717f1a32ccbf23615b7bcc824f37c35616c13d3d6e6cd9be5f8f1e37638c9caca8aecdab5cb0e273979f2a41d5a9346a3916cdcb8d10e27b9f9e69be5c081037638c9f2f2b2ecddbbd70e27397af4a86cddbad50e2779c94b5e220f3cf0801d5e73f6efdf2ff7dc738f1d4eb261c30679f5ab5f6d8793bcf4a52f955b6fbdd50e27dbb973a73cf3cc337638c9430f3d24dbb66db3c349eebefb6ed9bf7fbf1d9eb9e79f7f5ebef295afd8e1b9f0d18f7e54366dda6487d794279e7842def6b6b7d9e164cf3df79cac5bb7ce0e27f9c55ffc4579e49147ecf09ab2b8b828c78e1db3c3c92693891d923ccf65341aad2675a35190d4d5e65f5996e593c9a46c99ebb32ccf6cafda90161717f32267ecdd565656ecf4c9f6eddb17ccbf16dbc99327eda9275b5a5a0ae64f6dcbcbcb76fa64870f1f0ee6a79dbbb67dfb76fb92247beaa9a782f9e7a51d3a74c81eee9a73ead4a9e0bc692fdcf6dc73cfd99738d935d75c13ccbfd6dad5575f6d4fbb972ccbf2f178dcad1579d8783c56f159ce53ae00000033e2f2ab589e55e55bf13ccdf5239e72050000988dbcccb7e27956956f557997fe7f3a9ea75c0100006640af8096f996aecc45f22daf32a7e279ca15000060466ca5cd8d155ff8dfacf334d717f19df7d0e90c12000000e96c9e155d118de569ae2fe2c33d747a6db6a1010000209daecc95f995edc5afd479799a8a0ff7d0794f4da88a5c992952a1030000e8ab6e0534569973bc3c4dc577df432754e8000000861054e85415ae710f9db7a29ab0878ea75c010000861154e86295ba323bab5b518deda1737d91f10599a35ed30500004012bd072e966755f9569577799539151feea1737d91f1051923153a000080def40a68996fe9ca5c24dff22a732abefb1e3a2a7400000083b0953637567ce17fb3ced35c5fc477de43a73348000000a4b3795674453496a7b9be880ff7d0794f4dd437000000a4d395b932bfb2bdf8953a2f4f53f1e11e3aefa90955912b33452a740000007dd5ad80c62a738e97a7a9f8ee7be8f81c3a0000804104153a55856bdc43e7ada826eca1e32957000080610415ba58a5aeccceea5654637be85c5f647c41e6a8d7740100009044ef818be55955be55e55d5e654ec5877be85c5f647c41c648850e18d4e9d3a78337746adbb163879d7ecdb9f8e28b83f3eed32ebcf042fb2392dd70c30dc1bfb653dbbffb77ffce4e9fecfefbef0fe64f6dd75f7f7d700dfbb40b2eb8c01e6eb223478e04f3cf43dbbf7fbf3dd4641b376e0ce6efd3d6ad5b677f44b2471f7d34987fadb5e3c78fdbd3ee45af80ba26ba3217c9b7f4f7e8f8ee7be85c0f0000805ebc7ccb56e622f996add9b9f8ce7be87406090000807436cf8aae88c6f234d717f1e11e3aefa989fa0600008074ba3257e657b617bf52e7e5692a3edc43e73d35a12a7265a648850e0000a0afba15d05865cef1f23415df7d0f1d9f430700003088a042a7aa708d7be8bc15d5843d743ce50a0000308ca04217abd495d959dd8a6a6c0f9deb8b8c2fc81cf59a2e00000092e83d70b13cabcab7aabccbabcca9f8700f9deb8b8c2fc818a9d0010000f4a65740cb7c4b57e622f996579953f1ddf7d051a10300001884adb4b9b1e20bff9b759ee6fa22bef31e3a9d41020000209dcdb3a22ba2b13ccdf5457cb887ce7b6aa2be010000209daecc95f995edc5afd479799a8a0ff7d0794f4da88a5c992952a1030000e8ab6e0534569973bc3c4dc577df43c7e7d00100000c22a8d0a92a5ce31e3a6f4535610f1d4fb90200000c23a8d0c52a75657656b7a21adb43e7fa22e30b3247bda60b000080247a0f5c2ccfaaf2ad2aeff22a732a3edc43e7fa22e30b32462a74000000bde915d032dfd295b948bee555e6547cf73d7454e800000006612b6d6eacf8c2ff669da7b9be88efbc874e67900000004867f3ace88a682c4f73bd8bcfb22c1f8d469217ff33cf73198d4653ab700b0b41712fd9e6cd9be5f8f1e37638c9caca8aecdab5cb0e27b9ebaebbe4ddef7eb71d5e734e9e3c291b376eb4c3496ebef9663970e0801d4eb2bcbc2c7bf7eeb5c3498e1e3d2a5bb76eb5c373e1f4e9d372d96597d9e124274e9c90bff88bbfb0c349f6efdf2ff7dc738f1d4e72c30d37c8830f3e6887939c39734676eedc6987932d2f2fcbf9e79f6f8767ee8a2bae909ff9999fb1c3493efff9cf0f76cdaeb8e20a3975ea941d4ef6a52f7d49c6e3b11d4eb269d32659bf7ebd1d9eb9cf7ce633f2d6b7bed50e2779e52b5f29274e9cb0c373e1ddef7eb7fcf99fffb91d9eb91b6eb8417efbb77fdb0ecf856c328956e1a629f3b4d1a88acfb22c9f4c26535be6fa6cb51fd2e2e2625e540f7bb79595153b7db27dfbf605f3afc576f2e4497beac996969682f953dbf2f2b29d3ed9e1c38783f9e7a59d3e7dda1eee5cf8d0873e141c6b6adbbe7dbb9d3ed9534f3d15ccdfa73dfdf4d3f647ac39f7dd775f70dea9edf2cb2fb7d3638afdfbf707d731b56ddcb8d14e3f37aeb9e69ae078e7a1eddab5cb1eeadcc8b22c68e3f1b8ecc7ae57cd7ebf6b41992d777ddd1e3a3e870e000060106eef5ce73d74fa69573e870e000060765c7e15cbb3aa7c2b9ea7b97ec4e7d0010000cc46b9125a936755f956957779953915cfe7d0010000cc805e012df32d5d998be45b5e654ec577df4347850e00006010b6d2e6c68a2ffc6fd6799aeb8bf8ce7be87406090000807436cf8aae88c6f234d717f1e11e3aefa989fa0600008074ba3257e657b617bf52e7e5692a3edc43e73d35a12a7265a648850e0000a0afba15d05865cef1f23415df7d0f1d9f430700003088a042a7aa708d7be8bc15d5843d743ce50a0000308ca04217abd495d959dd8a6a6c0f9deb8b8c2fc81cf59a2e00000092e83d70b13cabcab7aabccbabcca9f8700f9deb8b8c2fc818a9d0010000f4a65740cb7c4b57e622f996579953f1ddf7d051a10300001884adb4b9b1e20bff9b759ee6fa22bef31e3a9d41020000209dcdb3a22ba2b13ccdf5457cb887ce7b6aa2be010000209daecc95f995edc5afd479799a8a0ff7d0794f4da88a5c992952a1030000e8ab6e0534569973bc3c4dc577df43c7e7d00100000c22a8d0a92a5ce31e3a6f4555649465593e5a588806c5e42aa31ccae6cd9be5f8f1e37638c9caca8aecdab5cb0e27b9ebaebbecd09ab475eb56d9b871a31d4e72f3cd37cb810307ec7092e5e565d9bb77af1d4e72faf469f9f4a73f6d87933cfffcf3f2918f7cc40e277bcf7bde23ebd7afb7c36bcac2c2824c26133b9c64c8b9a4b8cfce3fff7c3b9ce4b39ffdac7ce31bdfb0c349aebffe7a79ddeb5e6787937cfef39f979d3b77dae124575c71859c3a75ca0ea3c1d7bef6353978f0a01d4ef2f7fffedf9777bdeb5d7638d99e3d7b643c1edbe12477df7db77ce73bdfb1c3496eb8e106f9b99ffb393b3c73975c7289bce31defb0c3c962bfcb5c9e95e7b98c46a322711b95a95c6dfe9565593e994cf26c32f1fb2ccbb3ccf5611bd2e2e2e26ae96f80b6b2b262a7c739b4b4b414bc26a96d7979d94e3f17ce9c39131ceb5a6cbb77efb6a79eecc1071f0ce64f6d175d74919d7e6e6cdbb62d38ded4b667cf1e3b7db2fbeebb2f983fb55d7ef9e5767abc80bde8452f0a5ee37968fbf6edb3879aec939ffc64307f6abbfaeaabedf4bd8cb32c1f8fc79d5be6e2547cb887cef5a3119f430700007096cc760f9d7eea02000000c9bc7cabcb1e3ad717f17c0e1d0000c08cd83c2bba221acbd35c5fc4f3397400000033a02b73657e657bf12b755e9ea6e2c33d747a6d5657e4ca4c910a1d0000405f752ba0b1ca9ce3e5692abefb1e3a3e870e0000601041854e55e11af7d0792baa097be878ca150000601841852e56a92bb3b3ba15d5d81e3ad717195f9039ea355d00000024d17be0627956956f557997579953f1e11e3ad717195f903152a1030000e84daf8096f996aecc45f22daf32a7e2bbefa1a34207000030085b697363c517fe37eb3ccdf5457ce73d743a83040000403a9b6745574463799aeb8bf8700f9df7d4447d030000403a5d992bf32bdb8b5fa9f3f234151feea1f39e9a5015b93253a442070000d057dd0a68ac32e778799a8aefbe878ecfa1030000184450a15355b8c63d74de8a6ac21e3a9e72050000184650a18b55eacaecac6e4535b687cef545c617648e7a4d1700000049f41eb8589e55e55b55dee555e6547cb887cef545c617648c54e80000007ad32ba065bea52b73917ccbabcce9f82ccb729da0e5c50fc8f37c7522dd2b0b0bc1f6bb649b376f96e3c78fdbe1242b2b2b7608e7d0d1a347e5c081037638c9f2f2b2ecddbbd70e27397dfab47cfad39fb6c3499e7ffe79f9c8473e6287d79c2d5bb6c8d6ad5bed7092d1683458657fc8b9a4b8cfce3fff7c3b9ce4c08103f2f8e38fdbe124d75d779d6cd9b2c50e273978f0a0dc78e38d7638c9faf5ebe53def798f1d4ef6fef7bf5fd6ad5b6787937cfad39f96d3a74fdbe1997bcd6b5e233b77eeb4c349fee22ffe42eebaeb2e3b9cec831ffca01d9a0b77dd7597bcf39defb4c3498e1f3f2e0f3cf0801d4e72f1c517cbaffffaafdbe16493c9c4cfb7da2812b9205fcbb22c9fe4793e994c3ab5212d2e2ee6aef248a3b9b6bcbc6c6f9564870f1f0ee6a79dbbb67dfb76fb92247beaa9a782f9fbb4a79f7edafe8835e7befbee0bce7b5eda33cf3c630f37d9b5d75e1bcc3f0f6d6969c91e6ab213274e04f3afc5b66fdf3e7bea6b529665411bdb7e3ccec7ae1f8f83ef772ddc43a7d7661b1a000000d2e5917c8bcfa1030000780119720f5db0114e57eacacc6f75a0c80aa9d00100000c21a8d0a92a5c6c5f5d7c4595cfa10300009899a04217abd4f13974000000f349ef818be55955be55e55d5e654ec5877be85c5f647c41c648850e0000a037bd025ae65bba3217c9b7bcca9c8aefbe878e0a1d0000c0206ca5cd8d155ff8dfacf334d717f19df7d0e90c12000000e96c9e155d118de569ae2fe2c33d74de5313f50d000000e97465aeccaf6c2f7ea5cecbd3547cb887ce7b6a4255e4ca4c910a1d0000405f752ba0b1ca9ce3e5692abefb1e3a3e870e0000601041854e55e11af7d0792baa097be878ca150000601841852e56a92bb3b3ba15d5d81e3ad717195f9039ea355d00000024d17be0627956956f557997579953f1e11e3ad717195f903152a1030000e84daf8096f996aecc45f22daf32a7e2bbefa1a34207000030085b697363c517fe37eb3ccdf5457ce73d743a83040000403a9b6745574463799aeb8bf8700f9df7d4447d030000403a5d992bf32bdb8b5fa9f3f234153fcab22c6f5371cb8b6c30cf5713c536316d6ddebcd90e01b275eb56d9bb77af1d4ef2d5af7e55defbdef7da619c23af7ded6be5f6db6fb7c349ce9c39233b77eeb4c3c90e1e3c281b366cb0c36bca1ffdd11fc9effcceefd8e1b9f0e0830fcaf9e79f6f8793dc7aebad72fcf8713b3c736f7ce31b65f7eedd7638c9134f3c21bffaabbf6a87d79cf7bef7bdf2cbbffccb7678cd994c26766835391b8d5613b7d1285876adcbbf8284ae4adcd4449109171682ed7700000068693299745ff52c9658bdc44f464542b7b010246c755cc257972102000060ba68854e15d674c2e61e83a8cbbfca0a9dfef6b242a726b0a8d0010000a4cb2693d605352db6241b2cb9d6b1095f9b18000000c4452b74897be882329b0bf39e9e581d589d9ca75c01000006e1e55b457e55e659917cabccd35c5fc477de43e7d46588000000982e5aa18b6d81d34bab35f957b887cedb84578f3d74000000e966bb87ee2c7c0e1d0000c08f9b6885ee9ceda113f6d00100000cc1ed9debbc874e7d6f9e27eca17395baba0c11000000d3452b74d12d706e575d7dfe15eea1d39bf0f81c3a000080b362b67be8a8d0010000f416add09db33d747c0e1d0000c020bc7cabcb1e3ad7f33974000000b313add0c5b6c0f13974000000f369b67be8f81c3a000080dea215ba73b6878ecfa10300001884db3bd7790f9dfade9ccfa1030000988d68852eba05ceedaaabcfbfc23d747a131e9f430700007056cc760f9d4ef85a3c75e19ced78993247737c95b836cd2175f129c7d1102f0d73f48d77a6c5cb9439bc7f39d4c44bc31c6de3a5668efef12eacdd4340760efd3ab4891733473cbeba0febb839aaf8ea3ca65d076754f70fb873142f2222a35143fcf4eb908bc88279986bdafd1ce58e2331be3c8e01e2dbdecf75fac4e7792e0b0b0bbde2ddbdd9f6fda0b91f2ba9f1ea3e28cfa3031d6fdfeb6d54f1eef2759ba78cd7f7b38cca6b324d34beb8b7dba88b97d4f330f751db39e2f1ab47d6668e687cca710c105ff77e6c334753bc34cc112474d32e481dfb03a61d90d5375ecc1c5558fb785173a4c64b3187be8ee57974a1fed054f1ab37762b657cda79e44d7f305b7071fa3c52e2cbe3f0e2a75f87c6f884e310593d97d4f83ccfa5dcda901aefeeef0ef18ebe8fba6cb170fac7fbf761f54e6dc75e07ef7ab4108b1791d673d4c54bcb39aa78751d0a9de2d571e8f7c1b439a2f1e63e6a9aa34dbc4c9943ce567cca7934c4cb9439a445bc4c99c3bb8f6ae2a5618e73152f3573e8f8729e06768eeac7b68b1733877e1ddbc68b9ae3ecc48f8affd36c34d27f57abeb30ed75f0d4fc83b8f31e3ac73b80945fd0e686ea1b2f5d7fc1d75cc8b673c45fd0556de6688a971673c4e3ab1baa7d7cfc3ac89439f44fd33794bd8feae6688eafce436ae6688c6f711e5de2a5660ee9102f3573748997c81c3adebe1fead839e41cc74b648e2a2cfe7e88d17344af430b6e8ed47829e6182ebeba0ef67dd0a8e617fcb4d7c1c967fc0fb85234befd7528cf43c777388ef23c64f558bac63b799ef60f38c7dd4722ea383a182ade3b8f0ebc78f53e6dabba6c69f3e8fb58bf1fdbce118f4fa898467e2fb639067dd787ef87faf308f7d025fc4212d16fc4ea85e824f60b29e58d5c7301a67171d579a4c597c7e1c5773b8ff2a572f109c721d22f3e4ffc8564afbfbba66de31d7d1ff6fe85d233be3c8f0eaacbd6ed8dec78afc360bf9056b599231ebf7a1f778b0faf83b43886eaa7d5bf9f9be6688e77ffa77e8ec6f894e3388bf1d230878ed7f7b38d978639a465bc34cc31647c753fc6c5e6a87eecf47889cc11bd8e53e83952e245cd315c7c751df4fba0c96874b6b758b4e31d4742bc883a0e2fbedd7510a989ef781cc1fba063bc13cc239125d73af11ba2326d8e787c75215bc737bca04d73b489978639dac64bcd1ccdf1d575706273485d7ce438fac64bcd1c5de2a5660ee9102f3573e42dff40c800f11299a36bbc9839aab0bebfa0bbc54b31473c3ebc0f63fcf8ea3cda5e071111e9f90b3e3fabff806b771d441f878eef781c7de3cbbbaa477c3ee01eba59c67be7d14175daea383ad0d7d11d47977962f152bcd7daa8e2abf3909478fd7e2a46dacc118d57af639b39a4215e5aced1142f2de688c7bb334b8c4f398f297f5feae60812ba360714e3e69855bc98939c7641ea94c791182fc51c5558f7f855abe5dde478f587aa3c8f0ef2d8666d75634fe3be3bf53cf44f0bfee0b530647c795795f1d3af838dcff3d9543c6dbce8e368c13b7e773f748877ecfba97aa7b663e3a5eb1f4cf57ed4ef87b673c4ae83d3668eb3192f2de6a8e2abeb6035cd51c6ebfb29f23ea89b231a1fb98ffbc64bc31c521b9f701e3abec3717489979a3964a0f8f23e6a88979a39bac44b648eaef162e6d0f1e53c2db839aa1f9b18af5ec72ef152ccd1275e4444a27fdf13f6d0d5dd90e14b1ed737deb1f1d2f9177cfc866a3b475dbcb49c43df50365e5acc31ed86488b1f15ff6755d31c6de2a5618e2abef93a48cd1cfaa7b9e3182e7efa7934c6a71c478b7889ccd1355ecc1c43c6d7bd1f6282f3e8182ff63812e245cd5185c5df4f4d463d7f41f78d171191e82ff8f6f2f21f3ed57588bd0feab8efd6c7d1e575e81bef54e7911e5fde55657cf7eb90e7b3f9079ce3ee03917ef1de7974e0dd87897fdfedfbd9fece68e21dbf7a3fb49da3fab189f10defe73673c4e3abfbb06e8e700f9dbea13adcc8e51bd1dc90ad6f849ef179e3124cc279e8f894e3e8115fbe54338877f2395982c9937fa1b81f9b18afeea3ba374f131bdf759e58bc246dcaadae837b1dda1c47191f793ff58d6f731e8df129c711891791a9734c8b972973445fc70ef14e53bcb498635abc4c99a34dbc34cc5185857fe0b4daf8d875ac119ba34bbc44e6e81a2f668e2abeba0ed51d5ecfcda1bfdb1d47d3ebe0f48d97daf3681f2fb1e3f0e2a75f0729e688c6a71cc700f1e57dd0215edc79f4887762f1c1926b9d6937c4b439ce76bc4c99a339bebaa19ae690baf894e36888978639fac63bd3e265ca1c6d6fc8ba39dac64bcd1cfde35d58bb5fd0760efd3ab4891733473cbeba0febb839aaf8ea3ca65d0767a47f31c6eea729fac68b8848e33fe0a65f87bce91f7029c791185f1ec700f16defe73a7de2f301ff01d7f6fda0b91f2ba9f1ea3e48da43a7e2ed7bbd8d2ade5dbe6ef394f1fa7e6ef10f1f271adfa13256172fa9e761eea3b673c4e3578faccd1cd1f894e31820beeefdd8668ea67869982348e8a65d903af6074c3b20ab6fbc9839aab0f6f1a2e6488d97620e7d1dcbf3e822ba84b27a63b752c6a79d47def407b30517a7cf2325be3c0e2f7efa75688c4f380e91d573498dcff3fe4b30e5fddd21ded1f75152c5b277bc7f1f56efd476ec75f0ae470bb17811693d475dbcb49ca38a57d7a1d0295e1d877e1f4c9b231a6feea3a639dac4cb9439e46cc5a79c4743bc4c99435ac4cb9439bcfba8265e1ae63857f15233878e2fe76960e7a87e6cbb783173e8d7b16dbca839ce4efca8f83fcd46a3b3f75059e73d748e770029bfa0cd0dd5375ebafe82afb9906de788bfa0abdaccd1142f2de688c7573754fbf8f875902973e89fa66f287b1fd5cdd11c5f9d87d4ccd118dfe23cbac44bcd1cd2215e6ae6e8122f913974bc7d3fd4b173c8398e97c81c5558fcfd10a3e7885e8716dc1ca9f152cc315c7c751decfba051cd2ff869af8393cff81f70a5687cfbeb509e878eef701ce579c8eab1748d77f23ced1f708ebb8f44d471743054bc771e1d78f1ea7dda5675d9d2e6d1f7b17e3fb69d231e9f50318dfc5e6c730cfaae0fdf0ff5e711eea14bf88524a2df88d50bd149ec1752ca1bb9e6024ce3e2aaf3488b2f8fc38bef761ee54be5e2138e43a45f7c9ef80bc95e7f774ddbc63bfa3eecfd0ba5677c791e1d5497addb1bd9f15e87c17e21ad6a33473c7ef53eee161f5e0769710cd54fab7f3f37cdd11ceffe4ffd1c8df129c77116e3a5610e1dafef671b2f0d7348cb78699863c8f8ea7e8c8bcd51fdd8e9f11299237a1da7d073a4c48b9a63b8f8ea3ae8f74193d1e86c6fb168c73b8e847811751c5e7cbbeb205213dff13882f741c77827984744fe3f91ed5386de8f92570000000049454e44ae426082, 'image/png', NULL, 1, 5000.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ent`
--

CREATE TABLE `ent` (
  `ident` int(11) NOT NULL,
  `fecha_ent` date NOT NULL,
  `hora_ent` time DEFAULT NULL,
  `direccion` varchar(255) NOT NULL,
  `estado_ent` varchar(255) DEFAULT 'Programada',
  `ped_idped` int(11) NOT NULL,
  `notas` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `histenvfac`
--

CREATE TABLE `histenvfac` (
  `id` int(11) NOT NULL,
  `idpedido` int(11) NOT NULL,
  `email_destino` varchar(255) NOT NULL,
  `fecha_envio` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `histenvfac`
--

INSERT INTO `histenvfac` (`id`, `idpedido`, `email_destino`, `fecha_envio`) VALUES
(1, 25, 'glomigue07@gmail.com', '2025-12-10 19:12:19'),
(2, 37, 'mauryecheverria948@gmail.com', '2026-03-14 13:26:05');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inv`
--

CREATE TABLE `inv` (
  `idinv` int(11) NOT NULL,
  `alimentacion` varchar(255) DEFAULT NULL,
  `tflor_idtflor` int(11) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `precio` decimal(10,2) NOT NULL,
  `precio_compra` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Precio unitario de compra al proveedor',
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `empleado_id` int(11) DEFAULT NULL COMMENT 'ID del empleado que realizó el movimiento',
  `motivo` varchar(255) DEFAULT NULL COMMENT 'Motivo del movimiento de inventario',
  `cantidad_disponible` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `inv`
--

INSERT INTO `inv` (`idinv`, `alimentacion`, `tflor_idtflor`, `stock`, `precio`, `precio_compra`, `fecha_actualizacion`, `empleado_id`, `motivo`, `cantidad_disponible`) VALUES
(34, 'Ambiente fresco y seco', 40, 52, 2500.00, 1500.00, '2026-03-16 00:58:06', 43, 'Producto nuevo agregado al inventario', 52),
(35, 'Agua y nutrientes', 41, 118, 1000.00, 500.00, '2026-03-16 00:58:06', 43, 'Producto nuevo agregado al inventario', 118),
(36, 'Agua y nutrientes', 42, 40, 15000.00, 12000.00, '2026-03-16 00:58:06', NULL, NULL, 40),
(37, 'Agua y nutrientes', 43, 35, 14000.00, 11000.00, '2026-03-16 00:58:06', NULL, NULL, 35),
(38, 'Agua y nutrientes', 44, 30, 14500.00, 11500.00, '2026-03-16 00:58:06', NULL, NULL, 30),
(39, 'Agua y nutrientes', 45, 25, 22000.00, 18000.00, '2026-03-16 00:58:06', NULL, NULL, 25),
(40, 'Agua y nutrientes', 46, 50, 10000.00, 8000.00, '2026-03-16 00:58:06', NULL, NULL, 50),
(41, 'Agua y nutrientes', 47, 60, 4500.00, 3500.00, '2026-03-16 00:58:06', NULL, NULL, 60),
(42, 'Agua y nutrientes', 48, 28, 12000.00, 9500.00, '2026-03-16 00:58:06', NULL, NULL, 28),
(43, 'Agua y nutrientes', 49, 16, 35000.00, 28000.00, '2026-03-16 00:58:06', NULL, NULL, 16),
(44, 'Agua y nutrientes', 50, 70, 3500.00, 2500.00, '2026-03-16 00:58:06', NULL, NULL, 70),
(45, 'Agua y nutrientes', 51, 18, 28000.00, 22000.00, '2026-03-16 00:58:06', NULL, NULL, 18),
(46, 'Agua y nutrientes', 52, 45, 5500.00, 4000.00, '2026-03-16 00:58:06', NULL, NULL, 45);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inv_historial`
--

CREATE TABLE `inv_historial` (
  `idhistorial` int(11) NOT NULL,
  `idinv` int(11) NOT NULL,
  `stock_anterior` int(11) NOT NULL,
  `stock_nuevo` int(11) NOT NULL,
  `fecha_cambio` datetime DEFAULT current_timestamp(),
  `idusu` int(11) DEFAULT NULL,
  `motivo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `inv_historial`
--

INSERT INTO `inv_historial` (`idhistorial`, `idinv`, `stock_anterior`, `stock_nuevo`, `fecha_cambio`, `idusu`, `motivo`) VALUES
(43, 28, 20, 15, '2026-03-10 20:22:18', 66, 'Descuento por pedido #30'),
(44, 28, 100, 102, '2026-03-12 20:47:27', 67, 'Pedido #31 cancelado - Restauración de stock'),
(45, 32, 100, 80, '2026-03-14 09:44:52', 70, 'Descuento por pedido #34'),
(46, 32, 80, 100, '2026-03-14 09:45:35', 70, 'Pedido #34 cancelado - Restauración de stock'),
(47, 32, 100, 99, '2026-03-14 10:51:57', 70, 'Descuento por pedido #35'),
(48, 32, 99, 98, '2026-03-14 11:07:19', 70, 'Descuento por pedido #36'),
(49, 31, 50, 49, '2026-03-14 11:07:19', 70, 'Descuento por pedido #36'),
(50, 32, 98, 97, '2026-03-14 12:32:55', 69, 'Descuento por pedido #37'),
(51, 32, 97, 96, '2026-03-14 14:07:45', 69, 'Descuento por pedido #38'),
(52, 31, 49, 48, '2026-03-14 14:07:45', 69, 'Descuento por pedido #38'),
(53, 32, 96, 95, '2026-03-14 16:20:11', 69, 'Descuento por pedido #39'),
(54, 32, 95, 94, '2026-03-14 19:03:38', 69, 'Descuento por pedido #40'),
(55, 32, 94, 93, '2026-03-14 19:27:01', 69, 'Descuento por pedido #41'),
(56, 31, 48, 46, '2026-03-15 08:51:58', 69, 'Descuento por pedido #42'),
(57, 31, 46, 48, '2026-03-15 08:53:23', 70, 'Pago rechazado (Pedido #42) - Restauración de stock'),
(58, 31, 48, 50, '2026-03-15 08:54:13', 43, 'Pedido #42 cancelado - Restauración de stock'),
(59, 31, 50, 48, '2026-03-15 08:55:26', 70, 'Venta online - Reducción de stock');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lotes`
--

CREATE TABLE `lotes` (
  `idlote` int(11) NOT NULL,
  `inv_idinv` int(11) NOT NULL,
  `numero_lote` varchar(50) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 0,
  `fecha_ingreso` date NOT NULL,
  `fecha_caducidad` date NOT NULL,
  `proveedor` varchar(255) DEFAULT NULL,
  `precio_compra` decimal(10,2) DEFAULT NULL,
  `estado` enum('activo','vendido','caducado','devuelto') DEFAULT 'activo',
  `observaciones` text DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `lotes`
--

INSERT INTO `lotes` (`idlote`, `inv_idinv`, `numero_lote`, `cantidad`, `fecha_ingreso`, `fecha_caducidad`, `proveedor`, `precio_compra`, `estado`, `observaciones`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(8, 43, 'LOTE-ORQUíDEAPHALAENOPSIS-001', 1, '2026-03-15', '2026-03-16', '', 1111.00, 'activo', '', '2026-03-16 00:01:01', '2026-03-16 00:01:01');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pag`
--

CREATE TABLE `pag` (
  `idpag` int(11) NOT NULL,
  `modulo` varchar(255) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `ruta` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `icono` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pag`
--

INSERT INTO `pag` (`idpag`, `modulo`, `nombre`, `ruta`, `descripcion`, `icono`) VALUES
(1, 'Ventas', 'Pedidos', '/ventas/pedidos', 'Gestión de pedidos de clientes', 'shopping-cart'),
(2, 'Ventas', 'Clientes', '/ventas/clientes', 'Gestión de clientes', 'users'),
(3, 'Inventario', 'Productos', '/inventario/productos', 'Gestión de productos', 'package'),
(4, 'Inventario', 'Stock', '/inventario/stock', 'Control de inventario', 'database'),
(5, 'Entregas', 'Programación', '/entregas/programacion', 'Programación de entregas', 'truck'),
(6, 'Reportes', 'Ventas', '/reportes/ventas', 'Reportes de ventas', 'bar-chart-2'),
(7, 'Configuración', 'Usuarios', '/config/usuarios', 'Gestión de usuarios', 'user'),
(8, 'Configuración', 'Sistema', '/config/sistema', 'Configuración del sistema', 'settings');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `idpago` int(11) NOT NULL,
  `fecha_pago` datetime DEFAULT current_timestamp(),
  `metodo_pago` varchar(50) NOT NULL,
  `estado_pag` varchar(255) DEFAULT 'Pendiente',
  `monto` decimal(10,2) NOT NULL,
  `ped_idped` int(11) NOT NULL,
  `transaccion_id` varchar(255) DEFAULT NULL,
  `comprobante_transferencia` varchar(255) DEFAULT NULL,
  `verificado_por` int(11) DEFAULT NULL,
  `fecha_verificacion` datetime DEFAULT NULL,
  `referencia` varchar(255) DEFAULT NULL,
  `comprobante` varchar(255) DEFAULT NULL,
  `comprobante_imagen` longblob DEFAULT NULL COMMENT 'Imagen del comprobante guardada en BD',
  `comprobante_tipo` varchar(100) DEFAULT NULL COMMENT 'MIME type ej. image/jpeg, image/png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pagos`
--

INSERT INTO `pagos` (`idpago`, `fecha_pago`, `metodo_pago`, `estado_pag`, `monto`, `ped_idped`, `transaccion_id`, `comprobante_transferencia`, `verificado_por`, `fecha_verificacion`, `referencia`, `comprobante`, `comprobante_imagen`, `comprobante_tipo`) VALUES
(44, '2026-03-15 12:10:28', 'nequi', 'Completado', 3000.00, 43, NULL, NULL, NULL, NULL, NULL, NULL, 0xffd8ffe000104a46494600010101006000600000ffdb0043000302020302020303030304030304050805050404050a070706080c0a0c0c0b0a0b0b0d0e12100d0e110e0b0b1016101113141515150c0f171816141812141514ffdb00430103040405040509050509140d0b0d1414141414141414141414141414141414141414141414141414141414141414141414141414141414141414141414141414ffc00011080211016003012200021101031101ffc4001f0000010501010101010100000000000000000102030405060708090a0bffc400b5100002010303020403050504040000017d01020300041105122131410613516107227114328191a1082342b1c11552d1f02433627282090a161718191a25262728292a3435363738393a434445464748494a535455565758595a636465666768696a737475767778797a838485868788898a92939495969798999aa2a3a4a5a6a7a8a9aab2b3b4b5b6b7b8b9bac2c3c4c5c6c7c8c9cad2d3d4d5d6d7d8d9dae1e2e3e4e5e6e7e8e9eaf1f2f3f4f5f6f7f8f9faffc4001f0100030101010101010101010000000000000102030405060708090a0bffc400b51100020102040403040705040400010277000102031104052131061241510761711322328108144291a1b1c109233352f0156272d10a162434e125f11718191a262728292a35363738393a434445464748494a535455565758595a636465666768696a737475767778797a82838485868788898a92939495969798999aa2a3a4a5a6a7a8a9aab2b3b4b5b6b7b8b9bac2c3c4c5c6c7c8c9cad2d3d4d5d6d7d8d9dae2e3e4e5e6e7e8e9eaf2f3f4f5f6f7f8f9faffda000c03010002110311003f00f63f8577a45f5c5bb1c6e4000f5eb5e9776826b79a2ea1d4afb104608af10f01dc797e24b6392a1982d7b96cc4857a8cfe95e1ad8f61a3917996d90468022c436ed1d000303f95741e1ab5fb3da89dc62498e403d87f935ceb58bdf788e4b65cf96ac0b1f6aecd1844815410106148aab9241afeaf1685a4dcdfcee162b753264f73d857c71e21d5ae7c57ae5eeab70434d3bee5f5d83b7e15eb5fb44f8e25ca7866c5c6f3892e493db8c0fe75e236f1de4837a6123e54002a5b1169ed65dd1b00bb08eb4e0c1b10b3ac783c9150258ddc881583960703071c55883c353a6df31b2492724d201f2bdbacab827ea3bd385e431b86c1661d01ab71f861a5381215f5db5713c356e368666761dcd02b1892ea2c63663184c1e012294df48eabc119e98ef5b52d9e9960d998a6d1d77734c4d474e926ff00478e4957b0488ff5a02c66436d75236fdae768cafd722bea1d16632e9b6121ce5ede33cfd2bc221b99250ab1e9d31f76e3b57b4f852e1ee3c37a633a947116d2a4f4c1a682c75b0382a39a7c8700556b53bb1569c8c568162b480e0f1918ad0d3a3df611e3920918fc2b39d8b301d056ae94aa2d1b0dcab67f9d2b0cf9d7e37422d7c7d72c38f362493f4c7f4af3d32e64af4ffda2a210f89ece6031e6db6d07d48edfad79297384f522901a31b92dc73565652edb94e4564accc071d4d5c8c6c182d8e2901b10ca1d0f34929214e0e335412631a81de9c6eb380d41561d34a32b96c8e87f2af3cf125eae95f1abe1f5eb30c3c8f0007d0ae3fc2bb69986f386c039cd799fc53f261f11f8175066c3c3a9c6809f7fff0055023e9ffb5048ce7860b823df18c56523b47ab5ded1c3847cfb62a496620904000e0f1eb54aeae19b5b7619556b65c01ec4d03b1d05ac9e645729d7286b8ad7d77ee8b868da22a7db8ae9f4c918bcc07cc4a9ebf4ac2d4222ce54a0c1539f638a5719e5fa3787d0089b27284f53efc574896491464103279a9f46d19a6b5cfda218c827e56193d6b722d1eda351e7dd8071d116992cc04811a301530454f140a1ff00d5f5fd2b67ecba5403991dc9f534e8aeb4b85f888bff00bd408c778b6b738e41c54b1db31854aa961ec2af37886d2d9ff75628cc0ff10cd49ff0964a08f2ed4607609d295c0ce4b09a424085c9ec76d491e87732b802dcef07237701bdaaddc7887519f2d1c457fdded547edfaacc773bec5041c3753f4a17711f0f7ed0de049bc09f132fe268562b6bf1f6d83674c36770fc0835e660e6beacfdacfc2b737de10d3bc412303269b71e43377f2a4cf27d811fad7ca98dbc7715ee509f3c2e799563cb20a28a2ba0c028a28a067ea7e837ad65a95ac88718719fa57d19673f9b6d14b9ddbd41c8af99ac1c6d57ea4722be86f095c7daf40b37ea76e0d7ccc5e87ba5c8228edb549a6001690014baf6ad0e83a55cdf5c32c70c1197258e067b0a7dc0f2ee91863e60467df8af13fda03e22456d7b69e188126b8404497663f4ec3f9d592cf36bebf4d7f54b9d56e433cf76e5c0eb85cf02ae22c663458addf9fef0e958765accd01fdce9df27250bb74f6ad4b6d6f589c30482de223bf5a9623563d3672f91129faf6ab31e993b3125a3880fc6b3124d4ef0e65bd0b8ed18c535ed15b89eee67fa3628035cd8c4ae0cb7a547b1001a8843a3ee2269fcce7d49ace486c632a0ee7c77639cd5a59ad63e45b75e840a00b66eb45817096c65f4222c9fd69abad46bff1ef607fe05c5356f0b0fdddb8c7d291a7b89187951a8001dd918a7602c36aba949b76451427b12735e9bf0eeea6baf0f29b870ed1ccc991dba5798149081b8e18e318af43f86cbe4e9fa8425f76245700f6ce7fc2981e876c42f7a9dcf06a9db39df8c64719ab4efbb23d29dc0aee429c9ad3d1a5dd1cc08da14027f5acd95722ace8f969e64fef47c7b9a2e07947ed216ca6db40b92b87def193ff007c9af0898f98480db47622be88fda2ac85c783ace724836f76093e80a91fe15f3a797d0f72a3834013c0a0606edc47ad5b7932c39cd670cc473d0fb558b594f73d7a93da901a1bb383d8f4a4949098e99ef5107e002d9f4a6cd212a282901976a156c13eb5e5ff001a576e9ba15ce71f67d4e2727d3915e8f2be4f15e7bf1ae347f02cf2487688a789c11ecd4ae267d06b379d6f148186d7da73f80aa525dbff006c59271ff1eceac7dc30ff001aa5a1df0b9d034d9d5b31c96f1b2ffdf356e460d142e576ca5ca83edc52e6291b3a75d1f3873cb64567cb97d4e153fdf21867b11525a381749f5c0a6a123555c8193260e7b51b8dab1c558584d35ece7ce2812675d9ed9ad96d2119b2d2b9cf4e69eba7ac77b7bb6e638dbce618cf35a89616f2488a6f147cbce0d1720cd8f4a85796c9fa9ab29631ab0d8983fceb4d62d2ed9544f70cdce01cd4e2e3448f0c26638cf7a77119ad668a858c7f31e981d69f0c2029fdd91f8568af88b47541fbb67c74cd23f8b6c547c96b9f4e2a5b0298b42d9dbbbf2a72d83c80298d8fe14b278ca5407c8b5007b8aa4fe36d4649147d9b68e7914d2b8ee61fc53f01bf8bfe1eebda51b666335a48d1f1fc6a370fe55f9b9c8186fbddf3edc1fd41afd3797c41aa5c6632c36364153fdd2083fa57e75fc46d09bc33e39d734c6e04176fb7fdd3861fcebd4c24b789c5885d4e728a28af44e00a28a2819fa8b059dde8ba9cba55f45b6eeddb0de8cbd88af71f86f7e2eb4268cf0d0b608f41587f12b4383c41e1db4f12d98f3244c17990637a1ea4fd31fad4bf0aee018ee632c0891430f7eb5f37cb63dc4ee8ea7c61aa0d07c3f79aa94327d8e332ec1debe3fd435bbad77569f549fe67ba6f30063ca83d057d93af59aea5e1ed4ed9867cd8245e99e319fe95f1e6951e99abe873eab61746782da530ce21f9b6152473e94c4c7c57122855edec09ad083cc0036eebe94963369f1ee516b71310796390335bb65796e8a045a5f27af980b5488a11c196f9de4d87d075ab90690d30f920771db835ad15dea772365b69b18c74db11ad28b48f14dcc4be559c8b9eb84c53b018369a0dcb49ff001e8c31d37715a03c3773c97786207a0622b597e1f78aaea424ac8838fbc71fd6b421f843ac5c006e2e154ffbd4580e71b418a24ccd7d0a81d553934e8d74c8e22bf6a6623f8556bb087e0bca8fba5bd5c1ec06735a76bf0734e8c9696e9d89ea00c5303805bdd2a28c3794cc4772735d37c3cd6e0bbd46f2d6283ca0f16727be3ffd75d3c7f0b7448c005657f5c9abb61e10d3348b8692d2dca49b19720d005cb4f971ce49e6ad12d9aceb13b63519ad0c9fce801189c7356b4538d4501e8411551f2719a9f4e252fa03fed8a00e53e38dab5d7c3bd4c2ff0004a8df9135f2fb01b65e09dad807d46057d77f13ed3ed1e0ad6600bb9bca2c07e35f2badb2b21207079cd02b99db418f38eb52c6a060e300d49343ba32806169b0a154553f757a50098f7010a8a1ce14719cd49280790323d6a0dc55b00e73fa52b9572b4ae841dc48fa571ff142049bc03ab1392891798777b5759710ef1d98f6ac0f195bf99e10d5e39402bf6570147d2901d8fc3ebc179f0fb409d5b96b48c9fa738ae89efd22860591777992ed56f4383fe7f0ae0fe0e5c6ff00869a1303f2adb85e7d89aea355ba71650c800012ea323f2614ac544dcb46db3c259b82462a6811a4d5d95891fbd0463bd508d834b19fbbb5c7f215ab6bf36b119c7cdbf23f4a0a91cd6a9e1adde23d4dd677557933b4f63dea54d1e34423ce66206320d7a25ee8ba73ea970f2c2eccd86c8381d2ac4163a6411e3fb391c8eef934cc8f381a5c2fb4306754c1049ef562df4e8f7ed485b69e49c1e6bd2a296da343e569d6e98ebf2e6ac0be91c26db68a303d1053b01e643459b2425a4841f44357ed7c377d22fcb6321ff80d7a17daee981080f3e829425ecb8019c1f76c54b40710be0fd55c7cb69b47fb640ab11f80f53700fee223fed35762d6b72e30d39c8ec5b38aaffd95297cb5c7eb54b603951f0e67dc59ef60466c0254e7bd7c31fb6678187833e2da4e922cb16ad651de6e5e81f2cac3f00abf9d7e87ff00670839662d8f9b83e95f21ff00c1407c37fe85e10d72342638e49ac656f52e15907fe3b2575e19daa5bb9cf5d5e27c6b45267760ff00b2296bd93cd0a28a2803f707e1559c3e21f841afd96cc2d889154375dbc11fd6b8bf003b59ea30a6d2a0e54e7dba55af863e2a9b42d3bc4f6334a1a4bfb40c231fc24673fcc553d2d96dae2393382ac09af9f9e8cf6a2cf578f0a4863b477f7cf18fa1af8b7e125be9be07fda57e28f8335f863b2b6bd51aadbc36f968520c3b3b1f4eaa6bec9b7b8128538f9580f9bf107fa578deb1e17d0fe1d7c4af137c47bf845cdfea0969a6c5b937604ac916ce7b12714a2c6cee7c27a7f8275ed356efc3e6cb52b46e77c3207eddc7515d341a469700f934f807b8515e33f10be0259d834fe23f005db783fc4d0b19505bb116b3107eec91f4c1c7eb5d5fc18f8a127c44d0ef60d56cbfb2bc51a44a2db55b1cf0af8f9645ff0065b922aac847a3469040729122ff00baa0538dcb95001200f4a888ca8fa67f0a4db95cf41eb4ac805926e9514931c0c1c66a0b9beb6b252f3dcc7120eb9615ccea7f12744d3093e71b971d12319cd2d80eafcd618c9e9514d39520f407b9e2bcd350f8b7717002e9f66154e70ef5cbea9e2bd67573b64bb645feec7de901ed379add958a30b8b98e32074dc2b163f1fe992df5bdb5bef99e47d9bd7a0fad7960b17b9844f3894a28e5e5a7d96a296f790adb26f64756f947bd4dc0f5f8c88dc8e9f31157d0ee02b36518b9604e0939c7d40abb1b0551cd302c3f414b0713c6738c30a616ce29d19dac09e94017fc51a79bcd1f505ea1eddcfe95f25cd6fb7280602e47eb5f64dcc5f68b475eaaf0e3f435f24ea307977f791f4292b281f8d016311ed0328e6a2367b509ea0d5f00862bd292285b9dc72b40ac662c782036714c9225462474f5ad096d4963c6076aad2c25508eb9a96333265019067079aa1acdaa5c6957b149921e171c7d2b69adcb1cf7038aad736eed6d22bae43232fe628198bf0466327c38b442df2c123c5cf6c1aec35a00e8c4961f2ca8dfafff005eb84f817229f08ea1031c3417f22edfc6bbad788fec2ba206080ad8fa30a071668a92befc0fceb7ac610ba9c2ecdd58115ce248de5c671c32e6ba4b03e64f62cd9520f353729ea7a1a58c57572c0fdedaa7f0e6ae47a5c108392a33eb5cc6af692cdabc252e258d4c431b3bd126812cb16d92e6e1893c726a96a41d3b436719f9a78d7d7e61504da8e9c842fda62c0f7ae74784f8e22924f52cc6957c17bba59939ee5aa846d9f116956e71f6a8f3dc0aad378c34b89fe5959c9fee8cd431782f68c7d9a34f763d6a61e118631b8f90847bd16029cbe36b0462424aec7b6daad278e50fdcb194fb915ac7c370282df6db741dea29b4bb288297bf8b69f4a00c29bc6572e7e5b1639e99af0afdb0ae2f75df83374d2da88d2caf20badc39c104a7fecf5f4532691183e65d064ff67bd709f1c74bf0eebdf087c59651c8ef3369d3490827ac88bb907e2c16ae9cb9669933578b47e6011b7f1278f4edfd292907ae739e73f5e7fad2d7d01e3851451401facde186b5b7f147d86dfcd95dc3c0ee4120679cff002adf7cc2ac0fdf5183f81af4f8b42b3d2379b0b28965653972bf3138c66bcd355b19ec2fbecf71c48c379fc735e0cd753d889df6897265d22063d4af5ae07e33e93a9f89f57f02e8f636ccda74facc579aa4e0642436ff003a83f572bf95759e0db81368c416c98d88fc2bd2fe187d926f10bdbde246e92c0caa241c6ee2b148a93b2b9c0eaf3a4eeb182a44992ca0f5ce011fafe95e6da3431e85fb4b59ac4847f6f786643758e8cd048ab1b9f7c3b0cd7d49e29f81567a94cd75a4dc9b29c7dd43cae7dabe75f0c785756b9f8d9e2bf13dc5bb49a2e95689a058dc28cac8e0ee99d4fd76d6c88534cf4cbbb29de16fb1c88931076f98323e95e59e26b8f1523b2df4cd6d1e480605f948af5a8e44767643805b233daa59238678cc7731acd19eaa4706a5a2d6a7cf6741d435578d6386eb50dd9c9c122b5b4cf843e21bb9159ac96ce13fc52f5af4ed63c3f7eb6cff00d897e6cf3cf920706bcbb5dd5bc45a3c87fb426b88cae79dec54d40cea6c3e0dc368bbefef3785ec1c28f7ef581ae6b7e1cf0897b5d3ad22b9bb5cfef5db760d711a87892ff52ce6e5d63ee7279a834bd225d4250d6d08724f32b53117aeb55bff0011c8df6b7f2a12388a318aded0b4c82dc445808e2e3737f10153e9fe1afb2006626593be0715ab1d86fdc16224630463a5401d35f4919b98de16df13c6acae7bd4f149b941ed551ed9a1b1b2ca6d223da7f0a9adc9110a2e068c6dbb1529fba7e955ad9b23156d541233dc62a8ab1d45b30fb3c23b3478af937c5f66d6be26d513254a5c3715f56581696d2061d895fe55f36fc55b5369e3cd5631fc727998fa8a0471f1beeea39a9f05517e5a58a304727269d863c74c5022161bba8c544f0e47156c0f5e69ea993f778a2c0633da1524939aa73a111b6393d87ad74135a007af5aa53da88883d49ed4ac33ce3e0edabc5ff09240ca54a5f6edbe99cd7a06b118fec4d400193e4123ea315c7fc3e8fecde2cf164433869e39001e9f364d777a85b8934abc55ebe4b823df149e835a1069f299acad98f43129fd2ba4d29db6dbb11c87c5739a042b368f6122b02ad12e0fae38ae9ac07c91a81d25150d95b9d1f8af5cbcd0f5ad37ec96cb38921218376231fe3519f18eb0f1fcb6c80e4e71daaf78aed8cba8698e8096d8c381feed42f632b6d0b1bb13fec9ab4f413466bf8875e7ce1953ea69bfdadaecb80d76a9edd735a434ab993a42fc7fb3522e8576bff2ece4f6e29dc8321ae7577e1eec9fa1a85edaf642375dc983d706ba54f0c5f4a03790c08f52054a9e14bf90f223007ab014c0e49b4e99410d7123ab77cf4a55d08b0f9a57653ea6bb78bc273676bcd021f76a9078595721efe01e9b4d00702da247dc1603a027ad54d5fc3b6d77a4de5bb47859a1746ffbe4d7a13f87ad15b0fa9c43d8544ba668c5d564d43ccc30c8e9dc0c7eb42d25713d8fc7dbab77b4ba9e091363c52346cbdc152548fcc5475d9fc67d2e1d17e2df8c6ced98b5bc7aadc188ff00b0cfb87f3ae32be862ee933c792b30a28a2a847ef794c39fa579dfc50b736b75a7dda7f1a146fa8c71fad7a3a8070c07f935e7df1988b1b4f0f48fd2f2ee4813fded99fe95e14f6b9ec229fc3e9c196f60033b94301f9d741ad5cdde9fa65cdd593986ee253246e3b62b8af065e35b6bb6aac4012a6c23f01fe35dfdfa24f0cb6f27dc70ca47a8231fd7f4ae74cd1ec789e97fb7078aeef50baf09e8cf0f896fe5530dc5e6dd89a70ce198b7738ce3e95f607c26f1af82f51f0d59e89a7dc4513c5180f6b7240677eacc7d4939fd2bf38fe10fc3897c296de25d0e4b702f6d3559d679a36c34a8c77c793f46fd6bd4f4df0e5cd9ba4b1c6d13e7208720afe35b29a5a1cee9dcfb8fc41f0c2c3520d358b7d9277e703ee1f4af3cd6bc33aae81204bab7df17389a319535c3f813e387893c2690dadd6ed4ec13e5d9372e07b1af7bf0bfc56f0ff8c23587cd5b7b870375b5c719cfa1aaba911ef419e57b4b01b1fa761d6a1bfd3edf518cc5750a5c44460ab8e6bd835ef871a7eabbe5b23f649c8c864fb8d5e75acf85754d04e2784cb18cfefa31907eb52e26aaa291e51aa7c24b07bcfb4d964a75368df74fb55132cba5930269296db78c63815ea114d9e833ebeb4dbeb4b7bd85a39a31267b11c8accd0f339357d41e301218a323af1d69914da8b0cbcc885bb28ae8efbc2c60937c4ad2c3d7677158d35ddb5aca51a275653cab0e94ac334ecc4d2e8d1f9d219191d9727b74a489b008f4a974ebc8ef6c6e638a32be5b86fa83ff00eaa89d4c65f8c52116ed1c331e6b401da3a56069b3979700f435bc58119cf518aa456c743a2c84e9c38fbaf5e19f1bed441f10e46db812c08c3dfad7b8f851bcfb6ba8db801b20fe75e5bf1eec13fb7b4e9ff89a12b9f718ff001a04795241b48c0e7bd4770854f4fd2b620d35d82b349853d2a77d1d5fac84fd050239958cb1cf415308f03ad74d1f87a029f33126a75f0f5b05039a00e3a51c7ae2aba1264fbbbabbcff846ed3072335345e1eb48c0291a83dcd2d46780f84644b7f89faf460fcb71102011dc67fc6bd16e151e094ee017636463ae56bb687c33a7a5ef9cb6d12cac30ce10026b4c68f6488710ab6de318a86aec2e792f855a34d12cd4239d88508da78393fe35d1daacbe641e5c0e419012715d82d9aa6162b68d231d00c0a909ba500476d1f5e32d51ca5a64be28bbbbb75d366d3e1133ab7cea31c0c0aa2baf6b8c1585a79783ce4835798eacec4ac36e571c12691135490fcc2203daad27606525d57c412ab6d8d4739e4d364b9f11330224451ec6b50437eac06e40bdf6d3f6dce482eac2aac41925f5e9b01ae9462924b0d5e5505ef7691d302b61a09c81f328cfa52adbc8bf7e40de98ed4c46336997cc417bd3f9534787a691b3f6b739ebcd74096e1c1cf35661823403834c0e5c7846391ff7924a7dc353ff00e10cb3059c093763232de9cff4aec639605520ae3ea2a45beb6528300f201e3a0271fd4fe545ae16b9f953fb56e83ff08e7c7ef15da2a148cc90ca84ff0010685093f9e6bc96be9fff008284e9f141f19f4bbe8936fdb74481e423f89d64914ffe3a16be60af7a96b04793515a4c28a28ad4ccfe81748d2a4b8b5b760372940491d860f35e63f1b628bfb57448c4c92bdb2bb08d4eef2dc851b8e3dabc3fe367ed17e3cd07e2278174cd3665d27c1baa3b5add35bafccb29076ee6fc47e75d169f777b70223772335c05c4ace724b64f53f9578337a1eca5a9b5a6c820d42ce700aec90649fcabd5e0856e427ab803f335e4c8ee11b241da370adcf891e3a7f05fc25d435c887997a2dc5bda443ac97127c88a07ae4d61145b303e0d6836fe3bd7fe22789f718ecef35c36b6db7eebac1122330ff81123f0af4f3e05b3c00657aabf06bc223c07f0ef46d05bfe3e2de0125c37779e4f9e46ff00be8e3f0aecde3c0cd696251ca1f02d96e1fbf90629f1f826d2370eb732023a11d47d2b69db73b007a53e3538aa481ebb9bde14f156a1e19d88d7b25f5a0e0c32f381ed5e93a4f8cf4ad75442eeb1bb7fcb2900c1af1bda49fa52147c614e0fad5dcc9d3ea8f56f107c35d3b55065b56fb24fc90f1fdd3f5af3ed77c25aa681869e1f3a11ff002da31907eb56f40f1a6a9a1ec4329b8b71f7a390e7f2af42d1bc73a6ebaab14d8b795fac52f434ac8cef28ee78d825c02a327d0d56d4b44b4d41489e24dc7a103915ed1aefc3ed3b58064b702da76191247f74fd6b80d63c17a9e8e4b49179f08e92c633f9d4346aaa27a1c2daf87db4817021cca92a8007a63ffd7597a8c657ccc9c60739e2bb30e54fa7b1a6dfe956da8c5e5cc830dd48eb53ca68ad73ca2d2f0dadde0b003249c9ae9ecaefcf0b8f994f7accf15fc2dbe910be98e274ddfea89c37e1573c296379696bf66be84c33c67183dc52d86ced3c18333dd459fbcbbbf9d52f89ba2d9ea0ba48b9b51248d2155909c6ce067f3fe957fc2a047a8b81fc519156fc7f007d36c251d63b81fc88ff000a047994de16b389cec8801d393d4531342b7527e402b76e41de723a7f85542464d00534d2adc758c1fad4eba55af19896a5c6e3c54d1c7f8d00449a4da30c794b53ae8f6aaa3f74067d2ac4516ecd4aa0f4eb8aab015468b67d7ca04d4c9a25930c18473d6a75523b54ea09a2c0525f0f586f07c80c073567fe11fd39ae3cdfb38518c6d15656a65048aa490155745b007fd4633db34dfec2b0038840cfbd5eda7d28c1a2c877338e89663a458a41e1fb2656f939ad2dbeb4a171d2901903c3369fdd341f0c599fe135b18c51d8d2118c3c3568bd011487c3f6e3ee96ad820f34cda7278a2c0637fc23b0b7193f8d2a7866de32598f1c1fd456c8524f4a795e99e78c628b0cfce9ff008297f87934bf14f81ef507fc7cd95cc47fed9bc67ff6a7eb5f1757e867fc14ff0042f3fc1fe06d676ffc7adfcf644ffd754561ff00a24fe55f9e78da00ee00cfe55ecd07781e5d65690514515d0607ead78b3c3561e31f0fdd693a82e609978917efc2c390ea7b303fcea8f872e6f7c3e6d742d6ee166be083ecd760e05da0e07fc080c67ea2ba253855ce0f39c11d6b17c4be1cb3f17696d6579e645b1fcc8678db1240ff00de46edf4af9cbdcf74eaacd19a421816ff0061b8e7dfd3ad729e16ba3f19fe2669d6b13193c27e079cdcdc3f54bdbec111a7b84c313f515c8cbf0e3c6fac5b9d32e3e235c0d25fe591e3b60b72e9fdd2df875af69f833a069be04d29f40d2edc5b59c67cce4e5a473f79d8f7270281367abdb5cb2c9b9f927a9f53deafadd2b2e0f15931cbf2a9f5ab066c818c7bf34ee48b71c3e547068865cb727a5432dcecc71d6a259c3f2074aa4c0d4deb9e0f5a5dc3d6b393510b91b09a71bc2dd171f5a77197bcc5f5a6b4e17a1e3b9aa4b7418e075a1a7dc3d290ac6e693e3cd4f4591445334b12ff00cb373906bd0341f8a3a66b1b61bc06ca63d4b8ca3578eb1519c9eb4c595509c15fc4d3e6b10e099eebabf83b4bd7d7cd8556295c644d11e0d70fac783b50d25f8437108ff968839fc6b9ad0fc597da44c3ecb70c23c8cc4e72a6bd434af1fc1732243771793238187eaa68d191671d8e12dd4c6e09cab0fe134ff14dbc674a8ae1630ae6419603b54be2ef8a9e09b2f10be95a979b6b7b90a2685372927d715a9e28d0264f0a19ade45bbb72a25595460edf7a45f377383d02e00d6621d01c8ae83c5f1eff000ecac4731c808ae574a574d4ad9cf18715d9f8993ccd06f948c054df9a965ad4f3cb8f9b9c727ad673ae18d5f91b2abeb8155872c78cd2021456cf02ac42fb4907ad22919f4a61916390ee38a3a81a117cfcf415327cd2153c3019ac69f548ad2dd4b48019182a73d4d55d53c5167a4e524ba58e4d9b8b37615682e752815fa73f854a42a632719af0bd73f69ff08f86ee6449751fb4aa2758f906bceb5cfdbd740d36464b2b37ba5c7c8d8aae562ba3ebadc10d4f1b647a57c1da97edfd7b2190a69c224c718aa371fb7feae228a286c46ec725aad458ae7e81e3e527b53148c9e6bf39f51fdbfbc5490b2c36b1fb1aab61fb77f8c924569a38991f8c7a7bd3e51731fa44c4019edeb4c3228ef5f00ea1fb6df89ac6d6c8cb245247236498cf55e3f9577de16fdb50ea322ac9691b051cf9ad827e952e22e63ec25395dddbd694735e3be00fda2348f18cbf663b60b95e763375fa7e55ea165ae4773b72000dd0fad4d8d133480cd2edcd2803a8e868a9000b8a5dbc8cd21c82294b1c5007ca9ff0524d3d6e7e005a4e4736daddbb83e998a65fe6c3f3afcbe07b7ebfe7db15fac1ff000500b2179fb30f88a6233f64b9b4981f4fdf2aff00ecd5f94046de0fa9fd0e3fa57a9867ee1e7623e20a28a2bb0e63f5b0636938f947438a648bb47435e2ba8fc3ef8c3a7eb1777161f106daea25942c76f736a009141ef8e95d64927c4c6936b43e1e5381ba5cb924e393815f387b88f41b790c678eb5bfe1ab865d7600a7fd62907f4af201a4f8fae1944fe25d32cf3d3ec963961f8b1ad2d2fc19e254bd82797e20ea292060018ada35033ea31cd0163e8cb6bb78e5603ef28254919191ea2b9a4f8dde218e79627b4d3a531b14cf9033815cfdc69ff117c3703cd6da869fe2a8530c6dae61fb3cccb82490ebdeb81d23569bc5b7b7f3db79f693c3398ae6d26c09217eb838ea39e0d023db2d7e32ea974712e93a7381eb0e2b6ed7e2307886fd02c893d7008af1cb3d0f506519b865fab574d63e1fd40c600b9c8ff7a9dc477f278ded18827c3d6c33d4aca453878c7493feb3441ff01b835c57fc23170df7e66cff00bd4abe179390663f89a40762be25d23cf0e6c1d10e7e5171d2adc7e27f0d6cc4b6b76873c6d981af3e7f0aec39f349cf5c934d4f0c23b61a5fd09a7715cf443e21f0bbe0335e443b61c1a0ea7e137e4dedeaff00c041ae2edbc27001feb09fc2b421f0b5b77627f0a4173a51a9f8608fddea3747d8c62ba0b9bb80e99f68b67f36168b11b11824d7116be1eb58b6820f5e6ba11b12d6ded909f2e343d681a661dbf862db53d5e29668966b891c12cfc9af63bbb4bf9bc2725b5a955b6da11b775c0f4ae33c356419da723ef0da86bd025bdf2b4c4b653f2ede7dcd3b83573807d2fc8d8fd769c6076addd46d1eff0049b88531ba4808e692e95645650318157ac977dba13fdd229ee079a0f0ede1891494660304138c1a8bfe119be5ce110e7d18574974a7cf75c138638ae7b5ad7ad74a1199e6f2d9f214138c9a915ccfd4b4bb9d361324db631838cb0e4d78ef8ffe33e97e19b56966b8890b7dc3bc7382377f315c77ed0bfb46dbe97148b6730996ddf6b047e9c735f086bbae5d78e7566b89a594d8c84b057638193ce2b48c5b139247d19e2dfdaa6e350d6b439ad245fb1db4f34bb50e738c601faf15e3de38f8ff00e2df16ead713cd72f0c13121625382171c8fe55c33dcd9e9a4c16d22ec5ecc39159979abc521699143900ae057546062e624f7d35c92599d9987cc4b1aa31dd496e820c33e496dc7b7b552975d8e2f942e481cd569b5b47dac0ed619e2b65121c8d7170f3a1562463ad22cdb18b16e474cd6447adbac64aa821baeea749ab472f5017819a3945cc6c1be32c403907e94ab3e0eec82074cd73a6f4296dad9cfbd40d7d2c6a3e6279e94f94398eb16ecbac618191917839e055bb4d52e5268dc165619ea718ae3a0d625230081eb9ab035499d94eee07a52710e63d3347f1beafa55e4534572d9cf0436315efdf0abf6c9f1078558596af0adf582f0aedcb0c9ed5f225adfc9804b723a0cd6e596b6193f78a73c608ac9c0d14cfd70f869f1d349f1fdb44d66e83700151986e3c735e9315c09465410075afc77f04fc41d57c15a845a8e9f78c0c6785c9c60d7de3fb3c7ed55a078ead534bd5a57b4d655304cd26d593d08ff003deb9e5168d548fa781c8fd2918e17229965a969b72a2512068dc654a3e47bd5b8eef476f95e72bf46ac6ecb3c4ff6b9d246bbfb37f8f6dd94b04b0fb4631ff3ce44909fc0293f857e3c8e79cf500fe6327f526bf6e7e3a5be8da87c19f1eda457ccd24ba1de2aa37f13792d803f1afc461f747f3f5af570ad38338310bde168a28aed390fd7ff0017581d2fc4ba8dbb2eddb3b151ec6b26e325770e49aeabe28418d7e2b840db268c0f9bd475ae589de83e95f387b88a8186e527ef0cf156e1706325bef2fcc07e354c9c12719ab36aa0abe4e49140cf6bd2e25bbb2b69f3f7e31cfe1d6bcd7c5be15b2f0f78caff00544912d65d55237941e03320233ff8f57a07812ecdcf872d8b72c8c508f402b98f8db06cb0d32fd62f33648626f6071fe1412cc4b59ed4aae7508c73d8e6ba4d3ef6c5000750435e576772555375a8cf5e6ba3d3b524c81f6503e82811e846f74eff009ff049feee4d2a5d69e339bb639f635cdc57795056d860f7e2a78ee1c9e215a00db6b9d3fbdc487e80d356f34ec9fde4a7f0acd7b8b80a36c69c7a0a8c6a174c71b1171ed4ae4b3686a7a7a632666fc0d588b5cb25202c133fe1580b79779e8833ed532cd759cb3a03db8a6074506b56d34aa8b6b20cf527b568e3cd648e3ce5cf4f4ac3d1a39096927c1c72315d67876cc4b21bb6e507083d7d68291d269b10b585100e001575e724609cd56b653b3d49a9b613daad2b8104cd81f5ad4d1c892d5327a122b3658f8fa7350dc788ed3c2fe1cb8bfbe7096b1b7cedfdd1eb45b5b207a1cbf8c35d83c3897d7174c238e32589271d057e7b7ed2ff00b51dbf8b921b5d16e248a5b591be681be65618c67dab4ff6d3fdacedbc4cdaaf86f4b95ed1e197f773c2dfeb94fafe5fad7c22bad137cd3a484b3e7767b9f535bc21aea8ca524755abf886ebc432196fa60af29dd202c7e63dcd50d73c482cecadaded36a90bb4e0d735737135ecbe60dcc49e31d29d0787f50bc60563624f4c8add72c4cb9652d8ad75ab999c024820e59b35524bd762c5090a4d7450fc3cd46793e742a4f622ae2fc3fb98ced61d7d055f3c514a84fb1c579b9c9e79f5a61621c67a575773e08bc80b7ee99813c1c5537f0a5c26e251bd3a53f68992e8cd1802e1946ded4d672c3d4d684fa34b0139071ee2aabdaec38cf3569a662d35a3220cca0638a952739c1e41a66dc139a708b3d78aad0561464392bc83532b91d734d11a8fe2a7955c0e49fa5022d44432f5c11cf35a967a9333aab20c0e38ac45651c063f8d4f03947041a4ca476ba75c8ba568b779600e6982eae34db98e4596489c310b2a36081fe7158b6375e5b7dfe5b19addf316fadc44403fedfa56325d0d148f6cf853fb50f88fc19796b67a85dcd7ba5e76ed66c951fe7f957da9f0ff00e20e93f10b468eff004ed43cc8db3b955b943df23ad7e579f36cc9466ef90c467a5763f0ebe25eb5e01d4e2b9d22f1e0c365e13f75d73c8c5734e97546ca7d0fd41f16f87c5ff83f5c8526795a6d3e74033d494200fcc8afc741803683951c035fad5f073e2e7877e2cf84e49a095ad6f628cc7756b29c156c751ec706bf25e685ade69226ce518a907b60e3fa574e155ae99cd59dda1b451457a0721fb65f16ace03a4da5cdbbb48f1c986cae300e2bcde25c02adc11c62bdabc77630ea3e16bd8a2911a4550cb8f6af188433aa97e59bb57ce1ee22938e481d69d1bed008a4bb478e56f97eb51db96dca1b81df340cf50f85d7866b5bb833c46e1f1ec73fe15a9f14f4f5bdf065cb727c96590015cbfc39ba36faef9391b678c81ee47ffaebd175cb617da2df5b1e43c4d81ea40e3fad0267cf36571079abfbb958633915d1e99736a25506194e6b9db3b8b749114cd82095231e86ba5d32e6cc303e78c8a08b9bf1496b818866c0ed5663b9b75e96d37e34cb6b9b4d8374c39ab2b73663fe5b0a02e235d4181fe8b21fa9c5352f60cffc793fe752bde592819973509beb353c4945845b4be848f96c893fed1a9219cbc800b3001ee4e6aa45a9da6ec09327dc1ad1b69e39e44319ce2802f47f3b2c29f79ce302bbbd32d56cad62b7c8dd8240f5ae5bc31682e3506b83cc71f03dcd6a0d6617f125bc2a5c90db723a64f514148e9eccf41f95593f2a9cf1deaa5ab00e72029048fad45aceb567a25bbdc6a17096b00192f230518fc6b58aba15c875fd66db46d3e4b9b870b1a8dc4e70703a9afcf1fdb4ff0069fd5b4bb9d43c296372a749d42d44d6f7b0364364f4cd5dfdaeff006c0b0f11697a8e83e11d43c8bd8252ad2a927cc0382aa7d3ae7f0afcf9d4f56d4bc4f7e96ecd35eb6f2b1c258b004f5c7e35bc61d4cdbbbb14753d4ef35dbe69659a4b895db193c935eb7f0c7f66cd57c56915eea79b5b3739f2f18622bd2be03fecdbf61823d735f8c35d38cc76cc3ee7f9e3f2afa72d34882ca08e388615464051803a572d7c572fbb03d4c360f9bdea88f12d13f67ed0f45b75845b0976f4de326b5e5f859a6db18d85aa478ce005af58915218cbec2c7d0d225a2dec7b9931ed5e6fb59b7ab3d98d0a715a23c7ef3e1c5bba6ff2c1519c60573f71e008e0391096ebc7a57d0074b8fca642bf4ac9bed0d0b00171eb4fda4bb9afb28f63e76bcf0b98da53f67181c81d7359cbe0f4b8ce203197e4e477af7cbdf0b2bb1c46bcf535522f0a852729bb1d38ab5524852a119743e76d4fe1ba5e13134448fef015c96b1f0b7ecc9279437e3ee9c74afad24f0a2b65bcbc7e158dabf83a178fee004e73c56f0aed3d4e4a9818c9688f87b56f0dcf68e41421867240acb6b79061483c74afaafc57f0e5274711a1466f41d6bc4fc4be04b8b2bc95046caa833bbae6bd1a75d4b43e7ebe1254de879eed2307b53c30c63bd59b8b5689c86e3b62a131ae467f4aed4d1e6c935b8c54ddcd48a594f1c8a62600c6315228e7da80b9622988239e95b3a5ea0506d2d58c8ab8ebcd3e293cb70474a86ba827a9d73c66e532a37567b892d64dc149c8c1c75028d3f5365650bcfb568dd379b1798982fdd4541674ff000ebe23ea7e09f11c3aa69b33be0a89610701d707208f5af3ed7e75bbd7f539d142472dcc8e8a3b296240ad5b56fb28f3106dcb0cfeb5857a49bc9b3dd89ada8ab36655190d14515d4607f40d2e91a54b6f3463f8d0ae3af24715f37deda1b2bfb8849c086464fa735f428bcbb2dc5d439ea320578cfc43b1fb0789aefe60c25c4bb9470735f3d63dbb9c95fe49ca9c83d6abe32339e9535cb82839aae8c08233c1a451bde14befb1eb961296c2890027d01af72893ccdc9d54f5fa1fff005d7cef6b21866475e88c0feb5f4069f289eca0995b3b901cd2259e3377e1fb1b2d62f2265c149997a7f9f5ad5b1d2b4e0172a09a4f88467d3fc5f726278d1274494071dce7359d65a8deb907ce840f602826c7676ba6d8103082ad7f64d8e7fd58ac8d3f50bac0ccf17e42b4bedd7391fbf887e0281139d1ec580c4429a744b1c8fdd8a4fb75c8c7fa445f90a6c97b79805678cfe02802d45a559c6095840f7a86489214f906dc9c0c5431df5db6448ea57a702b63c3fa7b5f6a092302d1479c8ec4f6fe5401d1e9f145a769b1ae006237138ef50d9fd9a2bb8a6d837990316c7f9f5aadae6a12addf9304a91aa8f9bbf355adaf6f14e3cf42091d8508a477174d1db4f3091bcb5424ee271818cd7c09fb6cfed3b0deea12785345b8dcf082df302526078ea3d39af6ff00dad3f687b3f87de0e9a3b5bc82e2f6fa336f22ab7cf1646370afcdcf0b78435af885afc37f2fda1f603133ca33919e0d6c9a8abb0517276479f43e17d53c437f1470c6c2ee662085ef93c9fd6be9cf82ff0000ac3c1308d4afd127bc2036e9467675cd75de00f84567e14d93ca04f77d72c3a57a641668f1946018608e3deb8aa621b5647b187c2c57bd2dc86c645b98d5e3c18fa2e07a569a0000cd25a58476916d51b5453b6873c1e95e7ea7ac90c11865c15eb52aa2a285c62a48e13918e6a478b9e451728af27cab90326aa32ef04b0e6adc9d3e9509c804e335771dcaab6eb9395eb522d9a672054a0820678a9b6ed5fad05ad8a4f600a02578acfbcd211d030e0735d07f0007bd4132ee5db8f96b4296879e6b1a1c6e33f7bf0af2ff0018783e2944edb4ee3d368af7cbeb4531b8039ed5c7eb5a479c8c59702ae2da7a18548292d4f8fbc5be0636ef21540dd48e2bcd2eacdad5c8752a338afafbc57e19c34abe5821fa1c578cf8b7c1e0a4bb63e53e6040ebeb5e9d1ad7d19f378ac2f2eb13c7db0180ef4a65098ad3d4b4a68a7c01dab35ad883822bd14d33c5945adc7adcad59b7c487e6e055078d93a2e6a68ee005033823ad3251af1ec47500f1ea2b5ace46fb98273deb9f82e7041eb5b76d3962840c1a9e52932c188ef084e39c915897b9fb54808e86b704b89496e0fbd62ea2775e39ec6b4a7a333a857a28a2ba4c0fded1a5c4cdf304c77c3570ff0012748f2fec53280772b2139f4c63f9d7a18d2a0c36d1cfb3573fe3cd250787249870617562739c0e9fe15f3e7b4789cf0fc8d9ed59c5483c0ae82731ca581c291c62b1e64d92103a54b29096fcee0c3b71f5af6ef055c0bcf0eda36795ca37d4578a40b8704f00d7a9fc32ba2d61776dff003ca4de07b1effa521331fe31e922e6e74dbada771468f23d78ae2ad34b4c280ac71fed115ea5f14ac45df86d643b95a19558303d01ebfd2bcc2dac7cc0ade6cd81fed5023a0d3f4b0bfc0c3fe059ad55d2c123e571f8d63e9b61b9f99a618c746adc8ec1fb5ccb412c46d241c70ff81a43a5071b50b83f5a7fd8e7dfc5c4a07b8ab567a649f6a491ee1da31ebebfe41a0763ca3e3efc7bd2ff0067bd0f476b989aeae2fa6f901eea3ef1fd4543f08ff6eaf04f8ad63b09664b2bc9431c336067b578cfed5f7b73f18fe29699e14d0f4e8751b7d22268a7965e556790ae003ea029fcebdbfe0f7ec43e05f02e8164f7da6477dacb44b25c4f37cc55cf503d315aa51b6a4bbf43d2acb5ed335c6f3e1be595e439e241f85731f1abe25e9ff09bc1177aadccc45c142208cb60b1f51fa5725f1e0e97f0cbfb3ecb40b75fed095f88a33d00c75fceb80f13e8aff136c2c53c40c18c1f38818ee5cf1c7e82a6cafa1ac6326785f86bc19adfc79f101f13788b745a6799ba28c93f38ed915ef9a569fa57876d16288c36aa831838cd70be3393c57a2dabdbe9291476883e548976803d05788788bc67ae440bde2c91cb9c724e335328396876d371a6b53ebeb0bfb29dc7953ac8ddf9ad159532bb0839cf435f15f873e2ddce9f74afe631c70d86af54f0d7c708eec42246db9700926b9e587b6a7647131d8fa23cddcb8ce6ac431a6c272335c1e9be3ab6befde23ae0f1f956a278a909c820a9e98ae771b1db0a89e874e1ca38cf02ad02197239ae6edf5c5b85240cfa7357ed7565908046dc562d5f53a522d4eb90702ab630a73c0ab924c84823a1eb59b793e2450bd0d34ae3b14ee67742428c8cd4ab7c5b009e452cb1a94f7aa8e02b67b55d8b5b1a6272ca39a43206ead8aa293718a6b3962314c65b95832e381e9ef5937d6eb2232b903d2af00c319a8278bce27da9ec4b387d774717518c1db835e67e21f0ea4b14910077e31922bdd6f2c55222c7bfad71fa9e98976cc63009ab4f5b98ce0a48f987c4fe1330bbc9e5e38c0e2bce751d1e582403612c73c7a57d31e2af0e4b234838e0f4af36f11682149648ba75635e852abd19e057c3eed23c6ae20921243a95c7ad42600c09aec756d1d9f7165098efeb5cd5d2793215ec2bd152ba3c5a907165788151c738ad6b19c92a33c8ac947c138e735a9638619ee302aae6691b16f12dd49bc1ddb41040f7ff00f5563dfa79570531d0673eb5a363218ee65553c639fd6a96aac1ae13fdc19aba7f11153629d14515d2739fd09ff63e9f27dd9187fdb515475ff0dda5d685a8c4921677818282e0f38c8fe556a59e60178b46fc051149333a8682d9d73cede38c7ff5ebe7cf68f9889c4df31e40c1fa8a86e6304935b1e29d3db4bf10dfdb95da04cc40ec01e6b35932bcd4b2cab12ef61ed5defc3972bad3441be5963c7d48ae1d61209c5745e12b8367add9481b037853f8d2158f49f15d9ff68e837d6c0904c64ae3d457905ae95722355fb4639c8e2bd5b5bd427372b1449940c51c7b1af3617096975342f15ce5242b953408d2b2d16e8ed26ef18e985c56edbe997ab8cdca91db22b2f4fd421247ef2ed07fb4335d4dade5bb46317130cfaa504329b69d7ae08f357eb8ae27e2e78e66f027844416605d6bda916b3b0857a99483f363db9af489278cc6c12e9c92390c31ef5e35f0fecd7e2bfc50d53c5d2fef34ad119b4fd2d0f2ad20e24907e38a57296ba09f033e115af843c6de20b0d414df5d42969ab48cdcb9b8746de3e9d2bde75cd5adb4dd2eeaf85c2992de32e613c1ddfe7f9578afc2ef1736b9f18fe29c974c636b39ade012838e155a8f8b3f11229f1126d93cbe0b8e3703ebf9544a5d8e8852e6679cea03ced66f7c43acbfda35098b18d5ce446a7a015c4eb3e2245bb32432edc8e707bd6678c3c672cccebbbef64ed07a570a97b36a536c4c9279cd5474d4f46314958f468fc5f9cc571f38f5eb5cef8a343d3bc4969221895c9e5481ce6a1b2d227284b725b1c9a9869f710be55f1b7b0ef5b29f414a95cf983e21781b52f0c5cc935b8736ecc49c0fbbd2b8eb6f13dc59b44bbd976b024e6bebbd5215bf592defa20d1b71d2bc17e217c254b2b892eec73e44992a807231ff00ebae88c9356679d568ca2ee8afe16f8cb79a65d04699bca63ce4d7b67847e28c1aa98d598063f7483d6be439eca6b394a3865db9e48e95b5e1ef11dd68d708fe66e503a75a9a94632574452af284b53eefd2f5b62cbd3919c835d2d86a01f3cf2306be77f869f1105ec4904b2ef2e9901ba8c57ace81af0ba21430c6302bc99c1c743e9a8d55348f4ab7d4048b8dd53bb0254f5ae52d6f981001ad58b500ff00296f9bd2b2b58e9349e5dd9154ae237703153c443739fa54db4714c7729062806e18156a35dc320d39a2122950b935623b7300e475a0642c1b2b9a648a108f7abac9bc0e3a554ba424af6c6680b14aed56508a4f19e6b196ca38988f5cd6bce366493c562dcca524c9fba7bd02b1cc6bfa624923e1739ce2bccfc41a4ab4520da40ce38af59d49c3375e4e715c3ebd661cca992075c8f5ad22ecee73d48dd58f13d5749ff496c1ca0c820d719ad690b13111296e724d7b16ada7a03b40278ebef5c5eb3a69539db8c66bd2a550f0b1146fb1e532c26166cf4cf3ed524577f67922607e52466b5f5fd3dd6266d98507ad73a53e53ec2bbd34cf1a717167487315d31077061907d73506aec0dcae0630a29f1309b4fb79b3f30f94ff004aaf7d932827b8ad61a48e7a9b15e8a28ae939cfdf96d15307047e0f4d4d336e086208eeaf5a5fd9168c0e181fa3542746815b8ce7d9ebe7cf691e47f15f4c7b5f11c5301f2cf086ce7b8ebfd2b8d2a48181d2bd73e2e68aa9a0e9f74a0968a73116ce7e56c63f91af2646f907f9ee6a59642720d5cd35d9183afdf8c8603f1aace09ab36190ecbd3777a407b15aa89e5b795515d658d5cb1f5af2ff00185a5cd8f8aeefca7d8921de011d8d7a4f846e0dcf87ad1f39280a13f4ae43e26594f06b769344cb89230a49f51dbf5a09662d94d7a401b94e3ae4574f617377b173b1ab9cb23745d7708c8f7ae92ce59962dad1a01d8ad049c57c76f1bcfe0af85fad6a4bb56e9e3fb2db6dff009e92703fad55f803ade89a2f83343f0e4529b5d520804b3090ff00ae91b25db3f95719fb543cfa80f06e851f315dea3e7385e842608ffd08d55b1d2e2d3a5176fcdc632841c151d31fa5635256d0f4b0f454d6a5cb8b53e0af19f8df5286e379d72e44981db6ae3ff6635e53e2cf114f7f3c91a64904e4935dc6ad34b74ecf23ee635c46b5a69f2de441f377ace376cf415151479adf595e5e5c039c1271d6ba6f0f786c69e12595c798411b722b83f1ecbab5844cf6448c1ce4d79fd97c4cd7ed65115cbee3938273c577c69b91c93a8a0eccfa88d8911c6216dc3bf359d7b1dc5b30daa59b39fa5788e8df1c6f34e18ba50533d41aebb47f8e7a66a8544bfbb7e99cf5a7ec65b94b1107a5ce92fef03bb0994839e715cfdfca8fc72d08ce41abda9eb963aadab4b14e0fa1cfad7306f8aab24e7f759c0229f2389129296c715e33f87be76ebdb58fcd494fcebfdd15e53a978766d3e59176ed4072a7dabe97d1ef2349a4b77c496f28dbf37415caf8d3c0cb652caa881ada65ca1f43ed4d54699c72a2a479e7810cd6d7119f30961c71dabe8ff07c82dec91893bb8e4d794f847c17225e2ed8f280e78af65d374b305b045e08c715c95a499e86160e2ce961d40039435a56376ed3ab37ddee6b99456b751b8f5ad18afd6241f30cd719ecdcecedafb9033902b4e2bb4279615e7cbad00464fe46ad45aeaf386e7eb40cef92e514efc8dbeb5652f12404122bcf23d7f3b50963cf635a31eba0ba857fce9027d0edc48bb78c565decf86c9e055387570c17e6045492cc97084e47141a58a97b202b86358572594904600e99ab9777856419200279aa1a8cc1a5f97a11d7d695c664ea328099ea47a5733aa0de0b608cd7457adb54f3863591740483e6e7dea93319a38dd4acc246589e6b96bfb3f3f77cbb8d77d7e14c8cb81ec4d73f7565b59c9ebdeba612b1e7548dcf2cf12e9ae6de41e5ed5f535e7b7b0792580191ea2bd975ed3bcdb771203b093c0e86bccb58d3f648f1c60a281c835e95295cf0f134fa9069ff003584083032c49a87500c1d377618aab6371f659943e4203d4d5ad4240f37038c64576d3d59e454d11568a28aea39cfe88bfb26c18731853ec48a89f43b363d1971e8c79aa11ea59c85bf207fb4b4ff00b64a0e57508dbd8ad7cf5cf6919fe36f0ea5cf81f585863679628c4eaa4e7ee91fd09af9e1b0c46de548c83f5afa8ec6e24be9c5acf710c904ea6175f6607fae2be63bdb63637f776cc36f9333c7b7d30c78a434cac47045496cdb5d73c73513939e2885b120cd228f51f87375bb4eb881bfe591dd8f4073fe152fc43b27bab0b59e3037c6f839f7ac8f87d7422d425889e258ff003c7ffaebb0f13c626d12e0f20a61b81412cf3ab3b4bc519f2c1c56ac3248b1012a857ce062a869dac24d8412b3b372032e2b0be2578d22f06f86aeeeddf170c85624ee49ef4369234a74dd5928a3cd3e34f89edb53f1ae816366a2e2e74b769e675e420e339a85affed918953fd5b0c81595e11d005a693717f7c3cdd475342cecfcb053d3f9d50178741bbf21831b763b558f418fff005d70c9f33b9f4b0a2a9ab2356e60690a91ef5997b6c1a278f182456b2dd24a37c6432fb53248c4a181e09eb42934ee5b4791f8c2cd446c92c7fbbee71d6bcb75ff000c5a5f4a6445f2d47dddbeb5f446bfa2a5edbbc6c01f7af20f11784ef2cae9da162d1fa0aeea5559e7d7a3cdb1e25adf86aeed63981412124e197b8ae24adce9872e8d1e49c135ee6667b799e19e227b924556bdd334cd5d1564894f5c1c57a30aa8f1e78792774796685e30bdb3946e9375b93f3027f2af45d23c4716ae0224c4330e53d3deb0754f86f1bb6eb338eb8cd6741a05e680ea183798b93b979cd54e51919c54e0ec7a4c170e641c9c021411df15e98ba70d66cade274050a8f998570fe10d30cfa7db5f5eab23f51111f9135e93a22dc5d38758d9cf40147ca3e95e5d4691ea52a7293b9ade13f03c30b004a824f0315d35cf863c8076a673e958cfa85ee8e239a58caaa9e6ba7d37c5f05fc4a0ed200e4d7237cc7a70f77738fd42d4c68e00c63ad731a85e88199436081923dabb2f144ab1c72c89d082c31e95e4fafea9b3cd70c325302928b6cd1ce295d17e7d7d631b790dd7ad574f132c0ea5a4c67ae4f4af30d7fc492007390437041ed5cb5ef8ae412160e4671f2e6bb2142e8f3e789e567d1307891197724a0d6a69fabe158bb65b82066be75d0fc781e458e4936281ce4f5aec749f1fc534c115c320c0ce7a544a834cba78a8bdcf74b0d5f6a025f19eb935d0daea45a2e1b20d78fd8f88639c29571b4fbd761a678810c61430f94573ca0e27a30ad192d0eaee94ce323939cd51bac67af4a7dbea225404630475aab3b80796e4f6acac6bcc8a37077bb7391552540c98ad17452a4e6b3dd776ea6b4225a98d776e4c84e38ac9bfb5121c1e878e2ba09e32b9cf7acd9a3c38623201aa52b1cf289c55fd8ac224dd938e31e95e77e2bd3b0acca720679af5cbfb22ecfc646735e7de2ab3314122633824d76d093b9e66260b94f267b7da0863f38e714f926f3950e3040c1abd71009ae1b6d5192311e3ea6bd9a7b9f3559590ca28a2bace33fa09492c9b3b98ff00c092868ec98712a63fda15a8447fc50381ea529af1da15e547e295f3d63d8b9991c76b1c8ae8c9be33bd4e71c8e715e31f142c1b4df1c6a2ac46272b70847421c67f9e6bdc5ed6cdce03273edd2bcb7e3769ca97da4dfc4a0a4d0185a453904a76fc8d2291e6add334b1e09140c327d40229f12e597d8d22ce8fc2f70d6faada3eedabbb6927debd5ee63fb4dacf1750ca403f8578fd91f2515ff895c1fd6bd92cdc3c713820a3a86cfd450268f25d2e39202e97006e5628001c8e6bc63c52aff10fe214d14cedfd93a71c6c1d091ffeaaf6df184ffd8afaa4edf2f93b881ee7a5791e8d1269da1dd5e3f135dbb3927a9ff39ae7aaefa1ec6061cb79329ea7784cec89855500281d97b5656a314777666391416ea0d4b2cc18649fa55492e06719ae73d7bdcc096eeeb41995f6f996c7a835b5a7ead05fa131c9f37753da9f388678446e1486f5ac4bcf0fb412096d1ca9073c74a09b1ab720c8c4563ddd9accec02023be6adc17d287fdf264e319ab30889c16c81f53549b416385d57c216b78ee0c4a1987040ae5aebe1ac71caceb9c63803b57aecc9086392b8f5cd54bcbfb58a201577b74c8ab551a319d34cf2c8bc0ad1db33b741c82d58d7ed6b6020296cb7374ec6340173d715de6bd7175713fd9a36326ffbb1a8aed7c0bf0aa05beb7d56f9332c4a0a211c03dff90ad1d77639de1eef633be1f7c2a9ef2c20b9d497a9cf938c6057a743a058e9602c68b1a2ff00081d2b7f7c76c842001715cb6bda970db5b1cd72f33933d5a7494236297886d6d2e6ddd4c609edbba578a58ea86d3c5b3698d91b8fcb83d6bd1f5ad5f75ab2efc9c16383d00af20b2bb07c45a97880e196d50c5073c339ce47e82b682d4c71168a3d0352d6acadd7ecef8790af2a7d2b8bd55f4fbacefb34f978183d4573b3ea17123bdcdc3169255c83e958d73ab2f984f984b28fbb9af4214efa9e33935a0cf13782ad35189e683745bba8ec2b82d67e1f5ca42ad146254c1c91d6bb4fede9f0bce63e722b434dd652e1d86dda02e327a735bd9c4e5947991e037da2dd59ceca4323af453c552b7bdb8b790aab952a7279afa36fb42d3759844770aaee7f8d0722bcf7c4df0b1e021acc19739e075615a29a7b9c92a6d6c61681f1125b59555d8aaf4f5af5bf0f78eade48d599f7a14e7b57806a3a04d612baf94e8c9c90474ab5a26bb2da9556c91d0544e9c65a8e9d69537a9f5ef877c4115e5b46623907b13c8ae8480e037ad7ccbe1cf1add594d6ec8c5f0d83183eb5ee3e17f1c43a84be4c8557e5e093d4f7af3a74b9763dba18852dcea663e5273deaac8c027b9ab12b09620d9ce4702a94b9070462b919e8dee5399b793918c74aa52c4cdc2f7abcc373629522e4e45213661df5bb244e48ed5e4be35b9843b076f50315eade2093c982570fc0ed5e23e31be1728c807cc49e6bbf0eb53cbc5cd451c9f11e48e493c0aada947e5f95918dc3754d6e9e50f9ceef9bf4a6eb0e19a1c765c57b34f73e62b3b99f45145759c47f4771ea1130c09a27f6ce2a5120947dc8c8f661589e64dfc56d0b81df18a9166c039b453fee9af9db9ec1aaf1c6d80d681d73ce154d79dfc71d162bcf032cf6f6ed11b0b8121f971846e0febb7f3aeb4cd08eb04aa7d8d6478aad9b59f0c6a963019b7cb092aaddcafcdfd29ee523e6986338ce393dbd2a5452ac29f0fccd96186239fd69eeb8348772fdb0df1b639e38fad7a87866f7cfd1ad59b9c2907f0af2ab0760029e2bbaf0f5dac5a449bdc24709decc4e00148b8ea79cfed0fac7d95adedadc7cf77b4b81f5e3fad705e2fba4b4b5b4b541b76c43207af19aea7c5ba85af8c7c6af711fef6d202155872a76f7af2ff19eaff69d5e6607e404803d05724efcc7d1524941245196efa00718aaed764673ce7a565b5e97391c8a4372dc67bd667522d4d7e63619279a747ae01c331ac997733e49e2a8de48b0839e7e948a474edaadbbafcc707deaadc4a19720f07a735c45cdf32163b880067ad75be0bd09f53b64b8b96600f2a09ea281dae28512bfcc4ed3efd6b46db409af18468b88cf39ae8ecb46b52c1400d835d2dbc36f68ca142e71cd43b8d45187e1df04dbdab79d3a8794e3ef0e98aec1a44b58b68c000718aa4da885c0200c566ea9aa7ee58e718071458d9248355d7bc9438231cd70fab6beb3e771e7b556d435276120663819c57137ba9319ce1b8078cd5240e56449e2ad7c5b6917f2a725632a06792490001f9d726d6d169b616ba5cca59ece3134a14f2d23f383f4c52dd4adac6bda769ac331977bc9bfeb9463249f6ce2b03c33adb6adf10835d822cef23791867bff0008ff003eb5dd4a17d4f1317579a562e6b504cf204d8eaac32b81d07a572f7ba7b4738ddb8647535e813cc246cb93855c1efdcf15cb78a2ea4b6589e34dc9ce7dabba2eda1c2f5312d61d8addd49ef5af636eb8c0c73d735876baec138f94fcaa4ee22ba0b2bc82708b1b03bb91572d762569b9b16da6ab2a34676b1fd6b596c59106f196f5c74a4d363695460609c57436abb6371247b94e30d5c77b3d4e9e4525a1c2ebfe0ab3d59245917e6238751d3eb5e49e2af86f3e8970b246375bb1182a3a63d7f3afa46eec1117cd46047a0aca9522bc768e54408781bc55aabd0e59d13e66d3e39ad255f3036ef30e3d85769a3ea2e91bb47232caa72066bb7f13fc388ef1249ed155241d7df3d31fad79f3584da65c4d1947668f00e455b7cc8ca317167b7f833c50352b682391b1215da01ee6ba8790488a7bf7af0bf08ea0f6fa9596e6c6650a003d335ee8885f6e395dbc1f7e735e7d585a47b9427ccacc86388b127f2a9654105bbb93f3638156218c8006de6a0d4a33e436d35925a9b4b6b9e63e2cd49c2c913ab2924e00af1dd72669657c125549073dabd3fc7cb2f9d27246d209c5794dcb34f3cc0e73bb906bd4a2b951f3d8b936ec521109a2ebc835535050b228073c56bc76de5c78c139acbd5176cabf8d7a54f73c5a8f429514515d4729fd182dade28c8746ff0078629c63be03ee44c3f2ad63a3129fbbb96e3f1a6ff65de2ae05c06ff796be70f60c57fb401f3da2b7ba9a64371b67557b6930e761ee003c1fe75b0d637c339643f8557922bc8c37eed588e78c53b85cf9775ad30e99ad5fdb1047973ba007b0078fe7551a3ce303a5773f1674dfb0f8d6e1b6943711a4c47b9ce6b8e2b8a0a23881475e38a678e67b8ff841ee6d6d2430bde1588cc0e360ee6ac22e4f356ffb22cfc51a3dce89a86638ae9485997ac6d8e0d24ec69057763ccc7862dbe1ef871a6875b6d51654380718438e707df3fa57906a57c6e19983165393935d7f8deda2f03e863458e67b836a590c84e77f3d6b83f09b36b5a80848dc0a925476ae79bbbb9f414972c47dbc12bc6081ef53085c75ae81f4cfb33ec0b8038a92db4e0c08c73ef5cedea7545dce4ee23949fba71599a92651b824d7a15ce88648ba608acdb8f0da3ae58f5a0d4f2ab98e49ee95426632c377d2a7d4fe30c3a13a59c2c15233b07b7ad7657de1c8ad6391f7107071c57ccdf13f4a91355b95c950d9da4776ad20b9b466736e3b1f5a68be2343a05b6a01b22719dc4ff9f5a9a2f162bb64ca327a0cd7841f18cb6de00f0dc4f26192d00723fbd9ff00eb5161e3277d811839c7393d2ae54adb131ad6dcfa1ffb783c60ee049f7acfbdd5b746406ce6bcb2d7c5929450582eef435af16aef2a82edc0ea7358b833a5548b34753bb223660460e735c65ddc07ce48c824d6cddce5d0ae7af4ae5b5e3f63b5763ee48fc3ff00af550dec6751e853d02f5750d5fc4d74060db409650b0f59325bf95496fa0dbd84a93c47f7a0fca47618c62abfc3e8d07867589c7ccd2ea08c4f7c60e3fad7450dae48c8cf3d2bb22f94f2650e677657b8b46fb2a3052a48f9b35cedf28594c7709946076e6bd0e2b6596dca118207715caf8974a76b61b17798c921bfcfd2b48ceec72a5a5cf38d53405d3ee44b1c67cb6ea3b73d6aa5e79fa2c5f6adfb630485c5758275ba8cc5228e0633ef5caf8e609db41b8480ee21f257be3d6bb60ee7955af13a7f037c4059658127655c9c649af6ab096df52b35d85493d307ad7c436d74f6ed1b45210c0e7ad7ae7c36f8992d94c905c4849e8067ad1528a6ae830f88b3b48f71ba516072f848c9c64739ae7b55213321398dba62b4edf558756b61b9f96e48cf4ac4bc864485e0719f9f2a7d6b8942ccf59d9a24d375858d44728f323e884755f5a935df0dc1e22b19258904336df9980fbf8ae662bdccec06005247d0d74fe1ed44a6e490178c8eddab5b591c525a9e457761359c93007c89e065914776c30ffebd7bf7842e46a1a3239c9910ed3fceb99f13f85535ab413dbc7be724ab15ecbea6babf869a6496fa53bc8a5776d46cfaae7fc4572d4773af0eeccdfb78038076e0f7aa97f6ae623b573eb9ade8edd44876a903d69b7d12184f181deb92f63ba6f43e7ef8951482eca2ae4498e47b7ffaebcbae2c4452b4884b67d3bd7b27c440915dc81c7ddcf3e99af2f81164832ca5510905b1eb5ea527747cee23de663c8d9456395c673581ab90d2ae0e7aff004ae83572b6e9b4f1b8fcb5cc5ea1590673cfad7a149dd9e3d52bd14515d8729fd1f457d29e9750b8edb971530bdb8e302071ece4550592d547cf0ba9efb874a942d83afcb20527d722be70f60be3519171beddff00e00f9a3edeacff00eae55faa8359c6ce3eb1ce7fe02d51b41749831cec79f5cd0079f7c74b41711697a946198b6617257183c103f9d7931505411ebfa57bafc45d3ee350f056a2b31f30db159e338e98383fa1af0d441b7f1fca81a1a89d69d7337d92ca69780557218f634edb8ce2b2fc5d706d34099ba6462a24ec74d18de67ce7f123529751d4245c161b89622ae7c2cf0e1d396e3519872508507deaed9e8ffdafac7ce9b958e4d74f78f6f616be5a1090c7c123a57233dd5a2b19cd189257278563926a487ecf1b613923a935cfcbad1bab831c39d80f2c2ad5bce4606723d6a1ab1b459b734a8f8518a8dad46ce463eb55507ef1496ab72392a067349336b987ace9c66b47da40af11f187c3d6bdbb5b99b8da4919e99afa06f0208d43363d6b8bf194f135b044038cf3f956d07a99cf547cebe23b478f4b4b63ff002c005c76eb591a0cff006562d36100f435bbe27b2ba9c5c1c12377e95e6daafda212e23631923f3aee86bb9e6557cacf4d8fc5b6291aaa60b83d58d6f689aefda597cb759149190a7a0af9c26fb4c51179262cdce066ba6f0078ae4d3ee22825761bcf24f7ad274d5ae8c69576a5a9f4de9d18ba88c858649c007d2b97f8967ecba4315237b305535abe1ad4d6ead5046db9b1923dab9af8b57e174db4833f3cb2eefc063fc6bce8a7cc7b329271b8ff008612a8d2f50b1382f2c8b2293df19ff1aed6cac9d0a961924f26bcebc1321b79e2718008c57a7c376aae88f850c0735bc9e86105a9a515aa95ce3b76acbbdd396556e322ba4b62047f2f2b8ea3bd453da992027eb8ac632b33770d0f15f12685369f72d3c0bba2ce48ae7ee5e2bb826dc83cc2b8da7bd7b15e69427cc32a1c37ad705e24f073db4ae61527392315e8d299e5d6a374cf15d7bc33e54de6c2a067a81542cec6512a90db2553c62bd06eac644768e5420e79c8accbcd2d903c91a8e3b8aeee6ba3c4952699dffc32d63cf912c272ad7057804f35d8f8adce9b64b391f346d8c0af9eecf51baf0feab0dfc2ec25461c57ae6a3e3c8fc4da4202abe705f9c7bd64d6b73b694e56b3312d2e52798ed6c163b8e3d6bb3d061dc50e491b81e7be335c16896cf2dc072472c70057a87832dc3862ebc46a4f358cf735bdcea3c29e5b6b77f632af22cd656cf41b89c7f2aed744b086d2ccc5191f3484e3f015c768a8b2df6ada846376e8e38411e8377f8d7a17c31d053c6bff00091c82e1e3361e56d44ee589cfe82b9651b9a427cac416e096f418c552d4a358eddbdeb624b7fdf3c68795665e7b8048aced7e0fb3d936e3f37f2ae271d6c7a3395e373c17e23289b53541cf9a0a1e3be38af3cd32df669ffe91fba661919ef82466bd43c6e0cef8046e8dd64071e87aff003af32f12ce82068e3e1a462c31db279af429ae547855b56ce3bc42fe7ddb464e581054fad606ab0cd1989a689a2de372861d47ad7aafc29f851a87c5af10ad8dac4e2d62702e2e80e231cff81ad6fdae7c0107c35f12f85b4684676e8c8eee7abb798ea4ff00e3b5df425ef58f22b2d2e783514515e89c87f493b6e54e5edb23d3ad218e1272f6db4f71b7356c9f28002f6703d1d03548975bb83751bffbc9b6be70f5398cd686c4ff000ec3ee3151c96168e859262b8feeb56e88d5fa089f3ee2a37b08e404b5b06c1c65680e6399bad17ed36b7301999e29a268ca939ea323f957ce2f0b4523c6d9dc8c5483ea0d7bcf89fc5969a56bb6ba3e9104d7dac4920f3122c95897bee3d077fc8d79378cb4b6d2fc517f03a08cf99bf68f7e68348bb9ce950320f06b98f1fc864d1d6107ef373fa57617312a8dd9e00cd715e29c5cac401cae4ff4ace67a1875adce25238f47b166c0f31fbd79af8fbc582d20169193bddb2c41aeb3c6daf7d9e468a3e401dabc9752d12e756b97ba72595ba035ce99ebdc9ed7c636d0148565f9f233cfad77da76b164110b4819c8e99ef5e11e2ef04dde976336a16e5bcc046d5f535d0f876498e8b6b73396599d06ec9ef57c97438dcf6ab6bf8a41bd9d413db3d29f2ea30ef03cd5cfa66bc817559e7de23948dbef59316b97f05e3334ac547a9acdc1a3a51ea5e22d7ca860a781d08ae3a6bd6bd73b892b483554bfb68ccae32734f861886769f7a714d1525a1cdf88b4d478cf9607cc0e78eb5e63ad681e5ee66405864e315ec3a8c61db6d735aae9f13bf032e41c57446563cfa90523e7dd5ac244965dd19dbe98e94ba4d82652423057a57ad6a3e1e4b990ee551c60f1583ff0848591821239e80575fb4d3538fd8da574751e02d6a2b0826695fe611f033f5ac1f11de4be24d6d6572de544b8553eff00feaadad1bc24b020624e48e73deaeff6198a650630148eddeb924d5ee7724ed6647a0a18618c8420822bbf8a133db2b704e064572f6b68f1215dbb5474adcd36f1d5407e3b54395cea8c6c8ddd3b516b595564ff0057d3e95bf0cf1ca32a41535831c51dcc1b78563d2a4b677b2213ef564cb366e2d2376dc0648ac8d474f12cab818041abd1ea8b236c6c29fe7534928287001f73daaa32712649356679aeb7e161713b11b783ce4572f3f846542fc02b9e00af5fb9b617208da01f6ef54a5d31141cae735d1ed99c12a11be87895df839a6b90c46c00e791c52c5e1692da232a0e1ce095f6af5b7d26125b7267bd54bdb046b1766555451900f154ab3662e9289c3e97a5342d185e07de27e9ff00ebae8ad75b92e3525d234c625d800f263819ea2b98b9be5b9956db4f2cafbbe663d31e82bd0fe1d68b0e98f1dc4d17fa431dedbba8ad5c95b532e46cf49d27418f46d20c0fc990649f7c5763fb3d39d1fc55af5928564d4707e6ff00641c7ea4d73b2dcbdcc41d57803a558f867a83e9bf1074d0c768925d84fd6b2e6d742dd3b2d4e95f4e6b7bfb80573895b91f527fad729e3abf16b03823e6eb8f5af4ad78269b797c5b00accc483d85788f8beee4bf6ba9d89548df81ea2b3715cd72dcd721e71e2cba3045248cb8597ef03d85725e05f865ac7c51f112db595bb2e9fe60f32e9c7caa33d3f1e7f2af40b4f07defc4cf1445a7584334964857ed7222e405ee3ebd6bee8f869a4f86bc0de1db6d3adb4e8e189100225870cc7dcd6fcfa1e45495d9c67c2ef02c5f0b74d6b5d3ed2d82b01b9828dc4f724d7c67ff00050fd4df51f8bfa27991794c9a2c7c7afefe6ff0afd2a7d53c3d70bb0c31267b2e56bf35bfe0a32f647e396949624989741833f36704cd39ff000ae9c36b3b9c1597ba7cb1451457ae701fd23289186629fea294fdad7afef33ed9a44b9b265cb068fd723156225b374675ba30aa8cb396c003f1af9c3d66549249d383029fd2b85d7fc69a86b1a93e8be19321bb5c2cfa82b663b7f503d4f5fcaad5d6a5ab7c41bd9b4dd0a66b3d151b64da937de93d553fcf715d4e8fe188bc31602d2c624110192d8c331f527be68118fe16d3adfc256e62b6690dc48774f73200cf23f7627f135c6fc5cb1fb4eb767a826196e6108c40fe35ebfa115e973444383f67c71dab9cf1ed9adef876397cad8f69264fd1bffd5414b73c3fc42cb69a35c31387236afd6bcfb52948b0879cb639aedbe22b88ed22851b0cff00363d79af3ed52610d83166e7a0cd633763d7c3c4f30d6accdd6a6ecc7200e41a4b7b78d8f94147b0c54fa84fb5ddb80c4d374a903bb3b6320715cbe67aaa26778974e896c82ccbf272715e6faf5a4f2e91b6cdb618faad7ad788a137b66703214735e5a13c8d525866e9d429ae883378c7991e6361e30953534b57243b49b5b9f4af40b59ace6e588323e405cd70ff00137c1cda3eab16b56680c191955edeb543ed533cb1cd6f211b46700f5cd6cd5c76b1d1eb1a8dc69933c16f92b9ce6934ef184e932a4a0e31824d64ddeaecf6d9910f98dde916d05dc0b2a0f9d874153ca88949ec7a1473fdac23a0ddb875a73e9cd237cc41cf4f6ae6f43bf7b050b21200fef76aeced2fedee615903a907bd4dacccdab9873e98c240a1377bd4b0e9eaae4edea3d2b7e25491c900114f58133c8c54b6f621233ad6c01500ae4ff2aa979a6ed9812b802ba78e248f04557bb404138193506e8e74db06538a8bca31906b4e44f2813eb54662cc46de94ac3b962daf9a338cf22b4aceefcd7258d6229c4b9eb56a1ba553918c1a7604cd6b901cfcb8c1efde990df4b66df78ba8fe135143741d80a95c29639028b0da08b5642482369cd5a1768f8c90477ac696db6bb30efd2aa4293198e7257deab730699aba9dfc76f1b11c8c741deb97bd375aeca6303642c31b47515d09804a841000fe74e82de3b72c5623d319a366438df732f4cf095b69823da80b8e77574ba6444dcf4c718e3bd16d66d23a93950477ae874ad30c643119c9a973772a30491bba4c58451d480320d59bbd39b47d6744d5917114778824c76048c568e8fa51728dd41c53fe225d43a6784ee0bb88da2292827d430ad693be8ccab349173e245c993c47a85be59518e78ec700915e4bab19eff505b1b046b99ee0ec8e3c673d39af46f18eb70ea9aa5ddcdbb8b84223da579dce50715d4fc34f83d710cb16bf753343724ee8d71f76b7946ccf1a7376b12fc1ef87ba97c36b0679151ae6e72f282a4727b7eb5e88daede051e658a1c0c1239ad696e75c40a18dbdc28fe271cd559aff0050001974d89c7aaf153638db32e7d760db9934f6f7c0afcebfdb97508750f8e6de4a145874bb68c823a1f9db1ff8f7eb5fa3736ad6e9febb4d953ae4af23a1afcd2fdb475086fbf686f102c0195218ad530ddbf708dffb3576e17e339ab3f76c787514515eb9c27f4a2face976d66f717571145022ee679463ff00d75c4c3653fc55b912edfecef0ba487e5076cb7647424765ff001ae5740d1af7c7d7a9a8ea73b1d2626db6f639e188eacdfa57a644d2dba2c71c4a63418000c63f2af9c3d477366d7c3567616e915a466d634180917dd148da54c092974d9f46aa716a92c64028e83d8e7357135d880f99883fed0a62d485f4fbf1cacc9281d8d666bfa75c5e681a8412438dd0970c3d57a56d49a9c12a8c2a39f6254d2c72c72c6e863601d4afdecf5a341ab9f18fc40be371ae18d7fd5c2a141fc39fd735e6be2cd4c345e529e54f205777f11b363e28d5e2624086675e7b62bc87539deee4720f5fe55c751dd9f43875a231ef65dc7ae493597a8ebb1698c14483a734ed48bc05ceece2bcfbc457acde6972411d2b38ab9dd276573bbd3bc68978d2c248da57b9ae63c4ec82f3ed2a09217b579849e209eca62d1b631ea6b760f141beb655964c332edf9aba230b32615adb9bd1ea89e26d325d38a001c1f9dbf848af31fb3c9e19d724d3ef4373ca39e8c3dab49f53baf0edf171968b702cbed5bfaa8b1f1ae8bb1dd5261f34370bd50fa1f6e056eb43a39d3571b15b5a4f68caa577100f34c82c4438556db9af3fb9d4b56f0add35b5e464a260f9a832ac3d6ba3d2fc590ddc6ac65538c1a5608c93dcdf962d9be2978602b36cb549f4b9157395248193c0ad8328bf83ce5c3123a8ae6bc5508b7d3d6712046dd81cf7ff00228488ab68aba3d0748d5cac7872371c723bd6ff00da86c1ea6bcbfc27ae7db6185091951d7d6bb58af0b8f9c903a0c56538d99cf09267410dc994002899b2064e2b2edee9554f2723d6a4130d8096eb591d17167dbd09eb596cf8320ec2ae4d282060f7ac7b9b808ec33cb138aab10d88d72cac71d2a4f34ef50a7e5acf57dee771c0ed5296dbca7205162548d349d9724355eb7baf94966ac08ae834639e7d2adc12ef18079a5635e6e86bb4a24c61b1f4a8fcf0ac3270338e7d6a9216ddd0f7c37a1ad0b6b17b8d8a30ec7934c65b817701dcd5db6b63336d0b9a9b4dd1a5542cebd4d753a4683b93207cc71c52d44cada7e9c0b01b73802badd2b4959020dbd2ade93e1ec105c6335d7697a34502ee2b9a8688e6338ac5a5d83371b857cfbfb4c78bee2dbc0b76b16e334ceb1285f73ffeaaf6df16df3dcdcbc36ca4c680723a1af1af89ba726a96a2de64caee0c0fa104106b6a6d44e5ab1753447b47c1ff0085d7ab0e8dad5f5bf9cb25a4323c2e7a3041d6be878f58b58e3447d3e48d71d17a0af917c3ff00b45f8874ab2b2b25b759fc8458813c6e038afa07e127c54b4f89113dbcc9f62d5230730b1fbc3d47e5fad6fce99e5d5c354846eceeff00b57497386df1e7d4535a7d364e22bc1ff02a9e6d2d980f9239063a8c7359d71a32b0e6dff11423cf1d35947281b6e22907719afca2fdb1323f690f19838f965b75c0ff0066da253fa823f0afd47b9d1635185122331c6573eb5f945fb4fcbe6fc7ff001b1c962b7db371ef855aeec22f7d9cd5b63cbe8a28af54e33fa1bf0cc9602344563080000a06315d5456c98063b8041f7ae7fc2135aceaaade4b31ea1c62bb15d22de41910a8c7428d5f387ad748845a5c0c10e920f7a8a4b790e7300fc2afa694003b24923fad02c6e909d93071fed8a0399192d1438c346c86b2b5dd4ad740d2ae75196e0a470296c13d5bb0aea5d6ed41dd0a483d457ccffb45fc4137776742b3dd12c7c4d8e9938ff03594e5ca74d187b476478e7c4bd6df54d4ee6f33f3deb994fb035e7ec9860b8e077ab9e2cd6313c1167e644da4564c37e246539e2b91c9bd4f729c79743235cb52dbb03debcbbc591b1dc554e3a135ed5791472a9c8cd709e23d084a65545c2f5fad38b3aa71ba3c32fad4c8cfc74a82398c322a67a735d4eb9a2c96970ef1a90a07cc08eb5cbdd580994fcc5589fbc2bba124cf3649c59a82e23d4e0c49b4b8e083deb06596f3c3f712b4049b77c064ea2a321ed9c00c73fed56b45729776c62942b67804568d6a3552c8b363e218b538d639b64e02e0abaf18aa379e15b4998cda6968598e7cbed9f6aa8fe1f78e6cc2e57be056e68915d5b9463b94ab0cae3ad348d635ac6df83b56bbd274ad5ec25d2d27b9ba8512099c73132b751f507f4ae4fe23d9ea134c1c46121249d9fed6066bd7a2d5631a62148c7da01ea56b90d688f9a4ba1bda4270a68d8752a73a3ce3c1778d6714715cfc8e09e57d6bd2ecf51568142e4e3924d79f3db4715e654e01626ba7d2d9988507e56181594f539a2dc59d84172261c1e31522cec5c2ff000fad52d36121b0780055846f3262074535ccd6a77c6775a96646ebe9591752e19978f9bbd695c44481f2eefe9542e6cd9806dbf4c5522272d4cf46e0ae7047af7a62bbb90e1b2a3238abd0e94ee54bfcc5880a2ba9d03c1e2e9954c242eee4e38ad544cb9ac735a758cf76a1d00604fa56eda69339382801f6af47d1fc0320d8822c2027a0aec34cf84ff69c3a023ea3ad69ecee2f6d63c7edfc313160d8254d75ba4f856575409192c6bd8f48f852d1a80e9f98ae8a3f87c2dca1550bb7dbad274ec355ee78f8f0eb47e5c7b09c75c5761e1ef0d282a48c71ce457751785122f999013ee2ae0b04b65f954640ed59f28fdadce7d3490808dbf77a551be94889a18ce19b8c8ed5adaaead15adb4b2020150493e83bd78ceabf1ab4986f66b4b43e65c0fba339e69591a46f23d06e61b1d2ed375dbc683f89dcf5cd79eeab0f86bc4172e9e6f9c549c60e07bd792f8bbc63acf8a6f0acd33c70024ed0719f4a8acae53c31a44972ff00eba4e13273cf7fe951269687a7470edea6f6bfe3cf0cf84af3c8b5b28a79d09524e588af75fd9d20b2f89cf67e23d2e06b1b985dadeea1230197b30fc8d7c602c24d53512df7aeee1f6a71dcd7e82fecf9f0e23f03f802ce0875216d7f2af992e3fbc7b1fd6953d599666951a367bb3d22e7c257b0bee43347c7f0366b3dadf56b5047da2518e9b9335a725c789ed18082fe0ba8c0fe2a6b78bbc47683336951dc28ee8d5d27c5ee614bab6b10f05a390020e0a73d6bf23ff686b97bcf8e1e379640049fda932301d8ae011f9822bf6107c40b77900bdd16588ee19f973fcabf1afe35ea716b3f18bc737d002b0dc6b7792203fdd33363f4af4308b56ce3afa238ca28a2bd3390fdfed28cba75e9c3e03364022bd06c2f2578558a03ee0e2b8c9edc5bdd08e7521a37643ec41fff0055763a4a41242823976b63a578135667ab1b33522d5194807cc5356d756c01ba407fde1550594a5c10eae3b034b2ab468ed2a031282c48ec0543d0ae54f631be2278dedbc21e1ab9bd660252856300f535f106b5a8cdacea735dcf233bcae5c96ed9af46f8dfe3d1e25d71ed2de422d2dd880a0f07a7f857953de283960304f02bceab3e6763dcc35154e373cb7c6d7b25b789668dfe51b4119f4355acb515c2658673d299f182411eb16d74a30ad1ed66f71ffebae320d7fcb2b8edd69a8dd1bf3f2c8f4e9355568fe5c1c5675e5ca4f1923258f4c573567aef98c3270a7bd6c5ac9f68da076a949a3ba3352461eb5a61bc89be5c76e4579beb7a3b5add1408471b87bd7b5cb6cad807be6b95d63488ca0271e628c64d6d17633a9052573c46fa3c4cfc135159dc013ac654807a9f4aecf5af0e86909dc148ce07ad72b3691245212b96607b575c6499e6ca0d33a9b36844499203fbd6b5bc285977100f5e2b8b865963c3f270318ad8b4d4a4f2c6d90120726af98948ea6e3514b640839fe95cfea4df6d70436eeb8aaeb70cf312e721a9cee634c819c52b94cc2bab555b85439c9cf4ed5b5a3c3b0819395f5a87ca2ec242304d741a3690d20329076e38c0a87a92b7352d95b9c8e4568db59eeda4a104f5ab7a7787e7925f30a945639e7d2bacb3d0c2ae5c640e86a394d53b1cc0b10070b91df8a9134a138f997083a1aeaa5d3d032e1768c1edd6ae68be189ae515d948049c0aa51072b987a378416ee653e5e547009e3aff00faabd4fc33e0e8d1507944e3039fe757bc37e1a5b6281d38aeeec6d16d5d0a2802ba22ac60d8fd17c2b182a42018c6722bb3b1d320b550bb5401ed59b6f7be5a2e1714e9354676007154dd8cb73a02f0a11c2e0557b9ba8c03c0ac196f98ae4b702ab3df12a76b64e33d6b3723450b972ff5348d0f418ae7aff55326591800bd706b9cf1478bedb4d4769ee563dbd89ae4a1f1b69da93f9897ca5573900e2b16d1d74a8ca4c87e296bf25a785b597864daff00676119f563dabe43f0d1974fd4a3bd725a766cb9639cfa7f335ee9f14fc709ac5b3e9b01f9093961dfa57929b10f2ed45e98e4562dea7d053a0a11d4ea2c261aa4e8cca37e40207e3543c5b2196ffecc87f71177f527ff00d55d0f84b4a1a6e953dfcfd08f9437734cf087c3dd53e206a7982174b72e4bdc30e1077ac9ddb3d1a4e14e3cf37648dbfd9c3c1316bbe35b6d46fe079ec6d1f736d19e7b7f2afb8bed5e1eb8dabe68849e30cbb703b572bf0c3e1de95e07f0e456767246d2150cee40058fbd7592787c4c01f2629063ad74538d95cf86cd318b1356f1d88a4d22c9ce6d6ff6fbacb5149a4eb11aeeb7bf6957fba48351dd786e251c40c84f75cd545d0ae2005adef2785bd3b1ad8f16e2dd5e6b56818c90a4b85271b39240afc60f13ea3fdb1e24d5aff208b9bc9a504770d2311fa115fb21acde6b7a76917d70f78b224303c8778e4000927f2cd7e2e966624bfdf38ce3d70335e8e116e7257d428a28af44e43fa27f15471c3ad99623bade7c4aadd8e7ad6e684f673c6a36c4c47079c115ccdf9fb6e811c81be7b67209f5538c7f234dd12462570ca4f5e6bc3aa7a508f63d0869f0825959e3f70d915e55f1dbe208f0ae8ada5dadc6ebbb8520faa2f1fcf3fa5759af789e3f0de8b3df5c3858e35c8c1ea47415f177c46f1e4de25d6aeafee656dd21f9149e82bcfad51c51e9616839cb99987a85e192566762ee4925bd6b1ef2ff00c919183dce6a99d73870c72bef591abea31c8a0c4dc11f31cd71a5767bcdd958e73e20ce355b39c1009519422bc6e6bffb3cac85beee33f5af42f116a8d1c8d83b97b8cd798f8824f32669130bbba8f5aeea68f3aabb6c6ee91aeaab0463cd777a2ea48989370f604d789d85f081f693823f88f7ae8ec35d65db973c74c1aa9c3a974aad8f646bb12aefc804f4159d3fcec770ea6b95b5d758c218bee51d2b5a1d711e319c6eac2ccee552e1a8e94973f3aa8f97dab9fbed0c2e19540619c8c75ae84ea6a43287c16e82a8dd5da48cbf3671914d268ce4d5ce3aff004cf9b030b8ed5949a7496cbf313f78d75f7702cb2641acf92ccceec80f707a56b16ce7925733608f1d5703a64d6d5ae86f78ca00624a92001d6b5741f0d36a1302d1e707a62bd6f44f042c56a1dd76ee18daa39c56c918b6794d8f844481085c86e381e95ddf87fc2056154284023d2bbcd1fc296d00d889c039e95d7e99a12b468553807ae2b78c518393b9c4d9784bc931170781d08abe3406909558cfb0af4cb5d00b484b2020018e2b5ed3c350e06e4018d69ecc7cecf28d3bc132cf2832445557d6baed2bc2062180bc71818aefad74c82dfe521463d7bd171756f6edb7e518e98a5c960e76cc8834111aa8c73538815303238a8ae35a064f6e79acd86f99ae581ced6350dd8773624b85890f3cd08fbc02783d6aa0883c9dc83535f5cc5636b248c7855e9eb50ddc22b5b14f55be4b581de57d9181cb66bc73c65f18e2b3692d6c5f248dbe603deb2fe2af8db53d48bc36e8f05aa820e78cd783df5c4b0aca630d2dc64b04ce4b564d5f447a30872c7999daf88daebc5c774f3481bd01c559d2bc02d6da2dcbf9aeac1772924fbd56f8746eb5d8cbdfdb1b053864673f7c1fff00556f78af52be5812d6d0f96a32acc3a1e9584a125b9dd879c672f74e1d7c27a95e956085c9e3807a574da4f836dac36497a7328e912f249adaf02f87bc5de2b992d2c2dddd49da652b8502be95f873fb3ed8e8af15e6b4ff006dbd519031f2a9effd28516ceaaf8b8515ab3cabc0ff00062ebc6f7d136adff12ed1d0031c2460b67ffd42be93f0e7c3ed3bc37a6c765a745008117000c6e39eb9adc3e168644022740a0001718db50ffc22376a73113ec51aad42c7cae271f3aeed7d0a571e14454c9b73e80ad541a135b8cc72cd0ffc08915a4da3eb96d8d92cc14750791486f75887e59123917fdb5c1ad4f30cf09a9403315d071e9252ff006aea89c496d0ccbedc1ab126b72c676cfa6ee1ea951aeb5a748ffbd866808ee471408e57e2678861b6f873e2a9a5d35a068b4aba73203c0c44d935f8b2adb941e84f38f4afd93fda2357d3ed3e05f8fa78ee94b8d16e9141eb968ca81f89602bf1ad1b70cfd3f903fd6bd4c2af75b38abbd50ea28a2bbce63f7b3c19f16bc21e2fb2b9874bf11e9f78b345bc22cca189ff0074d51f889f15342f843e0ebdf116af70b0c76cb9890b7fad7c70a3d6bf2bbe13fc3e1e28f145a5b2ea8ba74d26762c77063925208c2023d6bebaf1be83a1789bc3f6fa2f8c04d26956d1b451a4d29dd1cdb3e5c1ea4e40fd2bc69c6ecf4232d0f0183f6e3f166abe2cbf7d7676b9f0d6a53e059b1c3dbae7e523f3aecaf3c6369afc42ead6e56585c7c8ead9c8ff00eb579c7c09f823078e3c11e3eb3f10e99f628fcc31d87882e7836ecac76e3d46319fc2bc96e6ef54f843e30d47c3775325e3d8ce51deddb31c8a402acbf50735855c373aba3bb0f8af672e567d206ff2492c5b8e003d6b36eb590b0c8ad9507a83581e15f16d8f896d239ad6552e06244cf2a6af6a48acaddc1f4ae074dc1ea7b2aaaa9aa38ef10ea2d1c92302594fdd15c75cdf79a37e71ea0d75fabd99f309c165c1ae56f3487ce40201ce47a574c1a473d4bb312e6ecbb290318cd245aa345221ddc03cd3e7b26889c8240acabb8664c9113106ba6e99c6ef16761a66bbbfe5693e53d2b56db559095c3115e6b6f7d24122af2847506b66cb5593077b6076c543a6ba171aad1df49ab49be320f20d69dbde2dcab60e48ae16c6e867e52ccc7a83dab6f4cbf68db18e18d64e1636556e741891d86d1d2baaf0df86daecc6ecbf78f7158da4bc52c8a5c85c75c8aedb4cf124361122aaa9dbdc534ac539dcf40f0ff856d6ca3599c2fddada9ae618ca2a100631815c08f19c9751a2478da33fad6fd94cd2c0923e01c0ab4c87a9dbe8964247576185f5aed2ce6b4b4b6c1655c1e6bca65f13c963100a428f5ae7b53f17df5ea3ac729c83c60d6c999d8f789bc61a65b1c060587bd509fe24dba12a9b58f6e7a57cf9f6ebdd4254837379a41c126ba1d2ec24b78633282d23360b66b4e7158f4e7f1d35d962a7009c6476a8e2d4e5b89c65c9fa9ac18f4c3040108c1639e2b560b731aa11d477a97312d19a7f33100b73572dad48604f7aa16b19970e4e07a9aedbc2fa0c9aa48a147ee8e32f8ae6949b6697b6e3745d167d46608aa421c65abba5f065825ba24f12cac3aee19adad334e8349b711a28c8ead8eb4971202c4e69a3195477d0f1cf8e3f0917c4fe10b84d123486f61cbaa85c6f1dc7e95f03789b4ad63c37ac4570b672c779692e5a1957e56f515fa9ef3053cd723e2ef03787bc456f24fa869b048fb8167db826a9593b9d10c55a3cb23e48b2d1750f1fe93a78d3d56ddda3f31e441ff8efe1cd7bf7c22fd9de1bcd252f7c443cc723e58cf7c773f5af61f09fc29d03c2f6ca34fb248832e471ea2ba1d2c048360006d62bc7b55c9a68e68d6941b71326dbc3b61e13d3e7874cb58e148d7700ab5059f8aa57b746b9b0494b71b90f35b9ae2910dc80482d0902bcdf4b3a8dac604770b20eb8619ac0c672737cd26766daee9927facb79ed4f7607229f1dfd9c99fb36afb0f60f5cc3eaf791ff00aeb249147f70d3a3d574c9ca89ed6484fa94e2a8c4eb9353d72203c8bab5b841eb8c9a7ffc25573167edba379b8ead180735ca791a6dc362daf8c4ddbe622a65b0d56119b6d47cdc7404e45161a3a23e28f0e5c90b756935a16eecbc0a46b0f0cea0a441a8a73d8915cb5cde6b900c4b6d15c67a92b8159d36ab68abfe95a4947ee621c5160b1e69fb77f87ed7c37fb2ff008cf51b4ba496465b7842a91c87b88c1fd2bf2288da480001938c7b1c7f4afd30fdbdb5bb04fd9e6ea1b79268fed3a95ac6d1313c80c5cfe5b2bf33f24a82c4eeef9ec7bd7ab855ee9c15fe2b0514515d8739e8b6dab5de9d7f677d6770f6d7d6920961947f0b0afad7e1cf8f741fda27c25359f88f5192dfc79a5afdbdcb308e2b98a323217d4e2be3b6732646d39c706961967b19a39ade668260369923386da782bf43fd2b8651b9d29e87d6bfb457ed3be133f0cb5df03f8555e2bc658e13751002229c1720fad7cb9aa7c39f17a78521f174fa35e4fa25cb84fb6b82589c00091d71c71c5741f037c0fe1ef1f78f6db48f116aff00d9637a49691b81e5dcce0938727b600fcebea1f8cbf1fbc3ff00072eefa195ff00b5b5c5b6482db4e5556b3b54fbb9083863dea35455cf86348d66eb42bd179613b433467ae783f515eb5e18f8cf6daa88adb575169747833a7dc73fe7f9d49f13bf678d6adbc3d6de37f0f79bac691aa859cda98bcbb98e491b92231fc3922bc32f20934fb99aceea1682785da392194152ac3ae476a89d35346d4eb4a9bd0fa6750513db24a18491372b22f208ac79ad378c83915e656f73e34f84f6d6575a869f77069375cc2b76374720c03f29edc30af47f0bf8fb40f15a05f356c6ece0182438049feed79d5294a1aa3d7a55e33f8b72bcda609010462a8dde8323b00806001d6bd06e34208aad8f948dc083c11552e34b4032a7b561ced1d5c89ea7985ef87f0e4ba807b60563dde9d2c0018c90d9c8af559ec23553bc726b0351d2a2756c13b89e2b68cdb39e54ae70f6ba83dbbe6463bb3d6ba3b7d76dc98d039c9e86ab6a1a107000f980e845615c6932447f76d871ce4f6adee9ad4c791a3d1acb5bc280b2f1ea3b56e699acef037b92bd89af1fb3d5aeecee1461402086c77ae9f4ed78c8143b04c75150f7d0b8be8cf64d12ff006cd1ed39426bd0a398bc5946c038af10f0ceb68b26d4f9db1d335e9ba0ea41ed914be598d2343a392337988cb124f4abba1e820c643a9dc5b0334cd2537cd1be462bd1b43d25658a2c8553b89e48e95b27a12cc2d3bc210c372b3104b7a0ad6feca8c82154b6c20d75f06976911669ae1100e7ad675cf8abc3ba3970f22c8e3d0f5a5269072b7b12da69725c88888b838e4d4b75696ba6499b9913703c460f26b99bcf89d73aaca2db49b53b4f1bb15db7c37f861a878b35482e757dcc83e60b5cdcf7762f97955e447e18f0ecde27bf59595a0b156e846335edda6595b69d6b1c30a0554181eb49ac68b0e869690dac2521c1e42f4e94eb58da5438cb56a958e39d4bbb215df0339aa72be7a55c96dd97ef022a9489b2a8e72a499279aa1ac02da65d00327cb247d722b464e7a553be52d6b3ae3928d8a9651dbe8d379da7593e721a2539fc2ab69bf289c63e612b714cf0bcbe678734f61ce2251fa9a9acf097772bd3f784fe945807eac3cc5191c321e7f0af3bd3aea68216592d03e1c8057ae335e9578b98a23db6115c0d96a50c52cc92c4e8048402050c4c72ded931fdf2cb0b1f6e29c20b3bb6c25da11d83d6843258dc92a26419ece29b368104c788e3917d50e0d17332a3f85565036c51ca0f746e6a07f0b4f067cb6b8833d36938abc3c3c63198e49a0f4f9aa6826d5ec81f26f7cd03b483345c7731cc1ad5a0db15e7983d24e6abcd79ab42a4dc59c13a0ebb0735d29f115f46035d69b05c8ee5460d035fd2a7004f633daee3cede40a2e3be87c43ff051bf105b9f84de1fd3c5935adc5c6b2b29cff12a43203fac89f98afcf023d7af435f777fc152759b3b8bef8776363333a471df4d2c6dc1c930053ff8eb57c219ce09f4c57b3875682679d55de62d14515d26276664e0114c7948038cd48ed93803a53194b76ae536b90c837ca928251d08657538653ea0d757f0bbc7da7782fc7b16ade25d113c5ba7b42619a0ba3bdbd55973dfafe95cb3c471d3150980e1b1839f6e47e35361dcfbafe0d789f55f8a5e20bbf19da78974fb7f0e5c593db7f623b059f4d48c82a421fc7f2af3cd73e19683fb48fc5c975fb5b43a4f8474c4f2efb5371b3fb4a556e36fbe777e75f29595c5e6917ad75677535a4c4152d1395dc3b82075af7dfd9b3e33e896ba0dcf80fc7fa84d61a02cbf6cb1bf4eab293f323e3920fcbf91a9e5d4b4f43ddfc5b1699e38b6bb1adc4b1781743440b14ea032b46186d56ef918ce3d057c77f1c62f02dfde683aff008155acadefa274bcb056e60914e011f5193f857adfed8ff135a38f42f09f852eade5f06ddd88b94bbb47c9b83bb90de8463bfad7caf32ac48060228c0c0eb83fe7f5a4a3dcae6ec763e12f8bbad78644504ee751d3e3e0c537de03dabd83c3fe3bd23c5f185b3b8f26e4e09b790e1b9f4af08d4be1febd69e10d3fc57269b21d06f7212f23f995581c6d6f43c56046ed011240ed1baf2b246d823f1ac6a508cb5474d2c4ce0f53ea2d46de50d85f98ff00b55cfdd41730b1df191e98af36f0d7c61d6748f26daff1a9db2f1f3fdf1f435ea3a078db47f145befb69c473670d6f31c153ed5c32a2e3b1ead3af0a862cb2b27de438fa5526788b1c8183eb5df49671cea43c5d3fba3ad573a0db3152c8303d4560e7cba33a796e713f63b695d4a8527bd30e970895b00a9aeda4f085ac877463631ee074a6b783222c713396ee4f4a154b87b2ea73767a7cb6ff00bc89d973deba5d1352d46d76ac6eedb7d3bd09e119c2308ee4e07406af5a687a95a282b3a9fad3732d53b9a906b7adb483cb91c608206715d5daeb9e2c665781a5c10063938ac4d2b55d4b4f452d1c52e3a9295dde8fe3cd496352b65115f555153ed0bf669176c344f17ebb6ca669658d48ebd3ad74fa47c2858a547d4ae4b360643354da3f8875dd57620884511efd2bbef0b7866e757d42dd26632166048ce78159b9b93b12ed1573a9f871f0f2c9a44682d97ca4c65c8eb5ee5a0dac7a5dcdb24602e1b1f2fa564e99696ba15aadac4aaac00ce3bd695b4fb654909c60823f3aea845247915aa39b3aab3d37edf7e6170ac0120ab8aaba8f829967f3217f20a93c0e86b56c65fb3eb48ffc2e3767ea2ba49809060e01cf20d6f638dbd4f2ad4ac6e6d3779b6e5d40fbea335ceb98ae18f96e0b775ee2bd9eff004f8de3631b6d6c73e86bcebc47a2db492190406de7cfdf8c633525ad8e4a784c7d8e2aa5c7ccb81ee3f4ad3bab3ba863fdd4ab327fb5d6b0ee35136ae05ddb3c2b9fbe06452b0ee755e02067f0d5b03c18cb211f43571013aa5daed239047d2a87c3a7597469c231389db00d748f6cab781b1f33a531dcad74a5ade023fbc4572cba6442494491900b9e715d86a11f95a7c2de9274ae5a1d76f46a3750a241344aff0075fa8a960f6187c316d72a3ca99413d770a8bfe10eba89bf7129f721bad6c2eb503fcb75a59c0fe384d4f16a5a4499115ecf6ae3f8655e0523330ffb3f59b4e8cec3d19770a68d52fa16c4d6a92fb2ae0d7616f717fb57ecd7d6974a7a06201a5b8d6aeedd4adee82b3a8ff9690e0e69d80e3db5cb427135a4d11e990381407d3ae5c225daac8dc0593dc803f9d6fbeafe18be056e639f4f3ff4d14e334c6f0a68daa45bad6fe091872b9201e39fe94ed60b5cfcb9ff00829bdc463e2df85ace278dc47a1ace761cf324f2ff004415f1dd7d15ff000500b9cfed49e28d395c490e9515b5944ca720a8895cff00e3d2357ceb5edd25cb048f366ef261451456c41dd8b7118c939a43e5a75aa773784ae299e7823ae38ae4352ccf74870157a54066e09c62aabb658734e39500f5f6a00473e664e381556e2dfcc001e8791df9041a998921bb67b5182107a8aab0ca2d03aa88cbb796b9d8a4e42027381e9cd74df08b47f0f6b9f12b45b4f146a074cd204c1de765cab30230a7d01ac578cb2824735565b33223295183eb4985cfd06bfb3d134d8f537d46c068de10d3239162d3db06d35157194753d01c86ff00beabe24f01fc309be33789f5b5d026b5d1f4eb5dd7256e9f01232c4003f11fca8ff85b3e28ff00841eefc2b7b7efa968932a2ada5c9e63dac082add7a6457d2ff08352f865ad0b7f0af8274e861d575fb60752fed29361451feb111bd46323eb59977b9e2ba67c00161f087c63af78a251a55fda5c84d3dc9e2464ce71ea1b2b8fa5788dbccf949e3731ca392e87906bed4f8b5a54df147e2f786be14690930f0fe82a971a8b3fddf2d4e4076efc679ac3d67e127c3ff8bbe25f16f853c17a4be93ab68c8d38d5637cdbb301f70fd769c7e34b47b949db63c03c35f16f57d1e448af0fdbecd700e78703dabd7bc3fe34d17c52aa2cee825c1c66de53861fe35f3d2786b59b84b8921d32eaee2b695a0965b784ba875383c8aa0aed03b30df0cb136d27254ab0ae6a9878d4d8eca58a9c1ea7d7c96850a9c81f5a94c449c600fc2bc36c7c6fe35f87fa7d94de20d2ee27d2ae82b43713ae77a9e98615eb3e13f1fe89e30850d9dc797718c1b790e083ed5e5d4a33a6cf728e2a9d45aee6c1464e3a935326e002e39a42c1dc81d7a8e2a58902b825813dc7a573391df1b334f4bb612b00c06d3d6bbcd0f4bb7f282f978e9f8d70f6122c6477aecf4dd66308aa0e180e07ad67cc36ae7a56891dbdb42a140ce2bd63e165ee9fa3dc4b7d7caf280b88d17b9eff00d2bc3bc3b7725e3a26d2598850077cd7d6de0ef01da59e856424b66924640c495e86b6a51bbb9e662a5caac713a8f8a27bbf1134f6d0cbe433602609e2bb6b5964014382a719c115d258b68da7dd46adf668dc90a0119e7353f88b486fedd999970aca0a81d3a57a2ae78cddcd18a5e2d25f555ae88dcb31cfad73489b2cecfd40da7dab73398d08e411d6b426d72c198b0209ae63c456c5f63211c13915bdbf19acad506e53458bb58e32f615452369dc0e6b12f141dc182b023a62ba4d4130e6b02f13824516026f8789e4adfc44ff00cbc1603d88aea2e98adedafa142335ccf82180d57518b3cfcac07b735d35f83e759b76dccb9f7e2a4066a8e7fb2cfaacaa73e839af28d52d5dbc537c5249226254fca78ef5eb97b1799a55e0c72bb5bf535e57adc375178b2e0c126c0c8ac4119cf152c7d0b36d7baad9a811dcac98eceb569bc4732edfb5e9c92e7ab20a8a19efa200c90c73ae074e0d4e97f679cdc5b4d03773d45246635759d2277fdeac9667d4022b5ece42e57ec3ad1e7eea96cfe754a3b5d32ff3e5dd464ff76415149e06f3099230bcf781f14ee06c5c4dab8fbf0db6a03d180ac5bc9ed659825d68f25abb328dd037ab283fa13503e8fa969b2621ba9e303a6ec9cd32ff00c43ad695a75d5cceb0dc45042f26e61c8daa589fc87e947506ec8fc71fda73568f5cfda0bc7f7713b4917f6bcd0a331c9c2109ff00b2d798d68f88f579b5ff0010eaba9dc01e7deddcd73211fde7918ff51f9d6757d04748a3cb6eec28a28aa11d0ca59c1e9d38c53795c7d0531588241e6a5452d918ae4350196e738a7ed2d804fd29c6108aa739cf5f6a7285041cf1401188bf114e64f96a4c8541cd0cb94cfaf4a7702001b03b523034fe7bd19e3d6802bbc2ac326a18b7db4f1dc5acaf6f71193b26898ab2e473822acb37cc33c039a8d97cb1c0e290cf40f83bf1df59f855e39935bbf7975bb3d462169a94133664922008c86f5009fcebd9e6f8b3f0cfc13f08fc6bff000ae6f6e2db5ed564dff67becf9803e7203770327bf7af9526459060633e954e4b70cc032f6208f51dea5c7a8eecfb36f6f751f803f02bc13a2e8091cde28f135d24a6e768951bccc16cf6ef5e6bfb567c24b0b4f891e16b2d0e345d7b5bb1f3351b684e22126065c0ed924d617c2efda7bc43f0f740b6d16ff004cb4f13e9d65279b65f6b1996dbd949fa0fcabd13e196b5a0fed0bf187c61afdfeac3c39ae9b41fd971dd38f29772e1c0cf4da40fceb3b58d22ee667c09d57c45f15fc3baa7813c4b0c177e19d074f92291dd3e68e5563b36b7a8da7f4ae7be2b7c02d03c2fe1fbbd73c29ac4f6baae990c73dfe8d72fb6548dbaba1efd07e75f42fc1df85775f0b3c273f86b549126bed6356f9b5481f725d44d9c608fc7f3af13fdb42f342d5bc59a56b3a16a80cc217d1751b68ce194c4401b87bd4ee56db1e65e11f8e5a8e991243aac5f6fb55c7ef87dfdbeffa57b5f85bc5fa2f8c214934db8477fe2858e197f3af92840dfbff002c392a9bdcc6a4841c75c55bd3afee34aba696d657864dc30f175cf6e9d6b9ea6194eed1dd431d2a6d296c7da7115006176f3835b5a6b3472821735f3f7c38f8f48e21b0f1302368f92f97a1ff007abdff00c393c1a8247736d32dc5bc982b221c8af16ad09d27ef1f494b114eb2bc59ee1f033c3c75cf1969eaea1e2cef653db15f6d7893528fc3be11b995130c17ca4e7b9e95f34fecb9a5ab5e5e5d15198a30aacdea73c7e95ea3f11f5c792e63b0dd88810ce01ea7b7f2ae8a2ad1b9e3e35f348c8f0a696fab788ed518efc3ee627db9af7abdd263ba60e46582e2bcd7e0fe8ecf2bea122fdf3f2e7b019af5a3cb0e6bba2792dea725a969e6d6d90ff75b9ab76d207b287d42e2af78821dd62e40ce39acfd388366b8e704d532e2c593239acbbf3b94f35a9231604566dda1da49141a1cbea0bf31239ae7eec9191eb5d45ec3bb3dab9cbe8c2862393537023f07b6cf135caff7a11fa66bb1d44623b563c0137f3ae2bc2cdb3c5f129e3cd8580f72315dd6a8a3ecaadfc293a367db9148091933a56a23193e493f8835e4fe218ef53c431cd6c3e57846ee3a9af5e887fa25e211f7e1615c95cdaabdcdb1474491d70bbfbd4b2ba1ccdaebb3c0aa2e2d41dbfddeb57d354d3aeb05b740c7a875c8adff00ec5948cdc58acb8fe28f06aa4da4e9b264316b76ee1d7148868a1fd91637846cf2a56f50714d6d0248183c12cf030fee3568af8222b840f6f791b376dadcd42da16afa50016661bb81b9b3ba9dae2b0d4bad5ede3c0992e57d255e6b8bf8dbe351e1bf835e37d42fac122f2346b9d92af18765d8bfab81f8d75e352d52c48135a89704f6c1af9d3fe0a09e385b3fd97f5fb5585adae352b9b6b40d9ea3cc0ecbf9213f856b08de493226d289f920a77229230e5549ff00be47f5cd2d04609e3001c628af77d0f3028a28a00df16ccaf9078a915595b23906a50c06680ff281deb94d4661d9318c9a8cb10b8dbb8d4fe6ed61c77e6a320b64e3b9a4026fdc1576e0d06561f29fe1a3a1cf714d6393fce8006bacf18e698b37cb83c114e0aa08ef43286270280232c1947a8cd20978c1a0aedf6cd22a1ea471400061c9239ed4a1972091cd388040a43b47519a77022f2d793dfb545bf62ee405250080e8486eddea657f98fcbc52e1597d0d2b148efbe1a7c7cf157c3b75b537726b3a26ecb58de316319c8c346dd430ed5c0f897556f11789756d566662f7f72d70d9e08c9ea47ad34dbb1e56a1680c8e109da41eb4ac55cfa07f651f105be9d6fe21d1a7f0cc5aa9d47f750dfbc5e66c66c831483b023bfb5278c7e0768fe06fdac3c1de17d1e42fa6ea1736d3c966e770873f3491e7b8f4fc6bca7e1d7c4cf127c23d7a5d53c3b72124986268251ba393d323db9fcebd6bc0bf1cbc39e32fda17c3be34f1b593e95796f0ac62e2ddbf74b2a03b588fc693124799fc73f0be9fe19f8bbe27d2f448248f498afbcb8f7a9f2d2460015ddd07356bc01f12f5df833afbd8dd41235b427173a5cddf383b973ec78afa50eabe1ed6fc0df1ae755b2f1121d4c6a36f1a60cac8fb7e641d7820fe55e6bfb4df80f53f147c5ed3df41d324bf97fe119b5bb2912e24d8ab8248ef8ce3d6a270535691bd394a9be68b3ef3fd99fe28681e24f0547a9e857697264ff5b086fdec471f75875e39aeea7bd935ad4300ee9657da39cf19e3fad7e417c2bf891adfc25f1847ad68d24b6f3c736cbbb22481228fbe857b1afd71fd9e75cd2fe2d695a4f8974b7074db88c381ff003ce4fe243ee0d70ce8a86c747b473d647d27e0ad1c693a4c51e3a2819f5ff39ae817a73d6996d108204403802a4a71564733656d4537d9cab8ce50d636929e6c2ea3f84d6fcabb9187fb26b034099639a78d8e0eee07e755b97163e78197a0cd675e0cc6477ae9a4895d6b1efed400702a4d53b9cade28c1ae6efe3c6ee2ba8bd8f93c573fa82658e2a59461e94c60f17694dd03332e7eb8aefb5b0534e978e3727fe855c039f235dd1a43c0172013f5af5892d526b6b92e372ac64e3e86901069f0994c8ac301a36c66bcd3c5d790d9b698664668f7b292bd41e2bd5f4e5dd34631c14fe95e4fe3c31c515a1930116e4824f6352ca44ba5eb16e1d3ecda9c96affdd724ff003ae93fb67517554952d35288ff007d4027f1ae122b5b6bc4f90a383d452b68f716c37dbcb2c1f4248a4896768d2690fbbced3ae2c1fbc96cc702b6fc2ba6d8ea97a221a9c97f06d244138c3023d0d79ddbeb1acd828cbc770bfdd938aebfe1e78a05f788adede5d396de6656fde277e95b53dc89ec76f79e0b5196b49da2efb241b97e95f9cbff000576d5dfc37e11f02785bc98e29352beb8d42578cfde58115578f73311f857ea112146589f979fd2bf1abfe0af3e3f8bc4dfb49699e1e824de9e1dd1a28a551fc334ced230faecf28fe55e8538ae6b9c0e4ec7c359c93fe7bd14515da60145145033a761b8039cd376e08ce7f0a780492074a490155e9cd729b5868625f04ee1fca9920cb70dc548a42ed3dcf5a8c9c9f4a42005b632e3af7a454d800ea689186debf95310918383f8d03b132609208a1ca8c638a8f0e64185241f4a9becace7ee91f5a571103ed623bd23150303826ac0b5e1bd476a5364ac9f375f5f4a60541c5358172369e9564da024007a7eb40806476a00aec49c03c1f6a6954607049357638d55893c8f5a8652884718cd0522b925547073da9d96550c4673d3daa676508095e0f7a72aa151938a06578c094b60f3de8683042b7218fafeb5692dd6304e46d6147d9d4b021f2b8e05160b92685aa5ff0087753fb669174f6772800ca9f96419070c3b838afa8be1afc7eb6f88ff0012743b9d4923d0bc48ba35ce92d221c4573215fdd60f6395fd6be5b788718254fb524511f31492eac8c1d1d4e0861d083ed52f42d33de3e3f7c28d4a4f14b6bfa7698573a2db5feb51a0dbb2762c85940eb9f2f9c57a4fec05f1d2e7e1ef8c5bc1734a5b45d7a7060539c413e79dbec723f2acff0080bf112ffc67a5ea91eaf7a2f75881adedd12600996cd164ca91dc7cd5f447ec9ffb1be91a9fc50baf19979068368c26b0808fbb21c971f4071594acc2e7e89c64bc48c7ab004fd7029d834a89e522a6490aa00cd29ac1a1a187904573291887559d07079ae9c1dac78eb5837384d6a4c8c64e693d0b8bd6c4f15d98d76bf51de99712892227ad45787b815464bac26dcd6573a1232f51c2b1ed5cddef24d6fea0fb98935897084eee29176398d61845358ca4e365d213ec2bd9238cbdb48b8fbf130ffc76bc6fc4918fb03be3ee32b7ea2bda34cc4f6b038e8f0673ebf2d344bd083489018ed1bf88a8fe58af31f1f2c291309b2aa97796e338af46d1370b6b527ae71fa9ae4bc61a7c775757c922e63f3b26a64813397b1d1acef543452a609e003b4d6c1f0ddf5b441a09e42a7a2b722a21e138258ff72fe5bfb9e952a691ade90b9b7b8771e99dc2a509b1860bb0c167b449fdd0735ade115b5b7f11dab18e5865c9015874a82dfc4f7f6eb8bcd3d25c752a306b7b40f10e977da8c086de4b79d8e1430ea69a76259c56aff1cafbc35e34bfb3b81e6d947301cf65cfcdff008ee4fe15f8affb537c471f167f684f1df8a14ee86ef52912161d1a28f08847b616bf4c7f685f14dbf84742f1d6b770446d650ceca58f25c8da83f12d5f8f4ce657691892ce4b313d771e4fea4d7a9866e49b671d54a2145145771ca828a28a067551fdea49bbd145721ba223d0547276a28a04c45e86a63f7051450344f0fde4ad193a2d1454b13294bf7cd467a8a28aa4210fdffc2a3ee3f1a28a0047fb82ab49d68a28290b2ffa9a8dba5145032c27faaa54eab4514012ff00cb41569feead1452607adfecc1ff0025323ffaf793f957ec1fecafff0024bacfeadfce8a2b09148f677e8bf4a6d1456452d86b751581a8ff00c864ff00ba28a2a645c7712f2b1a5ea68a2b13a519b79dbf1acd9fa1a28a0b39af12ff00c82ee3f0fe75ec3e1dff00905587fd705ffd04d14535b912d8ada4ff00c7b5b7fbe7f9d737e25ff8fcd43feba7f85145296e2440ff00ea87d7fc2b674cfba7e828a2a109916adf74d62e9bff0021dd3ffeba1a28a047c5dfb7bffc927f1d7fd7d27fe862bf2e0fdf6fa9fe74515eae17e1670d7f890514515dc7385145140cffd9, 'image/jpeg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ped`
--

CREATE TABLE `ped` (
  `idped` int(11) NOT NULL,
  `numped` varchar(255) NOT NULL,
  `fecha_pedido` datetime DEFAULT current_timestamp(),
  `monto_total` decimal(10,2) NOT NULL,
  `cli_idcli` int(11) NOT NULL,
  `estado` varchar(50) DEFAULT 'Pendiente',
  `empleado_id` int(11) DEFAULT NULL COMMENT 'ID del empleado que creó el pedido',
  `fecha_actualizacion` datetime DEFAULT NULL COMMENT 'Fecha de última actualización',
  `empleado_actualizacion` int(11) DEFAULT NULL COMMENT 'ID del empleado que actualizó por última vez',
  `notas` text DEFAULT NULL COMMENT 'Notas adicionales del pedido',
  `direccion_entrega` text DEFAULT NULL,
  `fecha_entrega_solicitada` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ped`
--

INSERT INTO `ped` (`idped`, `numped`, `fecha_pedido`, `monto_total`, `cli_idcli`, `estado`, `empleado_id`, `fecha_actualizacion`, `empleado_actualizacion`, `notas`, `direccion_entrega`, `fecha_entrega_solicitada`) VALUES
(43, 'PED-20260315181028-20', '2026-03-15 12:10:28', 3000.00, 20, 'Pendiente', 70, NULL, NULL, '3 rosas para este increible cliente super verga y mamado :)', 'Calle 7 #1C-84', '2026-03-15');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `perf`
--

CREATE TABLE `perf` (
  `idperf` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `perf`
--

INSERT INTO `perf` (`idperf`, `nombre`, `descripcion`) VALUES
(1, 'Super Admin', 'Acceso total al sistema sin restricciones'),
(2, 'Gerente', 'Acceso a todas las funciones excepto configuración del sistema'),
(3, 'Vendedor Senior', 'Puede realizar ventas y gestionar clientes'),
(4, 'Vendedor Junior', 'Puede realizar ventas básicas'),
(5, 'Cliente', 'Solo acceso a módulo de entregas');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `perf_perm`
--

CREATE TABLE `perf_perm` (
  `idperf` int(11) NOT NULL,
  `idperm` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `perf_perm`
--

INSERT INTO `perf_perm` (`idperf`, `idperm`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(2, 1),
(2, 2),
(2, 3),
(2, 4),
(2, 5),
(2, 6),
(2, 7),
(2, 10),
(3, 1),
(3, 2),
(3, 4),
(3, 5),
(3, 6),
(4, 1),
(4, 4),
(5, 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `perm`
--

CREATE TABLE `perm` (
  `idperm` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `codigo` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `perm`
--

INSERT INTO `perm` (`idperm`, `nombre`, `codigo`, `descripcion`) VALUES
(1, 'Crear pedidos', 'PED_CREATE', 'Permite crear nuevos pedidos'),
(2, 'Editar pedidos', 'PED_EDIT', 'Permite modificar pedidos existentes'),
(3, 'Eliminar pedidos', 'PED_DELETE', 'Permite eliminar pedidos'),
(4, 'Ver todos los pedidos', 'PED_VIEW_ALL', 'Permite ver todos los pedidos del sistema'),
(5, 'Crear clientes', 'CLI_CREATE', 'Permite crear nuevos clientes'),
(6, 'Editar clientes', 'CLI_EDIT', 'Permite modificar clientes existentes'),
(7, 'Gestionar inventario', 'INV_MANAGE', 'Permite gestionar el inventario'),
(8, 'Administrar usuarios', 'USER_ADMIN', 'Permite gestionar usuarios del sistema'),
(9, 'Configurar sistema', 'SYS_CONFIG', 'Permite modificar configuración del sistema'),
(10, 'Ver reportes', 'REP_VIEW', 'Permite acceder a los reportes');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

CREATE TABLE `permisos` (
  `idpermiso` int(11) NOT NULL,
  `idempleado` int(11) NOT NULL,
  `tipo` varchar(100) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `estado` varchar(50) NOT NULL DEFAULT 'Pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `permisos`
--

INSERT INTO `permisos` (`idpermiso`, `idempleado`, `tipo`, `fecha_inicio`, `fecha_fin`, `estado`) VALUES
(8, 66, 'cita medica', '2026-03-14', '2026-03-14', 'Aprobado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `perm_tpusu`
--

CREATE TABLE `perm_tpusu` (
  `idtpusu` int(11) NOT NULL,
  `idperm` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `perm_tpusu`
--

INSERT INTO `perm_tpusu` (`idtpusu`, `idperm`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(2, 1),
(2, 2),
(2, 4),
(2, 5),
(2, 6),
(3, 7),
(4, 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

CREATE TABLE `proveedores` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `categoria` varchar(100) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `notas` text DEFAULT NULL,
  `estado` varchar(20) DEFAULT 'activo',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedor_producto`
--

CREATE TABLE `proveedor_producto` (
  `id` int(11) NOT NULL,
  `proveedor_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proyecciones_pagos`
--

CREATE TABLE `proyecciones_pagos` (
  `idproy` int(11) NOT NULL,
  `titulo` varchar(150) NOT NULL DEFAULT 'Meta de pagos',
  `monto_objetivo` decimal(12,2) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `creado_por` int(11) DEFAULT NULL,
  `estado` varchar(20) DEFAULT 'Activa',
  `notas` text DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT current_timestamp(),
  `fecha_cierre` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pxp`
--

CREATE TABLE `pxp` (
  `idperf` int(11) NOT NULL,
  `idpag` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pxp`
--

INSERT INTO `pxp` (`idperf`, `idpag`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(2, 1),
(2, 2),
(2, 3),
(2, 4),
(2, 5),
(2, 6),
(3, 1),
(3, 2),
(3, 6),
(4, 1),
(5, 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tflor`
--

CREATE TABLE `tflor` (
  `idtflor` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `naturaleza` varchar(255) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL DEFAULT 0.00,
  `fecha_creacion` datetime DEFAULT current_timestamp() COMMENT 'Fecha de creación del producto',
  `activo` tinyint(1) DEFAULT 1 COMMENT 'Si el producto está activo',
  `color` varchar(100) DEFAULT 'Multicolor',
  `precio_venta` decimal(10,2) DEFAULT 0.00,
  `estado` varchar(50) DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tflor`
--

INSERT INTO `tflor` (`idtflor`, `nombre`, `naturaleza`, `descripcion`, `precio`, `fecha_creacion`, `activo`, `color`, `precio_venta`, `estado`) VALUES
(40, 'Chocolatina jet', 'Comestible', '', 1500.00, '2026-03-15 11:04:43', 1, 'Multicolor', 2500.00, 'activo'),
(41, 'Rosa Amarilla', 'Natural', '', 500.00, '2026-03-15 11:14:44', 1, 'Amarillo', 1000.00, 'activo'),
(42, 'Rosa Roja', 'Natural', 'Rosa roja fresca, ideal para ramos y declaraciones', 12000.00, '2026-03-15 13:54:54', 1, 'Rojo', 15000.00, 'activo'),
(43, 'Rosa Blanca', 'Natural', 'Rosa blanca elegante para bodas y eventos', 11000.00, '2026-03-15 13:54:54', 1, 'Blanco', 14000.00, 'activo'),
(44, 'Rosa Rosa', 'Natural', 'Rosa en tono rosa suave, muy demandada', 11500.00, '2026-03-15 13:54:54', 1, 'Rosa', 14500.00, 'activo'),
(45, 'Tulipán', 'Natural', 'Tulipán fresco, varios colores disponibles', 18000.00, '2026-03-15 13:54:54', 1, 'Multicolor', 22000.00, 'activo'),
(46, 'Girasol', 'Natural', 'Girasol grande y alegre, perfecto para arreglos', 8000.00, '2026-03-15 13:54:54', 1, 'Amarillo', 10000.00, 'activo'),
(47, 'Clavel', 'Natural', 'Clavel resistente y aromático', 3500.00, '2026-03-15 13:54:54', 1, 'Multicolor', 4500.00, 'activo'),
(48, 'Lirio', 'Natural', 'Lirio elegante y fragante', 9500.00, '2026-03-15 13:54:54', 1, 'Blanco', 12000.00, 'activo'),
(49, 'Orquídea Phalaenopsis', 'Natural', 'Orquídea en maceta, larga duración', 28000.00, '2026-03-15 13:54:54', 1, 'Blanco', 35000.00, 'activo'),
(50, 'Margarita', 'Natural', 'Margarita fresca para ramos mixtos', 2500.00, '2026-03-15 13:54:54', 1, 'Blanco', 3500.00, 'activo'),
(51, 'Hortensia', 'Natural', 'Hortensia en ramo, ideal para centros de mesa', 22000.00, '2026-03-15 13:54:54', 1, 'Azul', 28000.00, 'activo'),
(52, 'Gipsófila', 'Natural', 'Nube de flores pequeñas para relleno de arreglos', 4000.00, '2026-03-15 13:54:54', 1, 'Blanco', 5500.00, 'activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tickets_soporte`
--

CREATE TABLE `tickets_soporte` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `asunto` varchar(255) NOT NULL,
  `descripcion` longtext NOT NULL,
  `archivo` varchar(255) DEFAULT NULL,
  `estado` enum('abierto','en_proceso','respondido','cerrado') DEFAULT 'abierto',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_respuesta` datetime DEFAULT NULL,
  `respuesta` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tokens_recuperacion`
--

CREATE TABLE `tokens_recuperacion` (
  `id` int(11) NOT NULL,
  `idUsuario` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expiracion` datetime NOT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tokens_recuperacion`
--

INSERT INTO `tokens_recuperacion` (`id`, `idUsuario`, `token`, `expiracion`, `creado_en`) VALUES
(9, 69, 'cd876ca8c091863b730e65b2e9b59e2e439043c9b35827c89a6dec01cb350a4a', '2026-03-14 20:20:48', '2026-03-14 18:20:48');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tpusu`
--

CREATE TABLE `tpusu` (
  `idtpusu` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tpusu`
--

INSERT INTO `tpusu` (`idtpusu`, `nombre`, `descripcion`) VALUES
(1, 'Administrador', 'Acceso completo al sistema'),
(2, 'Vendedor', 'Puede realizar ventas y gestionar clientes'),
(3, 'Inventario', 'Gestiona el stock y productos'),
(4, 'Repartidor', 'Encargado de entregas a clientes'),
(5, 'Cliente', 'Usuario que realiza compras');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `turnos`
--

CREATE TABLE `turnos` (
  `idturno` int(11) NOT NULL,
  `idempleado` int(11) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `horario` varchar(100) NOT NULL,
  `tipo_temporada` varchar(50) DEFAULT '',
  `turno` varchar(50) DEFAULT '',
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usu`
--

CREATE TABLE `usu` (
  `idusu` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `nombre_completo` varchar(255) NOT NULL,
  `naturaleza` varchar(255) DEFAULT NULL,
  `telefono` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `clave` varchar(255) NOT NULL,
  `tpusu_idtpusu` int(11) NOT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  `fecha_ultimo_acceso` datetime DEFAULT NULL COMMENT 'Marca el último login exitoso',
  `activo` tinyint(1) DEFAULT 1,
  `vacaciones` int(11) DEFAULT 0,
  `intentos_fallidos` int(11) DEFAULT 0,
  `fecha_bloqueo` datetime DEFAULT NULL,
  `motivo_bloqueo` varchar(255) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL COMMENT 'Ruta del avatar del usuario',
  `avatar_data` mediumblob DEFAULT NULL COMMENT 'Imagen de perfil en binario',
  `avatar_tipo` varchar(30) DEFAULT NULL COMMENT 'MIME type ej. image/jpeg, image/png',
  `notificaciones_email` tinyint(1) DEFAULT 1 COMMENT 'Recibir notificaciones por email'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usu`
--

INSERT INTO `usu` (`idusu`, `username`, `nombre_completo`, `naturaleza`, `telefono`, `email`, `clave`, `tpusu_idtpusu`, `fecha_registro`, `fecha_ultimo_acceso`, `activo`, `vacaciones`, `intentos_fallidos`, `fecha_bloqueo`, `motivo_bloqueo`, `avatar`, `avatar_data`, `avatar_tipo`, `notificaciones_email`) VALUES
(43, 'maria', 'maria sanchez', 'Administrador', '3153646053', 'maria@gmail.com', '$2y$10$3Niqc0FgAX8drTAaN45Eg.ObF7HMeUfGDpsNFeuPLr2Z5pGftk4W.', 1, '2025-08-14 00:00:00', '2026-03-16 10:40:55', 1, 0, 0, NULL, NULL, 'uploads/avatars/avatar_43_1773511048.jpeg', NULL, NULL, 1),
(69, 'Mau278', 'Maury Yesid Echeverria Silva', 'Calle 7 #1C-84', '3137970263', 'mauryecheverria948@gmail.com', '$2y$10$9FhC8KwT5hqsrboEO4Se..kQkKLJDIpYf/NKPPlbz9MVzVJWfaGoO', 5, '2026-03-12 21:00:53', '2026-03-15 17:49:40', 1, 0, 0, NULL, NULL, NULL, NULL, NULL, 1);
INSERT INTO `usu` (`idusu`, `username`, `nombre_completo`, `naturaleza`, `telefono`, `email`, `clave`, `tpusu_idtpusu`, `fecha_registro`, `fecha_ultimo_acceso`, `activo`, `vacaciones`, `intentos_fallidos`, `fecha_bloqueo`, `motivo_bloqueo`, `avatar`, `avatar_data`, `avatar_tipo`, `notificaciones_email`) VALUES
(70, 'david', 'Brayan David Lopez', 'Calle 7 #1C-84', '3137970263', 'mauryecheverria278@gmail.com', '$2y$10$6FcjuOXUs3GreNAQmJhE8eh2HjmP6ueJN0vX6ZTp5IF6YJprTg.uW', 2, '2026-03-12 21:03:50', '2026-03-15 13:33:30', 1, 0, 0, NULL, NULL, NULL, 0xffd8ffe000104a46494600010101006000600000ffdb0043000302020302020303030304030304050805050404050a070706080c0a0c0c0b0a0b0b0d0e12100d0e110e0b0b1016101113141515150c0f171816141812141514ffdb00430103040405040509050509140d0b0d1414141414141414141414141414141414141414141414141414141414141414141414141414141414141414141414141414ffc00011080211016003012200021101031101ffc4001f0000010501010101010100000000000000000102030405060708090a0bffc400b5100002010303020403050504040000017d01020300041105122131410613516107227114328191a1082342b1c11552d1f02433627282090a161718191a25262728292a3435363738393a434445464748494a535455565758595a636465666768696a737475767778797a838485868788898a92939495969798999aa2a3a4a5a6a7a8a9aab2b3b4b5b6b7b8b9bac2c3c4c5c6c7c8c9cad2d3d4d5d6d7d8d9dae1e2e3e4e5e6e7e8e9eaf1f2f3f4f5f6f7f8f9faffc4001f0100030101010101010101010000000000000102030405060708090a0bffc400b51100020102040403040705040400010277000102031104052131061241510761711322328108144291a1b1c109233352f0156272d10a162434e125f11718191a262728292a35363738393a434445464748494a535455565758595a636465666768696a737475767778797a82838485868788898a92939495969798999aa2a3a4a5a6a7a8a9aab2b3b4b5b6b7b8b9bac2c3c4c5c6c7c8c9cad2d3d4d5d6d7d8d9dae2e3e4e5e6e7e8e9eaf2f3f4f5f6f7f8f9faffda000c03010002110311003f00f63f8577a45f5c5bb1c6e4000f5eb5e9776826b79a2ea1d4afb104608af10f01dc797e24b6392a1982d7b96cc4857a8cfe95e1ad8f61a3917996d90468022c436ed1d000303f95741e1ab5fb3da89dc62498e403d87f935ceb58bdf788e4b65cf96ac0b1f6aecd1844815410106148aab9241afeaf1685a4dcdfcee162b753264f73d857c71e21d5ae7c57ae5eeab70434d3bee5f5d83b7e15eb5fb44f8e25ca7866c5c6f3892e493db8c0fe75e236f1de4837a6123e54002a5b1169ed65dd1b00bb08eb4e0c1b10b3ac783c9150258ddc881583960703071c55883c353a6df31b2492724d201f2bdbacab827ea3bd385e431b86c1661d01ab71f861a5381215f5db5713c356e368666761dcd02b1892ea2c63663184c1e012294df48eabc119e98ef5b52d9e9960d998a6d1d77734c4d474e926ff00478e4957b0488ff5a02c66436d75236fdae768cafd722bea1d16632e9b6121ce5ede33cfd2bc221b99250ab1e9d31f76e3b57b4f852e1ee3c37a633a947116d2a4f4c1a682c75b0382a39a7c8700556b53bb1569c8c568162b480e0f1918ad0d3a3df611e3920918fc2b39d8b301d056ae94aa2d1b0dcab67f9d2b0cf9d7e37422d7c7d72c38f362493f4c7f4af3d32e64af4ffda2a210f89ece6031e6db6d07d48edfad79297384f522901a31b92dc73565652edb94e4564accc071d4d5c8c6c182d8e2901b10ca1d0f34929214e0e335412631a81de9c6eb380d41561d34a32b96c8e87f2af3cf125eae95f1abe1f5eb30c3c8f0007d0ae3fc2bb69986f386c039cd799fc53f261f11f8175066c3c3a9c6809f7fff0055023e9ffb5048ce7860b823df18c56523b47ab5ded1c3847cfb62a496620904000e0f1eb54aeae19b5b7619556b65c01ec4d03b1d05ac9e645729d7286b8ad7d77ee8b868da22a7db8ae9f4c918bcc07cc4a9ebf4ac2d4222ce54a0c1539f638a5719e5fa3787d0089b27284f53efc574896491464103279a9f46d19a6b5cfda218c827e56193d6b722d1eda351e7dd8071d116992cc04811a301530454f140a1ff00d5f5fd2b67ecba5403991dc9f534e8aeb4b85f888bff00bd408c778b6b738e41c54b1db31854aa961ec2af37886d2d9ff75628cc0ff10cd49ff0964a08f2ed4607609d295c0ce4b09a424085c9ec76d491e87732b802dcef07237701bdaaddc7887519f2d1c457fdded547edfaacc773bec5041c3753f4a17711f0f7ed0de049bc09f132fe268562b6bf1f6d83674c36770fc0835e660e6beacfdacfc2b737de10d3bc412303269b71e43377f2a4cf27d811fad7ca98dbc7715ee509f3c2e799563cb20a28a2ba0c028a28a067ea7e837ad65a95ac88718719fa57d19673f9b6d14b9ddbd41c8af99ac1c6d57ea4722be86f095c7daf40b37ea76e0d7ccc5e87ba5c8228edb549a6001690014baf6ad0e83a55cdf5c32c70c1197258e067b0a7dc0f2ee91863e60467df8af13fda03e22456d7b69e188126b8404497663f4ec3f9d592cf36bebf4d7f54b9d56e433cf76e5c0eb85cf02ae22c663458addf9fef0e958765accd01fdce9df27250bb74f6ad4b6d6f589c30482de223bf5a9623563d3672f91129faf6ab31e993b3125a3880fc6b3124d4ef0e65bd0b8ed18c535ed15b89eee67fa3628035cd8c4ae0cb7a547b1001a8843a3ee2269fcce7d49ace486c632a0ee7c77639cd5a59ad63e45b75e840a00b66eb45817096c65f4222c9fd69abad46bff1ef607fe05c5356f0b0fdddb8c7d291a7b89187951a8001dd918a7602c36aba949b76451427b12735e9bf0eeea6baf0f29b870ed1ccc991dba5798149081b8e18e318af43f86cbe4e9fa8425f76245700f6ce7fc2981e876c42f7a9dcf06a9db39df8c64719ab4efbb23d29dc0aee429c9ad3d1a5dd1cc08da14027f5acd95722ace8f969e64fef47c7b9a2e07947ed216ca6db40b92b87def193ff007c9af0898f98480db47622be88fda2ac85c783ace724836f76093e80a91fe15f3a797d0f72a3834013c0a0606edc47ad5b7932c39cd670cc473d0fb558b594f73d7a93da901a1bb383d8f4a4949098e99ef5107e002d9f4a6cd212a282901976a156c13eb5e5ff001a576e9ba15ce71f67d4e2727d3915e8f2be4f15e7bf1ae347f02cf2487688a789c11ecd4ae267d06b379d6f148186d7da73f80aa525dbff006c59271ff1eceac7dc30ff001aa5a1df0b9d034d9d5b31c96f1b2ffdf356e460d142e576ca5ca83edc52e6291b3a75d1f3873cb64567cb97d4e153fdf21867b11525a381749f5c0a6a123555c8193260e7b51b8dab1c558584d35ece7ce2812675d9ed9ad96d2119b2d2b9cf4e69eba7ac77b7bb6e638dbce618cf35a89616f2488a6f147cbce0d1720cd8f4a85796c9fa9ab29631ab0d8983fceb4d62d2ed9544f70cdce01cd4e2e3448f0c26638cf7a77119ad668a858c7f31e981d69f0c2029fdd91f8568af88b47541fbb67c74cd23f8b6c547c96b9f4e2a5b0298b42d9dbbbf2a72d83c80298d8fe14b278ca5407c8b5007b8aa4fe36d4649147d9b68e7914d2b8ee61fc53f01bf8bfe1eebda51b666335a48d1f1fc6a370fe55f9b9c8186fbddf3edc1fd41afd3797c41aa5c6632c36364153fdd2083fa57e75fc46d09bc33e39d734c6e04176fb7fdd3861fcebd4c24b789c5885d4e728a28af44e00a28a2819fa8b059dde8ba9cba55f45b6eeddb0de8cbd88af71f86f7e2eb4268cf0d0b608f41587f12b4383c41e1db4f12d98f3244c17990637a1ea4fd31fad4bf0aee018ee632c0891430f7eb5f37cb63dc4ee8ea7c61aa0d07c3f79aa94327d8e332ec1debe3fd435bbad77569f549fe67ba6f30063ca83d057d93af59aea5e1ed4ed9867cd8245e99e319fe95f1e6951e99abe873eab61746782da530ce21f9b6152473e94c4c7c57122855edec09ad083cc0036eebe94963369f1ee516b71310796390335bb65796e8a045a5f27af980b5488a11c196f9de4d87d075ab90690d30f920771db835ad15dea772365b69b18c74db11ad28b48f14dcc4be559c8b9eb84c53b018369a0dcb49ff001e8c31d37715a03c3773c97786207a0622b597e1f78aaea424ac8838fbc71fd6b421f843ac5c006e2e154ffbd4580e71b418a24ccd7d0a81d553934e8d74c8e22bf6a6623f8556bb087e0bca8fba5bd5c1ec06735a76bf0734e8c9696e9d89ea00c5303805bdd2a28c3794cc4772735d37c3cd6e0bbd46f2d6283ca0f16727be3ffd75d3c7f0b7448c005657f5c9abb61e10d3348b8692d2dca49b19720d005cb4f971ce49e6ad12d9aceb13b63519ad0c9fce801189c7356b4538d4501e8411551f2719a9f4e252fa03fed8a00e53e38dab5d7c3bd4c2ff0004a8df9135f2fb01b65e09dad807d46057d77f13ed3ed1e0ad6600bb9bca2c07e35f2badb2b21207079cd02b99db418f38eb52c6a060e300d49343ba32806169b0a154553f757a50098f7010a8a1ce14719cd49280790323d6a0dc55b00e73fa52b9572b4ae841dc48fa571ff142049bc03ab1392891798777b5759710ef1d98f6ac0f195bf99e10d5e39402bf6570147d2901d8fc3ebc179f0fb409d5b96b48c9fa738ae89efd22860591777992ed56f4383fe7f0ae0fe0e5c6ff00869a1303f2adb85e7d89aea355ba71650c800012ea323f2614ac544dcb46db3c259b82462a6811a4d5d95891fbd0463bd508d834b19fbbb5c7f215ab6bf36b119c7cdbf23f4a0a91cd6a9e1adde23d4dd677557933b4f63dea54d1e34423ce66206320d7a25ee8ba73ea970f2c2eccd86c8381d2ac4163a6411e3fb391c8eef934cc8f381a5c2fb4306754c1049ef562df4e8f7ed485b69e49c1e6bd2a296da343e569d6e98ebf2e6ac0be91c26db68a303d1053b01e643459b2425a4841f44357ed7c377d22fcb6321ff80d7a17daee981080f3e829425ecb8019c1f76c54b40710be0fd55c7cb69b47fb640ab11f80f53700fee223fed35762d6b72e30d39c8ec5b38aaffd95297cb5c7eb54b603951f0e67dc59ef60466c0254e7bd7c31fb6678187833e2da4e922cb16ad651de6e5e81f2cac3f00abf9d7e87ff00670839662d8f9b83e95f21ff00c1407c37fe85e10d72342638e49ac656f52e15907fe3b2575e19daa5bb9cf5d5e27c6b45267760ff00b2296bd93cd0a28a2803f707e1559c3e21f841afd96cc2d889154375dbc11fd6b8bf003b59ea30a6d2a0e54e7dba55af863e2a9b42d3bc4f6334a1a4bfb40c231fc24673fcc553d2d96dae2393382ac09af9f9e8cf6a2cf578f0a4863b477f7cf18fa1af8b7e125be9be07fda57e28f8335f863b2b6bd51aadbc36f968520c3b3b1f4eaa6bec9b7b8128538f9580f9bf107fa578deb1e17d0fe1d7c4af137c47bf845cdfea0969a6c5b937604ac916ce7b12714a2c6cee7c27a7f8275ed356efc3e6cb52b46e77c3207eddc7515d341a469700f934f807b8515e33f10be0259d834fe23f005db783fc4d0b19505bb116b3107eec91f4c1c7eb5d5fc18f8a127c44d0ef60d56cbfb2bc51a44a2db55b1cf0af8f9645ff0065b922aac847a3469040729122ff00baa0538dcb95001200f4a888ca8fa67f0a4db95cf41eb4ac805926e9514931c0c1c66a0b9beb6b252f3dcc7120eb9615ccea7f12744d3093e71b971d12319cd2d80eafcd618c9e9514d39520f407b9e2bcd350f8b7717002e9f66154e70ef5cbea9e2bd67573b64bb645feec7de901ed379add958a30b8b98e32074dc2b163f1fe992df5bdb5bef99e47d9bd7a0fad7960b17b9844f3894a28e5e5a7d96a296f790adb26f64756f947bd4dc0f5f8c88dc8e9f31157d0ee02b36518b9604e0939c7d40abb1b0551cd302c3f414b0713c6738c30a616ce29d19dac09e94017fc51a79bcd1f505ea1eddcfe95f25cd6fb7280602e47eb5f64dcc5f68b475eaaf0e3f435f24ea307977f791f4292b281f8d016311ed0328e6a2367b509ea0d5f00862bd292285b9dc72b40ac662c782036714c9225462474f5ad096d4963c6076aad2c25508eb9a96333265019067079aa1acdaa5c6957b149921e171c7d2b69adcb1cf7038aad736eed6d22bae43232fe628198bf0466327c38b442df2c123c5cf6c1aec35a00e8c4961f2ca8dfafff005eb84f817229f08ea1031c3417f22edfc6bbad788fec2ba206080ad8fa30a071668a92befc0fceb7ac610ba9c2ecdd58115ce248de5c671c32e6ba4b03e64f62cd9520f353729ea7a1a58c57572c0fdedaa7f0e6ae47a5c108392a33eb5cc6af692cdabc252e258d4c431b3bd126812cb16d92e6e1893c726a96a41d3b436719f9a78d7d7e61504da8e9c842fda62c0f7ae74784f8e22924f52cc6957c17bba59939ee5aa846d9f116956e71f6a8f3dc0aad378c34b89fe5959c9fee8cd431782f68c7d9a34f763d6a61e118631b8f90847bd16029cbe36b0462424aec7b6daad278e50fdcb194fb915ac7c370282df6db741dea29b4bb288297bf8b69f4a00c29bc6572e7e5b1639e99af0afdb0ae2f75df83374d2da88d2caf20badc39c104a7fecf5f4532691183e65d064ff67bd709f1c74bf0eebdf087c59651c8ef3369d3490827ac88bb907e2c16ae9cb9669933578b47e6011b7f1278f4edfd292907ae739e73f5e7fad2d7d01e3851451401facde186b5b7f147d86dfcd95dc3c0ee4120679cff002adf7cc2ac0fdf5183f81af4f8b42b3d2379b0b28965653972bf3138c66bcd355b19ec2fbecf71c48c379fc735e0cd753d889df6897265d22063d4af5ae07e33e93a9f89f57f02e8f636ccda74facc579aa4e0642436ff003a83f572bf95759e0db81368c416c98d88fc2bd2fe187d926f10bdbde246e92c0caa241c6ee2b148a93b2b9c0eaf3a4eeb182a44992ca0f5ce011fafe95e6da3431e85fb4b59ac4847f6f786643758e8cd048ab1b9f7c3b0cd7d49e29f81567a94cd75a4dc9b29c7dd43cae7dabe75f0c785756b9f8d9e2bf13dc5bb49a2e95689a058dc28cac8e0ee99d4fd76d6c88534cf4cbbb29de16fb1c88931076f98323e95e59e26b8f1523b2df4cd6d1e480605f948af5a8e44767643805b233daa59238678cc7731acd19eaa4706a5a2d6a7cf6741d435578d6386eb50dd9c9c122b5b4cf843e21bb9159ac96ce13fc52f5af4ed63c3f7eb6cff00d897e6cf3cf920706bcbb5dd5bc45a3c87fb426b88cae79dec54d40cea6c3e0dc368bbefef3785ec1c28f7ef581ae6b7e1cf0897b5d3ad22b9bb5cfef5db760d711a87892ff52ce6e5d63ee7279a834bd225d4250d6d08724f32b53117aeb55bff0011c8df6b7f2a12388a318aded0b4c82dc445808e2e3737f10153e9fe1afb2006626593be0715ab1d86fdc16224630463a5401d35f4919b98de16df13c6acae7bd4f149b941ed551ed9a1b1b2ca6d223da7f0a9adc9110a2e068c6dbb1529fba7e955ad9b23156d541233dc62a8ab1d45b30fb3c23b3478af937c5f66d6be26d513254a5c3715f56581696d2061d895fe55f36fc55b5369e3cd5631fc727998fa8a0471f1beeea39a9f05517e5a58a304727269d863c74c5022161bba8c544f0e47156c0f5e69ea993f778a2c0633da1524939aa73a111b6393d87ad74135a007af5aa53da88883d49ed4ac33ce3e0edabc5ff09240ca54a5f6edbe99cd7a06b118fec4d400193e4123ea315c7fc3e8fecde2cf164433869e39001e9f364d777a85b8934abc55ebe4b823df149e835a1069f299acad98f43129fd2ba4d29db6dbb11c87c5739a042b368f6122b02ad12e0fae38ae9ac07c91a81d25150d95b9d1f8af5cbcd0f5ad37ec96cb38921218376231fe3519f18eb0f1fcb6c80e4e71daaf78aed8cba8698e8096d8c381feed42f632b6d0b1bb13fec9ab4f413466bf8875e7ce1953ea69bfdadaecb80d76a9edd735a434ab993a42fc7fb3522e8576bff2ece4f6e29dc8321ae7577e1eec9fa1a85edaf642375dc983d706ba54f0c5f4a03790c08f52054a9e14bf90f223007ab014c0e49b4e99410d7123ab77cf4a55d08b0f9a57653ea6bb78bc273676bcd021f76a9078595721efe01e9b4d00702da247dc1603a027ad54d5fc3b6d77a4de5bb47859a1746ffbe4d7a13f87ad15b0fa9c43d8544ba668c5d564d43ccc30c8e9dc0c7eb42d25713d8fc7dbab77b4ba9e091363c52346cbdc152548fcc5475d9fc67d2e1d17e2df8c6ced98b5bc7aadc188ff00b0cfb87f3ae32be862ee933c792b30a28a2a847ef794c39fa579dfc50b736b75a7dda7f1a146fa8c71fad7a3a8070c07f935e7df1988b1b4f0f48fd2f2ee4813fded99fe95e14f6b9ec229fc3e9c196f60033b94301f9d741ad5cdde9fa65cdd593986ee253246e3b62b8af065e35b6bb6aac4012a6c23f01fe35dfdfa24f0cb6f27dc70ca47a8231fd7f4ae74cd1ec789e97fb7078aeef50baf09e8cf0f896fe5530dc5e6dd89a70ce198b7738ce3e95f607c26f1af82f51f0d59e89a7dc4513c5180f6b7240677eacc7d4939fd2bf38fe10fc3897c296de25d0e4b702f6d3559d679a36c34a8c77c793f46fd6bd4f4df0e5cd9ba4b1c6d13e7208720afe35b29a5a1cee9dcfb8fc41f0c2c3520d358b7d9277e703ee1f4af3cd6bc33aae81204bab7df17389a319535c3f813e387893c2690dadd6ed4ec13e5d9372e07b1af7bf0bfc56f0ff8c23587cd5b7b870375b5c719cfa1aaba911ef419e57b4b01b1fa761d6a1bfd3edf518cc5750a5c44460ab8e6bd835ef871a7eabbe5b23f649c8c864fb8d5e75acf85754d04e2784cb18cfefa31907eb52e26aaa291e51aa7c24b07bcfb4d964a75368df74fb55132cba5930269296db78c63815ea114d9e833ebeb4dbeb4b7bd85a39a31267b11c8accd0f339357d41e301218a323af1d69914da8b0cbcc885bb28ae8efbc2c60937c4ad2c3d7677158d35ddb5aca51a275653cab0e94ac334ecc4d2e8d1f9d219191d9727b74a489b008f4a974ebc8ef6c6e638a32be5b86fa83ff00eaa89d4c65f8c52116ed1c331e6b401da3a56069b3979700f435bc58119cf518aa456c743a2c84e9c38fbaf5e19f1bed441f10e46db812c08c3dfad7b8f851bcfb6ba8db801b20fe75e5bf1eec13fb7b4e9ff89a12b9f718ff001a04795241b48c0e7bd4770854f4fd2b620d35d82b349853d2a77d1d5fac84fd050239958cb1cf415308f03ad74d1f87a029f33126a75f0f5b05039a00e3a51c7ae2aba1264fbbbabbcff846ed3072335345e1eb48c0291a83dcd2d46780f84644b7f89faf460fcb71102011dc67fc6bd16e151e094ee017636463ae56bb687c33a7a5ef9cb6d12cac30ce10026b4c68f6488710ab6de318a86aec2e792f855a34d12cd4239d88508da78393fe35d1daacbe641e5c0e419012715d82d9aa6162b68d231d00c0a909ba500476d1f5e32d51ca5a64be28bbbbb75d366d3e1133ab7cea31c0c0aa2baf6b8c1585a79783ce4835798eacec4ac36e571c12691135490fcc2203daad27606525d57c412ab6d8d4739e4d364b9f11330224451ec6b50437eac06e40bdf6d3f6dce482eac2aac41925f5e9b01ae9462924b0d5e5505ef7691d302b61a09c81f328cfa52adbc8bf7e40de98ed4c46336997cc417bd3f9534787a691b3f6b739ebcd74096e1c1cf35661823403834c0e5c7846391ff7924a7dc353ff00e10cb3059c093763232de9cff4aec639605520ae3ea2a45beb6528300f201e3a0271fd4fe545ae16b9f953fb56e83ff08e7c7ef15da2a148cc90ca84ff0010685093f9e6bc96be9fff008284e9f141f19f4bbe8936fdb74481e423f89d64914ffe3a16be60af7a96b04793515a4c28a28ad4ccfe81748d2a4b8b5b760372940491d860f35e63f1b628bfb57448c4c92bdb2bb08d4eef2dc851b8e3dabc3fe367ed17e3cd07e2278174cd3665d27c1baa3b5add35bafccb29076ee6fc47e75d169f777b70223772335c05c4ace724b64f53f9578337a1eca5a9b5a6c820d42ce700aec90649fcabd5e0856e427ab803f335e4c8ee11b241da370adcf891e3a7f05fc25d435c887997a2dc5bda443ac97127c88a07ae4d61145b303e0d6836fe3bd7fe22789f718ecef35c36b6db7eebac1122330ff81123f0af4f3e05b3c00657aabf06bc223c07f0ef46d05bfe3e2de0125c37779e4f9e46ff00be8e3f0aecde3c0cd696251ca1f02d96e1fbf90629f1f826d2370eb732023a11d47d2b69db73b007a53e3538aa481ebb9bde14f156a1e19d88d7b25f5a0e0c32f381ed5e93a4f8cf4ad75442eeb1bb7fcb2900c1af1bda49fa52147c614e0fad5dcc9d3ea8f56f107c35d3b55065b56fb24fc90f1fdd3f5af3ed77c25aa681869e1f3a11ff002da31907eb56f40f1a6a9a1ec4329b8b71f7a390e7f2af42d1bc73a6ebaab14d8b795fac52f434ac8cef28ee78d825c02a327d0d56d4b44b4d41489e24dc7a103915ed1aefc3ed3b58064b702da76191247f74fd6b80d63c17a9e8e4b49179f08e92c633f9d4346aaa27a1c2daf87db4817021cca92a8007a63ffd7597a8c657ccc9c60739e2bb30e54fa7b1a6dfe956da8c5e5cc830dd48eb53ca68ad73ca2d2f0dadde0b003249c9ae9ecaefcf0b8f994f7accf15fc2dbe910be98e274ddfea89c37e1573c296379696bf66be84c33c67183dc52d86ced3c18333dd459fbcbbbf9d52f89ba2d9ea0ba48b9b51248d2155909c6ce067f3fe957fc2a047a8b81fc519156fc7f007d36c251d63b81fc88ff000a047994de16b389cec8801d393d4531342b7527e402b76e41de723a7f85542464d00534d2adc758c1fad4eba55af19896a5c6e3c54d1c7f8d00449a4da30c794b53ae8f6aaa3f74067d2ac4516ecd4aa0f4eb8aab015468b67d7ca04d4c9a25930c18473d6a75523b54ea09a2c0525f0f586f07c80c073567fe11fd39ae3cdfb38518c6d15656a65048aa490155745b007fd4633db34dfec2b0038840cfbd5eda7d28c1a2c877338e89663a458a41e1fb2656f939ad2dbeb4a171d2901903c3369fdd341f0c599fe135b18c51d8d2118c3c3568bd011487c3f6e3ee96ad820f34cda7278a2c0637fc23b0b7193f8d2a7866de32598f1c1fd456c8524f4a795e99e78c628b0cfce9ff008297f87934bf14f81ef507fc7cd95cc47fed9bc67ff6a7eb5f1757e867fc14ff0042f3fc1fe06d676ffc7adfcf644ffd754561ff00a24fe55f9e78da00ee00cfe55ecd07781e5d65690514515d0607ead78b3c3561e31f0fdd693a82e609978917efc2c390ea7b303fcea8f872e6f7c3e6d742d6ee166be083ecd760e05da0e07fc080c67ea2ba253855ce0f39c11d6b17c4be1cb3f17696d6579e645b1fcc8678db1240ff00de46edf4af9cbdcf74eaacd19a421816ff0061b8e7dfd3ad729e16ba3f19fe2669d6b13193c27e079cdcdc3f54bdbec111a7b84c313f515c8cbf0e3c6fac5b9d32e3e235c0d25fe591e3b60b72e9fdd2df875af69f833a069be04d29f40d2edc5b59c67cce4e5a473f79d8f7270281367abdb5cb2c9b9f927a9f53deafadd2b2e0f15931cbf2a9f5ab066c818c7bf34ee48b71c3e547068865cb727a5432dcecc71d6a259c3f2074aa4c0d4deb9e0f5a5dc3d6b393510b91b09a71bc2dd171f5a77197bcc5f5a6b4e17a1e3b9aa4b7418e075a1a7dc3d290ac6e693e3cd4f4591445334b12ff00cb373906bd0341f8a3a66b1b61bc06ca63d4b8ca3578eb1519c9eb4c595509c15fc4d3e6b10e099eebabf83b4bd7d7cd8556295c644d11e0d70fac783b50d25f8437108ff968839fc6b9ad0fc597da44c3ecb70c23c8cc4e72a6bd434af1fc1732243771793238187eaa68d191671d8e12dd4c6e09cab0fe134ff14dbc674a8ae1630ae6419603b54be2ef8a9e09b2f10be95a979b6b7b90a2685372927d715a9e28d0264f0a19ade45bbb72a25595460edf7a45f377383d02e00d6621d01c8ae83c5f1eff000ecac4731c808ae574a574d4ad9cf18715d9f8993ccd06f948c054df9a965ad4f3cb8f9b9c727ad673ae18d5f91b2abeb8155872c78cd2021456cf02ac42fb4907ad22919f4a61916390ee38a3a81a117cfcf415327cd2153c3019ac69f548ad2dd4b48019182a73d4d55d53c5167a4e524ba58e4d9b8b37615682e752815fa73f854a42a632719af0bd73f69ff08f86ee6449751fb4aa2758f906bceb5cfdbd740d36464b2b37ba5c7c8d8aae562ba3ebadc10d4f1b647a57c1da97edfd7b2190a69c224c718aa371fb7feae228a286c46ec725aad458ae7e81e3e527b53148c9e6bf39f51fdbfbc5490b2c36b1fb1aab61fb77f8c924569a38991f8c7a7bd3e51731fa44c4019edeb4c3228ef5f00ea1fb6df89ac6d6c8cb245247236498cf55e3f9577de16fdb50ea322ac9691b051cf9ad827e952e22e63ec25395dddbd694735e3be00fda2348f18cbf663b60b95e763375fa7e55ea165ae4773b72000dd0fad4d8d133480cd2edcd2803a8e868a9000b8a5dbc8cd21c82294b1c5007ca9ff0524d3d6e7e005a4e4736daddbb83e998a65fe6c3f3afcbe07b7ebfe7db15fac1ff000500b2179fb30f88a6233f64b9b4981f4fdf2aff00ecd5f94046de0fa9fd0e3fa57a9867ee1e7623e20a28a2bb0e63f5b0636938f947438a648bb47435e2ba8fc3ef8c3a7eb1777161f106daea25942c76f736a009141ef8e95d64927c4c6936b43e1e5381ba5cb924e393815f387b88f41b790c678eb5bfe1ab865d7600a7fd62907f4af201a4f8fae1944fe25d32cf3d3ec963961f8b1ad2d2fc19e254bd82797e20ea292060018ada35033ea31cd0163e8cb6bb78e5603ef28254919191ea2b9a4f8dde218e79627b4d3a531b14cf9033815cfdc69ff117c3703cd6da869fe2a8530c6dae61fb3cccb82490ebdeb81d23569bc5b7b7f3db79f693c3398ae6d26c09217eb838ea39e0d023db2d7e32ea974712e93a7381eb0e2b6ed7e2307886fd02c893d7008af1cb3d0f506519b865fab574d63e1fd40c600b9c8ff7a9dc477f278ded18827c3d6c33d4aca453878c7493feb3441ff01b835c57fc23170df7e66cff00bd4abe179390663f89a40762be25d23cf0e6c1d10e7e5171d2adc7e27f0d6cc4b6b76873c6d981af3e7f0aec39f349cf5c934d4f0c23b61a5fd09a7715cf443e21f0bbe0335e443b61c1a0ea7e137e4dedeaff00c041ae2edbc27001feb09fc2b421f0b5b77627f0a4173a51a9f8608fddea3747d8c62ba0b9bb80e99f68b67f36168b11b11824d7116be1eb58b6820f5e6ba11b12d6ded909f2e343d681a661dbf862db53d5e29668966b891c12cfc9af63bbb4bf9bc2725b5a955b6da11b775c0f4ae33c356419da723ef0da86bd025bdf2b4c4b653f2ede7dcd3b83573807d2fc8d8fd769c6076addd46d1eff0049b88531ba4808e692e95645650318157ac977dba13fdd229ee079a0f0ede1891494660304138c1a8bfe119be5ce110e7d18574974a7cf75c138638ae7b5ad7ad74a1199e6f2d9f214138c9a915ccfd4b4bb9d361324db631838cb0e4d78ef8ffe33e97e19b56966b8890b7dc3bc7382377f315c77ed0bfb46dbe97148b6730996ddf6b047e9c735f086bbae5d78e7566b89a594d8c84b057638193ce2b48c5b139247d19e2dfdaa6e350d6b439ad245fb1db4f34bb50e738c601faf15e3de38f8ff00e2df16ead713cd72f0c13121625382171c8fe55c33dcd9e9a4c16d22ec5ecc39159979abc521699143900ae057546062e624f7d35c92599d9987cc4b1aa31dd496e820c33e496dc7b7b552975d8e2f942e481cd569b5b47dac0ed619e2b65121c8d7170f3a1562463ad22cdb18b16e474cd6447adbac64aa821baeea749ab472f5017819a3945cc6c1be32c403907e94ab3e0eec82074cd73a6f4296dad9cfbd40d7d2c6a3e6279e94f94398eb16ecbac618191917839e055bb4d52e5268dc165619ea718ae3a0d625230081eb9ab035499d94eee07a52710e63d3347f1beafa55e4534572d9cf0436315efdf0abf6c9f1078558596af0adf582f0aedcb0c9ed5f225adfc9804b723a0cd6e596b6193f78a73c608ac9c0d14cfd70f869f1d349f1fdb44d66e83700151986e3c735e9315c09465410075afc77f04fc41d57c15a845a8e9f78c0c6785c9c60d7de3fb3c7ed55a078ead534bd5a57b4d655304cd26d593d08ff003deb9e5168d548fa781c8fd2918e17229965a969b72a2512068dc654a3e47bd5b8eef476f95e72bf46ac6ecb3c4ff6b9d246bbfb37f8f6dd94b04b0fb4631ff3ce44909fc0293f857e3c8e79cf500fe6327f526bf6e7e3a5be8da87c19f1eda457ccd24ba1de2aa37f13792d803f1afc461f747f3f5af570ad38338310bde168a28aed390fd7ff0017581d2fc4ba8dbb2eddb3b151ec6b26e325770e49aeabe28418d7e2b840db268c0f9bd475ae589de83e95f387b88a8186e527ef0cf156e1706325bef2fcc07e354c9c12719ab36aa0abe4e49140cf6bd2e25bbb2b69f3f7e31cfe1d6bcd7c5be15b2f0f78caff00544912d65d55237941e03320233ff8f57a07812ecdcf872d8b72c8c508f402b98f8db06cb0d32fd62f33648626f6071fe1412cc4b59ed4aae7508c73d8e6ba4d3ef6c5000750435e576772555375a8cf5e6ba3d3b524c81f6503e82811e846f74eff009ff049feee4d2a5d69e339bb639f635cdc57795056d860f7e2a78ee1c9e215a00db6b9d3fbdc487e80d356f34ec9fde4a7f0acd7b8b80a36c69c7a0a8c6a174c71b1171ed4ae4b3686a7a7a632666fc0d588b5cb25202c133fe1580b79779e8833ed532cd759cb3a03db8a6074506b56d34aa8b6b20cf527b568e3cd648e3ce5cf4f4ac3d1a39096927c1c72315d67876cc4b21bb6e507083d7d68291d269b10b585100e001575e724609cd56b653b3d49a9b613daad2b8104cd81f5ad4d1c892d5327a122b3658f8fa7350dc788ed3c2fe1cb8bfbe7096b1b7cedfdd1eb45b5b207a1cbf8c35d83c3897d7174c238e32589271d057e7b7ed2ff00b51dbf8b921b5d16e248a5b591be681be65618c67dab4ff6d3fdacedbc4cdaaf86f4b95ed1e197f773c2dfeb94fafe5fad7c22bad137cd3a484b3e7767b9f535bc21aea8ca524755abf886ebc432196fa60af29dd202c7e63dcd50d73c482cecadaded36a90bb4e0d735737135ecbe60dcc49e31d29d0787f50bc60563624f4c8add72c4cb9652d8ad75ab999c024820e59b35524bd762c5090a4d7450fc3cd46793e742a4f622ae2fc3fb98ced61d7d055f3c514a84fb1c579b9c9e79f5a61621c67a575773e08bc80b7ee99813c1c5537f0a5c26e251bd3a53f68992e8cd1802e1946ded4d672c3d4d684fa34b0139071ee2aabdaec38cf3569a662d35a3220cca0638a952739c1e41a66dc139a708b3d78aad0561464392bc83532b91d734d11a8fe2a7955c0e49fa5022d44432f5c11cf35a967a9333aab20c0e38ac45651c063f8d4f03947041a4ca476ba75c8ba568b779600e6982eae34db98e4596489c310b2a36081fe7158b6375e5b7dfe5b19addf316fadc44403fedfa56325d0d148f6cf853fb50f88fc19796b67a85dcd7ba5e76ed66c951fe7f957da9f0ff00e20e93f10b468eff004ed43cc8db3b955b943df23ad7e579f36cc9466ef90c467a5763f0ebe25eb5e01d4e2b9d22f1e0c365e13f75d73c8c5734e97546ca7d0fd41f16f87c5ff83f5c8526795a6d3e74033d494200fcc8afc741803683951c035fad5f073e2e7877e2cf84e49a095ad6f628cc7756b29c156c751ec706bf25e685ade69226ce518a907b60e3fa574e155ae99cd59dda1b451457a0721fb65f16ace03a4da5cdbbb48f1c986cae300e2bcde25c02adc11c62bdabc77630ea3e16bd8a2911a4550cb8f6af188433aa97e59bb57ce1ee22938e481d69d1bed008a4bb478e56f97eb51db96dca1b81df340cf50f85d7866b5bb833c46e1f1ec73fe15a9f14f4f5bdf065cb727c96590015cbfc39ba36faef9391b678c81ee47ffaebd175cb617da2df5b1e43c4d81ea40e3fad0267cf36571079abfbb958633915d1e99736a25506194e6b9db3b8b749114cd82095231e86ba5d32e6cc303e78c8a08b9bf1496b818866c0ed5663b9b75e96d37e34cb6b9b4d8374c39ab2b73663fe5b0a02e235d4181fe8b21fa9c5352f60cffc793fe752bde592819973509beb353c4945845b4be848f96c893fed1a9219cbc800b3001ee4e6aa45a9da6ec09327dc1ad1b69e39e44319ce2802f47f3b2c29f79ce302bbbd32d56cad62b7c8dd8240f5ae5bc31682e3506b83cc71f03dcd6a0d6617f125bc2a5c90db723a64f514148e9eccf41f95593f2a9cf1deaa5ab00e72029048fad45aceb567a25bbdc6a17096b00192f230518fc6b58aba15c875fd66db46d3e4b9b870b1a8dc4e70703a9afcf1fdb4ff0069fd5b4bb9d43c296372a749d42d44d6f7b0364364f4cd5dfdaeff006c0b0f11697a8e83e11d43c8bd8252ad2a927cc0382aa7d3ae7f0afcf9d4f56d4bc4f7e96ecd35eb6f2b1c258b004f5c7e35bc61d4cdbbbb14753d4ef35dbe69659a4b895db193c935eb7f0c7f66cd57c56915eea79b5b3739f2f18622bd2be03fecdbf61823d735f8c35d38cc76cc3ee7f9e3f2afa72d34882ca08e388615464051803a572d7c572fbb03d4c360f9bdea88f12d13f67ed0f45b75845b0976f4de326b5e5f859a6db18d85aa478ce005af58915218cbec2c7d0d225a2dec7b9931ed5e6fb59b7ab3d98d0a715a23c7ef3e1c5bba6ff2c1519c60573f71e008e0391096ebc7a57d0074b8fca642bf4ac9bed0d0b00171eb4fda4bb9afb28f63e76bcf0b98da53f67181c81d7359cbe0f4b8ce203197e4e477af7cbdf0b2bb1c46bcf535522f0a852729bb1d38ab5524852a119743e76d4fe1ba5e13134448fef015c96b1f0b7ecc9279437e3ee9c74afad24f0a2b65bcbc7e158dabf83a178fee004e73c56f0aed3d4e4a9818c9688f87b56f0dcf68e41421867240acb6b79061483c74afaafc57f0e5274711a1466f41d6bc4fc4be04b8b2bc95046caa833bbae6bd1a75d4b43e7ebe1254de879eed2307b53c30c63bd59b8b5689c86e3b62a131ae467f4aed4d1e6c935b8c54ddcd48a594f1c8a62600c6315228e7da80b9622988239e95b3a5ea0506d2d58c8ab8ebcd3e293cb70474a86ba827a9d73c66e532a37567b892d64dc149c8c1c75028d3f5365650bcfb568dd379b1798982fdd4541674ff000ebe23ea7e09f11c3aa69b33be0a89610701d707208f5af3ed7e75bbd7f539d142472dcc8e8a3b296240ad5b56fb28f3106dcb0cfeb5857a49bc9b3dd89ada8ab36655190d14515d4607f40d2e91a54b6f3463f8d0ae3af24715f37deda1b2bfb8849c086464fa735f428bcbb2dc5d439ea320578cfc43b1fb0789aefe60c25c4bb9470735f3d63dbb9c95fe49ca9c83d6abe32339e9535cb82839aae8c08233c1a451bde14befb1eb961296c2890027d01af72893ccdc9d54f5fa1fff005d7cef6b21866475e88c0feb5f4069f289eca0995b3b901cd2259e3377e1fb1b2d62f2265c149997a7f9f5ad5b1d2b4e0172a09a4f88467d3fc5f726278d1274494071dce7359d65a8deb907ce840f602826c7676ba6d8103082ad7f64d8e7fd58ac8d3f50bac0ccf17e42b4bedd7391fbf887e0281139d1ec580c4429a744b1c8fdd8a4fb75c8c7fa445f90a6c97b79805678cfe02802d45a559c6095840f7a86489214f906dc9c0c5431df5db6448ea57a702b63c3fa7b5f6a092302d1479c8ec4f6fe5401d1e9f145a769b1ae006237138ef50d9fd9a2bb8a6d837990316c7f9f5aadae6a12addf9304a91aa8f9bbf355adaf6f14e3cf42091d8508a477174d1db4f3091bcb5424ee271818cd7c09fb6cfed3b0deea12785345b8dcf082df302526078ea3d39af6ff00dad3f687b3f87de0e9a3b5bc82e2f6fa336f22ab7cf1646370afcdcf0b78435af885afc37f2fda1f603133ca33919e0d6c9a8abb0517276479f43e17d53c437f1470c6c2ee662085ef93c9fd6be9cf82ff0000ac3c1308d4afd127bc2036e9467675cd75de00f84567e14d93ca04f77d72c3a57a641668f1946018608e3deb8aa621b5647b187c2c57bd2dc86c645b98d5e3c18fa2e07a569a0000cd25a58476916d51b5453b6873c1e95e7ea7ac90c11865c15eb52aa2a285c62a48e13918e6a478b9e451728af27cab90326aa32ef04b0e6adc9d3e9509c804e335771dcaab6eb9395eb522d9a672054a0820678a9b6ed5fad05ad8a4f600a02578acfbcd211d030e0735d07f0007bd4132ee5db8f96b4296879e6b1a1c6e33f7bf0af2ff0018783e2944edb4ee3d368af7cbeb4531b8039ed5c7eb5a479c8c59702ae2da7a18548292d4f8fbc5be0636ef21540dd48e2bcd2eacdad5c8752a338afafbc57e19c34abe5821fa1c578cf8b7c1e0a4bb63e53e6040ebeb5e9d1ad7d19f378ac2f2eb13c7db0180ef4a65098ad3d4b4a68a7c01dab35ad883822bd14d33c5945adc7adcad59b7c487e6e055078d93a2e6a68ee005033823ad3251af1ec47500f1ea2b5ace46fb98273deb9f82e7041eb5b76d3962840c1a9e52932c188ef084e39c915897b9fb54808e86b704b89496e0fbd62ea2775e39ec6b4a7a333a857a28a2ba4c0fded1a5c4cdf304c77c3570ff0012748f2fec53280772b2139f4c63f9d7a18d2a0c36d1cfb3573fe3cd250787249870617562739c0e9fe15f3e7b4789cf0fc8d9ed59c5483c0ae82731ca581c291c62b1e64d92103a54b29096fcee0c3b71f5af6ef055c0bcf0eda36795ca37d4578a40b8704f00d7a9fc32ba2d61776dff003ca4de07b1effa521331fe31e922e6e74dbada771468f23d78ae2ad34b4c280ac71fed115ea5f14ac45df86d643b95a19558303d01ebfd2bcc2dac7cc0ade6cd81fed5023a0d3f4b0bfc0c3fe059ad55d2c123e571f8d63e9b61b9f99a618c746adc8ec1fb5ccb412c46d241c70ff81a43a5071b50b83f5a7fd8e7dfc5c4a07b8ab567a649f6a491ee1da31ebebfe41a0763ca3e3efc7bd2ff0067bd0f476b989aeae2fa6f901eea3ef1fd4543f08ff6eaf04f8ad63b09664b2bc9431c336067b578cfed5f7b73f18fe29699e14d0f4e8751b7d22268a7965e556790ae003ea029fcebdbfe0f7ec43e05f02e8164f7da6477dacb44b25c4f37cc55cf503d315aa51b6a4bbf43d2acb5ed335c6f3e1be595e439e241f85731f1abe25e9ff09bc1177aadccc45c142208cb60b1f51fa5725f1e0e97f0cbfb3ecb40b75fed095f88a33d00c75fceb80f13e8aff136c2c53c40c18c1f38818ee5cf1c7e82a6cafa1ac6326785f86bc19adfc79f101f13788b745a6799ba28c93f38ed915ef9a569fa57876d16288c36aa831838cd70be3393c57a2dabdbe9291476883e548976803d05788788bc67ae440bde2c91cb9c724e335328396876d371a6b53ebeb0bfb29dc7953ac8ddf9ad159532bb0839cf435f15f873e2ddce9f74afe631c70d86af54f0d7c708eec42246db9700926b9e587b6a7647131d8fa23cddcb8ce6ac431a6c272335c1e9be3ab6befde23ae0f1f956a278a909c820a9e98ae771b1db0a89e874e1ca38cf02ad02197239ae6edf5c5b85240cfa7357ed7565908046dc562d5f53a522d4eb90702ab630a73c0ab924c84823a1eb59b793e2450bd0d34ae3b14ee67742428c8cd4ab7c5b009e452cb1a94f7aa8e02b67b55d8b5b1a6272ca39a43206ead8aa293718a6b3962314c65b95832e381e9ef5937d6eb2232b903d2af00c319a8278bce27da9ec4b387d774717518c1db835e67e21f0ea4b14910077e31922bdd6f2c55222c7bfad71fa9e98976cc63009ab4f5b98ce0a48f987c4fe1330bbc9e5e38c0e2bce751d1e582403612c73c7a57d31e2af0e4b234838e0f4af36f11682149648ba75635e852abd19e057c3eed23c6ae20921243a95c7ad42600c09aec756d1d9f7165098efeb5cd5d2793215ec2bd152ba3c5a907165788151c738ad6b19c92a33c8ac947c138e735a9638619ee302aae6691b16f12dd49bc1ddb41040f7ff00f5563dfa79570531d0673eb5a363218ee65553c639fd6a96aac1ae13fdc19aba7f11153629d14515d2739fd09ff63e9f27dd9187fdb515475ff0dda5d685a8c4921677818282e0f38c8fe556a59e60178b46fc051149333a8682d9d73cede38c7ff5ebe7cf68f9889c4df31e40c1fa8a86e6304935b1e29d3db4bf10dfdb95da04cc40ec01e6b35932bcd4b2cab12ef61ed5defc3972bad3441be5963c7d48ae1d61209c5745e12b8367add9481b037853f8d2158f49f15d9ff68e837d6c0904c64ae3d457905ae95722355fb4639c8e2bd5b5bd427372b1449940c51c7b1af3617096975342f15ce5242b953408d2b2d16e8ed26ef18e985c56edbe997ab8cdca91db22b2f4fd421247ef2ed07fb4335d4dade5bb46317130cfaa504329b69d7ae08f357eb8ae27e2e78e66f027844416605d6bda916b3b0857a99483f363db9af489278cc6c12e9c92390c31ef5e35f0fecd7e2bfc50d53c5d2fef34ad119b4fd2d0f2ad20e24907e38a57296ba09f033e115af843c6de20b0d414df5d42969ab48cdcb9b8746de3e9d2bde75cd5adb4dd2eeaf85c2992de32e613c1ddfe7f9578afc2ef1736b9f18fe29c974c636b39ade012838e155a8f8b3f11229f1126d93cbe0b8e3703ebf9544a5d8e8852e6679cea03ced66f7c43acbfda35098b18d5ce446a7a015c4eb3e2245bb32432edc8e707bd6678c3c672cccebbbef64ed07a570a97b36a536c4c9279cd5474d4f46314958f468fc5f9cc571f38f5eb5cef8a343d3bc4969221895c9e5481ce6a1b2d227284b725b1c9a9869f710be55f1b7b0ef5b29f414a95cf983e21781b52f0c5cc935b8736ecc49c0fbbd2b8eb6f13dc59b44bbd976b024e6bebbd5215bf592defa20d1b71d2bc17e217c254b2b892eec73e44992a807231ff00ebae88c9356679d568ca2ee8afe16f8cb79a65d04699bca63ce4d7b67847e28c1aa98d598063f7483d6be439eca6b394a3865db9e48e95b5e1ef11dd68d708fe66e503a75a9a94632574452af284b53eefd2f5b62cbd3919c835d2d86a01f3cf2306be77f869f1105ec4904b2ef2e9901ba8c57ace81af0ba21430c6302bc99c1c743e9a8d55348f4ab7d4048b8dd53bb0254f5ae52d6f981001ad58b500ff00296f9bd2b2b58e9349e5dd9154ae237703153c443739fa54db4714c7729062806e18156a35dc320d39a2122950b935623b7300e475a0642c1b2b9a648a108f7abac9bc0e3a554ba424af6c6680b14aed56508a4f19e6b196ca38988f5cd6bce366493c562dcca524c9fba7bd02b1cc6bfa624923e1739ce2bccfc41a4ab4520da40ce38af59d49c3375e4e715c3ebd661cca992075c8f5ad22ecee73d48dd58f13d5749ff496c1ca0c820d719ad690b13111296e724d7b16ada7a03b40278ebef5c5eb3a69539db8c66bd2a550f0b1146fb1e532c26166cf4cf3ed524577f67922607e52466b5f5fd3dd6266d98507ad73a53e53ec2bbd34cf1a717167487315d31077061907d73506aec0dcae0630a29f1309b4fb79b3f30f94ff004aaf7d932827b8ad61a48e7a9b15e8a28ae939cfdf96d15307047e0f4d4d336e086208eeaf5a5fd9168c0e181fa3542746815b8ce7d9ebe7cf691e47f15f4c7b5f11c5301f2cf086ce7b8ebfd2b8d2a48181d2bd73e2e68aa9a0e9f74a0968a73116ce7e56c63f91af2646f907f9ee6a59642720d5cd35d9183afdf8c8603f1aace09ab36190ecbd3777a407b15aa89e5b795515d658d5cb1f5af2ff00185a5cd8f8aeefca7d8921de011d8d7a4f846e0dcf87ad1f39280a13f4ae43e26594f06b769344cb89230a49f51dbf5a09662d94d7a401b94e3ae4574f617377b173b1ab9cb23745d7708c8f7ae92ce59962dad1a01d8ad049c57c76f1bcfe0af85fad6a4bb56e9e3fb2db6dff009e92703fad55f803ade89a2f83343f0e4529b5d520804b3090ff00ae91b25db3f95719fb543cfa80f06e851f315dea3e7385e842608ffd08d55b1d2e2d3a5176fcdc632841c151d31fa5635256d0f4b0f454d6a5cb8b53e0af19f8df5286e379d72e44981db6ae3ff6635e53e2cf114f7f3c91a64904e4935dc6ad34b74ecf23ee635c46b5a69f2de441f377ace376cf415151479adf595e5e5c039c1271d6ba6f0f786c69e12595c798411b722b83f1ecbab5844cf6448c1ce4d79fd97c4cd7ed65115cbee3938273c577c69b91c93a8a0eccfa88d8911c6216dc3bf359d7b1dc5b30daa59b39fa5788e8df1c6f34e18ba50533d41aebb47f8e7a66a8544bfbb7e99cf5a7ec65b94b1107a5ce92fef03bb0994839e715cfdfca8fc72d08ce41abda9eb963aadab4b14e0fa1cfad7306f8aab24e7f759c0229f2389129296c715e33f87be76ebdb58fcd494fcebfdd15e53a978766d3e59176ed4072a7dabe97d1ef2349a4b77c496f28dbf37415caf8d3c0cb652caa881ada65ca1f43ed4d54699c72a2a479e7810cd6d7119f30961c71dabe8ff07c82dec91893bb8e4d794f847c17225e2ed8f280e78af65d374b305b045e08c715c95a499e86160e2ce961d40039435a56376ed3ab37ddee6b99456b751b8f5ad18afd6241f30cd719ecdcecedafb9033902b4e2bb4279615e7cbad00464fe46ad45aeaf386e7eb40cef92e514efc8dbeb5652f12404122bcf23d7f3b50963cf635a31eba0ba857fce9027d0edc48bb78c565decf86c9e055387570c17e6045492cc97084e47141a58a97b202b86358572594904600e99ab9777856419200279aa1a8cc1a5f97a11d7d695c664ea328099ea47a5733aa0de0b608cd7457adb54f3863591740483e6e7dea93319a38dd4acc246589e6b96bfb3f3f77cbb8d77d7e14c8cb81ec4d73f7565b59c9ebdeba612b1e7548dcf2cf12e9ae6de41e5ed5f535e7b7b0792580191ea2bd975ed3bcdb771203b093c0e86bccb58d3f648f1c60a281c835e95295cf0f134fa9069ff003584083032c49a87500c1d377618aab6371f659943e4203d4d5ad4240f37038c64576d3d59e454d11568a28aea39cfe88bfb26c18731853ec48a89f43b363d1971e8c79aa11ea59c85bf207fb4b4ff00b64a0e57508dbd8ad7cf5cf6919fe36f0ea5cf81f585863679628c4eaa4e7ee91fd09af9e1b0c46de548c83f5afa8ec6e24be9c5acf710c904ea6175f6607fae2be63bdb63637f776cc36f9333c7b7d30c78a434cac47045496cdb5d73c73513939e2885b120cd228f51f87375bb4eb881bfe591dd8f4073fe152fc43b27bab0b59e3037c6f839f7ac8f87d7422d425889e258ff003c7ffaebb0f13c626d12e0f20a61b81412cf3ab3b4bc519f2c1c56ac3248b1012a857ce062a869dac24d8412b3b372032e2b0be2578d22f06f86aeeeddf170c85624ee49ef4369234a74dd5928a3cd3e34f89edb53f1ae816366a2e2e74b769e675e420e339a85affed918953fd5b0c81595e11d005a693717f7c3cdd475342cecfcb053d3f9d50178741bbf21831b763b558f418fff005d70c9f33b9f4b0a2a9ab2356e60690a91ef5997b6c1a278f182456b2dd24a37c6432fb53248c4a181e09eb42934ee5b4791f8c2cd446c92c7fbbee71d6bcb75ff000c5a5f4a6445f2d47dddbeb5f446bfa2a5edbbc6c01f7af20f11784ef2cae9da162d1fa0aeea5559e7d7a3cdb1e25adf86aeed63981412124e197b8ae24adce9872e8d1e49c135ee6667b799e19e227b924556bdd334cd5d1564894f5c1c57a30aa8f1e78792774796685e30bdb3946e9375b93f3027f2af45d23c4716ae0224c4330e53d3deb0754f86f1bb6eb338eb8cd6741a05e680ea183798b93b979cd54e51919c54e0ec7a4c170e641c9c021411df15e98ba70d66cade274050a8f998570fe10d30cfa7db5f5eab23f51111f9135e93a22dc5d38758d9cf40147ca3e95e5d4691ea52a7293b9ade13f03c30b004a824f0315d35cf863c8076a673e958cfa85ee8e239a58caaa9e6ba7d37c5f05fc4a0ed200e4d7237cc7a70f77738fd42d4c68e00c63ad731a85e88199436081923dabb2f144ab1c72c89d082c31e95e4fafea9b3cd70c325302928b6cd1ce295d17e7d7d631b790dd7ad574f132c0ea5a4c67ae4f4af30d7fc492007390437041ed5cb5ef8ae412160e4671f2e6bb2142e8f3e789e567d1307891197724a0d6a69fabe158bb65b82066be75d0fc781e458e4936281ce4f5aec749f1fc534c115c320c0ce7a544a834cba78a8bdcf74b0d5f6a025f19eb935d0daea45a2e1b20d78fd8f88639c29571b4fbd761a678810c61430f94573ca0e27a30ad192d0eaee94ce323939cd51bac67af4a7dbea225404630475aab3b80796e4f6acac6bcc8a37077bb7391552540c98ad17452a4e6b3dd776ea6b4225a98d776e4c84e38ac9bfb5121c1e878e2ba09e32b9cf7acd9a3c38623201aa52b1cf289c55fd8ac224dd938e31e95e77e2bd3b0acca720679af5cbfb22ecfc646735e7de2ab3314122633824d76d093b9e66260b94f267b7da0863f38e714f926f3950e3040c1abd71009ae1b6d5192311e3ea6bd9a7b9f3559590ca28a2bace33fa09492c9b3b98ff00c092868ec98712a63fda15a8447fc50381ea529af1da15e547e295f3d63d8b9991c76b1c8ae8c9be33bd4e71c8e715e31f142c1b4df1c6a2ac46272b70847421c67f9e6bdc5ed6cdce03273edd2bcb7e3769ca97da4dfc4a0a4d0185a453904a76fc8d2291e6add334b1e09140c327d40229f12e597d8d22ce8fc2f70d6faada3eedabbb6927debd5ee63fb4dacf1750ca403f8578fd91f2515ff895c1fd6bd92cdc3c713820a3a86cfd450268f25d2e39202e97006e5628001c8e6bc63c52aff10fe214d14cedfd93a71c6c1d091ffeaaf6df184ffd8afaa4edf2f93b881ee7a5791e8d1269da1dd5e3f135dbb3927a9ff39ae7aaefa1ec6061cb79329ea7784cec89855500281d97b5656a314777666391416ea0d4b2cc18649fa55492e06719ae73d7bdcc096eeeb41995f6f996c7a835b5a7ead05fa131c9f37753da9f388678446e1486f5ac4bcf0fb412096d1ca9073c74a09b1ab720c8c4563ddd9accec02023be6adc17d287fdf264e319ab30889c16c81f53549b416385d57c216b78ee0c4a1987040ae5aebe1ac71caceb9c63803b57aecc9086392b8f5cd54bcbfb58a201577b74c8ab551a319d34cf2c8bc0ad1db33b741c82d58d7ed6b6020296cb7374ec6340173d715de6bd7175713fd9a36326ffbb1a8aed7c0bf0aa05beb7d56f9332c4a0a211c03dff90ad1d77639de1eef633be1f7c2a9ef2c20b9d497a9cf938c6057a743a058e9602c68b1a2ff00081d2b7f7c76c842001715cb6bda970db5b1cd72f33933d5a7494236297886d6d2e6ddd4c609edbba578a58ea86d3c5b3698d91b8fcb83d6bd1f5ad5f75ab2efc9c16383d00af20b2bb07c45a97880e196d50c5073c339ce47e82b682d4c71168a3d0352d6acadd7ecef8790af2a7d2b8bd55f4fbacefb34f978183d4573b3ea17123bdcdc3169255c83e958d73ab2f984f984b28fbb9af4214efa9e33935a0cf13782ad35189e683745bba8ec2b82d67e1f5ca42ad146254c1c91d6bb4fede9f0bce63e722b434dd652e1d86dda02e327a735bd9c4e5947991e037da2dd59ceca4323af453c552b7bdb8b790aab952a7279afa36fb42d3759844770aaee7f8d0722bcf7c4df0b1e021acc19739e075615a29a7b9c92a6d6c61681f1125b59555d8aaf4f5af5bf0f78eade48d599f7a14e7b57806a3a04d612baf94e8c9c90474ab5a26bb2da9556c91d0544e9c65a8e9d69537a9f5ef877c4115e5b46623907b13c8ae8480e037ad7ccbe1cf1add594d6ec8c5f0d83183eb5ee3e17f1c43a84be4c8557e5e093d4f7af3a74b9763dba18852dcea663e5273deaac8c027b9ab12b09620d9ce4702a94b9070462b919e8dee5399b793918c74aa52c4cdc2f7abcc373629522e4e45213661df5bb244e48ed5e4be35b9843b076f50315eade2093c982570fc0ed5e23e31be1728c807cc49e6bbf0eb53cbc5cd451c9f11e48e493c0aada947e5f95918dc3754d6e9e50f9ceef9bf4a6eb0e19a1c765c57b34f73e62b3b99f45145759c47f4771ea1130c09a27f6ce2a5120947dc8c8f661589e64dfc56d0b81df18a9166c039b453fee9af9db9ec1aaf1c6d80d681d73ce154d79dfc71d162bcf032cf6f6ed11b0b8121f971846e0febb7f3aeb4cd08eb04aa7d8d6478aad9b59f0c6a963019b7cb092aaddcafcdfd29ee523e6986338ce393dbd2a5452ac29f0fccd96186239fd69eeb8348772fdb0df1b639e38fad7a87866f7cfd1ad59b9c2907f0af2ab0760029e2bbaf0f5dac5a449bdc24709decc4e00148b8ea79cfed0fac7d95adedadc7cf77b4b81f5e3fad705e2fba4b4b5b4b541b76c43207af19aea7c5ba85af8c7c6af711fef6d202155872a76f7af2ff19eaff69d5e6607e404803d05724efcc7d1524941245196efa00718aaed764673ce7a565b5e97391c8a4372dc67bd667522d4d7e63619279a747ae01c331ac997733e49e2a8de48b0839e7e948a474edaadbbafcc707deaadc4a19720f07a735c45cdf32163b880067ad75be0bd09f53b64b8b96600f2a09ea281dae28512bfcc4ed3efd6b46db409af18468b88cf39ae8ecb46b52c1400d835d2dbc36f68ca142e71cd43b8d45187e1df04dbdab79d3a8794e3ef0e98aec1a44b58b68c000718aa4da885c0200c566ea9aa7ee58e718071458d9248355d7bc9438231cd70fab6beb3e771e7b556d435276120663819c57137ba9319ce1b8078cd5240e56449e2ad7c5b6917f2a725632a06792490001f9d726d6d169b616ba5cca59ece3134a14f2d23f383f4c52dd4adac6bda769ac331977bc9bfeb9463249f6ce2b03c33adb6adf10835d822cef23791867bff0008ff003eb5dd4a17d4f1317579a562e6b504cf204d8eaac32b81d07a572f7ba7b4738ddb8647535e813cc246cb93855c1efdcf15cb78a2ea4b6589e34dc9ce7dabba2eda1c2f5312d61d8addd49ef5af636eb8c0c73d735876baec138f94fcaa4ee22ba0b2bc82708b1b03bb91572d762569b9b16da6ab2a34676b1fd6b596c59106f196f5c74a4d363695460609c57436abb6371247b94e30d5c77b3d4e9e4525a1c2ebfe0ab3d59245917e6238751d3eb5e49e2af86f3e8970b246375bb1182a3a63d7f3afa46eec1117cd46047a0aca9522bc768e54408781bc55aabd0e59d13e66d3e39ad255f3036ef30e3d85769a3ea2e91bb47232caa72066bb7f13fc388ef1249ed155241d7df3d31fad79f3584da65c4d1947668f00e455b7cc8ca317167b7f833c50352b682391b1215da01ee6ba8790488a7bf7af0bf08ea0f6fa9596e6c6650a003d335ee8885f6e395dbc1f7e735e7d585a47b9427ccacc86388b127f2a9654105bbb93f3638156218c8006de6a0d4a33e436d35925a9b4b6b9e63e2cd49c2c913ab2924e00af1dd72669657c125549073dabd3fc7cb2f9d27246d209c5794dcb34f3cc0e73bb906bd4a2b951f3d8b936ec521109a2ebc835535050b228073c56bc76de5c78c139acbd5176cabf8d7a54f73c5a8f429514515d4729fd182dade28c8746ff0078629c63be03ee44c3f2ad63a3129fbbb96e3f1a6ff65de2ae05c06ff796be70f60c57fb401f3da2b7ba9a64371b67557b6930e761ee003c1fe75b0d637c339643f8557922bc8c37eed588e78c53b85cf9775ad30e99ad5fdb1047973ba007b0078fe7551a3ce303a5773f1674dfb0f8d6e1b6943711a4c47b9ce6b8e2b8a0a23881475e38a678e67b8ff841ee6d6d2430bde1588cc0e360ee6ac22e4f356ffb22cfc51a3dce89a86638ae9485997ac6d8e0d24ec69057763ccc7862dbe1ef871a6875b6d51654380718438e707df3fa57906a57c6e19983165393935d7f8deda2f03e863458e67b836a590c84e77f3d6b83f09b36b5a80848dc0a925476ae79bbbb9f414972c47dbc12bc6081ef53085c75ae81f4cfb33ec0b8038a92db4e0c08c73ef5cedea7545dce4ee23949fba71599a92651b824d7a15ce88648ba608acdb8f0da3ae58f5a0d4f2ab98e49ee95426632c377d2a7d4fe30c3a13a59c2c15233b07b7ad7657de1c8ad6391f7107071c57ccdf13f4a91355b95c950d9da4776ad20b9b466736e3b1f5a68be2343a05b6a01b22719dc4ff9f5a9a2f162bb64ca327a0cd7841f18cb6de00f0dc4f26192d00723fbd9ff00eb5161e3277d811839c7393d2ae54adb131ad6dcfa1ffb783c60ee049f7acfbdd5b746406ce6bcb2d7c5929450582eef435af16aef2a82edc0ea7358b833a5548b34753bb223660460e735c65ddc07ce48c824d6cddce5d0ae7af4ae5b5e3f63b5763ee48fc3ff00af550dec6751e853d02f5750d5fc4d74060db409650b0f59325bf95496fa0dbd84a93c47f7a0fca47618c62abfc3e8d07867589c7ccd2ea08c4f7c60e3fad7450dae48c8cf3d2bb22f94f2650e677657b8b46fb2a3052a48f9b35cedf28594c7709946076e6bd0e2b6596dca118207715caf8974a76b61b17798c921bfcfd2b48ceec72a5a5cf38d53405d3ee44b1c67cb6ea3b73d6aa5e79fa2c5f6adfb630485c5758275ba8cc5228e0633ef5caf8e609db41b8480ee21f257be3d6bb60ee7955af13a7f037c4059658127655c9c649af6ab096df52b35d85493d307ad7c436d74f6ed1b45210c0e7ad7ae7c36f8992d94c905c4849e8067ad1528a6ae830f88b3b48f71ba516072f848c9c64739ae7b55213321398dba62b4edf558756b61b9f96e48cf4ac4bc864485e0719f9f2a7d6b8942ccf59d9a24d375858d44728f323e884755f5a935df0dc1e22b19258904336df9980fbf8ae662bdccec06005247d0d74fe1ed44a6e490178c8eddab5b591c525a9e457761359c93007c89e065914776c30ffebd7bf7842e46a1a3239c9910ed3fceb99f13f85535ab413dbc7be724ab15ecbea6babf869a6496fa53bc8a5776d46cfaae7fc4572d4773af0eeccdfb78038076e0f7aa97f6ae623b573eb9ade8edd44876a903d69b7d12184f181deb92f63ba6f43e7ef8951482eca2ae4498e47b7ffaebcbae2c4452b4884b67d3bd7b27c440915dc81c7ddcf3e99af2f81164832ca5510905b1eb5ea527747cee23de663c8d9456395c673581ab90d2ae0e7aff004ae83572b6e9b4f1b8fcb5cc5ea1590673cfad7a149dd9e3d52bd14515d8729fd1f457d29e9750b8edb971530bdb8e302071ece4550592d547cf0ba9efb874a942d83afcb20527d722be70f60be3519171beddff00e00f9a3edeacff00eae55faa8359c6ce3eb1ce7fe02d51b41749831cec79f5cd0079f7c74b41711697a946198b6617257183c103f9d7931505411ebfa57bafc45d3ee350f056a2b31f30db159e338e98383fa1af0d441b7f1fca81a1a89d69d7337d92ca69780557218f634edb8ce2b2fc5d706d34099ba6462a24ec74d18de67ce7f123529751d4245c161b89622ae7c2cf0e1d396e3519872508507deaed9e8ffdafac7ce9b958e4d74f78f6f616be5a1090c7c123a57233dd5a2b19cd189257278563926a487ecf1b613923a935cfcbad1bab831c39d80f2c2ad5bce4606723d6a1ab1b459b734a8f8518a8dad46ce463eb55507ef1496ab72392a067349336b987ace9c66b47da40af11f187c3d6bdbb5b99b8da4919e99afa06f0208d43363d6b8bf194f135b044038cf3f956d07a99cf547cebe23b478f4b4b63ff002c005c76eb591a0cff006562d36100f435bbe27b2ba9c5c1c12377e95e6daafda212e23631923f3aee86bb9e6557cacf4d8fc5b6291aaa60b83d58d6f689aefda597cb759149190a7a0af9c26fb4c51179262cdce066ba6f0078ae4d3ee22825761bcf24f7ad274d5ae8c69576a5a9f4de9d18ba88c858649c007d2b97f8967ecba4315237b305535abe1ad4d6ead5046db9b1923dab9af8b57e174db4833f3cb2eefc063fc6bce8a7cc7b329271b8ff008612a8d2f50b1382f2c8b2293df19ff1aed6cac9d0a961924f26bcebc1321b79e2718008c57a7c376aae88f850c0735bc9e86105a9a515aa95ce3b76acbbdd396556e322ba4b62047f2f2b8ea3bd453da992027eb8ac632b33770d0f15f12685369f72d3c0bba2ce48ae7ee5e2bb826dc83cc2b8da7bd7b15e69427cc32a1c37ad705e24f073db4ae61527392315e8d299e5d6a374cf15d7bc33e54de6c2a067a81542cec6512a90db2553c62bd06eac644768e5420e79c8accbcd2d903c91a8e3b8aeee6ba3c4952699dffc32d63cf912c272ad7057804f35d8f8adce9b64b391f346d8c0af9eecf51baf0feab0dfc2ec25461c57ae6a3e3c8fc4da4202abe705f9c7bd64d6b73b694e56b3312d2e52798ed6c163b8e3d6bb3d061dc50e491b81e7be335c16896cf2dc072472c70057a87832dc3862ebc46a4f358cf735bdcea3c29e5b6b77f632af22cd656cf41b89c7f2aed744b086d2ccc5191f3484e3f015c768a8b2df6ada846376e8e38411e8377f8d7a17c31d053c6bff00091c82e1e3361e56d44ee589cfe82b9651b9a427cac416e096f418c552d4a358eddbdeb624b7fdf3c68795665e7b8048aced7e0fb3d936e3f37f2ae271d6c7a3395e373c17e23289b53541cf9a0a1e3be38af3cd32df669ffe91fba661919ef82466bd43c6e0cef8046e8dd64071e87aff003af32f12ce82068e3e1a462c31db279af429ae547855b56ce3bc42fe7ddb464e581054fad606ab0cd1989a689a2de372861d47ad7aafc29f851a87c5af10ad8dac4e2d62702e2e80e231cff81ad6fdae7c0107c35f12f85b4684676e8c8eee7abb798ea4ff00e3b5df425ef58f22b2d2e783514515e89c87f493b6e54e5edb23d3ad218e1272f6db4f71b7356c9f28002f6703d1d03548975bb83751bffbc9b6be70f5398cd686c4ff000ec3ee3151c96168e859262b8feeb56e88d5fa089f3ee2a37b08e404b5b06c1c65680e6399bad17ed36b7301999e29a268ca939ea323f957ce2f0b4523c6d9dc8c5483ea0d7bcf89fc5969a56bb6ba3e9104d7dac4920f3122c95897bee3d077fc8d79378cb4b6d2fc517f03a08cf99bf68f7e68348bb9ce950320f06b98f1fc864d1d6107ef373fa57617312a8dd9e00cd715e29c5cac401cae4ff4ace67a1875adce25238f47b166c0f31fbd79af8fbc582d20169193bddb2c41aeb3c6daf7d9e468a3e401dabc9752d12e756b97ba72595ba035ce99ebdc9ed7c636d0148565f9f233cfad77da76b164110b4819c8e99ef5e11e2ef04dde976336a16e5bcc046d5f535d0f876498e8b6b73396599d06ec9ef57c97438dcf6ab6bf8a41bd9d413db3d29f2ea30ef03cd5cfa66bc817559e7de23948dbef59316b97f05e3334ac547a9acdc1a3a51ea5e22d7ca860a781d08ae3a6bd6bd73b892b483554bfb68ccae32734f861886769f7a714d1525a1cdf88b4d478cf9607cc0e78eb5e63ad681e5ee66405864e315ec3a8c61db6d735aae9f13bf032e41c57446563cfa90523e7dd5ac244965dd19dbe98e94ba4d82652423057a57ad6a3e1e4b990ee551c60f1583ff0848591821239e80575fb4d3538fd8da574751e02d6a2b0826695fe611f033f5ac1f11de4be24d6d6572de544b8553eff00feaadad1bc24b020624e48e73deaeff6198a650630148eddeb924d5ee7724ed6647a0a18618c8420822bbf8a133db2b704e064572f6b68f1215dbb5474adcd36f1d5407e3b54395cea8c6c8ddd3b516b595564ff0057d3e95bf0cf1ca32a41535831c51dcc1b78563d2a4b677b2213ef564cb366e2d2376dc0648ac8d474f12cab818041abd1ea8b236c6c29fe7534928287001f73daaa32712649356679aeb7e161713b11b783ce4572f3f846542fc02b9e00af5fb9b617208da01f6ef54a5d31141cae735d1ed99c12a11be87895df839a6b90c46c00e791c52c5e1692da232a0e1ce095f6af5b7d26125b7267bd54bdb046b1766555451900f154ab3662e9289c3e97a5342d185e07de27e9ff00ebae8ad75b92e3525d234c625d800f263819ea2b98b9be5b9956db4f2cafbbe663d31e82bd0fe1d68b0e98f1dc4d17fa431dedbba8ad5c95b532e46cf49d27418f46d20c0fc990649f7c5763fb3d39d1fc55af5928564d4707e6ff00641c7ea4d73b2dcbdcc41d57803a558f867a83e9bf1074d0c768925d84fd6b2e6d742dd3b2d4e95f4e6b7bfb80573895b91f527fad729e3abf16b03823e6eb8f5af4ad78269b797c5b00accc483d85788f8beee4bf6ba9d89548df81ea2b3715cd72dcd721e71e2cba3045248cb8597ef03d85725e05f865ac7c51f112db595bb2e9fe60f32e9c7caa33d3f1e7f2af40b4f07defc4cf1445a7584334964857ed7222e405ee3ebd6bee8f869a4f86bc0de1db6d3adb4e8e189100225870cc7dcd6fcfa1e45495d9c67c2ef02c5f0b74d6b5d3ed2d82b01b9828dc4f724d7c67ff00050fd4df51f8bfa27991794c9a2c7c7afefe6ff0afd2a7d53c3d70bb0c31267b2e56bf35bfe0a32f647e396949624989741833f36704cd39ff000ae9c36b3b9c1597ba7cb1451457ae701fd23289186629fea294fdad7afef33ed9a44b9b265cb068fd723156225b374675ba30aa8cb396c003f1af9c3d66549249d383029fd2b85d7fc69a86b1a93e8be19321bb5c2cfa82b663b7f503d4f5fcaad5d6a5ab7c41bd9b4dd0a66b3d151b64da937de93d553fcf715d4e8fe188bc31602d2c624110192d8c331f527be68118fe16d3adfc256e62b6690dc48774f73200cf23f7627f135c6fc5cb1fb4eb767a826196e6108c40fe35ebfa115e973444383f67c71dab9cf1ed9adef876397cad8f69264fd1bffd5414b73c3fc42cb69a35c31387236afd6bcfb52948b0879cb639aedbe22b88ed22851b0cff00363d79af3ed52610d83166e7a0cd633763d7c3c4f30d6accdd6a6ecc7200e41a4b7b78d8f94147b0c54fa84fb5ddb80c4d374a903bb3b6320715cbe67aaa26778974e896c82ccbf272715e6faf5a4f2e91b6cdb618faad7ad788a137b66703214735e5a13c8d525866e9d429ae883378c7991e6361e30953534b57243b49b5b9f4af40b59ace6e588323e405cd70ff00137c1cda3eab16b56680c191955edeb543ed533cb1cd6f211b46700f5cd6cd5c76b1d1eb1a8dc69933c16f92b9ce6934ef184e932a4a0e31824d64ddeaecf6d9910f98dde916d05dc0b2a0f9d874153ca88949ec7a1473fdac23a0ddb875a73e9cd237cc41cf4f6ae6f43bf7b050b21200fef76aeced2fedee615903a907bd4dacccdab9873e98c240a1377bd4b0e9eaae4edea3d2b7e25491c900114f58133c8c54b6f621233ad6c01500ae4ff2aa979a6ed9812b802ba78e248f04557bb404138193506e8e74db06538a8bca31906b4e44f2813eb54662cc46de94ac3b962daf9a338cf22b4aceefcd7258d6229c4b9eb56a1ba553918c1a7604cd6b901cfcb8c1efde990df4b66df78ba8fe135143741d80a95c29639028b0da08b5642482369cd5a1768f8c90477ac696db6bb30efd2aa4293198e7257deab730699aba9dfc76f1b11c8c741deb97bd375aeca6303642c31b47515d09804a841000fe74e82de3b72c5623d319a366438df732f4cf095b69823da80b8e77574ba6444dcf4c718e3bd16d66d23a93950477ae874ad30c643119c9a973772a30491bba4c58451d480320d59bbd39b47d6744d5917114778824c76048c568e8fa51728dd41c53fe225d43a6784ee0bb88da2292827d430ad693be8ccab349173e245c993c47a85be59518e78ec700915e4bab19eff505b1b046b99ee0ec8e3c673d39af46f18eb70ea9aa5ddcdbb8b84223da579dce50715d4fc34f83d710cb16bf753343724ee8d71f76b7946ccf1a7376b12fc1ef87ba97c36b0679151ae6e72f282a4727b7eb5e88daede051e658a1c0c1239ad696e75c40a18dbdc28fe271cd559aff0050001974d89c7aaf153638db32e7d760db9934f6f7c0afcebfdb97508750f8e6de4a145874bb68c823a1f9db1ff8f7eb5fa3736ad6e9febb4d953ae4af23a1afcd2fdb475086fbf686f102c0195218ad530ddbf708dffb3576e17e339ab3f76c787514515eb9c27f4a2face976d66f717571145022ee679463ff00d75c4c3653fc55b912edfecef0ba487e5076cb7647424765ff001ae5740d1af7c7d7a9a8ea73b1d2626db6f639e188eacdfa57a644d2dba2c71c4a63418000c63f2af9c3d477366d7c3567616e915a466d634180917dd148da54c092974d9f46aa716a92c64028e83d8e7357135d880f99883fed0a62d485f4fbf1cacc9281d8d666bfa75c5e681a8412438dd0970c3d57a56d49a9c12a8c2a39f6254d2c72c72c6e863601d4afdecf5a341ab9f18fc40be371ae18d7fd5c2a141fc39fd735e6be2cd4c345e529e54f205777f11b363e28d5e2624086675e7b62bc87539deee4720f5fe55c751dd9f43875a231ef65dc7ae493597a8ebb1698c14483a734ed48bc05ceece2bcfbc457acde6972411d2b38ab9dd276573bbd3bc68978d2c248da57b9ae63c4ec82f3ed2a09217b579849e209eca62d1b631ea6b760f141beb655964c332edf9aba230b32615adb9bd1ea89e26d325d38a001c1f9dbf848af31fb3c9e19d724d3ef4373ca39e8c3dab49f53baf0edf171968b702cbed5bfaa8b1f1ae8bb1dd5261f34370bd50fa1f6e056eb43a39d3571b15b5a4f68caa577100f34c82c4438556db9af3fb9d4b56f0add35b5e464a260f9a832ac3d6ba3d2fc590ddc6ac65538c1a5608c93dcdf962d9be2978602b36cb549f4b9157395248193c0ad8328bf83ce5c3123a8ae6bc5508b7d3d6712046dd81cf7ff00228488ab68aba3d0748d5cac7872371c723bd6ff00da86c1ea6bcbfc27ae7db6185091951d7d6bb58af0b8f9c903a0c56538d99cf09267410dc994002899b2064e2b2edee9554f2723d6a4130d8096eb591d17167dbd09eb596cf8320ec2ae4d282060f7ac7b9b808ec33cb138aab10d88d72cac71d2a4f34ef50a7e5acf57dee771c0ed5296dbca7205162548d349d9724355eb7baf94966ac08ae834639e7d2adc12ef18079a5635e6e86bb4a24c61b1f4a8fcf0ac3270338e7d6a9216ddd0f7c37a1ad0b6b17b8d8a30ec7934c65b817701dcd5db6b63336d0b9a9b4dd1a5542cebd4d753a4683b93207cc71c52d44cada7e9c0b01b73802badd2b4959020dbd2ade93e1ec105c6335d7697a34502ee2b9a8688e6338ac5a5d83371b857cfbfb4c78bee2dbc0b76b16e334ceb1285f73ffeaaf6df16df3dcdcbc36ca4c680723a1af1af89ba726a96a2de64caee0c0fa104106b6a6d44e5ab1753447b47c1ff0085d7ab0e8dad5f5bf9cb25a4323c2e7a3041d6be878f58b58e3447d3e48d71d17a0af917c3ff00b45f8874ab2b2b25b759fc8458813c6e038afa07e127c54b4f89113dbcc9f62d5230730b1fbc3d47e5fad6fce99e5d5c354846eceeff00b57497386df1e7d4535a7d364e22bc1ff02a9e6d2d980f9239063a8c7359d71a32b0e6dff11423cf1d35947281b6e22907719afca2fdb1323f690f19838f965b75c0ff0066da253fa823f0afd47b9d1635185122331c6573eb5f945fb4fcbe6fc7ff001b1c962b7db371ef855aeec22f7d9cd5b63cbe8a28af54e33fa1bf0cc9602344563080000a06315d5456c98063b8041f7ae7fc2135aceaaade4b31ea1c62bb15d22de41910a8c7428d5f387ad748845a5c0c10e920f7a8a4b790e7300fc2afa694003b24923fad02c6e909d93071fed8a0399192d1438c346c86b2b5dd4ad740d2ae75196e0a470296c13d5bb0aea5d6ed41dd0a483d457ccffb45fc4137776742b3dd12c7c4d8e9938ff03594e5ca74d187b476478e7c4bd6df54d4ee6f33f3deb994fb035e7ec9860b8e077ab9e2cd6313c1167e644da4564c37e246539e2b91c9bd4f729c79743235cb52dbb03debcbbc591b1dc554e3a135ed5791472a9c8cd709e23d084a65545c2f5fad38b3aa71ba3c32fad4c8cfc74a82398c322a67a735d4eb9a2c96970ef1a90a07cc08eb5cbdd580994fcc5589fbc2bba124cf3649c59a82e23d4e0c49b4b8e083deb06596f3c3f712b4049b77c064ea2a321ed9c00c73fed56b45729776c62942b67804568d6a3552c8b363e218b538d639b64e02e0abaf18aa379e15b4998cda6968598e7cbed9f6aa8fe1f78e6cc2e57be056e68915d5b9463b94ab0cae3ad348d635ac6df83b56bbd274ad5ec25d2d27b9ba8512099c73132b751f507f4ae4fe23d9ea134c1c46121249d9fed6066bd7a2d5631a62148c7da01ea56b90d688f9a4ba1bda4270a68d8752a73a3ce3c1778d6714715cfc8e09e57d6bd2ecf51568142e4e3924d79f3db4715e654e01626ba7d2d9988507e56181594f539a2dc59d84172261c1e31522cec5c2ff000fad52d36121b0780055846f3262074535ccd6a77c6775a96646ebe9591752e19978f9bbd695c44481f2eefe9542e6cd9806dbf4c5522272d4cf46e0ae7047af7a62bbb90e1b2a3238abd0e94ee54bfcc5880a2ba9d03c1e2e9954c242eee4e38ad544cb9ac735a758cf76a1d00604fa56eda69339382801f6af47d1fc0320d8822c2027a0aec34cf84ff69c3a023ea3ad69ecee2f6d63c7edfc313160d8254d75ba4f856575409192c6bd8f48f852d1a80e9f98ae8a3f87c2dca1550bb7dbad274ec355ee78f8f0eb47e5c7b09c75c5761e1ef0d282a48c71ce457751785122f999013ee2ae0b04b65f954640ed59f28fdadce7d3490808dbf77a551be94889a18ce19b8c8ed5adaaead15adb4b2020150493e83bd78ceabf1ab4986f66b4b43e65c0fba339e69591a46f23d06e61b1d2ed375dbc683f89dcf5cd79eeab0f86bc4172e9e6f9c549c60e07bd792f8bbc63acf8a6f0acd33c70024ed0719f4a8acae53c31a44972ff00eba4e13273cf7fe951269687a7470edea6f6bfe3cf0cf84af3c8b5b28a79d09524e588af75fd9d20b2f89cf67e23d2e06b1b985dadeea1230197b30fc8d7c602c24d53512df7aeee1f6a71dcd7e82fecf9f0e23f03f802ce0875216d7f2af992e3fbc7b1fd6953d599666951a367bb3d22e7c257b0bee43347c7f0366b3dadf56b5047da2518e9b9335a725c789ed18082fe0ba8c0fe2a6b78bbc47683336951dc28ee8d5d27c5ee614bab6b10f05a390020e0a73d6bf23ff686b97bcf8e1e379640049fda932301d8ae011f9822bf6107c40b77900bdd16588ee19f973fcabf1afe35ea716b3f18bc737d002b0dc6b7792203fdd33363f4af4308b56ce3afa238ca28a2bd3390fdfed28cba75e9c3e03364022bd06c2f2578558a03ee0e2b8c9edc5bdd08e7521a37643ec41fff0055763a4a41242823976b63a578135667ab1b33522d5194807cc5356d756c01ba407fde1550594a5c10eae3b034b2ab468ed2a031282c48ec0543d0ae54f631be2278dedbc21e1ab9bd660252856300f535f106b5a8cdacea735dcf233bcae5c96ed9af46f8dfe3d1e25d71ed2de422d2dd880a0f07a7f857953de283960304f02bceab3e6763dcc35154e373cb7c6d7b25b789668dfe51b4119f4355acb515c2658673d299f182411eb16d74a30ad1ed66f71ffebae320d7fcb2b8edd69a8dd1bf3f2c8f4e9355568fe5c1c5675e5ca4f1923258f4c573567aef98c3270a7bd6c5ac9f68da076a949a3ba3352461eb5a61bc89be5c76e4579beb7a3b5add1408471b87bd7b5cb6cad807be6b95d63488ca0271e628c64d6d17633a9052573c46fa3c4cfc135159dc013ac654807a9f4aecf5af0e86909dc148ce07ad72b3691245212b96607b575c6499e6ca0d33a9b36844499203fbd6b5bc285977100f5e2b8b865963c3f270318ad8b4d4a4f2c6d90120726af98948ea6e3514b640839fe95cfea4df6d70436eeb8aaeb70cf312e721a9cee634c819c52b94cc2bab555b85439c9cf4ed5b5a3c3b0819395f5a87ca2ec242304d741a3690d20329076e38c0a87a92b7352d95b9c8e4568db59eeda4a104f5ab7a7787e7925f30a945639e7d2bacb3d0c2ae5c640e86a394d53b1cc0b10070b91df8a9134a138f997083a1aeaa5d3d032e1768c1edd6ae68be189ae515d948049c0aa51072b987a378416ee653e5e547009e3aff00faabd4fc33e0e8d1507944e3039fe757bc37e1a5b6281d38aeeec6d16d5d0a2802ba22ac60d8fd17c2b182a42018c6722bb3b1d320b550bb5401ed59b6f7be5a2e1714e9354676007154dd8cb73a02f0a11c2e0557b9ba8c03c0ac196f98ae4b702ab3df12a76b64e33d6b3723450b972ff5348d0f418ae7aff55326591800bd706b9cf1478bedb4d4769ee563dbd89ae4a1f1b69da93f9897ca5573900e2b16d1d74a8ca4c87e296bf25a785b597864daff00676119f563dabe43f0d1974fd4a3bd725a766cb9639cfa7f335ee9f14fc709ac5b3e9b01f9093961dfa57929b10f2ed45e98e4562dea7d053a0a11d4ea2c261aa4e8cca37e40207e3543c5b2196ffecc87f71177f527ff00d55d0f84b4a1a6e953dfcfd08f9437734cf087c3dd53e206a7982174b72e4bdc30e1077ac9ddb3d1a4e14e3cf37648dbfd9c3c1316bbe35b6d46fe079ec6d1f736d19e7b7f2afb8bed5e1eb8dabe68849e30cbb703b572bf0c3e1de95e07f0e456767246d2150cee40058fbd7592787c4c01f2629063ad74538d95cf86cd318b1356f1d88a4d22c9ce6d6ff6fbacb5149a4eb11aeeb7bf6957fba48351dd786e251c40c84f75cd545d0ae2005adef2785bd3b1ad8f16e2dd5e6b56818c90a4b85271b39240afc60f13ea3fdb1e24d5aff208b9bc9a504770d2311fa115fb21acde6b7a76917d70f78b224303c8778e4000927f2cd7e2e966624bfdf38ce3d70335e8e116e7257d428a28af44e43fa27f15471c3ad99623bade7c4aadd8e7ad6e684f673c6a36c4c47079c115ccdf9fb6e811c81be7b67209f5538c7f234dd12462570ca4f5e6bc3aa7a508f63d0869f0825959e3f70d915e55f1dbe208f0ae8ada5dadc6ebbb8520faa2f1fcf3fa5759af789e3f0de8b3df5c3858e35c8c1ea47415f177c46f1e4de25d6aeafee656dd21f9149e82bcfad51c51e9616839cb99987a85e192566762ee4925bd6b1ef2ff00c919183dce6a99d73870c72bef591abea31c8a0c4dc11f31cd71a5767bcdd958e73e20ce355b39c1009519422bc6e6bffb3cac85beee33f5af42f116a8d1c8d83b97b8cd798f8824f32669130bbba8f5aeea68f3aabb6c6ee91aeaab0463cd777a2ea48989370f604d789d85f081f693823f88f7ae8ec35d65db973c74c1aa9c3a974aad8f646bb12aefc804f4159d3fcec770ea6b95b5d758c218bee51d2b5a1d711e319c6eac2ccee552e1a8e94973f3aa8f97dab9fbed0c2e19540619c8c75ae84ea6a43287c16e82a8dd5da48cbf3671914d268ce4d5ce3aff004cf9b030b8ed5949a7496cbf313f78d75f7702cb2641acf92ccceec80f707a56b16ce7925733608f1d5703a64d6d5ae86f78ca00624a92001d6b5741f0d36a1302d1e707a62bd6f44f042c56a1dd76ee18daa39c56c918b6794d8f844481085c86e381e95ddf87fc2056154284023d2bbcd1fc296d00d889c039e95d7e99a12b468553807ae2b78c518393b9c4d9784bc931170781d08abe3406909558cfb0af4cb5d00b484b2020018e2b5ed3c350e06e4018d69ecc7cecf28d3bc132cf2832445557d6baed2bc2062180bc71818aefad74c82dfe521463d7bd171756f6edb7e518e98a5c960e76cc8834111aa8c73538815303238a8ae35a064f6e79acd86f99ae581ced6350dd8773624b85890f3cd08fbc02783d6aa0883c9dc83535f5cc5636b248c7855e9eb50ddc22b5b14f55be4b581de57d9181cb66bc73c65f18e2b3692d6c5f248dbe603deb2fe2af8db53d48bc36e8f05aa820e78cd783df5c4b0aca630d2dc64b04ce4b564d5f447a30872c7999daf88daebc5c774f3481bd01c559d2bc02d6da2dcbf9aeac1772924fbd56f8746eb5d8cbdfdb1b053864673f7c1fff00556f78af52be5812d6d0f96a32acc3a1e9584a125b9dd879c672f74e1d7c27a95e956085c9e3807a574da4f836dac36497a7328e912f249adaf02f87bc5de2b992d2c2dddd49da652b8502be95f873fb3ed8e8af15e6b4ff006dbd519031f2a9effd28516ceaaf8b8515ab3cabc0ff00062ebc6f7d136adff12ed1d0031c2460b67ffd42be93f0e7c3ed3bc37a6c765a745008117000c6e39eb9adc3e168644022740a0001718db50ffc22376a73113ec51aad42c7cae271f3aeed7d0a571e14454c9b73e80ad541a135b8cc72cd0ffc08915a4da3eb96d8d92cc14750791486f75887e59123917fdb5c1ad4f30cf09a9403315d071e9252ff006aea89c496d0ccbedc1ab126b72c676cfa6ee1ea951aeb5a748ffbd866808ee471408e57e2678861b6f873e2a9a5d35a068b4aba73203c0c44d935f8b2adb941e84f38f4afd93fda2357d3ed3e05f8fa78ee94b8d16e9141eb968ca81f89602bf1ad1b70cfd3f903fd6bd4c2af75b38abbd50ea28a2bbce63f7b3c19f16bc21e2fb2b9874bf11e9f78b345bc22cca189ff0074d51f889f15342f843e0ebdf116af70b0c76cb9890b7fad7c70a3d6bf2bbe13fc3e1e28f145a5b2ea8ba74d26762c77063925208c2023d6bebaf1be83a1789bc3f6fa2f8c04d26956d1b451a4d29dd1cdb3e5c1ea4e40fd2bc69c6ecf4232d0f0183f6e3f166abe2cbf7d7676b9f0d6a53e059b1c3dbae7e523f3aecaf3c6369afc42ead6e56585c7c8ead9c8ff00eb579c7c09f823078e3c11e3eb3f10e99f628fcc31d87882e7836ecac76e3d46319fc2bc96e6ef54f843e30d47c3775325e3d8ce51deddb31c8a402acbf50735855c373aba3bb0f8af672e567d206ff2492c5b8e003d6b36eb590b0c8ad9507a83581e15f16d8f896d239ad6552e06244cf2a6af6a48acaddc1f4ae074dc1ea7b2aaaa9aa38ef10ea2d1c92302594fdd15c75cdf79a37e71ea0d75fabd99f309c165c1ae56f3487ce40201ce47a574c1a473d4bb312e6ecbb290318cd245aa345221ddc03cd3e7b26889c8240acabb8664c9113106ba6e99c6ef16761a66bbbfe5693e53d2b56db559095c3115e6b6f7d24122af2847506b66cb5593077b6076c543a6ba171aad1df49ab49be320f20d69dbde2dcab60e48ae16c6e867e52ccc7a83dab6f4cbf68db18e18d64e1636556e741891d86d1d2baaf0df86daecc6ecbf78f7158da4bc52c8a5c85c75c8aedb4cf124361122aaa9dbdc534ac539dcf40f0ff856d6ca3599c2fddada9ae618ca2a100631815c08f19c9751a2478da33fad6fd94cd2c0923e01c0ab4c87a9dbe8964247576185f5aed2ce6b4b4b6c1655c1e6bca65f13c963100a428f5ae7b53f17df5ea3ac729c83c60d6c999d8f789bc61a65b1c060587bd509fe24dba12a9b58f6e7a57cf9f6ebdd4254837379a41c126ba1d2ec24b78633282d23360b66b4e7158f4e7f1d35d962a7009c6476a8e2d4e5b89c65c9fa9ac18f4c3040108c1639e2b560b731aa11d477a97312d19a7f33100b73572dad48604f7aa16b19970e4e07a9aedbc2fa0c9aa48a147ee8e32f8ae6949b6697b6e3745d167d46608aa421c65abba5f065825ba24f12cac3aee19adad334e8349b711a28c8ead8eb4971202c4e69a3195477d0f1cf8e3f0917c4fe10b84d123486f61cbaa85c6f1dc7e95f03789b4ad63c37ac4570b672c779692e5a1957e56f515fa9ef3053cd723e2ef03787bc456f24fa869b048fb8167db826a9593b9d10c55a3cb23e48b2d1750f1fe93a78d3d56ddda3f31e441ff8efe1cd7bf7c22fd9de1bcd252f7c443cc723e58cf7c773f5af61f09fc29d03c2f6ca34fb248832e471ea2ba1d2c048360006d62bc7b55c9a68e68d6941b71326dbc3b61e13d3e7874cb58e148d7700ab5059f8aa57b746b9b0494b71b90f35b9ae2910dc80482d0902bcdf4b3a8dac604770b20eb8619ac0c672737cd26766daee9927facb79ed4f7607229f1dfd9c99fb36afb0f60f5cc3eaf791ff00aeb249147f70d3a3d574c9ca89ed6484fa94e2a8c4eb9353d72203c8bab5b841eb8c9a7ffc25573167edba379b8ead180735ca791a6dc362daf8c4ddbe622a65b0d56119b6d47cdc7404e45161a3a23e28f0e5c90b756935a16eecbc0a46b0f0cea0a441a8a73d8915cb5cde6b900c4b6d15c67a92b8159d36ab68abfe95a4947ee621c5160b1e69fb77f87ed7c37fb2ff008cf51b4ba496465b7842a91c87b88c1fd2bf2288da480001938c7b1c7f4afd30fdbdb5bb04fd9e6ea1b79268fed3a95ac6d1313c80c5cfe5b2bf33f24a82c4eeef9ec7bd7ab855ee9c15fe2b0514515d8739e8b6dab5de9d7f677d6770f6d7d6920961947f0b0afad7e1cf8f741fda27c25359f88f5192dfc79a5afdbdcb308e2b98a323217d4e2be3b6732646d39c706961967b19a39ade668260369923386da782bf43fd2b8651b9d29e87d6bfb457ed3be133f0cb5df03f8555e2bc658e13751002229c1720fad7cb9aa7c39f17a78521f174fa35e4fa25cb84fb6b82589c00091d71c71c5741f037c0fe1ef1f78f6db48f116aff00d9637a49691b81e5dcce0938727b600fcebea1f8cbf1fbc3ff00072eefa195ff00b5b5c5b6482db4e5556b3b54fbb9083863dea35455cf86348d66eb42bd179613b433467ae783f515eb5e18f8cf6daa88adb575169747833a7dc73fe7f9d49f13bf678d6adbc3d6de37f0f79bac691aa859cda98bcbb98e491b92231fc3922bc32f20934fb99aceea1682785da392194152ac3ae476a89d35346d4eb4a9bd0fa6750513db24a18491372b22f208ac79ad378c83915e656f73e34f84f6d6575a869f77069375cc2b76374720c03f29edc30af47f0bf8fb40f15a05f356c6ece0182438049feed79d5294a1aa3d7a55e33f8b72bcda609010462a8dde8323b00806001d6bd06e34208aad8f948dc083c11552e34b4032a7b561ced1d5c89ea7985ef87f0e4ba807b60563dde9d2c0018c90d9c8af559ec23553bc726b0351d2a2756c13b89e2b68cdb39e54ae70f6ba83dbbe6463bb3d6ba3b7d76dc98d039c9e86ab6a1a107000f980e845615c6932447f76d871ce4f6adee9ad4c791a3d1acb5bc280b2f1ea3b56e699acef037b92bd89af1fb3d5aeecee1461402086c77ae9f4ed78c8143b04c75150f7d0b8be8cf64d12ff006cd1ed39426bd0a398bc5946c038af10f0ceb68b26d4f9db1d335e9ba0ea41ed914be598d2343a392337988cb124f4abba1e820c643a9dc5b0334cd2537cd1be462bd1b43d25658a2c8553b89e48e95b27a12cc2d3bc210c372b3104b7a0ad6feca8c82154b6c20d75f06976911669ae1100e7ad675cf8abc3ba3970f22c8e3d0f5a5269072b7b12da69725c88888b838e4d4b75696ba6499b9913703c460f26b99bcf89d73aaca2db49b53b4f1bb15db7c37f861a878b35482e757dcc83e60b5cdcf7762f97955e447e18f0ecde27bf59595a0b156e846335edda6595b69d6b1c30a0554181eb49ac68b0e869690dac2521c1e42f4e94eb58da5438cb56a958e39d4bbb215df0339aa72be7a55c96dd97ef022a9489b2a8e72a499279aa1ac02da65d00327cb247d722b464e7a553be52d6b3ae3928d8a9651dbe8d379da7593e721a2539fc2ab69bf289c63e612b714cf0bcbe678734f61ce2251fa9a9acf097772bd3f784fe945807eac3cc5191c321e7f0af3bd3aea68216592d03e1c8057ae335e9578b98a23db6115c0d96a50c52cc92c4e8048402050c4c72ded931fdf2cb0b1f6e29c20b3bb6c25da11d83d6843258dc92a26419ece29b368104c788e3917d50e0d17332a3f85565036c51ca0f746e6a07f0b4f067cb6b8833d36938abc3c3c63198e49a0f4f9aa6826d5ec81f26f7cd03b483345c7731cc1ad5a0db15e7983d24e6abcd79ab42a4dc59c13a0ebb0735d29f115f46035d69b05c8ee5460d035fd2a7004f633daee3cede40a2e3be87c43ff051bf105b9f84de1fd3c5935adc5c6b2b29cff12a43203fac89f98afcf023d7af435f777fc152759b3b8bef8776363333a471df4d2c6dc1c930053ff8eb57c219ce09f4c57b3875682679d55de62d14515d26276664e0114c7948038cd48ed93803a53194b76ae536b90c837ca928251d08657538653ea0d757f0bbc7da7782fc7b16ade25d113c5ba7b42619a0ba3bdbd55973dfafe95cb3c471d3150980e1b1839f6e47e35361dcfbafe0d789f55f8a5e20bbf19da78974fb7f0e5c593db7f623b059f4d48c82a421fc7f2af3cd73e19683fb48fc5c975fb5b43a4f8474c4f2efb5371b3fb4a556e36fbe777e75f29595c5e6917ad75677535a4c4152d1395dc3b82075af7dfd9b3e33e896ba0dcf80fc7fa84d61a02cbf6cb1bf4eab293f323e3920fcbf91a9e5d4b4f43ddfc5b1699e38b6bb1adc4b1781743440b14ea032b46186d56ef918ce3d057c77f1c62f02dfde683aff008155acadefa274bcb056e60914e011f5193f857adfed8ff135a38f42f09f852eade5f06ddd88b94bbb47c9b83bb90de8463bfad7caf32ac48060228c0c0eb83fe7f5a4a3dcae6ec763e12f8bbad78644504ee751d3e3e0c537de03dabd83c3fe3bd23c5f185b3b8f26e4e09b790e1b9f4af08d4be1febd69e10d3fc57269b21d06f7212f23f995581c6d6f43c56046ed011240ed1baf2b246d823f1ac6a508cb5474d2c4ce0f53ea2d46de50d85f98ff00b55cfdd41730b1df191e98af36f0d7c61d6748f26daff1a9db2f1f3fdf1f435ea3a078db47f145befb69c473670d6f31c153ed5c32a2e3b1ead3af0a862cb2b27de438fa5526788b1c8183eb5df49671cea43c5d3fba3ad573a0db3152c8303d4560e7cba33a796e713f63b695d4a8527bd30e970895b00a9aeda4f085ac877463631ee074a6b783222c713396ee4f4a154b87b2ea73767a7cb6ff00bc89d973deba5d1352d46d76ac6eedb7d3bd09e119c2308ee4e07406af5a687a95a282b3a9fad3732d53b9a906b7adb483cb91c608206715d5daeb9e2c665781a5c10063938ac4d2b55d4b4f452d1c52e3a9295dde8fe3cd496352b65115f555153ed0bf669176c344f17ebb6ca669658d48ebd3ad74fa47c2858a547d4ae4b360643354da3f8875dd57620884511efd2bbef0b7866e757d42dd26632166048ce78159b9b93b12ed1573a9f871f0f2c9a44682d97ca4c65c8eb5ee5a0dac7a5dcdb24602e1b1f2fa564e99696ba15aadac4aaac00ce3bd695b4fb654909c60823f3aea845247915aa39b3aab3d37edf7e6170ac0120ab8aaba8f829967f3217f20a93c0e86b56c65fb3eb48ffc2e3767ea2ba49809060e01cf20d6f638dbd4f2ad4ac6e6d3779b6e5d40fbea335ceb98ae18f96e0b775ee2bd9eff004f8de3631b6d6c73e86bcebc47a2db492190406de7cfdf8c633525ad8e4a784c7d8e2aa5c7ccb81ee3f4ad3bab3ba863fdd4ab327fb5d6b0ee35136ae05ddb3c2b9fbe06452b0ee755e02067f0d5b03c18cb211f43571013aa5daed239047d2a87c3a7597469c231389db00d748f6cab781b1f33a531dcad74a5ade023fbc4572cba6442494491900b9e715d86a11f95a7c2de9274ae5a1d76f46a3750a241344aff0075fa8a960f6187c316d72a3ca99413d770a8bfe10eba89bf7129f721bad6c2eb503fcb75a59c0fe384d4f16a5a4499115ecf6ae3f8655e0523330ffb3f59b4e8cec3d19770a68d52fa16c4d6a92fb2ae0d7616f717fb57ecd7d6974a7a06201a5b8d6aeedd4adee82b3a8ff9690e0e69d80e3db5cb427135a4d11e990381407d3ae5c225daac8dc0593dc803f9d6fbeafe18be056e639f4f3ff4d14e334c6f0a68daa45bad6fe091872b9201e39fe94ed60b5cfcb9ff00829bdc463e2df85ace278dc47a1ace761cf324f2ff004415f1dd7d15ff000500b9cfed49e28d395c490e9515b5944ca720a8895cff00e3d2357ceb5edd25cb048f366ef261451456c41dd8b7118c939a43e5a75aa773784ae299e7823ae38ae4352ccf74870157a54066e09c62aabb658734e39500f5f6a00473e664e381556e2dfcc001e8791df9041a998921bb67b5182107a8aab0ca2d03aa88cbb796b9d8a4e42027381e9cd74df08b47f0f6b9f12b45b4f146a074cd204c1de765cab30230a7d01ac578cb2824735565b33223295183eb4985cfd06bfb3d134d8f537d46c068de10d3239162d3db06d35157194753d01c86ff00beabe24f01fc309be33789f5b5d026b5d1f4eb5dd7256e9f01232c4003f11fca8ff85b3e28ff00841eefc2b7b7efa968932a2ada5c9e63dac082add7a6457d2ff08352f865ad0b7f0af8274e861d575fb60752fed29361451feb111bd46323eb59977b9e2ba67c00161f087c63af78a251a55fda5c84d3dc9e2464ce71ea1b2b8fa5788dbccf949e3731ca392e87906bed4f8b5a54df147e2f786be14690930f0fe82a971a8b3fddf2d4e4076efc679ac3d67e127c3ff8bbe25f16f853c17a4be93ab68c8d38d5637cdbb301f70fd769c7e34b47b949db63c03c35f16f57d1e448af0fdbecd700e78703dabd7bc3fe34d17c52aa2cee825c1c66de53861fe35f3d2786b59b84b8921d32eaee2b695a0965b784ba875383c8aa0aed03b30df0cb136d27254ab0ae6a9878d4d8eca58a9c1ea7d7c96850a9c81f5a94c449c600fc2bc36c7c6fe35f87fa7d94de20d2ee27d2ae82b43713ae77a9e98615eb3e13f1fe89e30850d9dc797718c1b790e083ed5e5d4a33a6cf728e2a9d45aee6c1464e3a935326e002e39a42c1dc81d7a8e2a58902b825813dc7a573391df1b334f4bb612b00c06d3d6bbcd0f4bb7f282f978e9f8d70f6122c6477aecf4dd66308aa0e180e07ad67cc36ae7a56891dbdb42a140ce2bd63e165ee9fa3dc4b7d7caf280b88d17b9eff00d2bc3bc3b7725e3a26d2598850077cd7d6de0ef01da59e856424b66924640c495e86b6a51bbb9e662a5caac713a8f8a27bbf1134f6d0cbe433602609e2bb6b5964014382a719c115d258b68da7dd46adf668dc90a0119e7353f88b486fedd999970aca0a81d3a57a2ae78cddcd18a5e2d25f555ae88dcb31cfad73489b2cecfd40da7dab73398d08e411d6b426d72c198b0209ae63c456c5f63211c13915bdbf19acad506e53458bb58e32f615452369dc0e6b12f141dc182b023a62ba4d4130e6b02f13824516026f8789e4adfc44ff00cbc1603d88aea2e98adedafa142335ccf82180d57518b3cfcac07b735d35f83e759b76dccb9f7e2a4066a8e7fb2cfaacaa73e839af28d52d5dbc537c5249226254fca78ef5eb97b1799a55e0c72bb5bf535e57adc375178b2e0c126c0c8ac4119cf152c7d0b36d7baad9a811dcac98eceb569bc4732edfb5e9c92e7ab20a8a19efa200c90c73ae074e0d4e97f679cdc5b4d03773d45246635759d2277fdeac9667d4022b5ece42e57ec3ad1e7eea96cfe754a3b5d32ff3e5dd464ff76415149e06f3099230bcf781f14ee06c5c4dab8fbf0db6a03d180ac5bc9ed659825d68f25abb328dd037ab283fa13503e8fa969b2621ba9e303a6ec9cd32ff00c43ad695a75d5cceb0dc45042f26e61c8daa589fc87e947506ec8fc71fda73568f5cfda0bc7f7713b4917f6bcd0a331c9c2109ff00b2d798d68f88f579b5ff0010eaba9dc01e7deddcd73211fde7918ff51f9d6757d04748a3cb6eec28a28aa11d0ca59c1e9d38c53795c7d0531588241e6a5452d918ae4350196e738a7ed2d804fd29c6108aa739cf5f6a7285041cf1401188bf114e64f96a4c8541cd0cb94cfaf4a7702001b03b523034fe7bd19e3d6802bbc2ac326a18b7db4f1dc5acaf6f71193b26898ab2e473822acb37cc33c039a8d97cb1c0e290cf40f83bf1df59f855e39935bbf7975bb3d462169a94133664922008c86f5009fcebd9e6f8b3f0cfc13f08fc6bff000ae6f6e2db5ed564dff67becf9803e7203770327bf7af9526459060633e954e4b70cc032f6208f51dea5c7a8eecfb36f6f751f803f02bc13a2e8091cde28f135d24a6e768951bccc16cf6ef5e6bfb567c24b0b4f891e16b2d0e345d7b5bb1f3351b684e22126065c0ed924d617c2efda7bc43f0f740b6d16ff004cb4f13e9d65279b65f6b1996dbd949fa0fcabd13e196b5a0fed0bf187c61afdfeac3c39ae9b41fd971dd38f29772e1c0cf4da40fceb3b58d22ee667c09d57c45f15fc3baa7813c4b0c177e19d074f92291dd3e68e5563b36b7a8da7f4ae7be2b7c02d03c2fe1fbbd73c29ac4f6baae990c73dfe8d72fb6548dbaba1efd07e75f42fc1df85775f0b3c273f86b549126bed6356f9b5481f725d44d9c608fc7f3af13fdb42f342d5bc59a56b3a16a80cc217d1751b68ce194c4401b87bd4ee56db1e65e11f8e5a8e991243aac5f6fb55c7ef87dfdbeffa57b5f85bc5fa2f8c214934db8477fe2858e197f3af92840dfbff002c392a9bdcc6a4841c75c55bd3afee34aba696d657864dc30f175cf6e9d6b9ea6194eed1dd431d2a6d296c7da7115006176f3835b5a6b3472821735f3f7c38f8f48e21b0f1302368f92f97a1ff007abdff00c393c1a8247736d32dc5bc982b221c8af16ad09d27ef1f494b114eb2bc59ee1f033c3c75cf1969eaea1e2cef653db15f6d7893528fc3be11b995130c17ca4e7b9e95f34fecb9a5ab5e5e5d15198a30aacdea73c7e95ea3f11f5c792e63b0dd88810ce01ea7b7f2ae8a2ad1b9e3e35f348c8f0a696fab788ed518efc3ee627db9af7abdd263ba60e46582e2bcd7e0fe8ecf2bea122fdf3f2e7b019af5a3cb0e6bba2792dea725a969e6d6d90ff75b9ab76d207b287d42e2af78821dd62e40ce39acfd388366b8e704d532e2c593239acbbf3b94f35a9231604566dda1da49141a1cbea0bf31239ae7eec9191eb5d45ec3bb3dab9cbe8c2862393537023f07b6cf135caff7a11fa66bb1d44623b563c0137f3ae2bc2cdb3c5f129e3cd8580f72315dd6a8a3ecaadfc293a367db9148091933a56a23193e493f8835e4fe218ef53c431cd6c3e57846ee3a9af5e887fa25e211f7e1615c95cdaabdcdb1474491d70bbfbd4b2ba1ccdaebb3c0aa2e2d41dbfddeb57d354d3aeb05b740c7a875c8adff00ec5948cdc58acb8fe28f06aa4da4e9b264316b76ee1d7148868a1fd91637846cf2a56f50714d6d0248183c12cf030fee3568af8222b840f6f791b376dadcd42da16afa50016661bb81b9b3ba9dae2b0d4bad5ede3c0992e57d255e6b8bf8dbe351e1bf835e37d42fac122f2346b9d92af18765d8bfab81f8d75e352d52c48135a89704f6c1af9d3fe0a09e385b3fd97f5fb5585adae352b9b6b40d9ea3cc0ecbf9213f856b08de493226d289f920a77229230e5549ff00be47f5cd2d04609e3001c628af77d0f3028a28a00df16ccaf9078a915595b23906a50c06680ff281deb94d4661d9318c9a8cb10b8dbb8d4fe6ed61c77e6a320b64e3b9a4026fdc1576e0d06561f29fe1a3a1cf714d6393fce8006bacf18e698b37cb83c114e0aa08ef43286270280232c1947a8cd20978c1a0aedf6cd22a1ea471400061c9239ed4a1972091cd388040a43b47519a77022f2d793dfb545bf62ee405250080e8486eddea657f98fcbc52e1597d0d2b148efbe1a7c7cf157c3b75b537726b3a26ecb58de316319c8c346dd430ed5c0f897556f11789756d566662f7f72d70d9e08c9ea47ad34dbb1e56a1680c8e109da41eb4ac55cfa07f651f105be9d6fe21d1a7f0cc5aa9d47f750dfbc5e66c66c831483b023bfb5278c7e0768fe06fdac3c1de17d1e42fa6ea1736d3c966e770873f3491e7b8f4fc6bca7e1d7c4cf127c23d7a5d53c3b72124986268251ba393d323db9fcebd6bc0bf1cbc39e32fda17c3be34f1b593e95796f0ac62e2ddbf74b2a03b588fc693124799fc73f0be9fe19f8bbe27d2f448248f498afbcb8f7a9f2d2460015ddd07356bc01f12f5df833afbd8dd41235b427173a5cddf383b973ec78afa50eabe1ed6fc0df1ae755b2f1121d4c6a36f1a60cac8fb7e641d7820fe55e6bfb4df80f53f147c5ed3df41d324bf97fe119b5bb2912e24d8ab8248ef8ce3d6a270535691bd394a9be68b3ef3fd99fe28681e24f0547a9e857697264ff5b086fdec471f75875e39aeea7bd935ad4300ee9657da39cf19e3fad7e417c2bf891adfc25f1847ad68d24b6f3c736cbbb22481228fbe857b1afd71fd9e75cd2fe2d695a4f8974b7074db88c381ff003ce4fe243ee0d70ce8a86c747b473d647d27e0ad1c693a4c51e3a2819f5ff39ae817a73d6996d108204403802a4a71564733656d4537d9cab8ce50d636929e6c2ea3f84d6fcabb9187fb26b034099639a78d8e0eee07e755b97163e78197a0cd675e0cc6477ae9a4895d6b1efed400702a4d53b9cade28c1ae6efe3c6ee2ba8bd8f93c573fa82658e2a59461e94c60f17694dd03332e7eb8aefb5b0534e978e3727fe855c039f235dd1a43c0172013f5af5892d526b6b92e372ac64e3e86901069f0994c8ac301a36c66bcd3c5d790d9b698664668f7b292bd41e2bd5f4e5dd34631c14fe95e4fe3c31c515a1930116e4824f6352ca44ba5eb16e1d3ecda9c96affdd724ff003ae93fb67517554952d35288ff007d4027f1ae122b5b6bc4f90a383d452b68f716c37dbcb2c1f4248a4896768d2690fbbced3ae2c1fbc96cc702b6fc2ba6d8ea97a221a9c97f06d244138c3023d0d79ddbeb1acd828cbc770bfdd938aebfe1e78a05f788adede5d396de6656fde277e95b53dc89ec76f79e0b5196b49da2efb241b97e95f9cbff000576d5dfc37e11f02785bc98e29352beb8d42578cfde58115578f73311f857ea112146589f979fd2bf1abfe0af3e3f8bc4dfb49699e1e824de9e1dd1a28a551fc334ced230faecf28fe55e8538ae6b9c0e4ec7c359c93fe7bd14515da60145145033a761b8039cd376e08ce7f0a780492074a490155e9cd729b5868625f04ee1fca9920cb70dc548a42ed3dcf5a8c9c9f4a42005b632e3af7a454d800ea689186debf95310918383f8d03b132609208a1ca8c638a8f0e64185241f4a9becace7ee91f5a571103ed623bd23150303826ac0b5e1bd476a5364ac9f375f5f4a60541c5358172369e9564da024007a7eb40806476a00aec49c03c1f6a6954607049357638d55893c8f5a8652884718cd0522b925547073da9d96550c4673d3daa676508095e0f7a72aa151938a06578c094b60f3de8683042b7218fafeb5692dd6304e46d6147d9d4b021f2b8e05160b92685aa5ff0087753fb669174f6772800ca9f96419070c3b838afa8be1afc7eb6f88ff0012743b9d4923d0bc48ba35ce92d221c4573215fdd60f6395fd6be5b788718254fb524511f31492eac8c1d1d4e0861d083ed52f42d33de3e3f7c28d4a4f14b6bfa7698573a2db5feb51a0dbb2762c85940eb9f2f9c57a4fec05f1d2e7e1ef8c5bc1734a5b45d7a7060539c413e79dbec723f2acff0080bf112ffc67a5ea91eaf7a2f75881adedd12600996cd164ca91dc7cd5f447ec9ffb1be91a9fc50baf19979068368c26b0808fbb21c971f4071594acc2e7e89c64bc48c7ab004fd7029d834a89e522a6490aa00cd29ac1a1a187904573291887559d07079ae9c1dac78eb5837384d6a4c8c64e693d0b8bd6c4f15d98d76bf51de99712892227ad45787b815464bac26dcd6573a1232f51c2b1ed5cddef24d6fea0fb98935897084eee29176398d61845358ca4e365d213ec2bd9238cbdb48b8fbf130ffc76bc6fc4918fb03be3ee32b7ea2bda34cc4f6b038e8f0673ebf2d344bd083489018ed1bf88a8fe58af31f1f2c291309b2aa97796e338af46d1370b6b527ae71fa9ae4bc61a7c775757c922e63f3b26a64813397b1d1acef543452a609e003b4d6c1f0ddf5b441a09e42a7a2b722a21e138258ff72fe5bfb9e952a691ade90b9b7b8771e99dc2a509b1860bb0c167b449fdd0735ade115b5b7f11dab18e5865c9015874a82dfc4f7f6eb8bcd3d25c752a306b7b40f10e977da8c086de4b79d8e1430ea69a76259c56aff1cafbc35e34bfb3b81e6d947301cf65cfcdff008ee4fe15f8affb537c471f167f684f1df8a14ee86ef52912161d1a28f08847b616bf4c7f685f14dbf84742f1d6b770446d650ceca58f25c8da83f12d5f8f4ce657691892ce4b313d771e4fea4d7a9866e49b671d54a2145145771ca828a28a067551fdea49bbd145721ba223d0547276a28a04c45e86a63f7051450344f0fde4ad193a2d1454b13294bf7cd467a8a28aa4210fdffc2a3ee3f1a28a0047fb82ab49d68a28290b2ffa9a8dba5145032c27faaa54eab4514012ff00cb41569feead1452607adfecc1ff0025323ffaf793f957ec1fecafff0024bacfeadfce8a2b09148f677e8bf4a6d1456452d86b751581a8ff00c864ff00ba28a2a645c7712f2b1a5ea68a2b13a519b79dbf1acd9fa1a28a0b39af12ff00c82ee3f0fe75ec3e1dff00905587fd705ffd04d14535b912d8ada4ff00c7b5b7fbe7f9d737e25ff8fcd43feba7f85145296e2440ff00ea87d7fc2b674cfba7e828a2a109916adf74d62e9bff0021dd3ffeba1a28a047c5dfb7bffc927f1d7fd7d27fe862bf2e0fdf6fa9fe74515eae17e1670d7f890514515dc7385145140cffd9, 'image/jpeg', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vacaciones`
--

CREATE TABLE `vacaciones` (
  `id` int(11) NOT NULL,
  `id_empleado` int(11) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `estado` varchar(20) DEFAULT 'En curso',
  `motivo` varchar(255) DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `auditoria_empleados`
--
ALTER TABLE `auditoria_empleados`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `empleado_id` (`empleado_id`);

--
-- Indices de la tabla `auditoria_inventario`
--
ALTER TABLE `auditoria_inventario`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `cfg_sis`
--
ALTER TABLE `cfg_sis`
  ADD PRIMARY KEY (`id_cfg`),
  ADD KEY `fk_cfg_usu` (`id_usu_mod`);

--
-- Indices de la tabla `cli`
--
ALTER TABLE `cli`
  ADD PRIMARY KEY (`idcli`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_cli_empleado` (`empleado_registro`),
  ADD KEY `idx_cli_fecha` (`fecha_registro`);

--
-- Indices de la tabla `configuracion_inventario`
--
ALTER TABLE `configuracion_inventario`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `detped`
--
ALTER TABLE `detped`
  ADD PRIMARY KEY (`iddetped`),
  ADD KEY `fk_detped_ped` (`idped`),
  ADD KEY `fk_detped_tflor` (`idtflor`);

--
-- Indices de la tabla `empresa`
--
ALTER TABLE `empresa`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ent`
--
ALTER TABLE `ent`
  ADD PRIMARY KEY (`ident`),
  ADD KEY `fk_ent_ped` (`ped_idped`);

--
-- Indices de la tabla `histenvfac`
--
ALTER TABLE `histenvfac`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pedido` (`idpedido`);

--
-- Indices de la tabla `inv`
--
ALTER TABLE `inv`
  ADD PRIMARY KEY (`idinv`),
  ADD KEY `fk_inv_tflor` (`tflor_idtflor`),
  ADD KEY `idx_inv_empleado` (`empleado_id`);

--
-- Indices de la tabla `inv_historial`
--
ALTER TABLE `inv_historial`
  ADD PRIMARY KEY (`idhistorial`),
  ADD KEY `fk_invhist_inv` (`idinv`),
  ADD KEY `fk_invhist_usu` (`idusu`);

--
-- Indices de la tabla `lotes`
--
ALTER TABLE `lotes`
  ADD PRIMARY KEY (`idlote`),
  ADD UNIQUE KEY `unique_lote` (`inv_idinv`,`numero_lote`),
  ADD KEY `idx_fecha_caducidad` (`fecha_caducidad`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_inv_idinv` (`inv_idinv`);

--
-- Indices de la tabla `pag`
--
ALTER TABLE `pag`
  ADD PRIMARY KEY (`idpag`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`idpago`),
  ADD KEY `fk_pagos_ped` (`ped_idped`),
  ADD KEY `idx_pagos_fecha_estado` (`fecha_pago`,`estado_pag`);

--
-- Indices de la tabla `ped`
--
ALTER TABLE `ped`
  ADD PRIMARY KEY (`idped`),
  ADD UNIQUE KEY `numped` (`numped`),
  ADD KEY `fk_ped_cli` (`cli_idcli`),
  ADD KEY `idx_ped_empleado` (`empleado_id`),
  ADD KEY `idx_ped_estado` (`estado`),
  ADD KEY `idx_ped_fecha` (`fecha_pedido`);

--
-- Indices de la tabla `perf`
--
ALTER TABLE `perf`
  ADD PRIMARY KEY (`idperf`);

--
-- Indices de la tabla `perf_perm`
--
ALTER TABLE `perf_perm`
  ADD PRIMARY KEY (`idperf`,`idperm`),
  ADD KEY `fk_perfperm_perf` (`idperf`),
  ADD KEY `fk_perfperm_perm` (`idperm`);

--
-- Indices de la tabla `perm`
--
ALTER TABLE `perm`
  ADD PRIMARY KEY (`idperm`),
  ADD UNIQUE KEY `codigo` (`codigo`);

--
-- Indices de la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD PRIMARY KEY (`idpermiso`),
  ADD KEY `idempleado` (`idempleado`);

--
-- Indices de la tabla `perm_tpusu`
--
ALTER TABLE `perm_tpusu`
  ADD PRIMARY KEY (`idtpusu`,`idperm`),
  ADD KEY `fk_perm_tpusu` (`idtpusu`),
  ADD KEY `fk_perm_perm` (`idperm`);

--
-- Indices de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `proveedor_producto`
--
ALTER TABLE `proveedor_producto`
  ADD PRIMARY KEY (`id`),
  ADD KEY `proveedor_id` (`proveedor_id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indices de la tabla `proyecciones_pagos`
--
ALTER TABLE `proyecciones_pagos`
  ADD PRIMARY KEY (`idproy`),
  ADD KEY `idx_proy_fechas` (`fecha_inicio`,`fecha_fin`),
  ADD KEY `idx_proy_creado_por` (`creado_por`);

--
-- Indices de la tabla `pxp`
--
ALTER TABLE `pxp`
  ADD PRIMARY KEY (`idperf`,`idpag`),
  ADD KEY `fk_pxp_perf` (`idperf`),
  ADD KEY `fk_pxp_pag` (`idpag`);

--
-- Indices de la tabla `tflor`
--
ALTER TABLE `tflor`
  ADD PRIMARY KEY (`idtflor`);

--
-- Indices de la tabla `tickets_soporte`
--
ALTER TABLE `tickets_soporte`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_admin` (`admin_id`),
  ADD KEY `idx_estado` (`estado`);

--
-- Indices de la tabla `tokens_recuperacion`
--
ALTER TABLE `tokens_recuperacion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idUsuario` (`idUsuario`),
  ADD KEY `idx_token` (`token`),
  ADD KEY `idx_expiracion` (`expiracion`);

--
-- Indices de la tabla `tpusu`
--
ALTER TABLE `tpusu`
  ADD PRIMARY KEY (`idtpusu`);

--
-- Indices de la tabla `turnos`
--
ALTER TABLE `turnos`
  ADD PRIMARY KEY (`idturno`),
  ADD KEY `idempleado` (`idempleado`);

--
-- Indices de la tabla `usu`
--
ALTER TABLE `usu`
  ADD PRIMARY KEY (`idusu`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_usu_tpusu` (`tpusu_idtpusu`),
  ADD KEY `idx_usu_ultimo_acceso` (`fecha_ultimo_acceso`);

--
-- Indices de la tabla `vacaciones`
--
ALTER TABLE `vacaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_empleado` (`id_empleado`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `auditoria_empleados`
--
ALTER TABLE `auditoria_empleados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `auditoria_inventario`
--
ALTER TABLE `auditoria_inventario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT de la tabla `cfg_sis`
--
ALTER TABLE `cfg_sis`
  MODIFY `id_cfg` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `cli`
--
ALTER TABLE `cli`
  MODIFY `idcli` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `configuracion_inventario`
--
ALTER TABLE `configuracion_inventario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `detped`
--
ALTER TABLE `detped`
  MODIFY `iddetped` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT de la tabla `empresa`
--
ALTER TABLE `empresa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `ent`
--
ALTER TABLE `ent`
  MODIFY `ident` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `histenvfac`
--
ALTER TABLE `histenvfac`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `inv`
--
ALTER TABLE `inv`
  MODIFY `idinv` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT de la tabla `inv_historial`
--
ALTER TABLE `inv_historial`
  MODIFY `idhistorial` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT de la tabla `lotes`
--
ALTER TABLE `lotes`
  MODIFY `idlote` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `pag`
--
ALTER TABLE `pag`
  MODIFY `idpag` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `idpago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT de la tabla `ped`
--
ALTER TABLE `ped`
  MODIFY `idped` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT de la tabla `perf`
--
ALTER TABLE `perf`
  MODIFY `idperf` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `perm`
--
ALTER TABLE `perm`
  MODIFY `idperm` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `idpermiso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `proveedor_producto`
--
ALTER TABLE `proveedor_producto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `proyecciones_pagos`
--
ALTER TABLE `proyecciones_pagos`
  MODIFY `idproy` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `tflor`
--
ALTER TABLE `tflor`
  MODIFY `idtflor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT de la tabla `tickets_soporte`
--
ALTER TABLE `tickets_soporte`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `tokens_recuperacion`
--
ALTER TABLE `tokens_recuperacion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `tpusu`
--
ALTER TABLE `tpusu`
  MODIFY `idtpusu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `turnos`
--
ALTER TABLE `turnos`
  MODIFY `idturno` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `usu`
--
ALTER TABLE `usu`
  MODIFY `idusu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT de la tabla `vacaciones`
--
ALTER TABLE `vacaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `auditoria_empleados`
--
ALTER TABLE `auditoria_empleados`
  ADD CONSTRAINT `auditoria_empleados_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usu` (`idusu`),
  ADD CONSTRAINT `auditoria_empleados_ibfk_2` FOREIGN KEY (`empleado_id`) REFERENCES `usu` (`idusu`);

--
-- Filtros para la tabla `auditoria_inventario`
--
ALTER TABLE `auditoria_inventario`
  ADD CONSTRAINT `auditoria_inventario_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usu` (`idusu`) ON DELETE SET NULL;

--
-- Filtros para la tabla `cfg_sis`
--
ALTER TABLE `cfg_sis`
  ADD CONSTRAINT `cfg_sis_ibfk_1` FOREIGN KEY (`id_usu_mod`) REFERENCES `usu` (`idusu`);

--
-- Filtros para la tabla `detped`
--
ALTER TABLE `detped`
  ADD CONSTRAINT `detped_ibfk_1` FOREIGN KEY (`idped`) REFERENCES `ped` (`idped`),
  ADD CONSTRAINT `detped_ibfk_2` FOREIGN KEY (`idtflor`) REFERENCES `tflor` (`idtflor`);

--
-- Filtros para la tabla `ent`
--
ALTER TABLE `ent`
  ADD CONSTRAINT `ent_ibfk_1` FOREIGN KEY (`ped_idped`) REFERENCES `ped` (`idped`);

--
-- Filtros para la tabla `histenvfac`
--
ALTER TABLE `histenvfac`
  ADD CONSTRAINT `histenvfac_ibfk_1` FOREIGN KEY (`idpedido`) REFERENCES `ped` (`idped`) ON DELETE CASCADE;

--
-- Filtros para la tabla `inv`
--
ALTER TABLE `inv`
  ADD CONSTRAINT `inv_ibfk_1` FOREIGN KEY (`tflor_idtflor`) REFERENCES `tflor` (`idtflor`);

--
-- Filtros para la tabla `inv_historial`
--
ALTER TABLE `inv_historial`
  ADD CONSTRAINT `inv_historial_ibfk_1` FOREIGN KEY (`idinv`) REFERENCES `inv` (`idinv`),
  ADD CONSTRAINT `inv_historial_ibfk_2` FOREIGN KEY (`idusu`) REFERENCES `usu` (`idusu`);

--
-- Filtros para la tabla `lotes`
--
ALTER TABLE `lotes`
  ADD CONSTRAINT `lotes_ibfk_1` FOREIGN KEY (`inv_idinv`) REFERENCES `inv` (`idinv`) ON DELETE CASCADE;

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`ped_idped`) REFERENCES `ped` (`idped`);

--
-- Filtros para la tabla `ped`
--
ALTER TABLE `ped`
  ADD CONSTRAINT `ped_ibfk_1` FOREIGN KEY (`cli_idcli`) REFERENCES `cli` (`idcli`);

--
-- Filtros para la tabla `perf_perm`
--
ALTER TABLE `perf_perm`
  ADD CONSTRAINT `perf_perm_ibfk_1` FOREIGN KEY (`idperf`) REFERENCES `perf` (`idperf`),
  ADD CONSTRAINT `perf_perm_ibfk_2` FOREIGN KEY (`idperm`) REFERENCES `perm` (`idperm`);

--
-- Filtros para la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD CONSTRAINT `permisos_ibfk_1` FOREIGN KEY (`idempleado`) REFERENCES `usu` (`idusu`) ON DELETE CASCADE;

--
-- Filtros para la tabla `perm_tpusu`
--
ALTER TABLE `perm_tpusu`
  ADD CONSTRAINT `perm_tpusu_ibfk_1` FOREIGN KEY (`idtpusu`) REFERENCES `tpusu` (`idtpusu`),
  ADD CONSTRAINT `perm_tpusu_ibfk_2` FOREIGN KEY (`idperm`) REFERENCES `perm` (`idperm`);

--
-- Filtros para la tabla `proveedor_producto`
--
ALTER TABLE `proveedor_producto`
  ADD CONSTRAINT `proveedor_producto_ibfk_1` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `proveedor_producto_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `inv` (`idinv`) ON DELETE CASCADE;

--
-- Filtros para la tabla `proyecciones_pagos`
--
ALTER TABLE `proyecciones_pagos`
  ADD CONSTRAINT `proyecciones_pagos_ibfk_1` FOREIGN KEY (`creado_por`) REFERENCES `usu` (`idusu`) ON DELETE SET NULL;

--
-- Filtros para la tabla `pxp`
--
ALTER TABLE `pxp`
  ADD CONSTRAINT `pxp_ibfk_1` FOREIGN KEY (`idperf`) REFERENCES `perf` (`idperf`),
  ADD CONSTRAINT `pxp_ibfk_2` FOREIGN KEY (`idpag`) REFERENCES `pag` (`idpag`);

--
-- Filtros para la tabla `tickets_soporte`
--
ALTER TABLE `tickets_soporte`
  ADD CONSTRAINT `tickets_soporte_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `usu` (`idusu`) ON DELETE CASCADE;

--
-- Filtros para la tabla `tokens_recuperacion`
--
ALTER TABLE `tokens_recuperacion`
  ADD CONSTRAINT `tokens_recuperacion_ibfk_1` FOREIGN KEY (`idUsuario`) REFERENCES `usu` (`idusu`) ON DELETE CASCADE;

--
-- Filtros para la tabla `turnos`
--
ALTER TABLE `turnos`
  ADD CONSTRAINT `turnos_ibfk_1` FOREIGN KEY (`idempleado`) REFERENCES `usu` (`idusu`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usu`
--
ALTER TABLE `usu`
  ADD CONSTRAINT `usu_ibfk_1` FOREIGN KEY (`tpusu_idtpusu`) REFERENCES `tpusu` (`idtpusu`);

--
-- Filtros para la tabla `vacaciones`
--
ALTER TABLE `vacaciones`
  ADD CONSTRAINT `vacaciones_ibfk_1` FOREIGN KEY (`id_empleado`) REFERENCES `usu` (`idusu`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
