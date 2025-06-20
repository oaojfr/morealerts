DROP TABLE IF EXISTS `glpi_plugin_alerting_config`;
ALTER TABLE `glpi_plugin_alerting_state` RENAME `glpi_plugin_morealerts_notificationstates`;
ALTER TABLE `glpi_plugin_alerting_type` RENAME `glpi_plugin_morealerts_notificationtypes`;
ALTER TABLE `glpi_plugin_alerting_profiles` RENAME `glpi_plugin_morealerts_profiles`;
DROP TABLE IF EXISTS `glpi_plugin_alerting_mailing`;

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

ALTER TABLE `glpi_plugin_morealerts_notificationstates` 
   CHANGE `ID` `id` int(11) NOT NULL auto_increment,
   CHANGE `state` `states_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_states (id)';

ALTER TABLE `glpi_plugin_morealerts_notificationtypes` 
   CHANGE `ID` `id` int(11) NOT NULL auto_increment,
   CHANGE `type` `types_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_computertypes (id)';

ALTER TABLE `glpi_plugin_morealerts_profiles` 
   CHANGE `ID` `id` int(11) NOT NULL auto_increment,
   ADD `profiles_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_profiles (id)',
   CHANGE `alerting` `morealerts` char(1) collate utf8_unicode_ci default NULL,
   ADD INDEX (`profiles_id`);

UPDATE `glpi_plugin_morealerts_profiles` SET `morealerts`='w' WHERE `morealerts` ='r';

INSERT INTO `glpi_notificationtemplates` VALUES(NULL, 'Alert infocoms', 'PluginMorealertsInfocomAlert', '2010-03-13 10:44:46','',NULL, '2010-03-13 10:44:46');
INSERT INTO `glpi_notificationtemplates` VALUES(NULL, 'Alert machines ocs', 'PluginMorealertsOcsAlert', '2010-03-13 10:44:46','',NULL, '2010-03-13 10:44:46');
INSERT INTO `glpi_notificationtemplates` VALUES(NULL, 'Alert reminders', 'PluginMorealertsReminderAlert', '2010-03-13 10:44:46','',NULL, '2010-03-13 10:44:46');