<?php
// Alerte : champs obligatoires manquants ou incohérents
class PluginMorealertsEquipmentQualityMissingFieldsAlert extends PluginMorealertsAdditionalalert {
    /**
     * Retourne la liste des équipements avec champs obligatoires manquants ou incohérents
     */
    public static function getEquipmentsWithMissingFields() {
        global $DB;
        $equipments = [];
        // Vérification pour les ordinateurs
        $sql = "SELECT id, name, 'Computer' AS type, manufacturers_id, os_name, otherserial, serial, date_mod, date_creation, date_buy
                FROM glpi_computers
                WHERE (manufacturers_id IS NULL OR manufacturers_id = 0)
                   OR (os_name IS NULL OR os_name = '')
                   OR (otherserial IS NULL OR otherserial = '')
                   OR (serial IS NULL OR serial = '')
                   OR (date_buy IS NULL OR date_buy = '0000-00-00')";
        $res = $DB->query($sql);
        while ($row = $DB->fetch_assoc($res)) {
            $equipments[] = $row;
        }
        // Vérification pour les moniteurs
        $sql = "SELECT id, name, 'Monitor' AS type, manufacturers_id, '' as os_name, otherserial, serial, date_mod, date_creation, date_buy
                FROM glpi_monitors
                WHERE (manufacturers_id IS NULL OR manufacturers_id = 0)
                   OR (otherserial IS NULL OR otherserial = '')
                   OR (serial IS NULL OR serial = '')
                   OR (date_buy IS NULL OR date_buy = '0000-00-00')";
        $res = $DB->query($sql);
        while ($row = $DB->fetch_assoc($res)) {
            $equipments[] = $row;
        }
        // Vérification pour les périphériques
        $sql = "SELECT id, name, 'Peripheral' AS type, manufacturers_id, '' as os_name, otherserial, serial, date_mod, date_creation, date_buy
                FROM glpi_peripherals
                WHERE (manufacturers_id IS NULL OR manufacturers_id = 0)
                   OR (otherserial IS NULL OR otherserial = '')
                   OR (serial IS NULL OR serial = '')
                   OR (date_buy IS NULL OR date_buy = '0000-00-00')";
        $res = $DB->query($sql);
        while ($row = $DB->fetch_assoc($res)) {
            $equipments[] = $row;
        }
        return $equipments;
    }

    /**
     * Affiche les alertes de champs obligatoires manquants ou incohérents
     */
    public static function displayAlerts() {
        $list = self::getEquipmentsWithMissingFields();
        if (empty($list)) {
            echo '<div class="center">'.__('No equipment with missing or inconsistent required fields', 'morealerts').'</div>';
            return;
        }
        echo '<table class="tab_cadre_fixe">';
        echo '<tr><th>'.__('Type').'</th><th>'.__('Name').'</th><th>'.__('Manufacturer').'</th><th>'.__('OS').'</th><th>'.__('Inventory number').'</th><th>'.__('Serial number').'</th><th>'.__('Buy date').'</th></tr>';
        foreach ($list as $eq) {
            echo '<tr>';
            echo '<td>'.htmlentities($eq['type']).'</td>';
            echo '<td><a href="/front/'.strtolower($eq['type']).'.form.php?id='.$eq['id'].'">'.htmlentities($eq['name']).'</a></td>';
            echo '<td>'.htmlentities($eq['manufacturers_id']).'</td>';
            echo '<td>'.htmlentities($eq['os_name']).'</td>';
            echo '<td>'.htmlentities($eq['otherserial']).'</td>';
            echo '<td>'.htmlentities($eq['serial']).'</td>';
            echo '<td>'.htmlentities($eq['date_buy']).'</td>';
            echo '</tr>';
        }
        echo '</table>';
    }
}
