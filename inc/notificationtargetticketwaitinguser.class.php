<?php
if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

class PluginMorealertsNotificationTargetTicketWaitingUser extends NotificationTarget {

   static $rightname = "plugin_morealerts";

   function getEvents() {
      return ['ticketwaitinguser' => PluginMorealertsTicketWaitingUser::getTypeName(2)];
   }

   function getTags() {
      $notificationTargetTicket = NotificationTarget::getInstance(new Ticket(), 'alertnotclosed', []);
      $notificationTargetTicket->getTags();
      $this->tag_descriptions = $notificationTargetTicket->tag_descriptions;
      asort($this->tag_descriptions);
   }

   function addDataForTemplate($event, $options = []) {
      $ticket = new Ticket();
      $ticket->getEmpty();
      $notificationTargetTicket = NotificationTarget::getInstance($ticket, 'ticketwaitinguser', $options);
      $notificationTargetTicket->obj->fields['id'] = 0;
      $notificationTargetTicket->addDataForTemplate('alertnotclosed', $options);
      $this->data = $notificationTargetTicket->data;
   }
}
