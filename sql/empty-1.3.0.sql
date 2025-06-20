DROP TABLE IF EXISTS `glpi_plugin_morealerts_configs`;
CREATE TABLE `glpi_plugin_morealerts_configs` (
	`id` int(11) NOT NULL auto_increment,
	`delay_reminder` int(11) NOT NULL default '-1',
	`delay_ocs` int(11) NOT NULL default '-1',
	`use_infocom_alert` TINYINT( 1 ) NOT NULL DEFAULT '-1',
	`use_newocs_alert` TINYINT( 1 ) NOT NULL DEFAULT '-1',
	PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `glpi_plugin_morealerts_configs` ( `id`,`delay_reminder`,`delay_ocs`,`use_infocom_alert`,`use_newocs_alert`) VALUES ('1','-1','-1','-1','-1');

DROP TABLE IF EXISTS `glpi_plugin_morealerts_reminderalerts`;
CREATE TABLE `glpi_plugin_morealerts_reminderalerts` (
	`id` int(11) NOT NULL auto_increment,
	`entities_id` int(11) NOT NULL default '0',
	`delay_reminder` int(11) NOT NULL default '-1',
	PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `glpi_plugin_morealerts_ocsalerts`;
CREATE TABLE `glpi_plugin_morealerts_ocsalerts` (
	`id` int(11) NOT NULL auto_increment,
	`entities_id` int(11) NOT NULL default '0',
	`delay_ocs` int(11) NOT NULL default '-1',
	`use_newocs_alert` TINYINT( 1 ) NOT NULL DEFAULT '-1',
	PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `glpi_plugin_morealerts_infocomalerts`;
CREATE TABLE `glpi_plugin_morealerts_infocomalerts` (
	`id` int(11) NOT NULL auto_increment,
	`entities_id` int(11) NOT NULL default '0',
	`use_infocom_alert` TINYINT( 1 ) NOT NULL DEFAULT '-1',
	PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `glpi_plugin_morealerts_notificationstates`;
CREATE TABLE `glpi_plugin_morealerts_notificationstates` (
	`id` int(11) NOT NULL auto_increment,
	`states_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_states (id)',
	PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `glpi_plugin_morealerts_notificationtypes`;
CREATE TABLE `glpi_plugin_morealerts_notificationtypes` (
	`id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
	`types_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_computertypes (id)',
	PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `glpi_plugin_morealerts_profiles`;
CREATE TABLE `glpi_plugin_morealerts_profiles` (
	`id` int(11) NOT NULL auto_increment,
	`profiles_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_profiles (id)',
	`morealerts` char(1) collate utf8_unicode_ci default NULL,
	PRIMARY KEY  (`id`),
	KEY `profiles_id` (`profiles_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `glpi_notificationtemplates` VALUES(NULL, 'Alert infocoms', 'PluginMorealertsInfocomAlert', '2010-03-13 10:44:46','',NULL);
INSERT INTO `glpi_notificationtemplates` VALUES(NULL, 'Alert machines ocs', 'PluginMorealertsOcsAlert', '2010-03-13 10:44:46','',NULL);
INSERT INTO `glpi_notificationtemplates` VALUES(NULL, 'Alert reminders', 'PluginMorealertsReminderAlert', '2010-03-13 10:44:46','',NULL);