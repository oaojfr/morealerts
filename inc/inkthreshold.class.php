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
 * Class PluginMorealertsInkThreshold
 */
class PluginMorealertsInkThreshold extends CommonDBTM {
   /**
    * @param $target
    * @param $id
    */
   function showForm($target, $id) {
      global $DB;

      $query  = "SELECT * FROM " . $this->getTable() . " WHERE cartridges_id='" . $id . "'";
      $result = $DB->query($query);
      if ($DB->numrows($result) == "0") {
         $this->add(["cartridges_id" => $id]);
         $result = $DB->query($query);
      }
      $data = $DB->fetchAssoc($result);

      echo "<form action='" . $target . "' method='post'>";
      echo "<table class='tab_cadre' cellpadding='5' width='950'>";
      echo "<tr><th colspan='2'>" . __('Ink level alerts', 'morealerts') . "</th></tr>";
      if ($DB->tableExists("glpi_plugin_fusioninventory_printercartridges")) {
         echo "<tr class='tab_bg_1'>";
         echo "<td>" . __('Ink level alerts', 'morealerts') . "</td>";
         echo "<td>";
         echo Html::input('text', ['value' => $data["threshold"], 'size' => 3]);
         echo " %";
         echo "</td>";
         echo "</tr>";
         echo "<tr class='tab_bg_2'>";
         echo "<td colspan='2' align='center'>";
         echo Html::submit(_sx('button', 'Save'), ['name' => 'update_threshold', 'class' => 'btn btn-primary']);
         echo "</td/>";
         echo "</tr>";
      } else {
         echo "<tr><td><div align='center'><b>" . __('Fusioninventory plugin is not installed', 'morealerts') . "</b></div></td></tr>";
      }
      echo "</table>";
      echo Html::hidden('id', ['value' => $data["id"]]);
      Html::closeForm();
   }
}
