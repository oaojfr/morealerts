<?php
if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

/**
 * Class PluginMorealertsTicketWaitingValidation
 */
class PluginMorealertsTicketWaitingValidation extends CommonDBTM {

   static $rightname = "plugin_morealerts";

   static function getTypeName($nb = 0) {
      return _n('Ticket waiting for validation', 'Tickets waiting for validation', $nb, 'morealerts');
   }

   static function cronInfo($name) {
      switch ($name) {
         case 'AdditionalalertsTicketWaitingValidation':
            return [
               'description' => PluginMorealertsTicketWaitingValidation::getTypeName(2)
            ];
            break;
      }
      return [];
   }

   static function query($delay_waiting_validation, $entity) {
      // Statut 2 = En attente de validation dans GLPI par défaut
      $delay_stamp = mktime(0, 0, 0, date("m"), date("d") - $delay_waiting_validation, date("Y"));
      $date = date("Y-m-d", $delay_stamp) . " 00:00:00";
      $query = "SELECT glpi_tickets.*\n"
             . "FROM glpi_tickets\n"
             . "WHERE glpi_tickets.date <= '" . $date . "'\n"
             . "AND glpi_tickets.status = 2\n"
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

   static function cronAdditionalalertsTicketWaitingValidation($task = null) {
      global $DB, $CFG_GLPI;
      if (!$CFG_GLPI["notifications_mailing"]) {
         return 0;
      }
      $config = PluginMorealertsConfig::getConfig();
      $delay = $config->getDelayTicketWaitingValidation();
      if ($delay <= 0) {
         return 0;
      }
      $entities = [$_SESSION["glpiactive_entity"] => $delay];
      $cron_status = 0;
      foreach ($entities as $entity => $delay_waiting_validation) {
         $query = self::query($delay_waiting_validation, $entity);
         $result = $DB->query($query);
         if ($DB->numrows($result) > 0) {
            // Ici, on pourrait déclencher la notification (à compléter selon la logique du plugin)
            $cron_status = 1;
         }
      }
      return $cron_status;
   }

   // ...autres méthodes à ajouter pour la gestion de la config, notification, etc.
}
