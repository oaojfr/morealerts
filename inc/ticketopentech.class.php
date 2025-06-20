<?php
if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

/**
 * Class PluginMorealertsTicketOpenTech
 */
class PluginMorealertsTicketOpenTech extends CommonDBTM {

   static $rightname = "plugin_morealerts";

   static function getTypeName($nb = 0) {
      return _n('Technician with too many open tickets', 'Technicians with too many open tickets', $nb, 'morealerts');
   }

   static function cronInfo($name) {
      switch ($name) {
         case 'AdditionalalertsTicketOpenTech':
            return [
               'description' => PluginMorealertsTicketOpenTech::getTypeName(2)
            ];
            break;
      }
      return [];
   }

   static function query($max_open_tickets, $entity) {
      $query = "SELECT tu.users_id, COUNT(t.id) as open_count\n"
             . "FROM glpi_tickets t\n"
             . "JOIN glpi_tickets_users tu ON t.id = tu.tickets_id AND tu.type = 2\n"
             . "WHERE t.status IN (1,2,3,4,5)\n" // Statuts ouverts
             . "AND t.entities_id = '" . $entity . "'\n"
             . "AND t.is_deleted = 0\n"
             . "GROUP BY tu.users_id\n"
             . "HAVING open_count > " . intval($max_open_tickets);
      return $query;
   }

   static function displayBody($data) {
      global $CFG_GLPI;
      $dbu = new DbUtils();
      $body = "<tr class='tab_bg_2'>";
      $body .= "<td>" . $dbu->getUserName($data["users_id"]) . "</td>";
      $body .= "<td class='center'>" . $data["open_count"] . "</td>";
      $body .= "</tr>";
      return $body;
   }

   static function cronAdditionalalertsTicketOpenTech($task = null) {
      global $DB, $CFG_GLPI;
      if (!$CFG_GLPI["notifications_mailing"]) {
         return 0;
      }
      $config = PluginMorealertsConfig::getConfig();
      $max = $config->getMaxOpenTicketsTech();
      if ($max <= 0) {
         return 0;
      }
      $entities = [$_SESSION["glpiactive_entity"] => $max];
      $cron_status = 0;
      foreach ($entities as $entity => $max_open_tickets) {
         $query = self::query($max_open_tickets, $entity);
         $result = $DB->query($query);
         if ($DB->numrows($result) > 0) {
            // Ici, on pourrait déclencher la notification (à compléter selon la logique du plugin)
            $cron_status = 1;
         }
      }
      return $cron_status;
   }
}
