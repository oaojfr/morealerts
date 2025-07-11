<?php
/*
 * @version $Id: HEADER 15930 2011-10-30 15:47:55Z tsmr $
 -------------------------------------------------------------------------
 morealerts plugin for GLPI
 Copyright (C) 2025 by Joao.

 https://github.com/joao/morealerts
 -------------------------------------------------------------------------

 LICENSE

 This file is part of morealerts.

 morealerts is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 morealerts is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with morealerts. If not, see <http://www.gnu.org/licenses/>.
 --------------------------------------------------------------------------
 */

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

/**
 * Class PluginMorealertsInkAlert
 */
class PluginMorealertsInkAlert extends CommonDBTM {

   static $rightname = "plugin_morealerts";

   /**
    * @param int $nb
    *
    * @return string|translated
    */
   static function getTypeName($nb = 0) {

      return __('Cartridges whose level is low', 'morealerts');
   }

   /**
    * @param CommonGLPI $item
    * @param int        $withtemplate
    *
    * @return string|translated
    */
   function getTabNameForItem(CommonGLPI $item, $withtemplate = 0) {

      if ($item->getType() == 'CronTask' && $item->getField('name') == "MorealertsInk") {
         return __('Plugin setup', 'morealerts');
      } else if (get_class($item) == 'CartridgeItem') {
         return PluginMorealertsAdditionalalert::getTypeName(2);
      }
      return '';
   }

   /**
    * @param CommonGLPI $item
    * @param int        $tabnum
    * @param int        $withtemplate
    *
    * @return bool
    */
   static function displayTabContentForItem(CommonGLPI $item, $tabnum = 1, $withtemplate = 0) {
      global $CFG_GLPI;

      if ($item->getType() == 'CronTask') {
         $target = PLUGIN_MOREALERTS_WEBDIR . "/front/inkalert.form.php";
         self::configCron($target);
      } else if ($item->getType() == 'CartridgeItem') {
         $PluginMorealertsInkThreshold = new PluginMorealertsInkThreshold();
         $PluginMorealertsInkThreshold->showForm(PLUGIN_MOREALERTS_WEBDIR . "/front/inkalert.form.php", $item->getField('id'));
      }
      return true;
   }

   // Cron action
   /**
    * @param $name
    *
    * @return array
    */
   static function cronInfo($name) {

      switch ($name) {
         case 'MorealertsInk':
            return [
               'description' => __('Cartridges whose level is low', 'morealerts')];   // Optional
            break;
      }
      return [];
   }

   /**
    * @param $entities
    *
    * @return string
    */
   static function query($entities) {
      global $DB;

      $query      = "SELECT DISTINCT(cartridges_id) 
                     FROM glpi_plugin_fusioninventory_printercartridges 
                     WHERE cartridges_id NOT IN 
                      (SELECT cartridges_id FROM glpi_plugin_morealerts_inkthresholds)";
      $cartridges = $DB->query($query);
      if ($DB->numrows($cartridges) > 0) {
         $PluginMorealertsInkThreshold = new PluginMorealertsInkThreshold();
         while ($cartridge = $DB->fetchArray($cartridges)) {
            $PluginMorealertsInkThreshold->add($cartridge);
         }
      }

      $query = "SELECT c.id, p.name, p.entities_id
                  FROM glpi_plugin_fusioninventory_printercartridges AS c, 
                       glpi_plugin_morealerts_inkthresholds AS t,
                       glpi_cartridgeitems AS i,
                       glpi_printers as p
                  WHERE c.cartridges_id = t.cartridges_id
                    AND i.id = t.cartridges_id
                    AND c.printers_id = p.id
                    AND c.state <= t.threshold
                    AND p.entities_id IN ($entities)
                    AND p.states_id IN (SELECT states_id FROM glpi_plugin_morealerts_inkprinterstates)
                  ORDER BY p.name";

      return $query;
   }


