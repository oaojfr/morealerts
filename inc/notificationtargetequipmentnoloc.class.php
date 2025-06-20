<?php
if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

class PluginMorealertsNotificationTargetEquipmentNoLoc extends NotificationTarget {

   static $rightname = "plugin_morealerts";

   function getEvents() {
      return ['equipmentnoloc' => PluginMorealertsEquipmentNoLoc::getTypeName(2)];
   }

   function getTags() {
      $notificationTargetComputer = NotificationTarget::getInstance(new Computer(), 'alertnotlocated', []);
      $notificationTargetComputer->getTags();
      $this->tag_descriptions = $notificationTargetComputer->tag_descriptions;
      asort($this->tag_descriptions);
   }

   function addDataForTemplate($event, $options = []) {
      $computer = new Computer();
      $computer->getEmpty();
      $notificationTargetComputer = NotificationTarget::getInstance($computer, 'equipmentnoloc', $options);
      $notificationTargetComputer->obj->fields['id'] = 0;
      $notificationTargetComputer->addDataForTemplate('alertnotlocated', $options);
      $this->data = $notificationTargetComputer->data;
   }
}
