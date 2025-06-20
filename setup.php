<?php
/*
 * @version $Id: HEADER 15930 2011-10-30 15:47:55Z tsmr $
 -------------------------------------------------------------------------
 morealerts plugin for GLPI
 Copyright (C) 2025 by Joao (oaojfr)
 Forked from additionalalerts by InfotelGLPI

 https://github.com/oaojfr/alerts
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

define('PLUGIN_MOREALERTS_VERSION', '2.4.0');

if (!defined("PLUGIN_MOREALERTS_DIR")) {
   define("PLUGIN_MOREALERTS_DIR", Plugin::getPhpDir("morealerts"));
   define("PLUGIN_MOREALERTS_DIR_NOFULL", Plugin::getPhpDir("morealerts",false));
   define("PLUGIN_MOREALERTS_WEBDIR", Plugin::getWebDir("morealerts"));
}

// Init the hooks of the plugins -Needed
function plugin_init_morealerts() {
   global $PLUGIN_HOOKS;

   $PLUGIN_HOOKS['csrf_compliant']['morealerts'] = true;
   $PLUGIN_HOOKS['change_profile']['morealerts'] = ['PluginMorealertsProfile', 'initProfile'];

   Plugin::registerClass('PluginMorealertsInfocomAlert', [
      'notificationtemplates_types' => true,
      'addtabon'                    => 'CronTask'
   ]);

   Plugin::registerClass('PluginMorealertsTicketUnresolved', [
      'notificationtemplates_types' => true
   ]);

   Plugin::registerClass('PluginMorealertsInkAlert', [
      'notificationtemplates_types' => true,
      'addtabon'                    => ['CartridgeItem', 'CronTask']
   ]);

   Plugin::registerClass('PluginMorealertsProfile',
                         ['addtabon' => 'Profile']);

   Plugin::registerClass('PluginMorealertsConfig',
                         ['addtabon' => ['NotificationMailSetting', 'Entity']]);

   if (Session::getLoginUserID()) {
      // Display a menu entry ?
      if (Session::haveRight("plugin_morealerts", READ)) {
         $PLUGIN_HOOKS['config_page']['morealerts']           = 'front/config.form.php';
         $PLUGIN_HOOKS["menu_toadd"]['morealerts']['admin'] = 'PluginMorealertsMenu';
      }
   }

}

// Get the name and the version of the plugin - Needed
/**
 * @return array
 */
function plugin_version_morealerts() {

   return [
      'name'           => _n('More alert', 'More alerts', 2, 'morealerts'),
      'version'        => PLUGIN_MOREALERTS_VERSION,
      'license'        => 'GPLv2+',
      'oldname'        => 'morealerts',
      'author'         => "Joao (forked from InfotelGLPI/additionalalerts)",
      'homepage'       => 'https://github.com/joao/morealerts',
      'requirements'   => [
         'glpi' => [
            'min' => '10.0',
            'max' => '11.0',
            'dev' => false
         ]
      ]
   ];
}
