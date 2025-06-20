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
 * Class PluginMorealertsAdditionalalert
 */
class PluginMorealertsAdditionalalert extends CommonDBTM {

   static $rightname = "plugin_morealerts";

   /**
    * @param int $nb
    *
    * @return translated
    */
   static function getTypeName($nb = 0) {

      return _n('Other alert', 'Others alerts', $nb, 'morealerts');
   }

   static function displayAlerts() {
      global $DB;

      $CronTask = new CronTask();

      $config = PluginMorealertsConfig::getConfig();

      $infocom = new PluginMorealertsInfocomAlert();
      $infocom->getFromDBbyEntity($_SESSION["glpiactive_entity"]);
      if (isset($infocom->fields["use_infocom_alert"])
          && $infocom->fields["use_infocom_alert"] > 0) {
         $use_infocom_alert = $infocom->fields["use_infocom_alert"];
      } else {
         $use_infocom_alert = $config->useInfocomAlert();
      }

      $ticketunresolved = new PluginMorealertsTicketUnresolved();
      $ticketunresolved->getFromDBbyEntity($_SESSION["glpiactive_entity"]);
      if (isset($ticketunresolved->fields["delay_ticket_alert"])
          && $ticketunresolved->fields["delay_ticket_alert"] > 0) {
         $delay_ticket_alert = $ticketunresolved->fields["delay_ticket_alert"];
      } else {
         $delay_ticket_alert = $config->getDelayTicketAlert();
      }

      $inkalert = new PluginMorealertsInkAlert();
      $inkalert->getFromDBbyEntity($_SESSION["glpiactive_entity"]);
      if (isset($inkalert->fields["use_ink_alert"])
          && $inkalert->fields["use_ink_alert"] > 0) {
         $use_ink_alert = $inkalert->fields["use_ink_alert"];
      } else {
         $use_ink_alert = $config->useInkAlert();
      }

      $additionalalerts_ink = 0;
      if ($CronTask->getFromDBbyName("PluginMorealertsInkAlert", "MorealertsInk")) {
         if ($CronTask->fields["state"] != CronTask::STATE_DISABLE && $use_ink_alert > 0) {
            $additionalalerts_ink = 1;
         }
      }

      $additionalalerts_not_infocom = 0;
      if ($CronTask->getFromDBbyName("PluginMorealertsInfocomAlert", "MorealertsNotInfocom")) {
         if ($CronTask->fields["state"] != CronTask::STATE_DISABLE && $use_infocom_alert > 0) {
            $additionalalerts_not_infocom = 1;
         }
      }

      $additionalalerts_ticket_unresolved = 0;
      if ($CronTask->getFromDBbyName("PluginMorealertsTicketUnresolved", "AdditionalalertsTicketUnresolved")) {
         if ($CronTask->fields["state"] != CronTask::STATE_DISABLE && $delay_ticket_alert > 0) {
            $additionalalerts_ticket_unresolved = 1;
         }
      }

      // Affichage des tickets en attente de validation
      $delay_ticket_waiting_validation = $config->getDelayTicketWaitingValidation();
      $additionalalerts_ticket_waiting_validation = 0;
      $CronTask = new CronTask();
      if ($CronTask->getFromDBbyName("PluginMorealertsTicketWaitingValidation", "AdditionalalertsTicketWaitingValidation")) {
         if ($CronTask->fields["state"] != CronTask::STATE_DISABLE && $delay_ticket_waiting_validation > 0) {
            $additionalalerts_ticket_waiting_validation = 1;
         }
      }
      if ($additionalalerts_ticket_waiting_validation != 0) {
         $entities = [$_SESSION["glpiactive_entity"] => $delay_ticket_waiting_validation];
         foreach ($entities as $entity => $delay) {
            $query = PluginMorealertsTicketWaitingValidation::query($delay, $entity);
            $result = $DB->query($query);
            $nbcol = 5;
            if ($DB->numrows($result) > 0) {
               echo "<div align='center'><table class='tab_cadre' cellspacing='2' cellpadding='3'><tr><th colspan='$nbcol'>";
               echo __('Tickets waiting for validation since more', 'morealerts') . " " . $delay . " " . _n('Day', 'Days', 2) . ", " . __('Entity') . " : " . Dropdown::getDropdownName("glpi_entities", $entity) . "</th></tr>";
               echo "<tr><th>" . __('Title') . "</th>";
               echo "<th>" . __('Entity') . "</th>";
               echo "<th>" . __('Status') . "</th>";
               echo "<th>" . __('Opening date') . "</th>";
               echo "<th>" . __('Last update') . "</th></tr>";
               while ($data = $DB->fetchArray($result)) {
                  echo PluginMorealertsTicketWaitingValidation::displayBody($data);
               }
               echo "</table></div>";
            } else {
               echo "<br><div align='center'><b>" . __('No tickets waiting for validation since more', 'morealerts') . " " . $delay . " " . _n('Day', 'Days', 2) . ", " . __('Entity') . " : " . Dropdown::getDropdownName("glpi_entities", $entity) . "</b></div>";
            }
            echo "<br>";
         }
      }

      // Affichage des tickets en attente de réponse utilisateur
      $delay_ticket_waiting_user = $config->getDelayTicketWaitingUser();
      $additionalalerts_ticket_waiting_user = 0;
      $CronTask = new CronTask();
      if ($CronTask->getFromDBbyName("PluginMorealertsTicketWaitingUser", "AdditionalalertsTicketWaitingUser")) {
         if ($CronTask->fields["state"] != CronTask::STATE_DISABLE && $delay_ticket_waiting_user > 0) {
            $additionalalerts_ticket_waiting_user = 1;
         }
      }
      if ($additionalalerts_ticket_waiting_user != 0) {
         $entities = [$_SESSION["glpiactive_entity"] => $delay_ticket_waiting_user];
         foreach ($entities as $entity => $delay) {
            $query = PluginMorealertsTicketWaitingUser::query($delay, $entity);
            $result = $DB->query($query);
            $nbcol = 5;
            if ($DB->numrows($result) > 0) {
               echo "<div align='center'><table class='tab_cadre' cellspacing='2' cellpadding='3'><tr><th colspan='$nbcol'>";
               echo __('Tickets waiting for user response since more', 'morealerts') . " " . $delay . " " . _n('Day', 'Days', 2) . ", " . __('Entity') . " : " . Dropdown::getDropdownName("glpi_entities", $entity) . "</th></tr>";
               echo "<tr><th>" . __('Title') . "</th>";
               echo "<th>" . __('Entity') . "</th>";
               echo "<th>" . __('Status') . "</th>";
               echo "<th>" . __('Opening date') . "</th>";
               echo "<th>" . __('Last update') . "</th></tr>";
               while ($data = $DB->fetchArray($result)) {
                  echo PluginMorealertsTicketWaitingUser::displayBody($data);
               }
               echo "</table></div>";
            } else {
               echo "<br><div align='center'><b>" . __('No tickets waiting for user response since more', 'morealerts') . " " . $delay . " " . _n('Day', 'Days', 2) . ", " . __('Entity') . " : " . Dropdown::getDropdownName("glpi_entities", $entity) . "</b></div>";
            }
            echo "<br>";
         }
      }

      // Affichage des techniciens avec trop de tickets ouverts
      $max_open_tickets_tech = $config->getMaxOpenTicketsTech();
      $additionalalerts_ticket_open_tech = 0;
      $CronTask = new CronTask();
      if ($CronTask->getFromDBbyName("PluginMorealertsTicketOpenTech", "AdditionalalertsTicketOpenTech")) {
         if ($CronTask->fields["state"] != CronTask::STATE_DISABLE && $max_open_tickets_tech > 0) {
            $additionalalerts_ticket_open_tech = 1;
         }
      }
      if ($additionalalerts_ticket_open_tech != 0) {
         $entities = [$_SESSION["glpiactive_entity"] => $max_open_tickets_tech];
         foreach ($entities as $entity => $max) {
            $query = PluginMorealertsTicketOpenTech::query($max, $entity);
            $result = $DB->query($query);
            $nbcol = 2;
            if ($DB->numrows($result) > 0) {
               echo "<div align='center'><table class='tab_cadre' cellspacing='2' cellpadding='3'><tr><th colspan='$nbcol'>";
               echo __('Technicians with too many open tickets', 'morealerts') . " (" . $max . ") - " . __('Entity') . " : " . Dropdown::getDropdownName("glpi_entities", $entity) . "</th></tr>";
               echo "<tr><th>" . __('Technician') . "</th>";
               echo "<th>" . __('Number of open tickets') . "</th></tr>";
               while ($data = $DB->fetchArray($result)) {
                  echo PluginMorealertsTicketOpenTech::displayBody($data);
               }
               echo "</table></div>";
            } else {
               echo "<br><div align='center'><b>" . __('No technician with too many open tickets', 'morealerts') . " (" . $max . ") - " . __('Entity') . " : " . Dropdown::getDropdownName("glpi_entities", $entity) . "</b></div>";
            }
            echo "<br>";
         }
      }

      // Affichage des tickets à priorité élevée non traités
      $delay_ticket_high_priority = $config->getDelayTicketHighPriority();
      $additionalalerts_ticket_high_priority = 0;
      $CronTask = new CronTask();
      if ($CronTask->getFromDBbyName("PluginMorealertsTicketHighPriority", "AdditionalalertsTicketHighPriority")) {
         if ($CronTask->fields["state"] != CronTask::STATE_DISABLE && $delay_ticket_high_priority > 0) {
            $additionalalerts_ticket_high_priority = 1;
         }
      }
      if ($additionalalerts_ticket_high_priority != 0) {
         $entities = [$_SESSION["glpiactive_entity"] => $delay_ticket_high_priority];
         foreach ($entities as $entity => $delay) {
            $query = PluginMorealertsTicketHighPriority::query($delay, $entity);
            $result = $DB->query($query);
            $nbcol = 6;
            if ($DB->numrows($result) > 0) {
               echo "<div align='center'><table class='tab_cadre' cellspacing='2' cellpadding='3'><tr><th colspan='$nbcol'>";
               echo __('High priority tickets not processed since more', 'morealerts') . " " . $delay . " " . _n('Day', 'Days', 2) . ", " . __('Entity') . " : " . Dropdown::getDropdownName("glpi_entities", $entity) . "</th></tr>";
               echo "<tr><th>" . __('Title') . "</th>";
               echo "<th>" . __('Entity') . "</th>";
               echo "<th>" . __('Status') . "</th>";
               echo "<th>" . __('Opening date') . "</th>";
               echo "<th>" . __('Last update') . "</th>";
               echo "<th>" . __('Priority') . "</th></tr>";
               while ($data = $DB->fetchArray($result)) {
                  echo PluginMorealertsTicketHighPriority::displayBody($data);
               }
               echo "</table></div>";
            } else {
               echo "<br><div align='center'><b>" . __('No high priority tickets not processed since more', 'morealerts') . " " . $delay . " " . _n('Day', 'Days', 2) . ", " . __('Entity') . " : " . Dropdown::getDropdownName("glpi_entities", $entity) . "</b></div>";
            }
            echo "<br>";
         }
      }

      // Affichage des tickets en attente depuis trop longtemps
      $delay_ticket_pending = $config->getDelayTicketPending();
      $additionalalerts_ticket_pending = 0;
      $CronTask = new CronTask();
      if ($CronTask->getFromDBbyName("PluginMorealertsTicketPending", "AdditionalalertsTicketPending")) {
         if ($CronTask->fields["state"] != CronTask::STATE_DISABLE && $delay_ticket_pending > 0) {
            $additionalalerts_ticket_pending = 1;
         }
      }
      if ($additionalalerts_ticket_pending != 0) {
         $entities = [$_SESSION["glpiactive_entity"] => $delay_ticket_pending];
         foreach ($entities as $entity => $delay) {
            $query = PluginMorealertsTicketPending::query($delay, $entity);
            $result = $DB->query($query);
            $nbcol = 5;
            if ($DB->numrows($result) > 0) {
               echo "<div align='center'><table class='tab_cadre' cellspacing='2' cellpadding='3'><tr><th colspan='$nbcol'>";
               echo __('Tickets pending too long since', 'morealerts') . " " . $delay . " " . _n('Day', 'Days', 2) . ", " . __('Entity') . " : " . Dropdown::getDropdownName("glpi_entities", $entity) . "</th></tr>";
               echo "<tr><th>" . __('Title') . "</th>";
               echo "<th>" . __('Entity') . "</th>";
               echo "<th>" . __('Status') . "</th>";
               echo "<th>" . __('Opening date') . "</th>";
               echo "<th>" . __('Last update') . "</th></tr>";
               while ($data = $DB->fetchArray($result)) {
                  echo PluginMorealertsTicketPending::displayBody($data);
               }
               echo "</table></div>";
            } else {
               echo "<br><div align='center'><b>" . __('No tickets pending too long since', 'morealerts') . " " . $delay . " " . _n('Day', 'Days', 2) . ", " . __('Entity') . " : " . Dropdown::getDropdownName("glpi_entities", $entity) . "</b></div>";
            }
            echo "<br>";
         }
      }

      // Affichage des matériels sans emplacement
      $use_equipment_noloc_alert = $config->useEquipmentNoLocAlert();
      $additionalalerts_equipment_noloc = 0;
      $CronTask = new CronTask();
      if ($CronTask->getFromDBbyName("PluginMorealertsEquipmentNoLoc", "AdditionalalertsEquipmentNoLoc")) {
         if ($CronTask->fields["state"] != CronTask::STATE_DISABLE && $use_equipment_noloc_alert > 0) {
            $additionalalerts_equipment_noloc = 1;
         }
      }
      if ($additionalalerts_equipment_noloc != 0) {
         $entities = [$_SESSION["glpiactive_entity"] => 1];
         foreach ($entities as $entity => $dummy) {
            $query = PluginMorealertsEquipmentNoLoc::query($entity);
            $result = $DB->query($query);
            $nbcol = Session::isMultiEntitiesMode() ? 7 : 6;
            if ($DB->numrows($result) > 0) {
               echo "<div align='center'><table class='tab_cadre' cellspacing='2' cellpadding='3'><tr><th colspan='$nbcol'>";
               echo __('Equipments with no location', 'morealerts') . " - " . __('Entity') . " : " . Dropdown::getDropdownName("glpi_entities", $entity) . "</th></tr>";
               echo "<tr><th>" . __('Name') . "</th>";
               if (Session::isMultiEntitiesMode()) {
                  echo "<th>" . __('Entity') . "</th>";
               }
               echo "<th>" . __('Type') . "</th>";
               echo "<th>" . __('Operating system') . "</th>";
               echo "<th>" . __('Status') . "</th>";
               echo "<th>" . __('Location') . "</th>";
               echo "<th>" . __('User') . " / " . __('Group') . " / " . __('Alternate username') . "</th></tr>";
               while ($data = $DB->fetchArray($result)) {
                  echo PluginMorealertsEquipmentNoLoc::displayBody($data);
               }
               echo "</table></div>";
            } else {
               echo "<br><div align='center'><b>" . __('No equipment with no location', 'morealerts') . " - " . __('Entity') . " : " . Dropdown::getDropdownName("glpi_entities", $entity) . "</b></div>";
            }
            echo "<br>";
         }
      }

      // Affichage des tickets sans catégorie
      $use_ticket_no_category_alert = $config->useTicketNoCategoryAlert();
      $additionalalerts_ticket_no_category = 0;
      $CronTask = new CronTask();
      if ($CronTask->getFromDBbyName("PluginMorealertsTicketNoCategory", "AdditionalalertsTicketNoCategory")) {
         if ($CronTask->fields["state"] != CronTask::STATE_DISABLE && $use_ticket_no_category_alert > 0) {
            $additionalalerts_ticket_no_category = 1;
         }
      }
      if ($additionalalerts_ticket_no_category != 0) {
         $entities = [$_SESSION["glpiactive_entity"] => 1];
         foreach ($entities as $entity => $dummy) {
            $query = PluginMorealertsTicketNoCategory::query($entity);
            $result = $DB->query($query);
            $nbcol = Session::isMultiEntitiesMode() ? 6 : 5;
            if ($DB->numrows($result) > 0) {
               echo "<div align='center'><table class='tab_cadre' cellspacing='2' cellpadding='3'><tr><th colspan='$nbcol'>";
               echo __('Tickets with no category', 'morealerts') . " - " . __('Entity') . " : " . Dropdown::getDropdownName("glpi_entities", $entity) . "</th></tr>";
               echo "<tr><th>" . __('Title') . "</th>";
               if (Session::isMultiEntitiesMode()) {
                  echo "<th>" . __('Entity') . "</th>";
               }
               echo "<th>" . __('Status') . "</th>";
               echo "<th>" . __('Opening date') . "</th>";
               echo "<th>" . __('Last update') . "</th>";
               echo "<th>" . __('Technician') . "</th></tr>";
               while ($data = $DB->fetchArray($result)) {
                  echo PluginMorealertsTicketNoCategory::displayBody($data);
               }
               echo "</table></div>";
            } else {
               echo "<br><div align='center'><b>" . __('No tickets with no category', 'morealerts') . " - " . __('Entity') . " : " . Dropdown::getDropdownName("glpi_entities", $entity) . "</b></div>";
            }
            echo "<br>";
         }
      }
      if ($additionalalerts_not_infocom == 0
          && $additionalalerts_ink == 0
          && $additionalalerts_ticket_unresolved == 0) {
         echo "<div align='center'><b>" . __('No used alerts', 'morealerts') . "</b></div>";
      }
      if ($additionalalerts_not_infocom != 0) {
         if (Session::haveRight("infocom", READ)) {

            $query  = PluginMorealertsInfocomAlert::query($_SESSION["glpiactive_entity"]);
            $result = $DB->query($query);

            if ($DB->numrows($result) > 0) {

               if (Session::isMultiEntitiesMode()) {
                  $nbcol = 7;
               } else {
                  $nbcol = 6;
               }
               echo "<div align='center'><table class='tab_cadre' cellspacing='2' cellpadding='3'><tr><th colspan='$nbcol'>";
               echo PluginMorealertsInfocomAlert::getTypeName(2) . "</th></tr>";
               echo "<tr><th>" . __('Name') . "</th>";
               if (Session::isMultiEntitiesMode()) {
                  echo "<th>" . __('Entity') . "</th>";
               }
               echo "<th>" . __('Type') . "</th>";
               echo "<th>" . __('Operating system') . "</th>";
               echo "<th>" . __('Status') . "</th>";
               echo "<th>" . __('Location') . "</th>";
               echo "<th>" . __('User') . " / " . __('Group') . " / " . __('Alternate username') . "</th></tr>";
               while ($data = $DB->fetchArray($result)) {

                  echo PluginMorealertsInfocomAlert::displayBody($data);
               }
               echo "</table></div>";
            } else {
               echo "<br><div align='center'><b>" . __('No computers with no buy date', 'morealerts') . "</b></div>";
            }
            echo "<br>";
         }
      }

      if ($additionalalerts_ink != 0) {

         if (Plugin::isPluginActive("fusioninventory")
            && $DB->tableExists("glpi_plugin_fusioninventory_printercartridges")) {
            if (Session::haveRight("cartridge", READ)) {
               $query  = PluginMorealertsInkAlert::query($_SESSION["glpiactiveentities_string"]);
               $result = $DB->query($query);

               if ($DB->numrows($result) > 0) {
                  if (Session::isMultiEntitiesMode()) {
                     $nbcol = 4;
                  } else {
                     $nbcol = 3;
                  }
                  echo "<div align='center'><table class='tab_cadre' cellspacing='2' cellpadding='3'>";
                  echo "<tr><th colspan='$nbcol'>" . __('Cartridges whose level is low', 'morealerts') . "</th></tr>";
                  echo "<tr>";
                  echo "<th>" . __('Printer') . "</th>";
                  if (Session::isMultiEntitiesMode()) {
                     echo "<th>" . __('Entity') . "</th>";
                  }
                  echo "<th>" . __('Cartridge') . "</th>";
                  echo "<th>" . __('Ink level', 'morealerts') . "</th></tr>";

                  while ($data = $DB->fetchArray($result)) {
                     echo PluginMorealertsInkAlert::displayBody($data);
                  }
                  echo "</table></div>";
               } else {
                  echo "<br><div align='center'><b>" . __('No cartridge is below the threshold', 'morealerts') . "</b></div>";
               }
            }
         } else {
            echo "<br><div align='center'><b>" . __('Ink level alerts', 'morealerts') . " : " . __('Fusioninventory plugin is not installed', 'morealerts') . "</b></div>";
         }
      }

      if ($additionalalerts_ticket_unresolved != 0) {
         $entities = PluginMorealertsTicketUnresolved::getEntitiesToNotify('delay_ticket_alert');

         foreach ($entities as $entity => $delay_ticket_alert) {
            $query  = PluginMorealertsTicketUnresolved::query($delay_ticket_alert, $entity);
            $result = $DB->query($query);
            $nbcol  = 7;


            if ($DB->numrows($result) > 0) {

               echo "<div align='center'><table class='tab_cadre' cellspacing='2' cellpadding='3'><tr><th colspan='$nbcol'>";
               echo __('Tickets unresolved since more', 'morealerts') . " " . $delay_ticket_alert . " " . _n('Day', 'Days', 2) . ", " . __('Entity') . " : " . Dropdown::getDropdownName("glpi_entities", $entity) . "</th></tr>";
               echo "<tr><th>" . __('Title') . "</th>";
               echo "<th>" . __('Entity') . "</th>";
               echo "<th>" . __('Status') . "</th>";
               echo "<th>" . __('Opening date') . "</th>";
               echo "<th>" . __('Last update') . "</th>";
               echo "<th>" . __('Technician') . "</th>";
               echo "<th>" . __('Manager') . "</th>";

               while ($data = $DB->fetchArray($result)) {
                  echo PluginMorealertsTicketUnresolved::displayBody($data);
               }


               echo "</table></div>";
            } else {
               echo "<br><div align='center'><b>" . __('No tickets unresolved since more', 'morealerts') . " " .
                    $delay_ticket_alert . " " . _n('Day', 'Days', 2) . ", " . __('Entity') . " : " . Dropdown::getDropdownName("glpi_entities", $entity) . "</b></div>";
            }

            echo "<br>";
         }
      }

      if (PluginMorealertsConfig::getConfig()->useEquipmentWarrantyAlert()) {
         echo '<h2>' . __('Warranty expired alert', 'morealerts') . '</h2>';
         PluginMorealertsEquipmentWarrantyAlert::displayAlerts();
      }
      if (PluginMorealertsConfig::getConfig()->useEquipmentEndOfLifeAlert()) {
         echo '<h2>' . __('End of life alert', 'morealerts') . '</h2>';
         PluginMorealertsEquipmentEndOfLifeAlert::displayAlerts();
      }
      if (PluginMorealertsConfig::getConfig()->useEquipmentNotInventoriedAlert()) {
         echo '<h2>' . __('Not inventoried since X days', 'morealerts') . '</h2>';
         PluginMorealertsEquipmentNotInventoriedAlert::displayAlerts();
      }
      if (PluginMorealertsConfig::getConfig()->useEquipmentNoAssignmentAlert()) {
         echo '<h2>' . __('No assignment alert', 'morealerts') . '</h2>';
         PluginMorealertsEquipmentNoAssignmentAlert::displayAlerts();
      }
      if (PluginMorealertsConfig::getConfig()->useEquipmentMissingInfoAlert()) {
         echo '<h2>' . __('Missing info alert', 'morealerts') . '</h2>';
         PluginMorealertsEquipmentMissingInfoAlert::displayAlerts();
      }
      if (PluginMorealertsConfig::getConfig()->useComputerNotUsedAlert()) {
         echo '<h2>' . __('Computer not used since X days', 'morealerts') . '</h2>';
         PluginMorealertsComputerNotUsedAlert::displayAlerts();
      }
      if (PluginMorealertsConfig::getConfig()->usePeripheralNotLinkedAlert()) {
         echo '<h2>' . __('Peripheral not linked alert', 'morealerts') . '</h2>';
         PluginMorealertsPeripheralNotLinkedAlert::displayAlerts();
      }
      if (PluginMorealertsConfig::getConfig()->useEquipmentBadLocationAlert()) {
         echo '<h2>' . __('Bad location alert', 'morealerts') . '</h2>';
         PluginMorealertsEquipmentBadLocationAlert::displayAlerts();
      }
      if (PluginMorealertsConfig::getConfig()->useEquipmentMaintenanceAlert()) {
         echo '<h2>' . __('Maintenance alert', 'morealerts') . '</h2>';
         PluginMorealertsEquipmentMaintenanceAlert::displayAlerts();
      }
      if (PluginMorealertsConfig::getConfig()->useEquipmentHighIncidentAlert()) {
         echo '<h2>' . __('High incident alert', 'morealerts') . '</h2>';
         PluginMorealertsEquipmentHighIncidentAlert::displayAlerts();
      }
      if (PluginMorealertsConfig::getConfig()->useEquipmentQualityMissingFieldsAlert()) {
            echo '<h2>' . __('Missing or inconsistent required fields', 'morealerts') . '</h2>';
            PluginMorealertsEquipmentQualityMissingFieldsAlert::displayAlerts();
        }
        if (PluginMorealertsConfig::getConfig()->useEquipmentQualityDuplicatesAlert()) {
            echo '<h2>' . __('Detected duplicates', 'morealerts') . '</h2>';
            PluginMorealertsEquipmentQualityDuplicatesAlert::displayAlerts();
        }
        if (PluginMorealertsConfig::getConfig()->useEquipmentQualityBadAssignmentAlert()) {
            echo '<h2>' . __('Assignment to disabled/nonexistent user or service', 'morealerts') . '</h2>';
            PluginMorealertsEquipmentQualityBadAssignmentAlert::displayAlerts();
        }
        if (PluginMorealertsConfig::getConfig()->useEquipmentQualityDateCoherenceAlert()) {
            echo '<h2>' . __('Incoherent dates (buy > warranty/commissioning)', 'morealerts') . '</h2>';
            PluginMorealertsEquipmentQualityDateCoherenceAlert::displayAlerts();
        }
        if (PluginMorealertsConfig::getConfig()->useEquipmentQualityObsoleteInfoAlert()) {
            echo '<h2>' . __('Obsolete information (unsupported OS/firmware/version)', 'morealerts') . '</h2>';
            PluginMorealertsEquipmentQualityObsoleteInfoAlert::displayAlerts();
        }
        if (PluginMorealertsConfig::getConfig()->useEquipmentQualityNoMoveHistoryAlert()) {
            echo '<h2>' . __('No move or maintenance history', 'morealerts') . '</h2>';
            PluginMorealertsEquipmentQualityNoMoveHistoryAlert::displayAlerts();
        }
        if (PluginMorealertsConfig::getConfig()->useEquipmentQualityBadLocationRefAlert()) {
            echo '<h2>' . __('Deleted or unreferenced location', 'morealerts') . '</h2>';
            PluginMorealertsEquipmentQualityBadLocationRefAlert::displayAlerts();
        }
        if (PluginMorealertsConfig::getConfig()->useEquipmentQualityIncompleteRelationAlert()) {
            echo '<h2>' . __('Incomplete relations (e.g. computer without monitor)', 'morealerts') . '</h2>';
            PluginMorealertsEquipmentQualityIncompleteRelationAlert::displayAlerts();
        }
        if (PluginMorealertsConfig::getConfig()->useEquipmentQualityBadStatusAlert()) {
            echo '<h2>' . __('Inconsistent status (e.g. in stock but assigned)', 'morealerts') . '</h2>';
            PluginMorealertsEquipmentQualityBadStatusAlert::displayAlerts();
        }
        if (PluginMorealertsConfig::getConfig()->useEquipmentQualityOldModifAlert()) {
            echo '<h2>' . __('Not modified for over a year', 'morealerts') . '</h2>';
            PluginMorealertsEquipmentQualityOldModifAlert::displayAlerts();
        }
    }

