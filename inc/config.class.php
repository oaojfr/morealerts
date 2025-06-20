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
 * Class PluginMorealertsConfig
 */
class PluginMorealertsConfig extends CommonDBTM {

   static $rightname = "plugin_morealerts";

   /**
    * @param int $nb
    * @return translated
    */
   static function getTypeName($nb = 0) {
      return __('Plugin setup', 'morealerts');
   }

   public static function getConfig() {
      static $config = null;

      if (is_null($config)) {
         $config = new self();
      }
      $config->getFromDB(1);

      return $config;
   }

   /**
    * @param CommonGLPI $item
    * @param int $withtemplate
    * @return string|translated
    */
   function getTabNameForItem(CommonGLPI $item, $withtemplate = 0) {
      global $CFG_GLPI;

      if ($item->getType()=='NotificationMailSetting'
            && $item->getField('id')
               && $CFG_GLPI["notifications_mailing"]) {
            return PluginMorealertsAdditionalalert::getTypeName(2);
      } else if ($item->getType()=='Entity') {
            return PluginMorealertsAdditionalalert::getTypeName(2);
      }
         return '';
   }

   /**
    * @param CommonGLPI $item
    * @param int $tabnum
    * @param int $withtemplate
    * @return bool
    */
   static function displayTabContentForItem(CommonGLPI $item, $tabnum = 1, $withtemplate = 0) {
      global $CFG_GLPI;

      if ($item->getType()=='NotificationMailSetting') {

         $target = PLUGIN_MOREALERTS_WEBDIR."/front/config.form.php";
         $conf = new PluginMorealertsConfig;
         $conf->showForm(['target' =>$target]);

      } else if ($item->getType()=='Entity') {

         PluginMorealertsInfocomAlert::showNotificationOptions($item);
         PluginMorealertsInkAlert::showNotificationOptions($item);
         PluginMorealertsTicketUnresolved::showNotificationOptions($item);

      }
      return true;
   }

   /**
    * @param array $options
    * @return bool
    */
   function showConfigForm() {
      global $DB;

      $this->getFromDB(1);
      $options['colspan'] = 1;
      $this->showFormHeader($options);

      echo "<tr class='tab_bg_2'>";
      echo "<td>" . PluginMorealertsInfocomAlert::getTypeName(2) . "</td><td>";
      Alert::dropdownYesNo(['name'=>"use_infocom_alert",
                              'value'=>$this->fields["use_infocom_alert"]]);
      echo "</td></tr>";

      echo "<tr class='tab_bg_2'>";
      echo "<td >" . __('Cartridges whose level is low', 'morealerts') . "</td><td>";
      if (Plugin::isPluginActive("fusioninventory") &&
            $DB->tableExists("glpi_plugin_fusioninventory_printercartridges")) {
         Alert::dropdownYesNo(['name'=>"use_ink_alert",
                                   'value'=>$this->fields["use_ink_alert"]]);
      } else {
         echo "<div align='center'><b>".__('Fusioninventory plugin is not installed', 'morealerts')."</b></div>";
      }
      echo "</td></tr>";

      echo "<tr class='tab_bg_2'>";
      echo "<td>" . __('Unresolved Ticket Alerts', 'morealerts') . "</td><td>";

      Alert::dropdownIntegerNever('delay_ticket_alert',
                                  $this->fields["delay_ticket_alert"],
                                  ['max'=>99]);
      echo "&nbsp;"._n('Day', 'Days', 2)."</td></tr>";
      echo "</td></tr>";

      echo "<tr class='tab_bg_2'>";
      echo "<td>" . __('Tickets waiting for validation alert', 'morealerts') . "</td><td>";
      Alert::dropdownIntegerNever('delay_ticket_waiting_validation',
                                  $this->fields["delay_ticket_waiting_validation"],
                                  ['max'=>99]);
      echo "&nbsp;"._n('Day', 'Days', 2)."</td></tr>";
      echo "</td></tr>";

      echo "<tr class='tab_bg_2'>";
      echo "<td>" . __('Tickets waiting for user response alert', 'morealerts') . "</td><td>";
      Alert::dropdownIntegerNever('delay_ticket_waiting_user',
                                  $this->fields["delay_ticket_waiting_user"],
                                  ['max'=>99]);
      echo "&nbsp;"._n('Day', 'Days', 2)."</td></tr>";
      echo "</td></tr>";

      echo "<tr class='tab_bg_2'>";
      echo "<td>" . __('Technician open tickets threshold alert', 'morealerts') . "</td><td>";
      Alert::dropdownIntegerNever('max_open_tickets_tech',
                                  $this->fields["max_open_tickets_tech"],
                                  ['max'=>99]);
      echo "&nbsp;"._n('Ticket', 'Tickets', 2)."</td></tr>";
      echo "</td></tr>";

      echo "<tr class='tab_bg_2'>";
      echo "<td>" . __('High priority ticket alert', 'morealerts') . "</td><td>";
      Alert::dropdownIntegerNever('delay_ticket_high_priority',
                                  $this->fields["delay_ticket_high_priority"],
                                  ['max'=>99]);
      echo "&nbsp;"._n('Day', 'Days', 2)."</td></tr>";
      echo "</td></tr>";

      echo "<tr class='tab_bg_2'>";
      echo "<td>" . __('Tickets pending too long alert', 'morealerts') . "</td><td>";
      Alert::dropdownIntegerNever('delay_ticket_pending',
                                  $this->fields["delay_ticket_pending"],
                                  ['max'=>99]);
      echo "&nbsp;"._n('Day', 'Days', 2)."</td></tr>";
      echo "</td></tr>";

      echo "<tr class='tab_bg_2'>";
      echo "<td>" . __('Equipment with no location alert', 'morealerts') . "</td><td>";
      Alert::dropdownYesNo(['name'=>"use_equipment_noloc_alert",
                              'value'=>$this->fields["use_equipment_noloc_alert"]]);
      echo "</td></tr>";

      echo "<tr class='tab_bg_2'><td class='center' colspan='2'>";
      echo Html::hidden('id', ['value' => 1]);
      echo "</td></tr>";

      $this->showFormButtons($options);

      return true;
   }


