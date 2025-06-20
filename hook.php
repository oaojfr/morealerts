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
 * @return bool
 */
function plugin_morealerts_install() {
   global $DB;

   include_once(PLUGIN_MOREALERTS_DIR. "/inc/profile.class.php");

   $install = false;
   $update78 = false;
   $update90 = false;

   //INSTALL
   if (!$DB->tableExists("glpi_plugin_morealerts_ticketunresolveds")
      && !$DB->tableExists("glpi_plugin_morealerts_configs")) {

      $install = true;
      $DB->runFile(PLUGIN_MOREALERTS_DIR. "/sql/empty-2.4.0.sql");

   }
   //UPDATE
   if ($DB->tableExists("glpi_plugin_alerting_profiles")
      && $DB->fieldExists("glpi_plugin_alerting_profiles", "interface")) {

      $update78 = true;
      $DB->runFile(PLUGIN_MOREALERTS_DIR. "/sql/update-1.2.0.sql");
      $DB->runFile(PLUGIN_MOREALERTS_DIR. "/sql/update-1.3.0.sql");

   }
   if (!$DB->tableExists("glpi_plugin_morealerts_infocomalerts")) {

      $update78 = true;
      $DB->runFile(PLUGIN_MOREALERTS_DIR. "/sql/update-1.3.0.sql");

   }
   if ($DB->tableExists("glpi_plugin_morealerts_reminderalerts")) {

      $DB->runFile(PLUGIN_MOREALERTS_DIR. "/sql/update-1.5.0.sql");

      $notif = new Notification();

      $options = ['itemtype' => 'PluginMorealertsReminderAlert',
         'event' => 'reminder',
         'FIELDS' => 'id'];
      foreach ($DB->request('glpi_notifications', $options) as $data) {
         $notif->delete($data);
      }

      $template = new NotificationTemplate();
      $translation = new NotificationTemplateTranslation();
      $notif_template = new Notification_NotificationTemplate();
      $options = ['itemtype' => 'PluginMorealertsReminderAlert',
         'FIELDS' => 'id'];
      foreach ($DB->request('glpi_notificationtemplates', $options) as $data) {
         $options_template = ['notificationtemplates_id' => $data['id'],
            'FIELDS' => 'id'];

         foreach ($DB->request('glpi_notificationtemplatetranslations', $options_template) as $data_template) {
            $translation->delete($data_template);
         }
         $template->delete($data);

         foreach ($DB->request('glpi_notifications_notificationtemplates', $options_template) as $data_template) {
            $notif_template->delete($data_template);
         }
      }

      $temp = new CronTask();
      if ($temp->getFromDBbyName('PluginMorealertsReminderAlert', 'MorealertsReminder')) {
         $temp->delete(['id' => $temp->fields["id"]]);
      }
   }
   if (!$DB->tableExists("glpi_plugin_morealerts_inkalerts")) {

      $DB->runFile(PLUGIN_MOREALERTS_DIR. "/sql/update-1.7.1.sql");

      $query_id = "SELECT `id` FROM `glpi_notificationtemplates` WHERE `itemtype`='PluginMorealertsInkAlert' AND `name` = 'Alert ink level'";
      $result = $DB->query($query_id) or die ($DB->error());
      $itemtype = $DB->result($result, 0, 'id');

      $query = "INSERT INTO `glpi_notificationtemplatetranslations`
                                VALUES(NULL, " . $itemtype . ", '','##lang.ink.title## : ##ink.entity##',
      '##lang.ink.title## :
      ##FOREACHinks##
      - ##ink.printer## - ##ink.cartridge## - ##ink.state##%
      ##ENDFOREACHinks##',
      '&lt;table class=\"tab_cadre\" border=\"1\" cellspacing=\"2\" cellpadding=\"3\"&gt;
      &lt;tbody&gt;
      &lt;tr&gt;
      &lt;td style=\"text-align: left;\" bgcolor=\"#cccccc\"&gt;&lt;span style=\"font-family: Verdana; font-size: 11px; text-align: left;\"&gt;##lang.ink.printer##&lt;/span&gt;&lt;/td&gt;
      &lt;td style=\"text-align: left;\" bgcolor=\"#cccccc\"&gt;&lt;span style=\"font-family: Verdana; font-size: 11px; text-align: left;\"&gt;##lang.ink.cartridge##&lt;/span&gt;&lt;/td&gt;
      &lt;td style=\"text-align: left;\" bgcolor=\"#cccccc\"&gt;&lt;span style=\"font-family: Verdana; font-size: 11px; text-align: left;\"&gt;##lang.ink.state##&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      ##FOREACHinks##
      &lt;tr&gt;
      &lt;td&gt;&lt;a href=\"##ink.urlprinter##\"&gt;&lt;span style=\"font-family: Verdana; font-size: 11px; text-align: left;\"&gt;##ink.printer##&lt;/span&gt;&lt;/a&gt;&lt;/td&gt;
      &lt;td&gt;&lt;a href=\"##ink.urlcartridge##\"&gt;&lt;span style=\"font-family: Verdana; font-size: 11px; text-align: left;\"&gt;##ink.cartridge##&lt;/span&gt;&lt;/a&gt;&lt;/td&gt;
      &lt;td&gt;&lt;span style=\"font-family: Verdana; font-size: 11px; text-align: left;\"&gt;##ink.state##%&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      ##ENDFOREACHinks##
      &lt;/tbody&gt;
      &lt;/table&gt;');";

      $DB->query($query);

      $query = "INSERT INTO `glpi_notifications` (`name`, `entities_id`, `itemtype`, `event`, `is_recursive`, `is_active`) 
                VALUES ('Alert ink level', 0, 'PluginMorealertsInkAlert', 'ink', 1, 1);";
       $DB->query($query);

      $query_id = "SELECT `id` FROM `glpi_notifications`
               WHERE `name` = 'Alert ink level' AND `itemtype` = 'PluginMorealertsInkAlert' AND `event` = 'ink'";
      $result = $DB->query($query_id) or die ($DB->error());
      $notification = $DB->result($result, 0, 'id');

      $query = "INSERT INTO `glpi_notifications_notificationtemplates` (`notifications_id`, `mode`, `notificationtemplates_id`) 
               VALUES (" . $notification . ", 'mailing', " . $itemtype . ");";
      $DB->query($query);
   }
   //version 1.8.0
   if (!$DB->tableExists("glpi_plugin_morealerts_ticketunresolveds")) {

      $update90 = true;
      $DB->runFile(PLUGIN_MOREALERTS_DIR. "/sql/update-1.8.0.sql");
   }

   //version 2.1.2
   if ($DB->tableExists("glpi_plugin_morealerts_ocsalerts")) {
      //ocsalert migrated to the ocsinventoryng plugin
      $notif = new Notification();

      $options = ['itemtype' => 'PluginMorealertsOcsAlert',
                  'event' => 'ocs',
                  'FIELDS' => 'id'];
      foreach ($DB->request('glpi_notifications', $options) as $data) {
         $notif->delete($data);
      }
      $options = ['itemtype' => 'PluginMorealertsOcsAlert',
                  'event' => 'newocs',
                  'FIELDS' => 'id'];
      foreach ($DB->request('glpi_notifications', $options) as $data) {
         $notif->delete($data);
      }

      //templates
      $template = new NotificationTemplate();
      $translation = new NotificationTemplateTranslation();
      $notif_template = new Notification_NotificationTemplate();
      $options = ['itemtype' => 'PluginMorealertsOcsAlert',
                  'FIELDS' => 'id'];
      foreach ($DB->request('glpi_notificationtemplates', $options) as $data) {
         $options_template = ['notificationtemplates_id' => $data['id'],
                              'FIELDS' => 'id'];

         foreach ($DB->request('glpi_notificationtemplatetranslations', $options_template) as $data_template) {
            $translation->delete($data_template);
         }
         $template->delete($data);
         foreach ($DB->request('glpi_notifications_notificationtemplates', $options_template) as $data_template) {
            $notif_template->delete($data_template);
         }
      }
      //delete tables
      $tables = [
         "glpi_plugin_morealerts_ocsalerts",
         "glpi_plugin_morealerts_notificationstates"];

      foreach ($tables as $table) {
         $DB->query("DROP TABLE IF EXISTS `$table`;");
      }
      //delete fields
      $DB->query("ALTER TABLE `glpi_plugin_morealerts_configs`
                 DROP `delay_ocs`,
                 DROP `use_newocs_alert`;");

      CronTask::Unregister('PluginMorealertsOcsAlert');

   }

   if ($install || $update78) {
      //Do One time on 0.78
      $query_id = "SELECT `id` 
                  FROM `glpi_notificationtemplates` 
                  WHERE `itemtype`='PluginMorealertsInfocomAlert' AND `name` = 'Alert infocoms'";
      $result = $DB->query($query_id) or die ($DB->error());
      $itemtype = $DB->result($result, 0, 'id');

      $query = "INSERT INTO `glpi_notificationtemplatetranslations`
                                 VALUES(NULL, " . $itemtype . ", '','##lang.notinfocom.title## : ##notinfocom.entity##',
                        '##FOREACHnotinfocoms##
   ##lang.notinfocom.name## : ##notinfocom.name##
   ##lang.notinfocom.computertype## : ##notinfocom.computertype##
   ##lang.notinfocom.operatingsystem## : ##notinfocom.operatingsystem##
   ##lang.notinfocom.state## : ##notinfocom.state##
   ##lang.notinfocom.location## : ##notinfocom.location##
   ##lang.notinfocom.user## : ##notinfocom.user## / ##notinfocom.group## / ##notinfocom.contact##
   ##ENDFOREACHnotinfocoms##',
                        '&lt;table class=\"tab_cadre\" border=\"1\" cellspacing=\"2\" cellpadding=\"3\"&gt;
   &lt;tbody&gt;
   &lt;tr&gt;
   &lt;td style=\"text-align: left;\" bgcolor=\"#cccccc\"&gt;&lt;span style=\"font-family: Verdana; font-size: 11px; text-align: left;\"&gt;##lang.notinfocom.name##&lt;/span&gt;&lt;/td&gt;
   &lt;td style=\"text-align: left;\" bgcolor=\"#cccccc\"&gt;&lt;span style=\"font-family: Verdana; font-size: 11px; text-align: left;\"&gt;##lang.notinfocom.computertype##&lt;/span&gt;&lt;/td&gt;
   &lt;td style=\"text-align: left;\" bgcolor=\"#cccccc\"&gt;&lt;span style=\"font-family: Verdana; font-size: 11px; text-align: left;\"&gt;##lang.notinfocom.operatingsystem##&lt;/span&gt;&lt;/td&gt;
   &lt;td style=\"text-align: left;\" bgcolor=\"#cccccc\"&gt;&lt;span style=\"font-family: Verdana; font-size: 11px; text-align: left;\"&gt;##lang.notinfocom.state##&lt;/span&gt;&lt;/td&gt;
   &lt;td style=\"text-align: left;\" bgcolor=\"#cccccc\"&gt;&lt;span style=\"font-family: Verdana; font-size: 11px; text-align: left;\"&gt;##lang.notinfocom.location##&lt;/span&gt;&lt;/td&gt;
   &lt;td style=\"text-align: left;\" bgcolor=\"#cccccc\"&gt;&lt;span style=\"font-family: Verdana; font-size: 11px; text-align: left;\"&gt;##lang.notinfocom.user##&lt;/span&gt;&lt;/td&gt;
   &lt;/tr&gt;
   ##FOREACHnotinfocoms##            
   &lt;tr&gt;
   &lt;td&gt;&lt;a href=\"##notinfocom.urlname##\"&gt;&lt;span style=\"font-family: Verdana; font-size: 11px; text-align: left;\"&gt;##notinfocom.name##&lt;/span&gt;&lt;/a&gt;&lt;/td&gt;
   &lt;td&gt;&lt;span style=\"font-family: Verdana; font-size: 11px; text-align: left;\"&gt;##notinfocom.computertype##&lt;/span&gt;&lt;/td&gt;
   &lt;td&gt;&lt;span style=\"font-family: Verdana; font-size: 11px; text-align: left;\"&gt;##notinfocom.operatingsystem##&lt;/span&gt;&lt;/td&gt;
   &lt;td&gt;&lt;span style=\"font-family: Verdana; font-size: 11px; text-align: left;\"&gt;##notinfocom.state##&lt;/span&gt;&lt;/td&gt;
   &lt;td&gt;&lt;span style=\"font-family: Verdana; font-size: 11px; text-align: left;\"&gt;##notinfocom.location##&lt;/span&gt;&lt;/td&gt;
   &lt;td&gt;&lt;a href=\"##notinfocom.urluser##\"&gt;&lt;span style=\"font-family: Verdana; font-size: 11px; text-align: left;\"&gt;##notinfocom.user##&lt;/span&gt;&lt;/a&gt; / &lt;a href=\"##notinfocom.urlgroup##\"&gt;&lt;span style=\"font-family: Verdana; font-size: 11px; text-align: left;\"&gt;##notinfocom.group##&lt;/span&gt;&lt;/a&gt; / &lt;span style=\"font-family: Verdana; font-size: 11px; text-align: left;\"&gt;##notinfocom.contact##&lt;/span&gt;&lt;/td&gt;
   &lt;/tr&gt;
   ##ENDFOREACHnotinfocoms##
   &lt;/tbody&gt;
   &lt;/table&gt;');";
      $DB->query($query);

      $query = "INSERT INTO `glpi_notifications` (`name`, `entities_id`, `itemtype`, `event`, `is_recursive`, `is_active`) 
                 VALUES ('Alert infocoms', 0, 'PluginMorealertsInfocomAlert', 'notinfocom', 1, 1);";
      $DB->query($query);

      //retrieve notification id
      $query_id = "SELECT `id` FROM `glpi_notifications`
               WHERE `name` = 'Alert infocoms' AND `itemtype` = 'PluginMorealertsInfocomAlert' AND `event` = 'notinfocom'";
      $result = $DB->query($query_id) or die ($DB->error());
      $notification = $DB->result($result, 0, 'id');

      $query = "INSERT INTO `glpi_notifications_notificationtemplates` (`notifications_id`, `mode`, `notificationtemplates_id`) 
               VALUES (" . $notification . ", 'mailing', " . $itemtype . ");";
      $DB->query($query);

      //////////////////////
      $query_id = "SELECT `id` 
                  FROM `glpi_notificationtemplates` 
                  WHERE `itemtype`='PluginMorealertsInkAlert' AND `name` = 'Alert ink level'";
      $result = $DB->query($query_id) or die ($DB->error());
      $itemtype = $DB->result($result, 0, 'id');

      $query = "INSERT INTO `glpi_notificationtemplatetranslations`
                                VALUES(NULL, " . $itemtype . ", '','##lang.ink.title## : ##ink.entity##',
      '##lang.ink.title## :
      ##FOREACHinks##
      - ##ink.printer## - ##ink.cartridge## - ##ink.state##%
      ##ENDFOREACHinks##',
      '&lt;table class=\"tab_cadre\" border=\"1\" cellspacing=\"2\" cellpadding=\"3\"&gt;
      &lt;tbody&gt;
      &lt;tr&gt;
      &lt;td style=\"text-align: left;\" bgcolor=\"#cccccc\"&gt;&lt;span style=\"font-family: Verdana; font-size: 11px; text-align: left;\"&gt;##lang.ink.printer##&lt;/span&gt;&lt;/td&gt;
      &lt;td style=\"text-align: left;\" bgcolor=\"#cccccc\"&gt;&lt;span style=\"font-family: Verdana; font-size: 11px; text-align: left;\"&gt;##lang.ink.cartridge##&lt;/span&gt;&lt;/td&gt;
      &lt;td style=\"text-align: left;\" bgcolor=\"#cccccc\"&gt;&lt;span style=\"font-family: Verdana; font-size: 11px; text-align: left;\"&gt;##lang.ink.state##&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      ##FOREACHinks##
      &lt;tr&gt;
      &lt;td&gt;&lt;a href=\"##ink.urlprinter##\"&gt;&lt;span style=\"font-family: Verdana; font-size: 11px; text-align: left;\"&gt;##ink.printer##&lt;/span&gt;&lt;/a&gt;&lt;/td&gt;
      &lt;td&gt;&lt;a href=\"##ink.urlcartridge##\"&gt;&lt;span style=\"font-family: Verdana; font-size: 11px; text-align: left;\"&gt;##ink.cartridge##&lt;/span&gt;&lt;/a&gt;&lt;/td&gt;
      &lt;td&gt;&lt;span style=\"font-family: Verdana; font-size: 11px; text-align: left;\"&gt;##ink.state##%&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      ##ENDFOREACHinks##
      &lt;/tbody&gt;
      &lt;/table&gt;');";

      $DB->query($query);

      $query = "INSERT INTO `glpi_notifications` (`name`, `entities_id`, `itemtype`, `event`, `is_recursive`, `is_active`) 
                 VALUES ('Alert ink level', 0, 'PluginMorealertsInkAlert', 'ink', 1, 1);";
      $DB->query($query);

      //retrieve notification id
      $query_id = "SELECT `id` FROM `glpi_notifications`
               WHERE `name` = 'Alert ink level' AND `itemtype` = 'PluginMorealertsInkAlert' AND `event` = 'ink'";
      $result = $DB->query($query_id) or die ($DB->error());
      $notification = $DB->result($result, 0, 'id');

      $query = "INSERT INTO `glpi_notifications_notificationtemplates` (`notifications_id`, `mode`, `notificationtemplates_id`) 
               VALUES (" . $notification . ", 'mailing', " . $itemtype . ");";
      $DB->query($query);

   }
   if ($update78) {
      //Do One time on 0.78
      $query_ = "SELECT *
            FROM `glpi_plugin_morealerts_profiles` ";
      $result_ = $DB->query($query_);
      if ($DB->numrows($result_) > 0) {

         while ($data = $DB->fetchArray($result_)) {
            $query = "UPDATE `glpi_plugin_morealerts_profiles`
                  SET `profiles_id` = '" . $data["id"] . "'
                  WHERE `id` = '" . $data["id"] . "';";
            $DB->query($query);

         }
      }

      $query = "ALTER TABLE `glpi_plugin_morealerts_profiles`
               DROP `name` ;";
      $DB->query($query);
   }

   if ($install || $update90) {
      ////////////////
      $query_id = "SELECT `id` 
                  FROM `glpi_notificationtemplates` 
                  WHERE `itemtype`='PluginMorealertsTicketUnresolved' AND `name` = 'Alert Ticket Unresolved'";
      $result = $DB->query($query_id) or die ($DB->error());
      $itemtype = $DB->result($result, 0, 'id');

      $query = "INSERT INTO `glpi_notificationtemplatetranslations`
                                VALUES(NULL, " . $itemtype . ", '','##ticket.action## ##ticket.entity##',
      '##lang.ticket.entity## : ##ticket.entity##
     ##FOREACHtickets##

      ##lang.ticket.title## : ##ticket.title##
       ##lang.ticket.status## : ##ticket.status##

       ##ticket.url## 
       ##ENDFOREACHtickets##','&lt;table class=\"tab_cadre\" border=\"1\" cellspacing=\"2\" cellpadding=\"3\"&gt;
&lt;tbody&gt;
&lt;tr&gt;
&lt;td style=\"text-align: left;\" width=\"auto\" bgcolor=\"#95bde4\"&gt;&lt;span style=\"font-family: Verdana; font-size: 11px; text-align: left;\"&gt;##lang.ticket.authors##&lt;/span&gt;&lt;/td&gt;
&lt;td style=\"text-align: left;\" width=\"auto\" bgcolor=\"#95bde4\"&gt;&lt;span style=\"font-family: Verdana; font-size: 11px; text-align: left;\"&gt;##lang.ticket.title##&lt;/span&gt;&lt;/td&gt;
&lt;td style=\"text-align: left;\" width=\"auto\" bgcolor=\"#95bde4\"&gt;&lt;span style=\"font-family: Verdana; font-size: 11px; text-align: left;\"&gt;##lang.ticket.priority##&lt;/span&gt;&lt;/td&gt;
&lt;td style=\"text-align: left;\" width=\"auto\" bgcolor=\"#95bde4\"&gt;&lt;span style=\"font-family: Verdana; font-size: 11px; text-align: left;\"&gt;##lang.ticket.status##&lt;/span&gt;&lt;/td&gt;
&lt;td style=\"text-align: left;\" width=\"auto\" bgcolor=\"#95bde4\"&gt;&lt;span style=\"font-family: Verdana; font-size: 11px; text-align: left;\"&gt;##lang.ticket.attribution##&lt;/span&gt;&lt;/td&gt;
&lt;td style=\"text-align: left;\" width=\"auto\" bgcolor=\"#95bde4\"&gt;&lt;span style=\"font-family: Verdana; font-size: 11px; text-align: left;\"&gt;##lang.ticket.creationdate##&lt;/span&gt;&lt;/td&gt;
&lt;td style=\"text-align: left;\" width=\"auto\" bgcolor=\"#95bde4\"&gt;&lt;span style=\"font-family: Verdana; font-size: 11px; text-align: left;\"&gt;##lang.ticket.content##&lt;/span&gt;&lt;/td&gt;
&lt;/tr&gt;
##FOREACHtickets## 
&lt;tr&gt;
&lt;td width=\"auto\"&gt;&lt;span style=\"font-family: Verdana; font-size: 11px; text-align: left;\"&gt;##ticket.authors##&lt;/span&gt;&lt;/td&gt;
&lt;td width=\"auto\"&gt;&lt;span style=\"font-family: Verdana; font-size: 11px; text-align: left;\"&gt;&lt;a href=\"##ticket.url##\"&gt;##ticket.title##&lt;/a&gt;&lt;/span&gt;&lt;/td&gt;
&lt;td width=\"auto\"&gt;&lt;span style=\"font-family: Verdana; font-size: 11px; text-align: left;\"&gt;##ticket.priority##&lt;/span&gt;&lt;/td&gt;
&lt;td width=\"auto\"&gt;&lt;span style=\"font-family: Verdana; font-size: 11px; text-align: left;\"&gt;##ticket.status##&lt;/span&gt;&lt;/td&gt;
&lt;td width=\"auto\"&gt;&lt;span style=\"font-family: Verdana; font-size: 11px; text-align: left;\"&gt;##IFticket.assigntousers####ticket.assigntousers##&lt;br /&gt;##ENDIFticket.assigntousers####IFticket.assigntogroups##&lt;br /&gt;##ticket.assigntogroups## ##ENDIFticket.assigntogroups####IFticket.assigntosupplier##&lt;br /&gt;##ticket.assigntosupplier## ##ENDIFticket.assigntosupplier##&lt;/span&gt;&lt;/td&gt;
&lt;td width=\"auto\"&gt;&lt;span style=\"font-family: Verdana; font-size: 11px; text-align: left;\"&gt;##ticket.creationdate##&lt;/span&gt;&lt;/td&gt;
&lt;td width=\"auto\"&gt;&lt;span style=\"font-family: Verdana; font-size: 11px; text-align: left;\"&gt;##ticket.content##&lt;/span&gt;&lt;/td&gt;
&lt;/tr&gt;
##ENDFOREACHtickets##
&lt;/tbody&gt;
&lt;/table&gt;')";

      $DB->query($query);

      $query = "INSERT INTO `glpi_notifications` (`name`, `entities_id`, `itemtype`, `event`, `is_recursive`, `is_active`) 
                VALUES ('Alert Ticket Unresolved', 0, 'PluginMorealertsTicketUnresolved', 'ticketunresolved', 1, 1);";
      $DB->query($query);

      //retrieve notification id
      $query_id = "SELECT `id` FROM `glpi_notifications`
               WHERE `name` = 'Alert Ticket Unresolved' 
               AND `itemtype` = 'PluginMorealertsTicketUnresolved' 
               AND `event` = 'ticketunresolved'";
      $result = $DB->query($query_id) or die ($DB->error());
      $notification = $DB->result($result, 0, 'id');

      $query = "INSERT INTO `glpi_notifications_notificationtemplates` (`notifications_id`, `mode`, `notificationtemplates_id`) 
               VALUES (" . $notification . ", 'mailing', " . $itemtype . ");";
      $DB->query($query);

   }

   // To be called for each task the plugin manage
   CronTask::Register('PluginMorealertsInfocomAlert', 'MorealertsNotInfocom', HOUR_TIMESTAMP);
   CronTask::Register('PluginMorealertsInkAlert', 'MorealertsInk', DAY_TIMESTAMP);
   CronTask::Register('PluginMorealertsTicketUnresolved', 'AdditionalalertsTicketUnresolved', DAY_TIMESTAMP);

   PluginMorealertsProfile::initProfile();
   PluginMorealertsProfile::createFirstAccess($_SESSION['glpiactiveprofile']['id']);

   if ($DB->tableExists("glpi_plugin_morealerts_profiles")) {
      $query = "DROP TABLE `glpi_plugin_morealerts_profiles`;";
      $DB->query($query);
   }

   return true;
}

/**
 * @return bool
 */
function plugin_morealerts_uninstall() {
   global $DB;

   include_once(PLUGIN_MOREALERTS_DIR. "/inc/profile.class.php");
   include_once(PLUGIN_MOREALERTS_DIR. "/inc/menu.class.php");

   $tables = [
      "glpi_plugin_morealerts_infocomalerts",
      "glpi_plugin_morealerts_inkalerts",
      "glpi_plugin_morealerts_notificationtypes",
      "glpi_plugin_morealerts_configs",
      "glpi_plugin_morealerts_inkthresholds",
      "glpi_plugin_morealerts_inkprinterstates",
      "glpi_plugin_morealerts_ticketunresolveds"];

   foreach ($tables as $table) {
      $DB->query("DROP TABLE IF EXISTS `$table`;");
   }

   //old versions
   $tables = ["glpi_plugin_morealerts_reminderalerts",
      "glpi_plugin_alerting_config",
      "glpi_plugin_alerting_state",
      "glpi_plugin_alerting_profiles",
      "glpi_plugin_alerting_mailing",
      "glpi_plugin_alerting_type",
      "glpi_plugin_morealerts_profiles",
      "glpi_plugin_alerting_cartridges",
      "glpi_plugin_alerting_cartridges_printer_state",
      "glpi_plugin_morealerts_profiles",
      "glpi_plugin_morealerts_ocsalerts",
      "glpi_plugin_morealerts_notificationstates"];

   foreach ($tables as $table) {
      $DB->query("DROP TABLE IF EXISTS `$table`;");
   }

   $notif = new Notification();

   $options = ['itemtype' => 'PluginMorealertsInkAlert',
      'event' => 'ink',
      'FIELDS' => 'id'];
   foreach ($DB->request('glpi_notifications', $options) as $data) {
      $notif->delete($data);
   }

   $options = ['itemtype' => 'PluginMorealertsInfocomAlert',
      'event' => 'notinfocom',
      'FIELDS' => 'id'];
   foreach ($DB->request('glpi_notifications', $options) as $data) {
      $notif->delete($data);
   }

   $options = ['itemtype' => 'PluginMorealertsTicketUnresolved',
      'event' => 'ticketunresolved',
      'FIELDS' => 'id'];
   foreach ($DB->request('glpi_notifications', $options) as $data) {
      $notif->delete($data);
   }

   //templates
   $template = new NotificationTemplate();
   $translation = new NotificationTemplateTranslation();
   $options = ['itemtype' => 'PluginMorealertsInfocomAlert',
      'FIELDS' => 'id'];
   foreach ($DB->request('glpi_notificationtemplates', $options) as $data) {
      $options_template = ['notificationtemplates_id' => $data['id'],
         'FIELDS' => 'id'];

      foreach ($DB->request('glpi_notificationtemplatetranslations', $options_template) as $data_template) {
         $translation->delete($data_template);
      }
      $template->delete($data);
   }

   //templates
   $template = new NotificationTemplate();
   $translation = new NotificationTemplateTranslation();
   $options = ['itemtype' => 'PluginMorealertsInkAlert',
      'FIELDS' => 'id'];
   foreach ($DB->request('glpi_notificationtemplates', $options) as $data) {
      $options_template = ['notificationtemplates_id' => $data['id'],
         'FIELDS' => 'id'];

      foreach ($DB->request('glpi_notificationtemplatetranslations', $options_template) as $data_template) {
         $translation->delete($data_template);
      }
      $template->delete($data);
   }

   //templates
   $template = new NotificationTemplate();
   $translation = new NotificationTemplateTranslation();
   $options = ['itemtype' => 'PluginMorealertsTicketUnresolved',
      'FIELDS' => 'id'];
   foreach ($DB->request('glpi_notificationtemplates', $options) as $data) {
      $options_template = ['notificationtemplates_id' => $data['id'],
         'FIELDS' => 'id'];

      foreach ($DB->request('glpi_notificationtemplatetranslations', $options_template) as $data_template) {
         $translation->delete($data_template);
      }
      $template->delete($data);
   }

   //Plugin::registerClass('PluginMorealertsProfile');

   //Delete rights associated with the plugin
   $profileRight = new ProfileRight();
   foreach (PluginMorealertsProfile::getAllRights() as $right) {
      $profileRight->deleteByCriteria(['name' => $right['field']]);
   }
   PluginMorealertsProfile::removeRightsFromSession();

   PluginMorealertsMenu::removeRightsFromSession();

   CronTask::Unregister('morealerts');

   return true;
}

// Define database relations
/**
 * @return array
 */
function plugin_morealerts_getDatabaseRelations() {

   $links = [];
   if (Plugin::isPluginActive("additionalalerts")) {
      $links = [
         "glpi_states" => [
            "glpi_plugin_morealerts_notificationstates" => "states_id"
         ],
         "glpi_computertypes" => [
            "glpi_plugin_morealerts_notificationtypes" => "types_id"
         ]];
   }
   if (Plugin::isPluginActive("fusioninventory")) {
      $links[] = ["glpi_plugin_fusioninventory_printercartridges" => [
         "glpi_plugin_morealerts_ink" => "cartridges_id"
      ]];
   }

   return $links;
}
