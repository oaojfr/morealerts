<?php
if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

class PluginMorealertsNotificationTargetTicketOpenTech extends NotificationTarget {

   static $rightname = "plugin_morealerts";

   function getEvents() {
      return ['ticketopentech' => PluginMorealertsTicketOpenTech::getTypeName(2)];
   }

   function getTags() {
      $this->tag_descriptions = [
         'tech.name' => __('Technician'),
         'tech.open_count' => __('Number of open tickets')
      ];
      asort($this->tag_descriptions);
   }

   function addDataForTemplate($event, $options = []) {
      $this->data['##tech.name##'] = $options['tech_name'] ?? '';
      $this->data['##tech.open_count##'] = $options['open_count'] ?? '';
   }
}
