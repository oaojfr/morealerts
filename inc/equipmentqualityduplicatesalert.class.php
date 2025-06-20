<?php
// Alerte : doublons détectés
class PluginMorealertsEquipmentQualityDuplicatesAlert extends PluginMorealertsAdditionalalert {
    /**
     * Retourne la liste des équipements avec doublons (numéro de série, inventaire, nom)
     */
    public static function getEquipmentsWithDuplicates() {
        global $DB;
        $duplicates = [];
        // Doublons sur le numéro de série (ordinateurs)
        $sql = "SELECT serial, COUNT(*) as nb FROM glpi_computers WHERE serial IS NOT NULL AND serial <> '' GROUP BY serial HAVING nb > 1";
        $res = $DB->query($sql);
        while ($row = $DB->fetch_assoc($res)) {
            $serial = $DB->escape($row['serial']);
            $sql2 = "SELECT id, name, 'Computer' AS type, serial FROM glpi_computers WHERE serial = '$serial'";
            $res2 = $DB->query($sql2);
            while ($eq = $DB->fetch_assoc($res2)) {
                $duplicates[] = $eq;
            }
        }
        // Doublons sur le numéro d'inventaire (ordinateurs)
        $sql = "SELECT otherserial, COUNT(*) as nb FROM glpi_computers WHERE otherserial IS NOT NULL AND otherserial <> '' GROUP BY otherserial HAVING nb > 1";
        $res = $DB->query($sql);
        while ($row = $DB->fetch_assoc($res)) {
            $otherserial = $DB->escape($row['otherserial']);
            $sql2 = "SELECT id, name, 'Computer' AS type, otherserial FROM glpi_computers WHERE otherserial = '$otherserial'";
            $res2 = $DB->query($sql2);
            while ($eq = $DB->fetch_assoc($res2)) {
                $duplicates[] = $eq;
            }
        }
        // Doublons sur le nom (ordinateurs)
        $sql = "SELECT name, COUNT(*) as nb FROM glpi_computers WHERE name IS NOT NULL AND name <> '' GROUP BY name HAVING nb > 1";
        $res = $DB->query($sql);
        while ($row = $DB->fetch_assoc($res)) {
            $name = $DB->escape($row['name']);
            $sql2 = "SELECT id, name, 'Computer' AS type FROM glpi_computers WHERE name = '$name'";
            $res2 = $DB->query($sql2);
            while ($eq = $DB->fetch_assoc($res2)) {
                $duplicates[] = $eq;
            }
        }
        // Répéter pour moniteurs et périphériques si besoin...
        return $duplicates;
    }

    /**
     * Affiche les alertes de doublons
     */
    public static function displayAlerts() {
        $list = self::getEquipmentsWithDuplicates();
        if (empty($list)) {
            echo '<div class="center">'.__('No equipment with detected duplicates', 'morealerts').'</div>';
            return;
        }
        echo '<table class="tab_cadre_fixe">';
        echo '<tr><th>'.__('Type').'</th><th>'.__('Name').'</th><th>'.__('Serial number').'</th><th>'.__('Inventory number').'</th></tr>';
        foreach ($list as $eq) {
            echo '<tr>';
            echo '<td>'.htmlentities($eq['type']).'</td>';
            echo '<td><a href="/front/'.strtolower($eq['type']).'.form.php?id='.$eq['id'].'">'.htmlentities($eq['name']).'</a></td>';
            echo '<td>'.(isset($eq['serial']) ? htmlentities($eq['serial']) : '').'</td>';
            echo '<td>'.(isset($eq['otherserial']) ? htmlentities($eq['otherserial']) : '').'</td>';
            echo '</tr>';
        }
        echo '</table>';
    }
}
