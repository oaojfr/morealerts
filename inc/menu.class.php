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

/**
 * Class PluginMorealertsMenu
 */
class PluginMorealertsMenu extends CommonGLPI {
   static $rightname = 'plugin_morealerts';

   /**
    * @return translated
    */
   static function getMenuName() {
      return _n('Other alert', 'Others alerts', 2, 'morealerts');
   }

   /**
    * @return array
    */
   static function getMenuContent() {

      $menu                    = [];
      $menu['title']           = self::getMenuName();
      $menu['page']            = PLUGIN_MOREALERTS_DIR_NOFULL."/front/additionalalert.form.php";
      $menu['links']['search'] = PluginMorealertsAdditionalalert::getFormURL(false);

      $menu['links']['config'] = PLUGIN_MOREALERTS_DIR_NOFULL.'/front/config.form.php';
      $menu['icon']                                       = self::getIcon();
      return $menu;
   }

   static function getIcon() {
      return "ti ti-bell-ringing";
   }

   static function removeRightsFromSession() {
      if (isset($_SESSION['glpimenu']['admin']['types']['PluginMorealertsMenu'])) {
         unset($_SESSION['glpimenu']['admin']['types']['PluginMorealertsMenu']);
      }
      if (isset($_SESSION['glpimenu']['admin']['content']['pluginadditionalalertsmenu'])) {
         unset($_SESSION['glpimenu']['admin']['content']['pluginadditionalalertsmenu']);
      }
   }
}
