-- ============================================================
--  PETGUARD — Schema MySQL Completo (v1.1 Validado)
--  Sistema de Gestión y Adopción de Mascotas · México 2026
--  Versión 1.1 | Charset: utf8mb4 | Engine: InnoDB
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';

CREATE DATABASE IF NOT EXISTS `petguard_db`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `petguard_db`;

-- ============================================================
-- 1. ROLES DEL SISTEMA
-- ============================================================
CREATE TABLE `roles` (
  `id`          TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre`      VARCHAR(30)  NOT NULL COMMENT 'administrador|voluntario|veterinario|adoptante|sistema',
  `descripcion` VARCHAR(150) NOT NULL,
  `permisos`    JSON         NOT NULL COMMENT 'Array de slugs de permiso',
  `activo`      TINYINT(1)   NOT NULL DEFAULT 1,
  `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_roles_nombre` (`nombre`)
) ENGINE=InnoDB COMMENT='Roles del sistema PetGuard';

INSERT INTO `roles` (`nombre`, `descripcion`, `permisos`) VALUES
('administrador', 'Acceso total al sistema', '["mascotas.*","adopciones.*","usuarios.*","voluntarios.*","veterinarios.*","rescates.*","citas.*","reportes.*","config.*","donaciones.*"]'),
('voluntario', 'Registro de rescates y seguimiento de mascotas', '["mascotas.read","mascotas.create","mascotas.update","rescates.*","adopciones.read","citas.read"]'),
('veterinario', 'Gestión de citas y expedientes clínicos', '["mascotas.read","citas.*","expedientes.*","vacunas.*","tratamientos.*"]'),
('adoptante', 'Solicitar adopción, agendar visitas y ver estado', '["mascotas.read","adopciones.create","adopciones.read_own","citas.create","citas.read_own"]'),
('sistema', 'Usuario interno para procesos automáticos', '["*"]');

-- ============================================================
-- 2. USUARIOS
-- ============================================================
CREATE TABLE `usuarios` (
  `id`              INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `rol_id`          TINYINT UNSIGNED NOT NULL,
  `nombre`          VARCHAR(60)     NOT NULL,
  `apellido_paterno` VARCHAR(60)    NOT NULL,
  `apellido_materno` VARCHAR(60)    NOT NULL DEFAULT '',
  `email`           VARCHAR(120)    NOT NULL,
  `password_hash`   VARCHAR(255)    NOT NULL COMMENT 'bcrypt cost=12',
  `telefono`        VARCHAR(20)     DEFAULT NULL,
  `curp`            CHAR(18)        DEFAULT NULL COMMENT 'Validación identidad adoptante/voluntario',
  `foto_url`        VARCHAR(255)    DEFAULT NULL,
  `fecha_nacimiento` DATE           DEFAULT NULL,
  `genero`          ENUM('M','F','NB','NE') DEFAULT 'NE' COMMENT 'NE=No especificado',
  -- Dirección
  `calle`           VARCHAR(120)    DEFAULT NULL,
  `num_exterior`    VARCHAR(10)     DEFAULT NULL,
  `num_interior`    VARCHAR(10)     DEFAULT NULL,
  `colonia`         VARCHAR(80)     DEFAULT NULL,
  `municipio`       VARCHAR(80)     DEFAULT NULL,
  `estado`          VARCHAR(60)     DEFAULT NULL,
  `cp`              CHAR(5)         DEFAULT NULL,
  `lat`             DECIMAL(10,7)   DEFAULT NULL COMMENT 'Geolocalización domicilio',
  `lng`             DECIMAL(10,7)   DEFAULT NULL,
  -- Estado cuenta
  `email_verified`  TINYINT(1)      NOT NULL DEFAULT 0,
  `activo`          TINYINT(1)      NOT NULL DEFAULT 1,
  `bloqueado`       TINYINT(1)      NOT NULL DEFAULT 0,
  `motivo_bloqueo`  VARCHAR(255)    DEFAULT NULL,
  `ultimo_login`    TIMESTAMP       NULL,
  `token_reset`     VARCHAR(100)    DEFAULT NULL,
  `token_reset_exp` TIMESTAMP       NULL,
  `created_at`      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_usuarios_email` (`email`),
  UNIQUE KEY `uk_usuarios_curp`  (`curp`),
  KEY `idx_usuarios_rol`    (`rol_id`),
  KEY `idx_usuarios_activo` (`activo`),
  CONSTRAINT `fk_usuarios_rol` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB COMMENT='Todos los actores del sistema';

INSERT INTO `usuarios` (`rol_id`,`nombre`,`apellido_paterno`,`apellido_materno`,`email`,`password_hash`,`telefono`,`email_verified`,`activo`,`estado`)
VALUES (1,'Admin','PetGuard','Sistema','admin@petguard.mx', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '5500000000', 1, 1, 'Ciudad de México');

-- ============================================================
-- 3. SESIONES (tokens JWT / session tokens)
-- ============================================================
CREATE TABLE `sesiones` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `usuario_id` INT UNSIGNED    NOT NULL,
  `token`      VARCHAR(255)    NOT NULL,
  `ip`         VARCHAR(45)     DEFAULT NULL,
  `user_agent` VARCHAR(255)    DEFAULT NULL,
  `expires_at` TIMESTAMP       NOT NULL,
  `created_at` TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_sesiones_token` (`token`),
  KEY `idx_sesiones_usuario` (`usuario_id`),
  KEY `idx_sesiones_expires` (`expires_at`),
  CONSTRAINT `fk_sesiones_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB COMMENT='Tokens de sesión activos';

-- ============================================================
-- 4. ESPECIES Y RAZAS
-- ============================================================
CREATE TABLE `especies` (
  `id`     TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(40) NOT NULL,
  `icono`  VARCHAR(10) DEFAULT '🐾',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_especies_nombre` (`nombre`)
) ENGINE=InnoDB;

INSERT INTO `especies` (`nombre`, `icono`) VALUES
('Perro','🐕'), ('Gato','🐈'), ('Conejo','🐰'), ('Ave','🐦'), ('Roedor','🐹'), ('Reptil','🦎'), ('Otro','🐾');

CREATE TABLE `razas` (
  `id`         SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `especie_id` TINYINT UNSIGNED  NOT NULL,
  `nombre`     VARCHAR(60)       NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_razas_especie` (`especie_id`),
  CONSTRAINT `fk_razas_especie` FOREIGN KEY (`especie_id`) REFERENCES `especies` (`id`)
) ENGINE=InnoDB;

INSERT INTO `razas` (`especie_id`, `nombre`) VALUES
(1,'Labrador Retriever'),(1,'Golden Retriever'),(1,'Bulldog'),(1,'Beagle'),(1,'Pastor Alemán'),(1,'Chihuahua'),(1,'Mestizo/Criollo'),(1,'Otro'),
(2,'Persa'),(2,'Siamés'),(2,'Maine Coon'),(2,'Bengalí'),(2,'Mestizo'),(2,'Otro'),
(3,'Holland Lop'),(3,'Mini Rex'),(3,'Angora'),(3,'Común'),
(4,'Perico/Periquito'),(4,'Canario'),(4,'Agapornis'),(4,'Guacamayo'),(4,'Otro'),
(5,'Hámster'),(5,'Cobaya/Cuy'),(5,'Ratón'),(5,'Otro');

-- ============================================================
-- 5. MASCOTAS
-- ============================================================
CREATE TABLE `mascotas` (
  `id`              INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `especie_id`      TINYINT UNSIGNED NOT NULL,
  `raza_id`         SMALLINT UNSIGNED DEFAULT NULL,
  `nombre`          VARCHAR(60)      NOT NULL,
  `nombre_interno`  VARCHAR(60)      DEFAULT NULL COMMENT 'Código interno del albergue',
  `genero`          ENUM('M','F','ND') NOT NULL DEFAULT 'ND',
  `fecha_nacimiento` DATE            DEFAULT NULL,
  `edad_aprox_meses` SMALLINT UNSIGNED DEFAULT NULL COMMENT 'Si no se conoce fecha exacta',
  `color`           VARCHAR(60)      DEFAULT NULL,
  `peso_kg`         DECIMAL(5,2)     DEFAULT NULL,
  `descripcion`     TEXT             DEFAULT NULL,
  `personalidad`    VARCHAR(255)     DEFAULT NULL COMMENT 'Tags: amigable, tímido, juguetón...',
  -- Estado
  `estatus`         ENUM('rescatado','en_albergue','en_adopcion','proceso_adopcion','adoptado','fallecido','extraviado') NOT NULL DEFAULT 'en_albergue',
  `urgente`         TINYINT(1)       NOT NULL DEFAULT 0 COMMENT 'Requiere atención inmediata',
  -- Salud
  `esterilizado`    TINYINT(1)       NOT NULL DEFAULT 0,
  `vacunado`        TINYINT(1)       NOT NULL DEFAULT 0,
  `microchip`       VARCHAR(20)      DEFAULT NULL,
  `enfermedades`    TEXT             DEFAULT NULL,
  `discapacidad`    VARCHAR(255)     DEFAULT NULL,
  -- Ubicación
  `ubicacion`       VARCHAR(120)     DEFAULT NULL COMMENT 'Albergue o zona de rescate',
  `lat`             DECIMAL(10,7)    DEFAULT NULL,
  `lng`             DECIMAL(10,7)    DEFAULT NULL,
  -- Registro
  `registrado_por`  INT UNSIGNED     NOT NULL COMMENT 'usuario_id voluntario/admin',
  `foto_principal`  VARCHAR(255)     DEFAULT NULL,
  `activo`          TINYINT(1)       NOT NULL DEFAULT 1,
  `created_at`      TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_mascotas_especie`  (`especie_id`),
  KEY `idx_mascotas_estatus`  (`estatus`),
  KEY `idx_mascotas_urgente`  (`urgente`),
  KEY `idx_mascotas_registrado` (`registrado_por`),
  FULLTEXT KEY `ft_mascotas_busqueda` (`nombre`,`descripcion`,`personalidad`),
  CONSTRAINT `fk_mascotas_especie`    FOREIGN KEY (`especie_id`)  REFERENCES `especies` (`id`),
  CONSTRAINT `fk_mascotas_raza`       FOREIGN KEY (`raza_id`)     REFERENCES `razas` (`id`),
  CONSTRAINT `fk_mascotas_registrado` FOREIGN KEY (`registrado_por`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB COMMENT='Catálogo principal de mascotas';

CREATE TABLE `mascotas_fotos` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `mascota_id` INT UNSIGNED NOT NULL,
  `url`        VARCHAR(255) NOT NULL,
  `orden`      TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_mfotos_mascota` (`mascota_id`),
  CONSTRAINT `fk_mfotos_mascota` FOREIGN KEY (`mascota_id`) REFERENCES `mascotas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- 6. RESCATES
-- ============================================================
CREATE TABLE `rescates` (
  `id`            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `mascota_id`    INT UNSIGNED  DEFAULT NULL COMMENT 'NULL hasta que se registra la mascota',
  `voluntario_id` INT UNSIGNED  NOT NULL,
  `fecha_rescate` DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `descripcion`   TEXT          NOT NULL,
  `condicion`     ENUM('critica','mala','regular','buena','excelente') NOT NULL DEFAULT 'regular',
  -- Ubicación del rescate
  `direccion`     VARCHAR(200)  DEFAULT NULL,
  `colonia`       VARCHAR(80)   DEFAULT NULL,
  `municipio`     VARCHAR(80)   DEFAULT NULL,
  `estado_mx`     VARCHAR(60)   NOT NULL DEFAULT 'Ciudad de México',
  `lat`           DECIMAL(10,7) DEFAULT NULL,
  `lng`           DECIMAL(10,7) DEFAULT NULL,
  -- Seguimiento
  `estatus`       ENUM('reportado','en_camino','rescatado','en_atencion','completado','cancelado') NOT NULL DEFAULT 'reportado',
  `notas_seguimiento` TEXT      DEFAULT NULL,
  `created_at`    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_rescates_mascota`    (`mascota_id`),
  KEY `idx_rescates_voluntario` (`voluntario_id`),
  KEY `idx_rescates_estatus`    (`estatus`),
  KEY `idx_rescates_fecha`      (`fecha_rescate`),
  CONSTRAINT `fk_rescates_mascota`    FOREIGN KEY (`mascota_id`)    REFERENCES `mascotas` (`id`),
  CONSTRAINT `fk_rescates_voluntario` FOREIGN KEY (`voluntario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB COMMENT='Registro de rescates de mascotas';

-- ============================================================
-- 7. ADOPCIONES
-- ============================================================
CREATE TABLE `adopciones` (
  `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `mascota_id`       INT UNSIGNED NOT NULL,
  `adoptante_id`     INT UNSIGNED NOT NULL,
  `revisor_id`       INT UNSIGNED DEFAULT NULL COMMENT 'Admin/voluntario que revisa',
  -- Formulario
  `tipo_vivienda`    ENUM('casa_propia','casa_rentada','departamento','otro') DEFAULT NULL,
  `tiene_jardin`     TINYINT(1)   DEFAULT NULL,
  `otras_mascotas`   TINYINT(1)   DEFAULT NULL,
  `descripcion_otras_mascotas` VARCHAR(255) DEFAULT NULL,
  `ninos_en_casa`    TINYINT(1)   DEFAULT NULL,
  `experiencia_previa` TINYINT(1) DEFAULT NULL,
  `motivo`           TEXT         DEFAULT NULL,
  `compromisos`      TEXT         DEFAULT NULL COMMENT 'Compromisos firmados por el adoptante',
  `contrato_url`     VARCHAR(255) DEFAULT NULL COMMENT 'Ruta al PDF del contrato de adopción firmado',
  -- Proceso
  `estatus`          ENUM('solicitada','en_revision','visita_programada','aprobada','rechazada','completada','cancelada') NOT NULL DEFAULT 'solicitada',
  `motivo_rechazo`   VARCHAR(255) DEFAULT NULL,
  `fecha_solicitud`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_aprobacion` TIMESTAMP    NULL,
  `fecha_entrega`    DATE         NULL COMMENT 'Fecha en que la mascota va a su hogar',
  -- Seguimiento post-adopción
  `seguimiento_1m`   TEXT         DEFAULT NULL COMMENT 'Reporte 1 mes',
  `seguimiento_3m`   TEXT         DEFAULT NULL COMMENT 'Reporte 3 meses',
  `seguimiento_6m`   TEXT         DEFAULT NULL COMMENT 'Reporte 6 meses',
  `created_at`       TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_adopciones_mascota`   (`mascota_id`),
  KEY `idx_adopciones_adoptante` (`adoptante_id`),
  KEY `idx_adopciones_estatus`   (`estatus`),
  CONSTRAINT `fk_adopciones_mascota`   FOREIGN KEY (`mascota_id`)   REFERENCES `mascotas` (`id`),
  CONSTRAINT `fk_adopciones_adoptante` FOREIGN KEY (`adoptante_id`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `fk_adopciones_revisor`   FOREIGN KEY (`revisor_id`)   REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB COMMENT='Proceso completo de adopción';

-- ============================================================
-- 8. VETERINARIAS / CLÍNICAS
-- ============================================================
CREATE TABLE `veterinarias` (
  `id`            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `registrado_por` INT UNSIGNED NOT NULL COMMENT 'Usuario que registró la clínica',
  `nombre`        VARCHAR(120)  NOT NULL,
  `razon_social`  VARCHAR(150)  DEFAULT NULL,
  `rfc`           VARCHAR(13)   DEFAULT NULL,
  -- Contacto
  `telefono`      VARCHAR(20)   DEFAULT NULL,
  `telefono_2`    VARCHAR(20)   DEFAULT NULL,
  `email`         VARCHAR(120)  DEFAULT NULL,
  `sitio_web`     VARCHAR(200)  DEFAULT NULL,
  -- Ubicación
  `calle`         VARCHAR(120)  NOT NULL,
  `num_exterior`  VARCHAR(10)   DEFAULT NULL,
  `colonia`       VARCHAR(80)   NOT NULL,
  `municipio`     VARCHAR(80)   NOT NULL,
  `estado`        VARCHAR(60)   NOT NULL DEFAULT 'Ciudad de México',
  `cp`            CHAR(5)       DEFAULT NULL,
  `lat`           DECIMAL(10,7) NOT NULL COMMENT 'Google Maps geolocalización',
  `lng`           DECIMAL(10,7) NOT NULL,
  `google_place_id` VARCHAR(100) DEFAULT NULL,
  -- Horarios (JSON por día)
  `horarios`      JSON          DEFAULT NULL COMMENT '{"lun":"09:00-18:00","mar":"09:00-18:00",...}',
  `servicios`     JSON          DEFAULT NULL COMMENT '["consulta","vacunacion","cirugia","urgencias"]',
  -- Estado
  `convenio`      TINYINT(1)    NOT NULL DEFAULT 0 COMMENT 'Tiene convenio con PetGuard',
  `descuento_pct` TINYINT UNSIGNED DEFAULT NULL COMMENT '% descuento para mascotas PetGuard',
  `activo`        TINYINT(1)    NOT NULL DEFAULT 1,
  `verificada`    TINYINT(1)    NOT NULL DEFAULT 0,
  `foto_url`      VARCHAR(255)  DEFAULT NULL,
  `created_at`    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_vets_activo`   (`activo`),
  KEY `idx_vets_convenio` (`convenio`),
  KEY `idx_vets_geo`      (`lat`, `lng`),
  FULLTEXT KEY `ft_vets_busqueda` (`nombre`,`colonia`,`municipio`),
  CONSTRAINT `fk_vets_registrado` FOREIGN KEY (`registrado_por`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB COMMENT='Clínicas y veterinarias registradas';

-- ============================================================
-- 9. VETERINARIOS (profesionales)
-- ============================================================
CREATE TABLE `veterinarios` (
  `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `usuario_id`      INT UNSIGNED NOT NULL,
  `cedula_prof`     VARCHAR(20)  NOT NULL COMMENT 'Cédula profesional SEP',
  `especialidad`    VARCHAR(100) DEFAULT NULL,
  `universidad`     VARCHAR(120) DEFAULT NULL,
  `anios_exp`       TINYINT UNSIGNED DEFAULT NULL,
  `bio`             TEXT         DEFAULT NULL,
  `disponible`      TINYINT(1)   NOT NULL DEFAULT 1,
  `atiende_emergencias` TINYINT(1) NOT NULL DEFAULT 0,
  `tarifa_consulta` DECIMAL(8,2) DEFAULT NULL COMMENT 'MXN',
  `created_at`      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_vet_usuario`  (`usuario_id`),
  UNIQUE KEY `uk_vet_cedula`   (`cedula_prof`),
  CONSTRAINT `fk_vet_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB COMMENT='Profesionales veterinarios registrados';

-- ============================================================
-- 9.1 VETERINARIOS_SUCURSALES (Relación N:M)
-- ============================================================
CREATE TABLE `veterinarios_sucursales` (
  `veterinario_id` INT UNSIGNED NOT NULL,
  `veterinaria_id` INT UNSIGNED NOT NULL,
  `es_principal`   TINYINT(1)   NOT NULL DEFAULT 0 COMMENT '1 si es su clínica base, 0 si es adicional',
  `created_at`     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`veterinario_id`, `veterinaria_id`),
  CONSTRAINT `fk_vs_veterinario` FOREIGN KEY (`veterinario_id`) REFERENCES `veterinarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_vs_veterinaria` FOREIGN KEY (`veterinaria_id`) REFERENCES `veterinarias` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB COMMENT='Relación N:M: Un veterinario puede atender en múltiples sucursales';

-- ============================================================
-- 10. CITAS VETERINARIAS
-- ============================================================
CREATE TABLE `citas` (
  `id`              INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `mascota_id`      INT UNSIGNED  NOT NULL,
  `veterinaria_id`  INT UNSIGNED  DEFAULT NULL,
  `veterinario_id`  INT UNSIGNED  DEFAULT NULL,
  `solicitante_id`  INT UNSIGNED  NOT NULL COMMENT 'Adoptante o voluntario que agenda',
  `tipo`            ENUM('consulta_general','vacunacion','cirugia','esterilizacion','urgencia','seguimiento','desparasitacion','otro') NOT NULL DEFAULT 'consulta_general',
  `fecha_cita`      DATE          NOT NULL,
  `hora_inicio`     TIME          NOT NULL,
  `hora_fin`        TIME          DEFAULT NULL,
  `motivo`          TEXT          DEFAULT NULL,
  `notas_vet`       TEXT          DEFAULT NULL COMMENT 'Diagnóstico / indicaciones',
  `estatus`         ENUM('programada','confirmada','en_curso','completada','cancelada','no_asistio') NOT NULL DEFAULT 'programada',
  `costo`           DECIMAL(8,2)  DEFAULT NULL COMMENT 'MXN',
  `pagado`          TINYINT(1)    NOT NULL DEFAULT 0,
  `recordatorio_enviado` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at`      TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_citas_mascota`     (`mascota_id`),
  KEY `idx_citas_veterinaria` (`veterinaria_id`),
  KEY `idx_citas_veterinario` (`veterinario_id`),
  KEY `idx_citas_solicitante` (`solicitante_id`),
  KEY `idx_citas_fecha`       (`fecha_cita`),
  KEY `idx_citas_estatus`     (`estatus`),
  CONSTRAINT `fk_citas_mascota`     FOREIGN KEY (`mascota_id`)     REFERENCES `mascotas` (`id`),
  CONSTRAINT `fk_citas_veterinaria` FOREIGN KEY (`veterinaria_id`) REFERENCES `veterinarias` (`id`),
  CONSTRAINT `fk_citas_veterinario` FOREIGN KEY (`veterinario_id`) REFERENCES `veterinarios` (`id`),
  CONSTRAINT `fk_citas_solicitante` FOREIGN KEY (`solicitante_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB COMMENT='Agenda de citas veterinarias';

-- ============================================================
-- 11. EXPEDIENTE CLÍNICO / HISTORIAL DE SALUD
-- ============================================================
CREATE TABLE `expedientes` (
  `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `mascota_id`      INT UNSIGNED NOT NULL,
  `veterinario_id`  INT UNSIGNED NOT NULL,
  `cita_id`         INT UNSIGNED DEFAULT NULL,
  `fecha`           DATE         NOT NULL,
  `tipo`            ENUM('consulta','vacuna','cirugia','tratamiento','desparasitacion','laboratorio','otro') NOT NULL,
  `diagnostico`     TEXT         DEFAULT NULL,
  `tratamiento`     TEXT         DEFAULT NULL,
  `medicamentos`    TEXT         DEFAULT NULL COMMENT 'Nombre, dosis, duración',
  `peso_kg`         DECIMAL(5,2) DEFAULT NULL,
  `temperatura`     DECIMAL(4,1) DEFAULT NULL COMMENT 'Celsius',
  `proxima_cita`    DATE         DEFAULT NULL,
  `archivos`        JSON         DEFAULT NULL COMMENT 'URLs de estudios, radiografías, etc.',
  `notas`           TEXT         DEFAULT NULL,
  `created_at`      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_exp_mascota`     (`mascota_id`),
  KEY `idx_exp_veterinario` (`veterinario_id`),
  KEY `idx_exp_cita`        (`cita_id`),
  KEY `idx_exp_fecha`       (`fecha`),
  CONSTRAINT `fk_exp_mascota`     FOREIGN KEY (`mascota_id`)     REFERENCES `mascotas` (`id`),
  CONSTRAINT `fk_exp_veterinario` FOREIGN KEY (`veterinario_id`) REFERENCES `veterinarios` (`id`),
  CONSTRAINT `fk_exp_cita`        FOREIGN KEY (`cita_id`)        REFERENCES `citas` (`id`)
) ENGINE=InnoDB COMMENT='Expediente clínico de cada mascota';

-- ============================================================
-- 12. VACUNAS
-- ============================================================
CREATE TABLE `vacunas` (
  `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `mascota_id`      INT UNSIGNED NOT NULL,
  `veterinario_id`  INT UNSIGNED DEFAULT NULL,
  `nombre`          VARCHAR(80)  NOT NULL COMMENT 'Rabia, Moquillo, etc.',
  `lote`            VARCHAR(40)  DEFAULT NULL,
  `laboratorio`     VARCHAR(80)  DEFAULT NULL,
  `fecha_aplicacion` DATE        NOT NULL,
  `fecha_refuerzo`  DATE         DEFAULT NULL,
  `created_at`      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_vacunas_mascota`     (`mascota_id`),
  KEY `idx_vacunas_veterinario` (`veterinario_id`),
  CONSTRAINT `fk_vacunas_mascota`     FOREIGN KEY (`mascota_id`)     REFERENCES `mascotas` (`id`),
  CONSTRAINT `fk_vacunas_veterinario` FOREIGN KEY (`veterinario_id`) REFERENCES `veterinarios` (`id`)
) ENGINE=InnoDB COMMENT='Registro de vacunación por mascota';

-- ============================================================
-- 13. VOLUNTARIOS (perfil extendido)
-- ============================================================
CREATE TABLE `voluntarios` (
  `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `usuario_id`      INT UNSIGNED NOT NULL,
  `zona_cobertura`  VARCHAR(100) DEFAULT NULL COMMENT 'Alcaldía/municipio principal',
  `zonas_json`      JSON         DEFAULT NULL COMMENT 'Lista de alcaldías cubiertas',
  `disponibilidad`  JSON         DEFAULT NULL COMMENT '{"lun":["09:00-13:00"],"vie":["16:00-20:00"]}',
  `transporte`      ENUM('auto','moto','transporte_publico','bicicleta','a_pie') DEFAULT NULL,
  `capacidad_temporal` TINYINT UNSIGNED DEFAULT NULL COMMENT 'Max mascotas en hogar temporal',
  `hogar_temporal`  TINYINT(1)   NOT NULL DEFAULT 0 COMMENT 'Puede hospedar mascotas',
  `experiencia`     TEXT         DEFAULT NULL,
  `habilidades`     JSON         DEFAULT NULL COMMENT '["primeros_auxilios","adiestramiento","fotografia"]',
  `rescates_realizados` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `adopciones_gestionadas` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `activo`          TINYINT(1)   NOT NULL DEFAULT 1,
  `verificado`      TINYINT(1)   NOT NULL DEFAULT 0,
  `fecha_alta`      DATE         NOT NULL,
  `created_at`      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_vol_usuario` (`usuario_id`),
  KEY `idx_vol_zona`    (`zona_cobertura`),
  KEY `idx_vol_activo`  (`activo`),
  CONSTRAINT `fk_vol_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB COMMENT='Perfil extendido de voluntarios';

-- ============================================================
-- 14. DONACIONES
-- ============================================================
CREATE TABLE `donaciones` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `donante_id` INT UNSIGNED DEFAULT NULL COMMENT 'NULL si es anónimo',
  `tipo` ENUM('monetaria', 'especie') NOT NULL,
  `concepto` VARCHAR(150) NOT NULL COMMENT 'Ej: 5kg croquetas, $500 MXN',
  `valor_estimado` DECIMAL(10,2) DEFAULT NULL COMMENT 'Valor en MXN',
  `estado` ENUM('pendiente', 'completada', 'cancelada') NOT NULL DEFAULT 'pendiente',
  `fecha` DATE NOT NULL,
  `notas` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_donaciones_fecha` (`fecha`),
  CONSTRAINT `fk_donaciones_donante` FOREIGN KEY (`donante_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB COMMENT='Registro de donaciones para transparencia';

-- ============================================================
-- 15. NOTIFICACIONES
-- ============================================================
CREATE TABLE `notificaciones` (
  `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `usuario_id`  INT UNSIGNED    NOT NULL,
  `tipo`        ENUM('adopcion','cita','rescate','sistema','alerta','recordatorio') NOT NULL,
  `titulo`      VARCHAR(120)    NOT NULL,
  `mensaje`     TEXT            NOT NULL,
  `url`         VARCHAR(255)    DEFAULT NULL COMMENT 'Link de acción',
  `leida`       TINYINT(1)      NOT NULL DEFAULT 0,
  `created_at`  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_notif_usuario` (`usuario_id`),
  KEY `idx_notif_leida`   (`leida`),
  CONSTRAINT `fk_notif_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB COMMENT='Centro de notificaciones por usuario';

-- ============================================================
-- 16. AUDITORÍA / LOG DE ACCIONES
-- ============================================================
CREATE TABLE `auditoria` (
  `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `usuario_id`  INT UNSIGNED    DEFAULT NULL,
  `accion`      VARCHAR(80)     NOT NULL COMMENT 'login, create_mascota, approve_adopcion...',
  `tabla`       VARCHAR(60)     DEFAULT NULL,
  `registro_id` INT UNSIGNED    DEFAULT NULL,
  `datos_antes` JSON            DEFAULT NULL,
  `datos_despues` JSON          DEFAULT NULL,
  `ip`          VARCHAR(45)     DEFAULT NULL,
  `created_at`  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_audit_usuario` (`usuario_id`),
  KEY `idx_audit_accion`  (`accion`),
  KEY `idx_audit_tabla`   (`tabla`),
  KEY `idx_audit_fecha`   (`created_at`)
) ENGINE=InnoDB COMMENT='Log de auditoría completo del sistema';

-- ============================================================
-- 17. CONFIGURACIÓN DEL SISTEMA
-- ============================================================
CREATE TABLE `config_sistema` (
  `clave`      VARCHAR(60)  NOT NULL,
  `valor`      TEXT         NOT NULL,
  `tipo`       ENUM('string','integer','boolean','json') NOT NULL DEFAULT 'string',
  `descripcion` VARCHAR(200) DEFAULT NULL,
  `updated_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`clave`)
) ENGINE=InnoDB COMMENT='Parámetros globales del sistema';

INSERT INTO `config_sistema` (`clave`, `valor`, `tipo`, `descripcion`) VALUES
('app_nombre',          'PetGuard',                'string',  'Nombre de la aplicación'),
('app_version',         '1.1.0',                  'string',  'Versión actual'),
('app_pais',            'MX',                     'string',  'País de operación'),
('app_timezone',        'America/Mexico_City',    'string',  'Zona horaria'),
('adopcion_dias_proceso', '30',                   'integer', 'Días máximo para completar adopción'),
('cita_anticipacion_min', '24',                   'integer', 'Horas mínimas de anticipación para cita'),
('recordatorio_horas',  '24',                     'integer', 'Horas antes de cita para enviar recordatorio'),
('mapa_lat_default',    '19.4326296',             'string',  'Lat centro mapa CDMX'),
('mapa_lng_default',    '-99.1331785',            'string',  'Lng centro mapa CDMX'),
('mapa_zoom_default',   '12',                     'integer', 'Zoom inicial del mapa'),
('google_maps_key',     'TU_API_KEY_AQUI',        'string',  'Google Maps API Key'),
('max_fotos_mascota',   '8',                      'integer', 'Máximo de fotos por mascota'),
('seguimiento_1m',      '30',                     'integer', 'Días para primer seguimiento post-adopción'),
('seguimiento_3m',      '90',                     'integer', 'Días para seguimiento 3 meses'),
('seguimiento_6m',      '180',                    'integer', 'Días para seguimiento 6 meses'),
('email_from',          'no-reply@petguard.mx',   'string',  'Email remitente'),
('whatsapp_numero',     '5215500000000',          'string',  'WhatsApp de contacto'),
('redes_instagram',     '@petguard_mx',           'string',  'Instagram oficial');

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- VISTAS ÚTILES
-- ============================================================

CREATE OR REPLACE VIEW `v_mascotas_disponibles` AS
SELECT
  m.id, m.nombre, e.nombre AS especie, r.nombre AS raza,
  m.genero, m.edad_aprox_meses, m.estatus, m.urgente,
  m.esterilizado, m.vacunado, m.foto_principal,
  m.ubicacion, m.lat, m.lng,
  CONCAT(u.nombre,' ',u.apellido_paterno) AS registrado_por
FROM mascotas m
JOIN especies e ON m.especie_id = e.id
LEFT JOIN razas r ON m.raza_id = r.id
JOIN usuarios u ON m.registrado_por = u.id
WHERE m.estatus IN ('en_albergue','en_adopcion') AND m.activo = 1;

CREATE OR REPLACE VIEW `v_citas_hoy` AS
SELECT
  c.id, c.tipo, c.fecha_cita, c.hora_inicio, c.hora_fin, c.estatus,
  m.nombre AS mascota, e.nombre AS especie,
  CONCAT(u.nombre,' ',u.apellido_paterno) AS solicitante,
  v.nombre AS veterinaria,
  CONCAT(uv.nombre,' ',uv.apellido_paterno) AS veterinario
FROM citas c
JOIN mascotas m ON c.mascota_id = m.id
JOIN especies e ON m.especie_id = e.id
JOIN usuarios u ON c.solicitante_id = u.id
LEFT JOIN veterinarias v ON c.veterinaria_id = v.id
LEFT JOIN veterinarios vt ON c.veterinario_id = vt.id
LEFT JOIN usuarios uv ON vt.usuario_id = uv.id
WHERE c.fecha_cita = CURDATE()
ORDER BY c.hora_inicio;

CREATE OR REPLACE VIEW `v_adopciones_pendientes` AS
SELECT
  a.id, a.estatus, a.fecha_solicitud,
  m.nombre AS mascota, e.nombre AS especie, m.foto_principal,
  CONCAT(u.nombre,' ',u.apellido_paterno) AS adoptante,
  u.email AS email_adoptante, u.telefono AS tel_adoptante
FROM adopciones a
JOIN mascotas m ON a.mascota_id = m.id
JOIN especies e ON m.especie_id = e.id
JOIN usuarios u ON a.adoptante_id = u.id
WHERE a.estatus IN ('solicitada','en_revision','visita_programada')
ORDER BY a.fecha_solicitud ASC;

CREATE OR REPLACE VIEW `v_estadisticas_generales` AS
SELECT
  (SELECT COUNT(*) FROM mascotas WHERE activo=1) AS total_mascotas,
  (SELECT COUNT(*) FROM mascotas WHERE estatus='en_adopcion' AND activo=1) AS en_adopcion,
  (SELECT COUNT(*) FROM mascotas WHERE estatus='adoptado') AS adoptadas_total,
  (SELECT COUNT(*) FROM mascotas WHERE estatus='adoptado' AND MONTH(updated_at)=MONTH(NOW()) AND YEAR(updated_at)=YEAR(NOW())) AS adoptadas_mes,
  (SELECT COUNT(*) FROM rescates WHERE MONTH(fecha_rescate)=MONTH(NOW())) AS rescates_mes,
  (SELECT COUNT(*) FROM voluntarios WHERE activo=1) AS voluntarios_activos,
  (SELECT COUNT(*) FROM veterinarias WHERE activo=1) AS veterinarias_activas,
  (SELECT COUNT(*) FROM adopciones WHERE estatus IN ('solicitada','en_revision')) AS adopciones_pendientes,
  (SELECT COUNT(*) FROM citas WHERE fecha_cita=CURDATE() AND estatus NOT IN ('cancelada','no_asistio')) AS citas_hoy;

-- ============================================================
-- FIN DEL SCHEMA PETGUARD v1.1
-- ============================================================