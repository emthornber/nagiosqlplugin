-- Table creation for Nagiosql plugin version 1.0.0
--
-- 14 November 2011 - E M Thornber
-- --------------------------------------------------
--

DROP TABLE IF EXISTS `glpi_plugin_nagiosql_hosts`;

CREATE TABLE `glpi_plugin_nagiosql_hosts` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `name` varchar(255) DEFAULT NULL,
   `alias` varchar(255) DEFAULT NULL,
   `template` varchar(255) DEFAULT NULL,
   PRIMARY KEY (`id`),
   KEY `name` (`name`),
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3;

DROP TABLE IF EXISTS `glpi_plugin_nagiosql_links`;

CREATE TABLE `glpi_plugin_nagiosql_links` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `items_id` int(11) NOT NULL default '0',
   `itemtype` varchar(100) NOT NULL default '',
   `parent_id` int(11) NOT NULL default '0',
   PRIMARY KEY (`id`),
   KEY `itemtype` (`itemtype`),
   KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3;

DROP TABLE IF EXISTS `glpi_plugin_nagiosql_profiles`;

CREATE TABLE IF NOT EXISTS `glpi_plugin_nagiosql_profiles` (
    `id` int(11) NOT NULL auto_increment,
    `name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
    `interface` varchar(50) collate utf8_unicode_ci NOT NULL default 'relations',
    `is_default` smallint(6) NOT NULL default '0',
    `relations` char(1) default NULL,
    PRIMARY KEY  (`ID`),
    KEY `interface` (`interface`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3;
