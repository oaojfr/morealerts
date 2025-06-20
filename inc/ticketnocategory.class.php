<?php
if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

class PluginMorealertsTicketNoCategory extends CommonDBTM {
   static $rightname = "plugin_morealerts";

   static function getTypeName($nb = 0) {
      return _n('Ticket with no category', 'Tickets with no category', $nb, 'morealerts');
   }

   static function cronInfo($name) {
      switch ($name) {
         case 'AdditionalalertsTicketNoCategory':
            return [
               'description' => PluginMorealertsTicketNoCategory::getTypeName(2)
            ];
      }
      return [];
   }

   static function query($entity) {
      $query = "SELECT glpi_tickets.*\n"
             . "FROM glpi_tickets\n"
             . "WHERE (glpi_tickets.itilcategories_id IS NULL OR glpi_tickets.itilcategories_id = 0)\n"
             . "AND glpi_tickets.status IN (1,2,3,4,5)\n"
             . "AND glpi_tickets.entities_id = '" . $entity . "'\n"
             . "AND glpi_tickets.is_deleted = 0\n"
             . "ORDER BY glpi_tickets.id";
      return $query;
   }

   static function displayBody($data) {
      global $CFG_GLPI;
      $body = "<tr class='tab_bg_2'><td><a href='" . $CFG_GLPI["root_doc"] . "/front/ticket.form.php?id=" . $data["id"] . "'>" . $data["name"] . "</a></td>";
      $body .= "<td class='center'>" . Dropdown::getDropdownName("glpi_entities", $data["entities_id"]) . "</td>";
      $body .= "<td>" . Ticket::getStatus($data["status"]) . "</td>";
      $body .= "<td>" . Html::convDateTime($data["date"]) . "</td>";
      $body .= "<td>" . Html::convDateTime($data["date_mod"]) . "</td>";
      $body .= "</tr>";
      return $body;
   }

   static function cronAdditionalalertsTicketNoCategory($task = null) {
      global $DB, $CFG_GLPI;
      if (!$CFG_GLPI["notifications_mailing"]) {
         return 0;
      }
      $config = PluginMorealertsConfig::getConfig();
      if (!$config->useTicketNoCategoryAlert()) {
         return 0;
      }
      $entities = [$_SESSION["glpiactive_entity"] => 1];
      $cron_status = 0;
      foreach ($entities as $entity => $dummy) {
         $query = self::query($entity);
         $result = $DB->query($query);
         if ($DB->numrows($result) > 0) {
            $cron_status = 1;
         }
      }
      return $cron_status;
   }
}
