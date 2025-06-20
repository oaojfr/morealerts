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

$state = new PluginMorealertsInkPrinterState();
$alert = new PluginMorealertsInkAlert();

if (isset($_POST["add"])) {
   if ($alert->canUpdate()) {
      $newID = $alert->add($_POST);
   }
} else if (isset($_POST["update"])) {
   if ($alert->canUpdate()) {
      $alert->update($_POST);
   }
} else if (isset($_POST["add_state"])) {
   if ($alert->canUpdate()) {
      $newID = $state->add($_POST);
   }
} else if (isset($_POST["delete_state"])) {
   if ($alert->canUpdate()) {
      $state->getFromDB($_POST["id"]);

      foreach ($_POST["item"] as $key => $val) {
         if ($val == 1) {
            $state->delete(['id' => $key]);
         }
      }
   }

} else if (isset($_POST["update_threshold"])) {

   $PluginMorealertsInkThreshold = new PluginMorealertsInkThreshold();
   if ($alert->canUpdate()) {
      $PluginMorealertsInkThreshold->update($_POST);
   } else {
      Html::displayRightError();
   }
}
Html::back();
