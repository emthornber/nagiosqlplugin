DROP TABLE IF EXISTS `glpi_plugin_nagiosql_host`;

CREATE TABLE `glpi_plugin_nagiosql_host` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `name` varchar(255) DEFAULT NULL,
   `alias` varchar(255) DEFAULT NULL,
   `parents` varchar(255) DEFAULT NULL,
   `template` varchar(255) DEFAULT NULL,
   PRIMARY KEY (`id`),
   KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

