CREATE TABLE IF NOT EXISTS `kn_contact_messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(190) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `subject` varchar(80) DEFAULT NULL,
  `message` text NOT NULL,
  `page_url` varchar(255) DEFAULT NULL,
  `ip_hash` char(64) DEFAULT NULL,
  `mail_sent` tinyint NOT NULL DEFAULT 0,
  `status` enum('new','done') NOT NULL DEFAULT 'new',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ip_time` (`ip_hash`,`created_at`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
