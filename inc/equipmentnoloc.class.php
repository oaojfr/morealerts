<?php
if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

/**
 * Class PluginMorealertsEquipmentNoLoc
 */
class PluginMorealertsEquipmentNoLoc extends CommonDBTM {

   static $rightname = "plugin_morealerts";

   static function getTypeName($nb = 0) {
      return _n('Equipment with no location', 'Equipments with no location', $nb, 'morealerts');
   }

   static function cronInfo($name) {
      switch ($name) {
         case 'AdditionalalertsEquipmentNoLoc':
            return [
               'description' => PluginMorealertsEquipmentNoLoc::getTypeName(2)
            ];
            break;
      }
      return [];
   }

   static function query($entity) {
      $query = "SELECT glpi_computers.*\n"
             . "FROM glpi_computers\n"
             . "WHERE (glpi_computers.locations_id IS NULL OR glpi_computers.locations_id = 0)\n"
             . "AND glpi_computers.is_deleted = 0\n"
             . "AND glpi_computers.is_template = 0\n"
             . "AND glpi_computers.entities_id = '" . $entity . "'\n"
             . "ORDER BY glpi_computers.name ASC";
      return $query;
   }

   static function displayBody($data) {
      global $CFG_GLPI;
      $body = "<tr class='tab_bg_2'><td><a href='" . $CFG_GLPI["root_doc"] . "/front/computer.form.php?id=" . $data["id"] . "'>" . $data["name"];
      if ($_SESSION["glpiis_ids_visible"] == 1 || empty($data["name"])) {
         $body .= " (";
         $body .= $data["id"] . ")";
      }
      $body .= "</a></td>";
      if (Session::isMultiEntitiesMode()) {
         $body .= "<td class='center'>" . Dropdown::getDropdownName("glpi_entities", $data["entities_id"]) . "</td>";
      }
      $body .= "<td>" . Dropdown::getDropdownName("glpi_computertypes", $data["computertypes_id"]) . "</td>";
      $body .= "<td>" . Dropdown::getDropdownName("glpi_operatingsystems", $data["operatingsystems_id"]) . "</td>";
      $body .= "<td>" . Dropdown::getDropdownName("glpi_states", $data["states_id"]) . "</td>";
      $body .= "<td>" . __('No location', 'morealerts') . "</td>";
      $body .= "<td>";
      if (!empty($data["users_id"])) {
         $dbu = new DbUtils();
         $body .= "<a href='" . $CFG_GLPI["root_doc"] . "/front/user.form.php?id=" . $data["users_id"] . "'>" . $dbu->getUserName($data["users_id"]) . "</a>";
      }
      if (!empty($data["groups_id"])) {
         $body .= " - <a href='" . $CFG_GLPI["root_doc"] . "/front/group.form.php?id=" . $data["groups_id"] . "'>";
         $body .= Dropdown::getDropdownName("glpi_groups", $data["groups_id"]);
         if ($_SESSION["glpiis_ids_visible"] == 1) {
            $body .= " (";
            $body .= $data["groups_id"] . ")";
         }
         $body .= "</a>";
      }
      if (!empty($data["contact"])) {
         $body .= " - " . $data["contact"];
      }
      $body .= "</td>";
      $body .= "</tr>";
      return $body;
   }

   static function cronAdditionalalertsEquipmentNoLoc($task = null) {
      global $DB, $CFG_GLPI;
      if (!$CFG_GLPI["notifications_mailing"]) {
         return 0;
      }
      $config = PluginMorealertsConfig::getConfig();
      if (!$config->useEquipmentNoLocAlert()) {
         return 0;
      }
      $entities = [$_SESSION["glpiactive_entity"] => 1];
      $cron_status = 0;
      foreach ($entities as $entity => $dummy) {
         $query = self::query($entity);
         $result = $DB->query($query);
         if ($DB->numrows($result) > 0) {
            // Déclencher la notification si besoin
            $cron_status = 1;
         }
      }
      return $cron_status;
   }
}