   /**
    * @param $data
    *
    * @return string
    */
   static function displayBody($data) {
      global $CFG_GLPI;

      $snmp = new PluginFusioninventoryPrinterCartridge();
      $snmp->getFromDB($data["id"]);

      $cartridge = new CartridgeItem();
      $cartridge->getFromDB($snmp->fields["cartridges_id"]);

      $printer = new Printer();
      $printer->getFromDB($snmp->fields["printers_id"]);

      $body = "<tr class='tab_bg_2'><td><a href=\"" . $CFG_GLPI["root_doc"] . "/front/printer.form.php?id=" . $printer->fields["id"] . "\">" . $printer->fields["name"];

      if ($_SESSION["glpiis_ids_visible"] == 1 || empty($printer->fields["name"])) {
         $body .= " (";
         $body .= $printer->fields["id"] . ")";
      }
      $body .= "</a></td>";
      if (Session::isMultiEntitiesMode()) {
         $body .= "<td align='center'>" . Dropdown::getDropdownName("glpi_entities", $printer->fields["entities_id"]) . "</td>";
      }

      $body .= "<td align='center'><a href=\"" . $CFG_GLPI["root_doc"] . "/front/cartridgeitem.form.php?id=" . $cartridge->fields["id"] . "\">" . $cartridge->fields["name"] . " (" . $cartridge->fields["ref"] . ")</a></td>";

      $body .= "<td align='center'>" . $snmp->fields["state"] . "%</td>";
      $body .= "</tr>";

      return $body;
   }


   /**
    * @param      $field
    * @param bool $with_value
    *
    * @return array
    */
   static function getEntitiesToNotify($field, $with_value = false) {
      global $DB;

      $query = "SELECT `entities_id` as `entity`,`$field`
                  FROM `glpi_plugin_morealerts_inkalerts`
                  ORDER BY `entities_id` ASC";

      $entities = [];
      $result   = $DB->query($query);

      if ($DB->numrows($result) > 0) {
         foreach ($DB->request($query) as $entitydatas) {
            PluginMorealertsInkAlert::getDefaultValueForNotification($field, $entities, $entitydatas);
         }
      } else {
         $config = new PluginMorealertsConfig();
         $config->getFromDB(1);
         $dbu = new DbUtils();
         foreach ($dbu->getAllDataFromTable('glpi_entities') as $entity) {
            $entities[$entity['id']] = $config->fields[$field];
         }
      }

      return $entities;
   }

   /**
    * @param $field
    * @param $entities
    * @param $entitydatas
    */
   static function getDefaultValueForNotification($field, &$entities, $entitydatas) {

      $config = new PluginMorealertsConfig();
      $config->getFromDB(1);
      //If there's a configuration for this entity & the value is not the one of the global config
      if (isset($entitydatas[$field]) && $entitydatas[$field] > 0) {
         $entities[$entitydatas['entity']] = $entitydatas[$field];
      } //No configuration for this entity : if global config allows notification then add the entity
      //to the array of entities to be notified
      else if ((!isset($entitydatas[$field]) || (isset($entitydatas[$field]) && $entitydatas[$field] == -1)) && $config->fields[$field]) {
         $dbu = new DbUtils();
         foreach ($dbu->getAllDataFromTable('glpi_entities') as $entity) {
            $entities[$entity['id']] = $config->fields[$field];
         }
      }
   }

   /**
    * Cron action
    *
    * @param $task for log, if NULL display
    *
    *
    * @return int
    */
   static function cronMorealertsInk($task = null) {
      global $DB, $CFG_GLPI;

      if (!$CFG_GLPI["notifications_mailing"] || !$DB->tableExists("glpi_plugin_fusioninventory_printercartridges")) {
         return 0;
      }

      $config = PluginMorealertsConfig::getConfig();

      $CronTask = new CronTask();
      if ($CronTask->getFromDBbyName("PluginMorealertsInkAlert", "MorealertsInk")) {
         if ($CronTask->fields["state"] == CronTask::STATE_DISABLE
            || !$config->useInkAlert()) {
            return 0;
         }
      } else {
         return 0;
      }

      $message     = [];
      $cron_status = 0;

      foreach (PluginMorealertsInkAlert::getEntitiesToNotify('use_ink_alert') as $entity => $repeat) {
         $query_ink = PluginMorealertsInkAlert::query($entity);

         $ink_infos    = [];
         $ink_messages = [];

         $type             = Alert::END;
         $ink_infos[$type] = [];
         foreach ($DB->request($query_ink) as $data) {
            $entity                      = $data['entities_id'];
            $message                     = $data["name"];
            $ink_infos[$type][$entity][] = $data;

            if (!isset($ink_messages[$type][$entity])) {
               $ink_messages[$type][$entity] = __('Cartridges whose level is low', 'morealerts') . "<br/>";
            }
            $ink_messages[$type][$entity] .= $message . "</br>";
         }

         foreach ($ink_infos[$type] as $entity => $ink) {
            Plugin::loadLang('morealerts');

            if (NotificationEvent::raiseEvent("ink",
                                              new PluginMorealertsInkAlert(),
                                              ['entities_id' => $entity,
                                                    'ink'         => $ink])) {
               $message     = $ink_messages[$type][$entity];
               $cron_status = 1;
               if ($task) {
                  $task->log(Dropdown::getDropdownName("glpi_entities",
                                                       $entity) . ":  $message\n");
                  $task->addVolume(1);
               } else {
                  Session::addMessageAfterRedirect(Dropdown::getDropdownName("glpi_entities",
                                                                             $entity) . ":  $message");
               }

            } else {
               if ($task) {
                  $task->log(Dropdown::getDropdownName("glpi_entities", $entity) .
                             ":  Send ink alert failed\n");
               } else {
                  Session::addMessageAfterRedirect(Dropdown::getDropdownName("glpi_entities", $entity) .
                                                   ":  Send ink alert failed", false, ERROR);
               }
            }
         }
      }

      return $cron_status;
   }

