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

include('../../../inc/includes.php');

if (Plugin::isPluginActive("additionalalerts")) {

   $config = new PluginMorealertsConfig();
   if (isset($_POST["update"])) {
      $config->update($_POST);
      Html::back();
   } else {
      Html::header(PluginMorealertsAdditionalalert::getTypeName(2), '', "admin", "pluginadditionalalertsmenu");
      $config = new PluginMorealertsConfig();
      $config->showConfigForm();
      Html::footer();
   }
} else {
   Html::header(__('Setup'), '', "config", "plugins");
   echo "<div class='alert alert-important alert-warning d-flex'>";
   echo "<b>" . __('Please activate the plugin', 'morealerts') . "</b></div>";
   Html::footer();
}
   echo '<tr><th colspan="2">'.__('Equipment alerts', 'morealerts').'</th></tr>';
   echo '<tr><td>' . __('Warranty expired alert', 'morealerts') . '</td><td>';
   Html::showCheckbox(['name' => 'use_equipment_warranty_alert', 'checked' => $this->fields['use_equipment_warranty_alert']]);
   echo '</td></tr>';
   echo '<tr><td>' . __('End of life alert', 'morealerts') . '</td><td>';
   Html::showCheckbox(['name' => 'use_equipment_endoflife_alert', 'checked' => $this->fields['use_equipment_endoflife_alert']]);
   echo '</td></tr>';
   echo '<tr><td>' . __('Not inventoried since X days', 'morealerts') . '</td><td>';
   Html::showCheckbox(['name' => 'use_equipment_notinventoried_alert', 'checked' => $this->fields['use_equipment_notinventoried_alert']]);
   echo '</td></tr>';
   echo '<tr><td>' . __('No assignment alert', 'morealerts') . '</td><td>';
   Html::showCheckbox(['name' => 'use_equipment_noassignment_alert', 'checked' => $this->fields['use_equipment_noassignment_alert']]);
   echo '</td></tr>';
   echo '<tr><td>' . __('Missing info alert', 'morealerts') . '</td><td>';
   Html::showCheckbox(['name' => 'use_equipment_missinginfo_alert', 'checked' => $this->fields['use_equipment_missinginfo_alert']]);
   echo '</td></tr>';
   echo '<tr><td>' . __('Computer not used since X days', 'morealerts') . '</td><td>';
   Html::showCheckbox(['name' => 'use_computer_notused_alert', 'checked' => $this->fields['use_computer_notused_alert']]);
   echo '</td></tr>';
   echo '<tr><td>' . __('Peripheral not linked alert', 'morealerts') . '</td><td>';
   Html::showCheckbox(['name' => 'use_peripheral_notlinked_alert', 'checked' => $this->fields['use_peripheral_notlinked_alert']]);
   echo '</td></tr>';
   echo '<tr><td>' . __('Bad location alert', 'morealerts') . '</td><td>';
   Html::showCheckbox(['name' => 'use_equipment_badlocation_alert', 'checked' => $this->fields['use_equipment_badlocation_alert']]);
   echo '</td></tr>';
   echo '<tr><td>' . __('Maintenance alert', 'morealerts') . '</td><td>';
   Html::showCheckbox(['name' => 'use_equipment_maintenance_alert', 'checked' => $this->fields['use_equipment_maintenance_alert']]);
   echo '</td></tr>';
   echo '<tr><td>' . __('High incident alert', 'morealerts') . '</td><td>';
   Html::showCheckbox(['name' => 'use_equipment_highincident_alert', 'checked' => $this->fields['use_equipment_highincident_alert']]);
   echo '</td></tr>';
   echo '<tr><th colspan="2">'.__('Quality control alerts', 'morealerts').'</th></tr>';
   echo '<tr><td>' . __('Missing or inconsistent required fields', 'morealerts') . '</td><td>';
   Html::showCheckbox(['name' => 'use_equipment_quality_missingfields_alert', 'checked' => $this->fields['use_equipment_quality_missingfields_alert']]);
   echo '</td></tr>';
   echo '<tr><td>' . __('Detected duplicates', 'morealerts') . '</td><td>';
   Html::showCheckbox(['name' => 'use_equipment_quality_duplicates_alert', 'checked' => $this->fields['use_equipment_quality_duplicates_alert']]);
   echo '</td></tr>';
   echo '<tr><td>' . __('Assignment to disabled/nonexistent user or service', 'morealerts') . '</td><td>';
   Html::showCheckbox(['name' => 'use_equipment_quality_badassignment_alert', 'checked' => $this->fields['use_equipment_quality_badassignment_alert']]);
   echo '</td></tr>';
   echo '<tr><td>' . __('Incoherent dates (buy > warranty/commissioning)', 'morealerts') . '</td><td>';
   Html::showCheckbox(['name' => 'use_equipment_quality_datecoherence_alert', 'checked' => $this->fields['use_equipment_quality_datecoherence_alert']]);
   echo '</td></tr>';
   echo '<tr><td>' . __('Obsolete information (unsupported OS/firmware/version)', 'morealerts') . '</td><td>';
   Html::showCheckbox(['name' => 'use_equipment_quality_obsoleteinfo_alert', 'checked' => $this->fields['use_equipment_quality_obsoleteinfo_alert']]);
   echo '</td></tr>';
   echo '<tr><td>' . __('No move or maintenance history', 'morealerts') . '</td><td>';
   Html::showCheckbox(['name' => 'use_equipment_quality_nomovehistory_alert', 'checked' => $this->fields['use_equipment_quality_nomovehistory_alert']]);
   echo '</td></tr>';
   echo '<tr><td>' . __('Deleted or unreferenced location', 'morealerts') . '</td><td>';
   Html::showCheckbox(['name' => 'use_equipment_quality_badlocationref_alert', 'checked' => $this->fields['use_equipment_quality_badlocationref_alert']]);
   echo '</td></tr>';
   echo '<tr><td>' . __('Incomplete relations (e.g. computer without monitor)', 'morealerts') . '</td><td>';
   Html::showCheckbox(['name' => 'use_equipment_quality_incompleterelation_alert', 'checked' => $this->fields['use_equipment_quality_incompleterelation_alert']]);
   echo '</td></tr>';
   echo '<tr><td>' . __('Inconsistent status (e.g. in stock but assigned)', 'morealerts') . '</td><td>';
   Html::showCheckbox(['name' => 'use_equipment_quality_badstatus_alert', 'checked' => $this->fields['use_equipment_quality_badstatus_alert']]);
   echo '</td></tr>';
   echo '<tr><td>' . __('Not modified for over a year', 'morealerts') . '</td><td>';
   Html::showCheckbox(['name' => 'use_equipment_quality_oldmodif_alert', 'checked' => $this->fields['use_equipment_quality_oldmodif_alert']]);
   echo '</td></tr>';
