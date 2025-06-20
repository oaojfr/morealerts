<?php
// Classe pour l'alerte de garantie expirée
class PluginMorealertsEquipmentWarrantyAlert extends PluginMorealertsAdditionalalert {
    /**
     * Retourne la liste des équipements dont la garantie est expirée
     */
    public static function getExpiredWarrantyEquipments() {
        global $DB;
        $today = date('Y-m-d');
        $sql = "SELECT `glpi_computers`.`id`, `glpi_computers`.`name`, `glpi_computers`.`warranty_date`, 'Computer' AS type FROM `glpi_computers` WHERE `warranty_date` IS NOT NULL AND `warranty_date` <> '' AND `warranty_date` < '$today' ";
        $sql .= "UNION ALL ";
        $sql .= "SELECT `glpi_monitors`.`id`, `glpi_monitors`.`name`, `glpi_monitors`.`warranty_date`, 'Monitor' AS type FROM `glpi_monitors` WHERE `warranty_date` IS NOT NULL AND `warranty_date` <> '' AND `warranty_date` < '$today' ";
        $sql .= "UNION ALL ";
        $sql .= "SELECT `glpi_peripherals`.`id`, `glpi_peripherals`.`name`, `glpi_peripherals`.`warranty_date`, 'Peripheral' AS type FROM `glpi_peripherals` WHERE `warranty_date` IS NOT NULL AND `warranty_date` <> '' AND `warranty_date` < '$today' ";
        $result = $DB->query($sql);
        $expired = [];
        if ($result) {
            while ($data = $DB->fetch_assoc($result)) {
                $expired[] = $data;
            }
        }
        return $expired;
    }

    /**
     * Affiche les alertes de garantie expirée
     */
    public static function displayAlerts() {
        $expired = self::getExpiredWarrantyEquipments();
        if (empty($expired)) {
            echo '<div class="center">'.__('No equipment with expired warranty', 'morealerts').'</div>';
            return;
        }
        echo '<table class="tab_cadre_fixe">';
        echo '<tr><th>'.__('Type').'</th><th>'.__('Name').'</th><th>'.__('Warranty date').'</th></tr>';
        foreach ($expired as $eq) {
            echo '<tr>';
            echo '<td>'.htmlentities($eq['type']).'</td>';
            echo '<td><a href="/front/'.strtolower($eq['type']).'.form.php?id='.$eq['id'].'">'.htmlentities($eq['name']).'</a></td>';
            echo '<td>'.htmlentities($eq['warranty_date']).'</td>';
            echo '</tr>';
        }
        echo '</table>';
    }
}
