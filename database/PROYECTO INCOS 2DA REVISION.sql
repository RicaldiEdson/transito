-- MySQL dump 10.13  Distrib 8.0.46, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: sistema_prevencion_accidentes
-- ------------------------------------------------------
-- Server version	8.0.46

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `asignacion_conductor`
--

DROP TABLE IF EXISTS `asignacion_conductor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `asignacion_conductor` (
  `id_asignacion` int NOT NULL AUTO_INCREMENT,
  `id_conductor` int NOT NULL,
  `id_vehiculo` int NOT NULL,
  `fecha_inicio` datetime DEFAULT CURRENT_TIMESTAMP,
  `fecha_fin` datetime DEFAULT NULL,
  `estado` enum('Activa','Finalizada') COLLATE utf8mb4_general_ci DEFAULT 'Activa',
  PRIMARY KEY (`id_asignacion`),
  KEY `idx_asignacion_conductor` (`id_conductor`),
  KEY `idx_asignacion_vehiculo` (`id_vehiculo`),
  CONSTRAINT `fk_asignacion_conductor` FOREIGN KEY (`id_conductor`) REFERENCES `conductores` (`id_conductor`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_asignacion_vehiculo` FOREIGN KEY (`id_vehiculo`) REFERENCES `vehiculos` (`id_vehiculo`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `chk_fechas_asignacion` CHECK (((`fecha_fin` is null) or (`fecha_fin` >= `fecha_inicio`)))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asignacion_conductor`
--

LOCK TABLES `asignacion_conductor` WRITE;
/*!40000 ALTER TABLE `asignacion_conductor` DISABLE KEYS */;
/*!40000 ALTER TABLE `asignacion_conductor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `conductores`
--

DROP TABLE IF EXISTS `conductores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `conductores` (
  `id_conductor` int NOT NULL AUTO_INCREMENT,
  `ci` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `apellido` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `direccion` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `licencia` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `categoria` enum('P','A','B','C') COLLATE utf8mb4_general_ci NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` enum('Activo','Inactivo') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Activo',
  `id_empresa` int NOT NULL,
  PRIMARY KEY (`id_conductor`),
  UNIQUE KEY `uk_ci_empresa` (`ci`,`id_empresa`),
  UNIQUE KEY `uk_licencia_empresa` (`licencia`,`id_empresa`),
  KEY `idx_conductor_empresa` (`id_empresa`),
  KEY `idx_conductor_estado` (`estado`),
  CONSTRAINT `fk_conductor_empresa` FOREIGN KEY (`id_empresa`) REFERENCES `empresas` (`id_empresa`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `conductores`
--

LOCK TABLES `conductores` WRITE;
/*!40000 ALTER TABLE `conductores` DISABLE KEYS */;
/*!40000 ALTER TABLE `conductores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dispositivos`
--

DROP TABLE IF EXISTS `dispositivos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dispositivos` (
  `id_dispositivo` int NOT NULL AUTO_INCREMENT,
  `codigo` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `tipo` enum('ESP32-CAM','MQ-3','Arduino') COLLATE utf8mb4_general_ci NOT NULL,
  `ip` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `puerto` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `estado` enum('Online','Offline') COLLATE utf8mb4_general_ci DEFAULT 'Offline',
  `fecha_registro` datetime DEFAULT CURRENT_TIMESTAMP,
  `ultima_conexion` datetime DEFAULT NULL,
  `id_vehiculo` int DEFAULT NULL,
  `id_empresa` int NOT NULL,
  PRIMARY KEY (`id_dispositivo`),
  UNIQUE KEY `uk_codigo_empresa` (`codigo`,`id_empresa`),
  UNIQUE KEY `uk_dispositivo_tipo_vehiculo` (`id_vehiculo`,`tipo`),
  KEY `idx_dispositivo_vehiculo` (`id_vehiculo`),
  KEY `idx_dispositivo_empresa` (`id_empresa`),
  CONSTRAINT `fk_dispositivo_empresa` FOREIGN KEY (`id_empresa`) REFERENCES `empresas` (`id_empresa`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_dispositivo_vehiculo` FOREIGN KEY (`id_vehiculo`) REFERENCES `vehiculos` (`id_vehiculo`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dispositivos`
--

LOCK TABLES `dispositivos` WRITE;
/*!40000 ALTER TABLE `dispositivos` DISABLE KEYS */;
/*!40000 ALTER TABLE `dispositivos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empresas`
--

DROP TABLE IF EXISTS `empresas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empresas` (
  `id_empresa` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `nit` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `direccion` varchar(250) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telefono` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `correo` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `estado` enum('Activo','Inactivo') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Activo',
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_empresa`),
  UNIQUE KEY `uk_empresa_nit` (`nit`),
  UNIQUE KEY `uk_empresa_correo` (`correo`),
  KEY `idx_empresa_estado` (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empresas`
--

LOCK TABLES `empresas` WRITE;
/*!40000 ALTER TABLE `empresas` DISABLE KEYS */;
/*!40000 ALTER TABLE `empresas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `eventos_alcohol`
--

DROP TABLE IF EXISTS `eventos_alcohol`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `eventos_alcohol` (
  `id_evento_alcohol` int NOT NULL AUTO_INCREMENT,
  `nivel_alcohol` decimal(5,2) NOT NULL,
  `resultado` enum('Apto','No Apto') COLLATE utf8mb4_general_ci NOT NULL,
  `fecha_hora` datetime DEFAULT CURRENT_TIMESTAMP,
  `id_vehiculo` int NOT NULL,
  `id_conductor` int NOT NULL,
  `id_dispositivo` int NOT NULL,
  `id_empresa` int NOT NULL,
  PRIMARY KEY (`id_evento_alcohol`),
  KEY `idx_evento_alcohol_dispositivo` (`id_dispositivo`),
  KEY `idx_evento_alcohol_empresa` (`id_empresa`),
  KEY `idx_evento_alcohol_vehiculo` (`id_vehiculo`),
  KEY `idx_evento_alcohol_fecha` (`fecha_hora`),
  KEY `idx_evento_alcohol_conductor_fecha` (`id_conductor`,`fecha_hora`),
  CONSTRAINT `fk_evento_alcohol_conductor` FOREIGN KEY (`id_conductor`) REFERENCES `conductores` (`id_conductor`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_evento_alcohol_dispositivo` FOREIGN KEY (`id_dispositivo`) REFERENCES `dispositivos` (`id_dispositivo`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_evento_alcohol_empresa` FOREIGN KEY (`id_empresa`) REFERENCES `empresas` (`id_empresa`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_evento_alcohol_vehiculo` FOREIGN KEY (`id_vehiculo`) REFERENCES `vehiculos` (`id_vehiculo`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `chk_nivel_alcohol` CHECK ((`nivel_alcohol` between 0 and 5))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `eventos_alcohol`
--

LOCK TABLES `eventos_alcohol` WRITE;
/*!40000 ALTER TABLE `eventos_alcohol` DISABLE KEYS */;
/*!40000 ALTER TABLE `eventos_alcohol` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `eventos_somnolencia`
--

DROP TABLE IF EXISTS `eventos_somnolencia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `eventos_somnolencia` (
  `id_evento_somnolencia` int NOT NULL AUTO_INCREMENT,
  `nivel_riesgo` decimal(5,2) NOT NULL,
  `resultado` enum('Normal','Riesgo','Somnolencia Critica') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Normal',
  `fecha_hora` datetime DEFAULT CURRENT_TIMESTAMP,
  `id_conductor` int NOT NULL,
  `id_dispositivo` int NOT NULL,
  `id_vehiculo` int NOT NULL,
  `id_empresa` int NOT NULL,
  PRIMARY KEY (`id_evento_somnolencia`),
  KEY `idx_evento_somnolencia_dispositivo` (`id_dispositivo`),
  KEY `idx_evento_somnolencia_empresa` (`id_empresa`),
  KEY `idx_evento_somnolencia_vehiculo` (`id_vehiculo`),
  KEY `idx_evento_somnolencia_fecha` (`fecha_hora`),
  KEY `idx_evento_somnolencia_conductor_fecha` (`id_conductor`,`fecha_hora`),
  CONSTRAINT `fk_evento_somnolencia_conductor` FOREIGN KEY (`id_conductor`) REFERENCES `conductores` (`id_conductor`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_evento_somnolencia_dispositivo` FOREIGN KEY (`id_dispositivo`) REFERENCES `dispositivos` (`id_dispositivo`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_evento_somnolencia_empresa` FOREIGN KEY (`id_empresa`) REFERENCES `empresas` (`id_empresa`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_evento_somnolencia_vehiculo` FOREIGN KEY (`id_vehiculo`) REFERENCES `vehiculos` (`id_vehiculo`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `chk_nivel_riesgo` CHECK ((`nivel_riesgo` between 0 and 100))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `eventos_somnolencia`
--

LOCK TABLES `eventos_somnolencia` WRITE;
/*!40000 ALTER TABLE `eventos_somnolencia` DISABLE KEYS */;
/*!40000 ALTER TABLE `eventos_somnolencia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id_rol` int NOT NULL,
  `nombre` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `descripcion` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id_rol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'SuperAdministrador','Administrador Global'),(2,'Administrador','Administrador de Empresa'),(3,'Operador','Usuario de Consulta');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id_usuario` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `apellido` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `usuario` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `correo` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `estado` enum('Activo','Inactivo') COLLATE utf8mb4_general_ci DEFAULT 'Activo',
  `id_rol` int NOT NULL,
  `id_empresa` int DEFAULT NULL,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `uk_usuario` (`usuario`),
  UNIQUE KEY `uk_usuario_correo` (`correo`),
  KEY `idx_usuario_rol` (`id_rol`),
  KEY `idx_usuario_empresa` (`id_empresa`),
  CONSTRAINT `fk_usuario_empresa` FOREIGN KEY (`id_empresa`) REFERENCES `empresas` (`id_empresa`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_usuario_rol` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'Policia','Transito','superadmin','123456','superadmin@safedrive.com','Activo',1,NULL);
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vehiculos`
--

DROP TABLE IF EXISTS `vehiculos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vehiculos` (
  `id_vehiculo` int NOT NULL AUTO_INCREMENT,
  `placa` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `marca` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `modelo` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `anio` int DEFAULT NULL,
  `color` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `capacidad` decimal(8,2) DEFAULT NULL COMMENT 'Capacidad en toneladas',
  `estado` enum('Activo','Mantenimiento','Inactivo') COLLATE utf8mb4_general_ci DEFAULT 'Activo',
  `id_empresa` int NOT NULL,
  PRIMARY KEY (`id_vehiculo`),
  UNIQUE KEY `uk_placa_empresa` (`placa`,`id_empresa`),
  KEY `idx_vehiculo_empresa` (`id_empresa`),
  CONSTRAINT `fk_vehiculo_empresa` FOREIGN KEY (`id_empresa`) REFERENCES `empresas` (`id_empresa`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `chk_anio` CHECK ((`anio` between 1980 and 2050))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehiculos`
--

LOCK TABLES `vehiculos` WRITE;
/*!40000 ALTER TABLE `vehiculos` DISABLE KEYS */;
/*!40000 ALTER TABLE `vehiculos` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-07-28 16:50:21