   //----------------- Getters and setters -------------------//

   public function useInfocomAlert() {
      return $this->fields['use_infocom_alert'];
   }

   public function useInkAlert() {
      return $this->fields['use_ink_alert'];
   }

   public function getDelayTicketAlert() {
      return $this->fields['delay_ticket_alert'];
   }

   public function getDelayTicketWaitingValidation() {
      return $this->fields['delay_ticket_waiting_validation'];
   }

   public function getDelayTicketWaitingUser() {
      return $this->fields['delay_ticket_waiting_user'];
   }

   public function getMaxOpenTicketsTech() {
      return $this->fields['max_open_tickets_tech'];
   }

   public function getDelayTicketHighPriority() {
      return $this->fields['delay_ticket_high_priority'];
   }
   public function getDelayTicketPending() {
      return $this->fields['delay_ticket_pending'];
   }
   public function useEquipmentNoLocAlert() {
      return $this->fields['use_equipment_noloc_alert'];
   }
   public function useEquipmentWarrantyAlert() {
      return isset($this->fields['use_equipment_warranty_alert']) ? $this->fields['use_equipment_warranty_alert'] : 0;
   }
   public function useEquipmentEndOfLifeAlert() {
      return isset($this->fields['use_equipment_endoflife_alert']) ? $this->fields['use_equipment_endoflife_alert'] : 0;
   }
   public function useEquipmentNotInventoriedAlert() {
      return isset($this->fields['use_equipment_notinventoried_alert']) ? $this->fields['use_equipment_notinventoried_alert'] : 0;
   }
   public function useEquipmentNoAssignmentAlert() {
      return isset($this->fields['use_equipment_noassignment_alert']) ? $this->fields['use_equipment_noassignment_alert'] : 0;
   }
   public function useEquipmentMissingInfoAlert() {
      return isset($this->fields['use_equipment_missinginfo_alert']) ? $this->fields['use_equipment_missinginfo_alert'] : 0;
   }
   public function useComputerNotUsedAlert() {
      return isset($this->fields['use_computer_notused_alert']) ? $this->fields['use_computer_notused_alert'] : 0;
   }
   public function usePeripheralNotLinkedAlert() {
      return isset($this->fields['use_peripheral_notlinked_alert']) ? $this->fields['use_peripheral_notlinked_alert'] : 0;
   }
   public function useEquipmentBadLocationAlert() {
      return isset($this->fields['use_equipment_badlocation_alert']) ? $this->fields['use_equipment_badlocation_alert'] : 0;
   }
   public function useEquipmentMaintenanceAlert() {
      return isset($this->fields['use_equipment_maintenance_alert']) ? $this->fields['use_equipment_maintenance_alert'] : 0;
   }
   public function useEquipmentHighIncidentAlert() {
      return isset($this->fields['use_equipment_highincident_alert']) ? $this->fields['use_equipment_highincident_alert'] : 0;
   }
   public function useEquipmentQualityMissingFieldsAlert() {
      return isset($this->fields['use_equipment_quality_missingfields_alert']) ? $this->fields['use_equipment_quality_missingfields_alert'] : 0;
   }
   public function useEquipmentQualityDuplicatesAlert() {
      return isset($this->fields['use_equipment_quality_duplicates_alert']) ? $this->fields['use_equipment_quality_duplicates_alert'] : 0;
   }
   public function useEquipmentQualityBadAssignmentAlert() {
      return isset($this->fields['use_equipment_quality_badassignment_alert']) ? $this->fields['use_equipment_quality_badassignment_alert'] : 0;
   }
   public function useEquipmentQualityDateCoherenceAlert() {
      return isset($this->fields['use_equipment_quality_datecoherence_alert']) ? $this->fields['use_equipment_quality_datecoherence_alert'] : 0;
   }
   public function useEquipmentQualityObsoleteInfoAlert() {
      return isset($this->fields['use_equipment_quality_obsoleteinfo_alert']) ? $this->fields['use_equipment_quality_obsoleteinfo_alert'] : 0;
   }
   public function useEquipmentQualityNoMoveHistoryAlert() {
      return isset($this->fields['use_equipment_quality_nomovehistory_alert']) ? $this->fields['use_equipment_quality_nomovehistory_alert'] : 0;
   }
   public function useEquipmentQualityBadLocationRefAlert() {
      return isset($this->fields['use_equipment_quality_badlocationref_alert']) ? $this->fields['use_equipment_quality_badlocationref_alert'] : 0;
   }
   public function useEquipmentQualityIncompleteRelationAlert() {
      return isset($this->fields['use_equipment_quality_incompleterelation_alert']) ? $this->fields['use_equipment_quality_incompleterelation_alert'] : 0;
   }
   public function useEquipmentQualityBadStatusAlert() {
      return isset($this->fields['use_equipment_quality_badstatus_alert']) ? $this->fields['use_equipment_quality_badstatus_alert'] : 0;
   }
   public function useEquipmentQualityOldModifAlert() {
      return isset($this->fields['use_equipment_quality_oldmodif_alert']) ? $this->fields['use_equipment_quality_oldmodif_alert'] : 0;
   }
}