   /**
    * @param $target
    * @param $ID
    */
   static function configCron($target) {

      $state = new PluginMorealertsInkPrinterState();
      $states = $state->find();
      $used = [];
      foreach ($states as $data) {
         $used[] = $data['states_id'];
      }

      echo "<div align='center'>";
      echo "<form method='post' action=\"$target\">";
      echo "<table class='tab_cadre_fixe' cellpadding='5'>";
      echo "<tr class='tab_bg_1'>";
      echo "<td>" . __('Parameter', 'morealerts') . "</td>";
      echo "<td>" . __('Statutes used for the ink level', 'morealerts') . " : ";
      Dropdown::show('State', ['name' => "states_id",
                               'used' => $used]);
      echo Html::submit(_sx('button', 'Update'), ['name' => 'add_state', 'class' => 'btn btn-primary']);
      echo "</div></td>";
      echo "</tr>";
      echo "</table>";
      Html::closeForm();
      echo "</div>";

      $state->configState();

   }

   /**
    * @param $entities_id
    *
    * @return bool
    */
   function getFromDBbyEntity($entities_id) {
      global $DB;

      $query = "SELECT *
                  FROM `" . $this->getTable() . "`
                  WHERE `entities_id` = '$entities_id'";

      if ($result = $DB->query($query)) {
         if ($DB->numrows($result) != 1) {
            return false;
         }
         $this->fields = $DB->fetchAssoc($result);
         if (is_array($this->fields) && count($this->fields)) {
            return true;
         }
         return false;
      }
      return false;
   }

   /**
    * @param Entity $entity
    *
    * @return bool
    */
   static function showNotificationOptions(Entity $entity) {
      global $DB;

      $ID = $entity->getField('id');
      if (!$entity->can($ID, READ)) {
         return false;
      }

      // Notification right applied
      $canedit = Session::haveRight('notification', UPDATE) && Session::haveAccessToEntity($ID);

      // Get data
      $entitynotification = new PluginMorealertsInkAlert();
      if (!$entitynotification->getFromDBbyEntity($ID)) {
         $entitynotification->getEmpty();
      }

      if ($canedit) {
         echo "<form method='post' name=form action='" . Toolbox::getItemTypeFormURL(__CLASS__) . "'>";
      }
      echo "<table class='tab_cadre_fixe'>";

      echo "<tr class='tab_bg_1'><td>" . __('Cartridges whose level is low', 'morealerts') . "</td><td>";
      if ($DB->tableExists("glpi_plugin_fusioninventory_printercartridges")) {
         $default_value = $entitynotification->fields['use_ink_alert'];
         Alert::dropdownYesNo(['name'           => "use_ink_alert",
                                    'value'          => $default_value,
                                    'inherit_global' => 1]);
      } else {
         echo "<div align='center'><b>" . __('Fusioninventory plugin is not installed', 'morealerts') . "</b></div>";
      }
      echo "</td></tr>";

      if ($canedit) {
         echo "<tr>";
         echo "<td class='tab_bg_2 center' colspan='4'>";
         echo Html::hidden('entities_id', ['value' => $ID]);
         if ($entitynotification->fields["id"]) {
            echo Html::hidden('entities_id', ['value' => $entitynotification->fields["id"]]);
            echo Html::submit(_sx('button', 'Save'), ['name' => 'update', 'class' => 'btn btn-primary']);
         } else {
            echo Html::submit(_sx('button', 'Save'), ['name' => 'add', 'class' => 'btn btn-primary']);
         }
         echo "</td></tr>";
         echo "</table>";
         Html::closeForm();
      } else {
         echo "</table>";
      }
   }
}
