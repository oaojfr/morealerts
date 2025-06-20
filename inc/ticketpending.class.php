<?php
if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

/**
 * Class PluginMorealertsTicketPending
 */
class PluginMorealertsTicketPending extends CommonDBTM {

   static $rightname = "plugin_morealerts";

   static function getTypeName($nb = 0) {
      return _n('Ticket pending too long', 'Tickets pending too long', $nb, 'morealerts');
   }

   static function cronInfo($name) {
      switch ($name) {
         case 'AdditionalalertsTicketPending':
            return [
               'description' => PluginMorealertsTicketPending::getTypeName(2)
            ];
            break;
      }
      return [];
   }

   static function query($delay_pending, $entity) {
      // Statut 5 = En attente dans GLPI par défaut
      $delay_stamp = mktime(0, 0, 0, date("m"), date("d") - $delay_pending, date("Y"));
      $date = date("Y-m-d", $delay_stamp) . " 00:00:00";
      $query = "SELECT glpi_tickets.*\n"
             . "FROM glpi_tickets\n"
             . "WHERE glpi_tickets.date_mod <= '" . $date . "'\n"
             . "AND glpi_tickets.status = 5\n"
             . "AND glpi_tickets.entities_id = '" . $entity . "'\n"
             . "AND glpi_tickets.is_deleted = 0\n"
             . "ORDER BY glpi_tickets.id";
      return $query;
   }

   static function displayBody($data) {
      global $CFG_GLPI;
      $dbu = new DbUtils();
      $body = "<tr class='tab_bg_2'><td><a href='" . $CFG_GLPI["root_doc"] . "/front/ticket.form.php?id=" . $data["id"] . "'>" . $data["name"] . "</a></td>";
      $body .= "<td class='center'>" . Dropdown::getDropdownName("glpi_entities", $data["entities_id"]) . "</td>";
      $body .= "<td>" . Ticket::getStatus($data["status"]) . "</td>";
      $body .= "<td>" . Html::convDateTime($data["date"]) . "</td>";
      $body .= "<td>" . Html::convDateTime($data["date_mod"]) . "</td>";
      $body .= "</tr>";
      return $body;
   }

   static function cronAdditionalalertsTicketPending($task = null) {
      global $DB, $CFG_GLPI;
      if (!$CFG_GLPI["notifications_mailing"]) {
         return 0;
      }
      $config = PluginMorealertsConfig::getConfig();
      $delay = $config->getDelayTicketPending();
      if ($delay <= 0) {
         return 0;
      }
      $entities = [$_SESSION["glpiactive_entity"] => $delay];
      $cron_status = 0;
      foreach ($entities as $entity => $delay_pending) {
         $query = self::query($delay_pending, $entity);
         $result = $DB->query($query);
         if ($DB->numrows($result) > 0) {
            // Déclencher la notification si besoin
            $cron_status = 1;
         }
      }
      return $cron_status;
   }
}
