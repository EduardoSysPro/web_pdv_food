-- ============================================================
--  WEB PDV FOOD - INSTALADOR ÚNICO  (instalar_food.sql)
--  Punto de Venta para Restaurantes / Negocios de Alimentos
--  Versión: FOOD  (caracteres UTF-8 / utf8mb4)
-- ============================================================
--
--  Este archivo crea la base de datos `web_pdv_food_db` con TODA
--  su estructura y los datos de ejemplo, listo para instalaciones
--  nuevas. No requiere migraciones previas.
--
--  CÓMO INSTALAR:
--    1) Abre phpMyAdmin -> pestaña "SQL", o en consola:
--         mysql -u root -p < instalar_food.sql
--    2) Verifica que termine sin errores ("Query OK").
--    3) Arranca Apache y MySQL (WAMP) y entra a:
--         http://localhost/web_pdv_food
--
--  IMPORTANTE:
--    - Si la base de datos ya existía, las tablas se recrean
--      (DROP TABLE IF EXISTS + CREATE).
--    - Codificación utf8mb4: soporta emojis y acentos reales.
--
--  USUARIOS DE DEMO  (CAMBIAR CONTRASEÑAS tras el primer ingreso):
--    admin    / password   (administrador / gerente)
--    cajero   / 1234       (cajero de barra / mostrador)
--    mesero   / 1234       (mesero: toma pedidos y comandas)
--    cocina   / 1234       (cocina: panel de comandas para preparar)
-- ============================================================

CREATE DATABASE IF NOT EXISTS `web_pdv_food_db`
  DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `web_pdv_food_db`;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET UNIQUE_CHECKS = 0;

-- ============================================================
--  ESTRUCTURA (definición de tablas)
-- ============================================================


-- MySQL dump 10.13  Distrib 8.4.7, for Win64 (x86_64)
--
-- Host: localhost    Database: web_pdv_food_db
-- ------------------------------------------------------
-- Server version	8.4.7

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `caja_cortes`
--

DROP TABLE IF EXISTS `caja_cortes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `caja_cortes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `caja_id` int DEFAULT NULL,
  `fecha_apertura` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fondo_inicial` decimal(10,2) NOT NULL DEFAULT '0.00',
  `fecha_cierre` datetime DEFAULT NULL,
  `monto_declarado` decimal(10,2) DEFAULT NULL,
  `diferencia` decimal(10,2) DEFAULT NULL,
  `estado` enum('abierta','cerrada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'abierta',
  PRIMARY KEY (`id`),
  KEY `idx_caja_cortes_usuario` (`usuario_id`),
  KEY `idx_caja_cortes_caja` (`caja_id`),
  KEY `idx_caja_cortes_estado` (`estado`),
  CONSTRAINT `fk_caja_cortes_caja` FOREIGN KEY (`caja_id`) REFERENCES `cajas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_caja_cortes_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Sesiones de apertura y corte de caja';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `caja_movimientos`
--

DROP TABLE IF EXISTS `caja_movimientos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `caja_movimientos` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'Identificador único del movimiento',
  `usuario_id` int NOT NULL COMMENT 'Llave foránea hacia el usuario que registró el movimiento',
  `caja_id` int DEFAULT NULL COMMENT 'Caja activa del movimiento',
  `tipo` enum('apertura','ingreso','egreso','cierre','ingreso_abono') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tipo de movimiento de caja',
  `monto` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Monto del movimiento',
  `concepto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Descripción o concepto del movimiento',
  `fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora del movimiento',
  PRIMARY KEY (`id`),
  KEY `idx_caja_movimientos_tipo` (`tipo`),
  KEY `idx_caja_movimientos_fecha` (`fecha`),
  KEY `idx_caja_movimientos_usuario_id` (`usuario_id`),
  KEY `idx_caja_movimientos_caja_id` (`caja_id`),
  CONSTRAINT `fk_caja_movimientos_caja` FOREIGN KEY (`caja_id`) REFERENCES `cajas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_caja_movimientos_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Movimientos de caja';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cajas`
