ALTER TABLE `glpi_plugin_morealerts_configs` ADD `delay_ticket_high_priority` int(11) NOT NULL DEFAULT '0';
ALTER TABLE `glpi_plugin_morealerts_configs` ADD `delay_ticket_pending` int(11) NOT NULL DEFAULT '0';
ALTER TABLE `glpi_plugin_morealerts_configs` ADD `use_equipment_noloc_alert` tinyint(1) NOT NULL DEFAULT '0';
