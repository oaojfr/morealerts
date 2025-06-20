DROP TABLE IF EXISTS `glpi_plugin_morealerts_configs`;
CREATE TABLE `glpi_plugin_morealerts_configs` (
   `id` int unsigned NOT NULL auto_increment,
   `use_infocom_alert` tinyint NOT NULL DEFAULT '0',
   `use_ink_alert` tinyint NOT NULL DEFAULT '0',
   `delay_ticket_alert` int unsigned NOT NULL default '0',
   `delay_ticket_waiting_validation` int unsigned NOT NULL default '0',
   `delay_ticket_waiting_user` int unsigned NOT NULL default '0',
   `max_open_tickets_tech` int unsigned NOT NULL default '0',
   `delay_ticket_high_priority` int unsigned NOT NULL default '0',
   `delay_ticket_pending` int unsigned NOT NULL default '0',
   `use_equipment_noloc_alert` tinyint NOT NULL DEFAULT '0',
   PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

INSERT INTO `glpi_plugin_morealerts_configs` ( `id`, `use_infocom_alert`, `use_ink_alert`, `delay_ticket_alert`, `delay_ticket_waiting_validation`, `delay_ticket_waiting_user`, `max_open_tickets_tech`, `delay_ticket_high_priority`, `delay_ticket_pending`, `use_equipment_noloc_alert`)
VALUES (1, 0, 0, 0, 0, 0, 0, 0, 0, 0);

DROP TABLE IF EXISTS `glpi_plugin_morealerts_infocomalerts`;
CREATE TABLE `glpi_plugin_morealerts_infocomalerts` (
   `id` int unsigned NOT NULL auto_increment,
   `entities_id` int unsigned NOT NULL default '0',
   `use_infocom_alert` tinyint NOT NULL DEFAULT '0',
   PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

DROP TABLE IF EXISTS `glpi_plugin_morealerts_ticketunresolveds`;
CREATE TABLE `glpi_plugin_morealerts_ticketunresolveds` (
   `id` int unsigned NOT NULL auto_increment,
   `entities_id` int unsigned NOT NULL default '0',
   `delay_ticket_alert` int unsigned NOT NULL DEFAULT '0',
   PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

DROP TABLE IF EXISTS `glpi_plugin_morealerts_inkalerts`;
CREATE TABLE `glpi_plugin_morealerts_inkalerts` (
   `id` int unsigned NOT NULL auto_increment,
   `entities_id` int unsigned NOT NULL default '0',
   `use_ink_alert` tinyint NOT NULL DEFAULT '0',
   PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

DROP TABLE IF EXISTS `glpi_plugin_morealerts_notificationtypes`;
CREATE TABLE `glpi_plugin_morealerts_notificationtypes` (
   `id` int unsigned NOT NULL AUTO_INCREMENT ,
   `types_id` int unsigned NOT NULL default '0' COMMENT 'RELATION to glpi_computertypes (id)',
   PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

DROP TABLE IF EXISTS `glpi_plugin_morealerts_inkthresholds`;
CREATE TABLE `glpi_plugin_morealerts_inkthresholds` (
   `id` int unsigned NOT NULL AUTO_INCREMENT,
   `cartridges_id` int unsigned NOT NULL default '0' COMMENT 'RELATION to glpi_cartridgeitems (id)',
   `threshold` int unsigned NOT NULL default '10',
   PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

DROP TABLE IF EXISTS `glpi_plugin_morealerts_inkprinterstates`;
CREATE TABLE `glpi_plugin_morealerts_inkprinterstates` (
   `id` int unsigned NOT NULL AUTO_INCREMENT,
   `states_id` int unsigned NOT NULL default '0' COMMENT 'RELATION to glpi_states (id)',
   PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

INSERT INTO `glpi_notificationtemplates` VALUES(NULL, 'Alert infocoms', 'PluginMorealertsInfocomAlert', '2010-03-13 10:44:46','',NULL, '2010-03-13 10:44:46');
INSERT INTO `glpi_notificationtemplates` VALUES(NULL, 'Alert machines ocs', 'PluginMorealertsOcsAlert', '2010-03-13 10:44:46','',NULL, '2010-03-13 10:44:46');
INSERT INTO `glpi_notificationtemplates` VALUES(NULL, 'Alert ink level', 'PluginMorealertsInkAlert', '2010-03-13 10:44:46','',NULL, '2010-03-13 10:44:46');
INSERT INTO `glpi_notificationtemplates` VALUES(NULL, 'Alert Ticket Unresolved', 'PluginMorealertsTicketUnresolved', '2010-03-13 10:44:46','',NULL, '2010-03-13 10:44:46');