   public static function getNotificationTargets() {
        return [
            'equipmentwarrantyexpired' => 'PluginMorealertsNotificationTargetEquipmentWarrantyAlert',
            'equipmentendoflife' => 'PluginMorealertsNotificationTargetEquipmentEndOfLifeAlert',
            'equipmentnotinventoried' => 'PluginMorealertsNotificationTargetEquipmentNotInventoriedAlert',
            'equipmentnoassignment' => 'PluginMorealertsNotificationTargetEquipmentNoAssignmentAlert',
            'equipmentmissinginfo' => 'PluginMorealertsNotificationTargetEquipmentMissingInfoAlert',
            'computernotused' => 'PluginMorealertsNotificationTargetComputerNotUsedAlert',
            'peripheralnotlinked' => 'PluginMorealertsNotificationTargetPeripheralNotLinkedAlert',
            'equipmentbadlocation' => 'PluginMorealertsNotificationTargetEquipmentBadLocationAlert',
            'equipmentmaintenance' => 'PluginMorealertsNotificationTargetEquipmentMaintenanceAlert',
            'equipmenthighincident' => 'PluginMorealertsNotificationTargetEquipmentHighIncidentAlert',
            'equipmentqualitymissingfields' => 'PluginMorealertsNotificationTargetEquipmentQualityMissingFieldsAlert',
            'equipmentqualityduplicates' => 'PluginMorealertsNotificationTargetEquipmentQualityDuplicatesAlert',
            'equipmentqualitybadassignment' => 'PluginMorealertsNotificationTargetEquipmentQualityBadAssignmentAlert',
            'equipmentqualitydatecoherence' => 'PluginMorealertsNotificationTargetEquipmentQualityDateCoherenceAlert',
            'equipmentqualityobsoleteinfo' => 'PluginMorealertsNotificationTargetEquipmentQualityObsoleteInfoAlert',
            'equipmentqualitynomovehistory' => 'PluginMorealertsNotificationTargetEquipmentQualityNoMoveHistoryAlert',
            'equipmentqualitybadlocationref' => 'PluginMorealertsNotificationTargetEquipmentQualityBadLocationRefAlert',
            'equipmentqualityincompleterelation' => 'PluginMorealertsNotificationTargetEquipmentQualityIncompleteRelationAlert',
            'equipmentqualitybadstatus' => 'PluginMorealertsNotificationTargetEquipmentQualityBadStatusAlert',
            'equipmentqualityoldmodif' => 'PluginMorealertsNotificationTargetEquipmentQualityOldModifAlert',
        ];
    }

}
