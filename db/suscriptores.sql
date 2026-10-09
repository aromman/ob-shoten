-- Lista de interesados de la página "Próximamente".
-- Ejecutar una vez en la base de cPanel (phpMyAdmin) y en la base local.

CREATE TABLE IF NOT EXISTS suscriptores (
  id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  email       VARCHAR(254)  NOT NULL,
  origen      VARCHAR(50)   NOT NULL DEFAULT 'proximamente',
  ip          VARCHAR(45)   NULL,
  user_agent  VARCHAR(255)  NULL,
  creado_en   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_suscriptores_email (email),
  KEY idx_suscriptores_ip_fecha (ip, creado_en)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