--

DROP TABLE IF EXISTS `cajas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cajas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_cajas_nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `categorias`
--

DROP TABLE IF EXISTS `categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categorias` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'Identificador único de la categoría',
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nombre de la categoría',
  `descripcion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Descripción detallada de la categoría',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_categorias_nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Categorías del menú';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `clientes`
--

DROP TABLE IF EXISTS `clientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clientes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `rtn_identidad` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nombre` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `limite_credito` decimal(10,2) NOT NULL DEFAULT '0.00',
  `saldo_pendiente` decimal(10,2) NOT NULL DEFAULT '0.00',
  `creado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_clientes_rtn` (`rtn_identidad`),
  KEY `idx_clientes_nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Clientes y crédito';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `combo_detalle`
--

DROP TABLE IF EXISTS `combo_detalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `combo_detalle` (
  `id` int NOT NULL AUTO_INCREMENT,
  `combo_id` int NOT NULL COMMENT 'Producto marcado como combo (es_combo = 1)',
  `producto_id` int NOT NULL COMMENT 'Producto componente incluido en el combo',
  `cantidad` decimal(10,3) NOT NULL DEFAULT '1.000' COMMENT 'Cantidad del componente por cada combo',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_combo_detalle` (`combo_id`,`producto_id`),
  KEY `idx_combo_detalle_producto` (`producto_id`),
  CONSTRAINT `fk_combo_detalle_combo` FOREIGN KEY (`combo_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_combo_detalle_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Componentes de los combos/paquetes de restaurante';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `compras`
--

DROP TABLE IF EXISTS `compras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `compras` (
  `id` int NOT NULL AUTO_INCREMENT,
  `folio` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Folio interno del sistema (COMP-000001)',
  `proveedor_id` int DEFAULT NULL COMMENT 'Proveedor del catálogo',
  `proveedor_nombre` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nombre del proveedor al momento de la compra',
  `proveedor_rtn` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `proveedor_tipo` enum('contribuyente','no_contribuyente') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'contribuyente',
  `usuario_id` int NOT NULL COMMENT 'Usuario que registró la compra',
  `sucursal` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Sucursal desde donde se registró',
  `tipo_documento` enum('factura_cai','recibo','nota_credito','nota_debito') COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero_factura` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Número de factura/recibo del proveedor',
  `cai` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rango_autorizado` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_limite_emision` date DEFAULT NULL,
  `fecha_emision` date NOT NULL,
  `condicion_pago` enum('contado','credito') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'contado',
  `dias_credito` int NOT NULL DEFAULT '0',
  `fecha_vencimiento` date DEFAULT NULL COMMENT 'Fecha límite de pago (emisión + días de crédito)',
  `documento_referencia_id` int DEFAULT NULL COMMENT 'Compra original que referencia la NC/ND',
  `estado` enum('recibida','anulada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'recibida',
  `importe_exento` decimal(10,2) NOT NULL DEFAULT '0.00',
  `importe_exonerado` decimal(10,2) NOT NULL DEFAULT '0.00',
  `importe_gravado_15` decimal(10,2) NOT NULL DEFAULT '0.00',
  `isv_15` decimal(10,2) NOT NULL DEFAULT '0.00',
  `importe_gravado_18` decimal(10,2) NOT NULL DEFAULT '0.00',
  `isv_18` decimal(10,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Suma de líneas antes de descuento',
  `descuento_total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `flete` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Costo de transporte prorrateable al costo del producto',
  `total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `saldo_pendiente` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Cuentas por pagar (0 si es de contado o ya saldada)',
  `isv_acreditable` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 = factura CAI a contribuyente: ISV acreditable (crédito fiscal)',
  `observaciones` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_compras_folio` (`folio`),
  KEY `idx_compras_proveedor` (`proveedor_id`),
  KEY `idx_compras_usuario` (`usuario_id`),
  KEY `idx_compras_fecha` (`fecha_emision`),
  KEY `idx_compras_estado` (`estado`),
  KEY `fk_compras_referencia` (`documento_referencia_id`),
  CONSTRAINT `fk_compras_proveedor` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_compras_referencia` FOREIGN KEY (`documento_referencia_id`) REFERENCES `compras` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_compras_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Encabezado de compras';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `configuracion`
--

DROP TABLE IF EXISTS `configuracion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `configuracion` (
  `clave` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor` text COLLATE utf8mb4_unicode_ci,
  `actualizado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`clave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Configuración general del sistema';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cotizaciones`
--

DROP TABLE IF EXISTS `cotizaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cotizaciones` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'Identificador único de la cotización',
  `folio` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Número de folio interno (COT-00000001 o COMA-00000001)',
  `vendedor_id` int NOT NULL COMMENT 'Usuario que creó la cotización / comanda',
  `cliente_id` int DEFAULT NULL COMMENT 'Cliente del catálogo (opcional)',
  `cliente_nombre` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nombre / razón social del cliente o número de mesa',
  `cliente_rtn` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'RTN o identidad del cliente',
  `cliente_telefono` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Teléfono del cliente',
  `cliente_direccion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Dirección del cliente',
  `estado` enum('no_confirmada','pendiente','en_cocina','listo','servida','facturada','cancelada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente' COMMENT 'no_confirmada = generada tras el cobro sin enviar a cocina, pendiente = lista para facturar, en_cocina/listo/servida = estados de cocina, facturada = ya convertida en venta',
  `tipo` enum('cotizacion','comanda') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cotizacion' COMMENT 'cotizacion = cotización clásica, comanda = orden de cocina',
  `venta_id` int DEFAULT NULL COMMENT 'Venta generada cuando la cotización fue facturada en caja',
  `importe_exento` decimal(10,2) NOT NULL DEFAULT '0.00',
  `importe_exonerado` decimal(10,2) NOT NULL DEFAULT '0.00',
  `importe_gravado_15` decimal(10,2) NOT NULL DEFAULT '0.00',
  `isv_15` decimal(10,2) NOT NULL DEFAULT '0.00',
  `importe_gravado_18` decimal(10,2) NOT NULL DEFAULT '0.00',
  `isv_18` decimal(10,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Suma de líneas antes de descuento',
  `descuento_total` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Total de descuentos otorgados',
  `total` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Monto total de la cotización/comanda',
  `observaciones` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Notas u observaciones del vendedor',
  `fecha_validez` date DEFAULT NULL COMMENT 'Fecha hasta la que la cotización tiene validez',
  `creada_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora de creación',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_cotizaciones_folio` (`folio`),
  KEY `idx_cotizaciones_estado` (`estado`),
  KEY `idx_cotizaciones_tipo` (`tipo`),
  KEY `idx_cotizaciones_vendedor` (`vendedor_id`),
  KEY `idx_cotizaciones_cliente` (`cliente_id`),
  KEY `idx_cotizaciones_venta` (`venta_id`),
  KEY `idx_cotizaciones_creada` (`creada_en`),
  CONSTRAINT `fk_cot_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_cot_vendedor` FOREIGN KEY (`vendedor_id`) REFERENCES `usuarios` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_cot_venta` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Cotizaciones y comandas de cocina';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `detalle_compras`
--

DROP TABLE IF EXISTS `detalle_compras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `detalle_compras` (
  `id` int NOT NULL AUTO_INCREMENT,
  `compra_id` int NOT NULL,
  `tipo_linea` enum('producto','gasto_operativo') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'producto',
  `producto_id` int DEFAULT NULL COMMENT 'NULL cuando es un gasto operativo',
  `nombre` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nombre del producto o concepto del gasto',
  `cantidad` decimal(10,3) NOT NULL DEFAULT '1.000',
  `costo_unitario` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Costo unitario acordado con el proveedor',
  `costo_ingreso` decimal(10,2) DEFAULT NULL COMMENT 'Costo unitario usado para inventario (incluye prorrateo de flete/gastos)',
  `tipo_presentacion` enum('unidad','empaque') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unidad',
  `nombre_presentacion` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Unidad',
  `factor_unidades` decimal(10,3) NOT NULL DEFAULT '1.000',
  `tipo_impuesto` enum('exento','gravado_15','gravado_18','exonerado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'gravado_15',
  `porcentaje_isv` decimal(5,2) NOT NULL DEFAULT '15.00',
  `monto_isv` decimal(10,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `idx_detalle_compras_compra` (`compra_id`),
  KEY `idx_detalle_compras_producto` (`producto_id`),
  CONSTRAINT `fk_detalle_compras_compra` FOREIGN KEY (`compra_id`) REFERENCES `compras` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_detalle_compras_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Detalle de productos y gastos por compra';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `detalle_cotizaciones`
--

DROP TABLE IF EXISTS `detalle_cotizaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `detalle_cotizaciones` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'Identificador único del detalle',
  `cotizacion_id` int NOT NULL COMMENT 'Llave foránea hacia la cotización',
  `producto_id` int DEFAULT NULL COMMENT 'Producto cotizado',
  `nombre_producto` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nombre del producto al momento de cotizar',
  `cantidad` decimal(10,3) NOT NULL DEFAULT '1.000' COMMENT 'Cantidad cotizada',
  `precio_lista` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Precio antes del descuento',
  `precio_unitario` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Precio final por unidad',
  `descuento_unitario` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Descuento fijo por unidad',
  `subtotal` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Subtotal = cantidad * precio_unitario',
  `tipo_presentacion` enum('unidad','empaque') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unidad' COMMENT 'Presentación cotizada',
  `nombre_presentacion` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Unidad' COMMENT 'Nombre de la presentación',
  `factor_unidades` decimal(10,3) NOT NULL DEFAULT '1.000' COMMENT 'Factor de unidades del empaque',
  `porcentaje_isv` decimal(5,2) NOT NULL DEFAULT '0.00' COMMENT 'Porcentaje de ISV de la línea',
  `monto_isv` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Monto de ISV de la línea',
  `es_exento` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 = exento de ISV',
  `es_exonerado` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 = exonerado de ISV',
  `estado_item` enum('pendiente','en_cocina','listo') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente' COMMENT 'Avance de cocina de la línea',
  `nota` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Instrucciones de preparación de la línea',
  PRIMARY KEY (`id`),
  KEY `idx_detalle_cotizaciones_cotizacion` (`cotizacion_id`),
  KEY `idx_detalle_cotizaciones_producto` (`producto_id`),
  CONSTRAINT `fk_detalle_cot_cotizacion` FOREIGN KEY (`cotizacion_id`) REFERENCES `cotizaciones` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_detalle_cot_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Líneas de productos por cotización';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `detalle_ventas`
--

DROP TABLE IF EXISTS `detalle_ventas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `detalle_ventas` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'Identificador único del detalle',
  `venta_id` int NOT NULL COMMENT 'Llave foránea hacia la venta',
  `producto_id` int NOT NULL COMMENT 'Llave foránea hacia el producto vendido',
  `cantidad` decimal(10,3) NOT NULL DEFAULT '1.000' COMMENT 'Cantidad de unidades vendidas',
  `precio_unitario` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Precio unitario al momento de la venta',
  `precio_lista` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Precio unitario antes del descuento',
  `descuento_unitario` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Descuento fijo aplicado a cada unidad',
  `subtotal` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Subtotal = cantidad * precio_unitario',
  `tipo_presentacion` enum('unidad','empaque') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unidad' COMMENT 'Indica si se vendió por unidad o empaque',
  `nombre_presentacion` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Unidad' COMMENT 'Nombre de la presentación vendida',
  `factor_unidades` decimal(10,3) NOT NULL DEFAULT '1.000' COMMENT 'Factor de unidades descontadas del inventario',
  `porcentaje_isv` decimal(5,2) NOT NULL DEFAULT '0.00' COMMENT 'Porcentaje de ISV aplicado a la línea',
  `monto_isv` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Monto de ISV de la línea',
  `es_exento` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 = producto exento de ISV',
  `es_exonerado` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 = producto exonerado de ISV',
  PRIMARY KEY (`id`),
  KEY `idx_detalle_ventas_venta_id` (`venta_id`),
  KEY `idx_detalle_ventas_producto_id` (`producto_id`),
  KEY `idx_detalle_ventas_venta_producto` (`venta_id`,`producto_id`),
  CONSTRAINT `fk_detalle_ventas_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_detalle_ventas_venta` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Detalle de productos por venta';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `inventario_movimientos`
--

DROP TABLE IF EXISTS `inventario_movimientos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventario_movimientos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `producto_id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `tipo_movimiento` enum('entrada','salida') COLLATE utf8mb4_unicode_ci NOT NULL,
  `cantidad` decimal(10,3) NOT NULL COMMENT 'Cantidad del movimiento',
  `motivo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_inventario_producto` (`producto_id`),
  KEY `idx_inventario_usuario` (`usuario_id`),
  CONSTRAINT `fk_inventario_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_inventario_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bitácora de ajustes de inventario';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `login_intentos`
--

DROP TABLE IF EXISTS `login_intentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `login_intentos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Nombre de usuario del intento',
  `ip` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Dirección IP del intento',
  `intentado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora del intento',
  `resultado` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fallo' COMMENT 'fallo o exito',
  PRIMARY KEY (`id`),
  KEY `idx_login_intentos_busqueda` (`usuario`,`ip`,`intentado_en`),
  KEY `idx_login_intentos_limpieza` (`intentado_en`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Registro de intentos de inicio de sesión para anti fuerza bruta';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pagos_clientes`
--

DROP TABLE IF EXISTS `pagos_clientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pagos_clientes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cliente_id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `forma_pago` enum('efectivo','tarjeta','transferencia') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'efectivo',
  `observacion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_pagos_cliente` (`cliente_id`),
  KEY `idx_pagos_usuario` (`usuario_id`),
  CONSTRAINT `fk_pagos_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_pagos_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Abonos de clientes';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pagos_proveedores`
--

DROP TABLE IF EXISTS `pagos_proveedores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pagos_proveedores` (
  `id` int NOT NULL AUTO_INCREMENT,
  `compra_id` int NOT NULL,
  `proveedor_id` int DEFAULT NULL,
  `usuario_id` int NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `forma_pago` enum('efectivo','tarjeta','transferencia') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'efectivo',
  `observacion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_pagos_proveedores_compra` (`compra_id`),
  KEY `idx_pagos_proveedores_proveedor` (`proveedor_id`),
  KEY `idx_pagos_proveedores_usuario` (`usuario_id`),
  CONSTRAINT `fk_pagos_prov_compra` FOREIGN KEY (`compra_id`) REFERENCES `compras` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_pagos_prov_proveedor` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_pagos_prov_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Pagos a proveedores';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `productos`
--

DROP TABLE IF EXISTS `productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `productos` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'Identificador único del producto',
  `codigo_barras` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Código de barras del producto (EAN/UPC)',
  `nombre` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nombre o descripción del producto',
  `precio_costo` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Precio de compra al proveedor',
  `precio_venta` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Precio de venta al público',
  `stock` decimal(10,3) NOT NULL DEFAULT '0.000' COMMENT 'Cantidad disponible (porciones en el inventario)',
  `stock_minimo` decimal(10,3) NOT NULL DEFAULT '1.000' COMMENT 'Stock mínimo para alerta de reposición',
  `unidad_medida` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unidad' COMMENT 'Unidad de medida del producto',
  `permite_decimales` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Indica si el producto acepta cantidades fraccionarias',
  `tipo_venta` enum('solo_unidad','solo_empaque','ambos') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'solo_unidad' COMMENT 'Modalidad de venta',
  `nombre_empaque` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Caja' COMMENT 'Nombre presentación mayorista: Caja, Bulto, Fardo, Paquete, etc.',
  `unidades_por_empaque` decimal(10,3) NOT NULL DEFAULT '1.000' COMMENT 'Unidades contenidas en 1 empaque',
  `precio_empaque` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Precio de venta por empaque',
  `tipo_impuesto` enum('exento','gravado_15','gravado_18','exonerado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'gravado_15' COMMENT 'Régimen de ISV del producto',
  `porcentaje_isv` decimal(5,2) NOT NULL DEFAULT '15.00' COMMENT 'Porcentaje de ISV aplicado',
  `imagen` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ruta de la foto del producto (uploads/productos/...)',
  `codigo_barras_empaque` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Código de barras exclusivo del empaque',
  `categoria_id` int DEFAULT NULL COMMENT 'Llave foránea hacia la categoría del producto',
  `creado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora de alta del producto',
  `controlar_stock` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 = controla inventario (descuenta stock), 0 = producto preparado al momento sin descuento de stock',
  `es_combo` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 = es un combo/paquete: se vende como conjunto de productos (combo_detalle)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_productos_codigo_barras` (`codigo_barras`),
  UNIQUE KEY `idx_productos_codigo_barras_empaque` (`codigo_barras_empaque`),
  KEY `idx_productos_nombre` (`nombre`),
  KEY `idx_productos_categoria_id` (`categoria_id`),
  KEY `idx_productos_stock` (`stock`),
  KEY `idx_productos_controlar_stock` (`controlar_stock`),
  FULLTEXT KEY `ft_productos_nombre` (`nombre`),
  CONSTRAINT `fk_productos_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Catálogo del menú';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `proveedores`
--

DROP TABLE IF EXISTS `proveedores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `proveedores` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Razón social / nombre del proveedor',
  `rtn` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'RTN del proveedor (único)',
  `tipo` enum('contribuyente','no_contribuyente') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'contribuyente' COMMENT 'Contribuyente = con capacidad de emitir crédito fiscal con factura CAI',
  `telefono` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contacto` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `correo` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_proveedores_rtn` (`rtn`),
  KEY `idx_proveedores_nombre` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Proveedores';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'Identificador único del usuario',
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nombre completo del usuario',
  `usuario` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nombre de usuario para iniciar sesión',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Contraseña hasheada con bcrypt',
  `rol` enum('admin','cajero','mesero','cocina') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cajero' COMMENT 'Rol del usuario en el sistema',
  `estado` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 = Activo, 0 = Inactivo',
  `caja_id` int DEFAULT NULL COMMENT 'Caja predeterminada del usuario',
  `sucursal` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Sucursal asignada',
  `caja` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Caja asignada',
  `creado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora de creación del registro',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_usuarios_usuario` (`usuario`),
  KEY `idx_usuarios_rol` (`rol`),
  KEY `idx_usuarios_estado` (`estado`),
  KEY `idx_usuarios_caja` (`caja_id`),
  CONSTRAINT `fk_usuarios_caja` FOREIGN KEY (`caja_id`) REFERENCES `cajas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Usuarios del sistema PDV';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ventas`
--

DROP TABLE IF EXISTS `ventas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ventas` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'Identificador único de la venta',
  `folio` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Número de folio o ticket de la venta',
  `usuario_id` int NOT NULL COMMENT 'Llave foránea hacia el usuario que realizó la venta',
  `caja_id` int DEFAULT NULL COMMENT 'Caja activa de la venta',
  `total` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Monto total de la venta',
  `descuento_total` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Total de descuentos otorgados en la venta',
  `pagado_con` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Cantidad de dinero entregada por el cliente',
  `cambio` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Cambio devuelto al cliente',
  `metodo_pago` enum('efectivo','tarjeta','transferencia','credito','mixto') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'efectivo',
  `pagos` text COLLATE utf8mb4_unicode_ci COMMENT 'Desglose de pagos en formato JSON: [{"metodo": "..", "monto": 0.00}]',
  `cliente_id` int DEFAULT NULL COMMENT 'Cliente asociado a la venta a crédito',
  `cliente_nombre` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nombre del cliente eventual o factura',
  `cliente_rtn` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'RTN/identidad del cliente eventual',
  `cliente_telefono` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Teléfono del cliente eventual',
  `cliente_direccion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Dirección del cliente eventual',
  `tipo_comprobante` enum('recibo','factura') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'recibo' COMMENT 'Tipo de comprobante de la venta',
  `cai` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Número de CAI asociado al comprobante',
  `correlativo_sar` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Correlativo SAR para facturación',
  `importe_exento` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Total de importes exentos de ISV',
  `importe_exonerado` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Total de importes exonerados de ISV',
  `importe_gravado_15` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Base gravada al 15% de ISV',
  `isv_15` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'ISV calculado al 15%',
  `importe_gravado_18` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Base gravada al 18% de ISV',
  `isv_18` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'ISV calculado al 18%',
  `rango_autorizado` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Rango CAI autorizado vigente al emitir la factura',
  `fecha_limite_emision` date DEFAULT NULL COMMENT 'Fecha límite de emisión del CAI vigente al emitir la factura',
  `fecha_venta` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora en que se realizó la venta',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_ventas_folio` (`folio`),
  KEY `idx_ventas_fecha_venta` (`fecha_venta`),
  KEY `idx_ventas_usuario_id` (`usuario_id`),
  KEY `idx_ventas_caja_id` (`caja_id`),
  KEY `idx_ventas_cliente_id` (`cliente_id`),
  CONSTRAINT `fk_ventas_caja` FOREIGN KEY (`caja_id`) REFERENCES `cajas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_ventas_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_ventas_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Encabezado de ventas';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-20 23:07:03


-- MySQL dump 10.13  Distrib 8.4.7, for Win64 (x86_64)
--
-- Host: localhost    Database: web_pdv_food_db
-- ------------------------------------------------------
-- Server version	8.4.7

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `cajas`
--

LOCK TABLES `cajas` WRITE;
/*!40000 ALTER TABLE `cajas` DISABLE KEYS */;
INSERT INTO `cajas` VALUES (1,'Caja 01',1),(2,'Caja 02',1);
/*!40000 ALTER TABLE `cajas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `categorias`
--

LOCK TABLES `categorias` WRITE;
/*!40000 ALTER TABLE `categorias` DISABLE KEYS */;
INSERT INTO `categorias` VALUES (1,'Desayunos','Platos de la mañana'),(2,'Platos Principales','Almuerzos y cenas'),(3,'Bocadillos','Aperitivos y acompañamientos'),(4,'Bebidas','Bebidas y refrescos'),(5,'Postres','Dulces y postres');
/*!40000 ALTER TABLE `categorias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `clientes`
--

LOCK TABLES `clientes` WRITE;
/*!40000 ALTER TABLE `clientes` DISABLE KEYS */;
INSERT INTO `clientes` VALUES (1,'08011990123456','Cliente de Restaurante','+504 9999-0000','Barrio El Centro, Tocoa',5000.00,150.00,'2026-09-19 15:26:12');
/*!40000 ALTER TABLE `clientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `combo_detalle`
--

LOCK TABLES `combo_detalle` WRITE;
/*!40000 ALTER TABLE `combo_detalle` DISABLE KEYS */;
/*!40000 ALTER TABLE `combo_detalle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `configuracion`
--

LOCK TABLES `configuracion` WRITE;
/*!40000 ALTER TABLE `configuracion` DISABLE KEYS */;
INSERT INTO `configuracion` VALUES ('ancho_ticket','80mm','2026-09-19 15:26:12'),('catalogo_visual','1','2026-09-19 15:26:12'),('cocina_refresco_seg','15','2026-09-19 15:26:12'),('cocina_visible','1','2026-09-19 15:26:12'),('cotizacion_dias_validez','15','2026-09-19 15:26:12'),('cotizacion_mensaje','Cotización sujeta a confirmación de precio y disponibilidad en caja.','2026-09-19 15:26:12'),('cotizacion_mostrar_precios','1','2026-09-19 15:26:12'),('direccion','Barrio El Centro, 2da Calle, Tocoa, Colón','2026-09-19 15:26:12'),('email','pedidos@elsabordehonduras.hn','2026-09-19 15:26:12'),('impuesto_porcentaje','15','2026-09-19 15:26:12'),('logotipo_path','uploads/logo.png','2026-09-19 15:26:12'),('mensaje_ticket','¡Gracias por su visita!','2026-09-19 15:26:12'),('modo_restaurante','1','2026-09-19 15:26:12'),('moneda_simbolo','L','2026-09-19 15:26:12'),('nombre_comanda','COMANDA','2026-09-19 15:26:12'),('nombre_negocio','RESTAURANTE EL SABOR DE HONDURAS','2026-09-19 15:26:12'),('rtn','08011995123456','2026-09-19 15:26:12'),('sar_activo','1','2026-09-19 15:26:12'),('sar_cai','3B8E9F-12A456-7890BC-DEF123-456789-A1','2026-09-19 15:26:12'),('sar_correlativo_actual','19','2026-09-20 22:58:33'),('sar_establecimiento','001','2026-09-19 15:26:12'),('sar_fecha_limite','2026-12-31','2026-09-19 15:26:12'),('sar_punto_venta','001','2026-09-19 15:26:12'),('sar_rango_final','000-001-01-00020000','2026-09-19 15:26:12'),('sar_rango_inicial','000-001-01-00000001','2026-09-19 15:26:12'),('sar_tipo_documento','01','2026-09-19 15:26:12'),('telefono','+504 2444-1234','2026-09-19 15:26:12'),('ticket_fuente','Arial','2026-09-19 15:26:12'),('ticket_mostrar_logo','0','2026-09-19 15:26:12'),('ticket_mostrar_sar','1','2026-09-19 15:26:12'),('ticket_tamano_fuente','12px','2026-09-19 15:26:12'),('tipo_comprobante_default','recibo','2026-09-19 15:26:12');
/*!40000 ALTER TABLE `configuracion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `productos`
--

LOCK TABLES `productos` WRITE;
/*!40000 ALTER TABLE `productos` DISABLE KEYS */;
/*!40000 ALTER TABLE `productos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'Admin','admin','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','admin',1,1,NULL,NULL,'2026-09-19 15:26:12'),(2,'Cajero Barra','cajero','$2y$10$vgi8eyHFvjEDpEHU1loTe.I0SBcUAfeYchnZ22NLDeps0UTTwp6uu','cajero',1,1,'Mi Negocio','','2026-09-19 15:26:12'),(3,'Mesero Principal','mesero','$2y$10$vgi8eyHFvjEDpEHU1loTe.I0SBcUAfeYchnZ22NLDeps0UTTwp6uu','mesero',1,NULL,'Mi Negocio','','2026-09-19 15:26:12'),(4,'Cocina Principal','cocina','$2y$10$vgi8eyHFvjEDpEHU1loTe.I0SBcUAfeYchnZ22NLDeps0UTTwp6uu','cocina',1,NULL,'Mi Negocio','','2026-09-19 15:26:12');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-20 23:07:03
