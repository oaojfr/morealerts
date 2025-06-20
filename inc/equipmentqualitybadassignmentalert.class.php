<?php
// Alerte : affectation à utilisateur/service désactivé ou inexistant
class PluginMorealertsEquipmentQualityBadAssignmentAlert extends PluginMorealertsAdditionalalert {
    /**
     * Retourne la liste des équipements affectés à des utilisateurs ou services désactivés/inexistants
     */
    public static function getEquipmentsWithBadAssignment() {
        global $DB;
        $equipments = [];
        // Ordinateurs affectés à un utilisateur désactivé ou inexistant
        $sql = "SELECT c.id, c.name, 'Computer' AS type, c.users_id, u.name as username, u.is_active
                FROM glpi_computers c
                LEFT JOIN glpi_users u ON c.users_id = u.id
                WHERE c.users_id IS NOT NULL AND c.users_id > 0 AND (u.id IS NULL OR u.is_active = 0)";
        $res = $DB->query($sql);
        while ($row = $DB->fetch_assoc($res)) {
            $equipments[] = $row;
        }
        // Moniteurs affectés à un utilisateur désactivé ou inexistant
        $sql = "SELECT m.id, m.name, 'Monitor' AS type, m.users_id, u.name as username, u.is_active
                FROM glpi_monitors m
                LEFT JOIN glpi_users u ON m.users_id = u.id
                WHERE m.users_id IS NOT NULL AND m.users_id > 0 AND (u.id IS NULL OR u.is_active = 0)";
        $res = $DB->query($sql);
        while ($row = $DB->fetch_assoc($res)) {
            $equipments[] = $row;
        }
        // Périphériques affectés à un utilisateur désactivé ou inexistant
        $sql = "SELECT p.id, p.name, 'Peripheral' AS type, p.users_id, u.name as username, u.is_active
                FROM glpi_peripherals p
                LEFT JOIN glpi_users u ON p.users_id = u.id
                WHERE p.users_id IS NOT NULL AND p.users_id > 0 AND (u.id IS NULL OR u.is_active = 0)";
        $res = $DB->query($sql);
        while ($row = $DB->fetch_assoc($res)) {
            $equipments[] = $row;
        }
        // (Idem pour les services si besoin...)
        return $equipments;
    }

    /**
     * Affiche les alertes d'affectation à utilisateur/service désactivé/inexistant
     */
    public static function displayAlerts() {
        $list = self::getEquipmentsWithBadAssignment();
        if (empty($list)) {
            echo '<div class="center">'.__('No equipment assigned to disabled/nonexistent user or service', 'morealerts').'</div>';
            return;
        }
        echo '<table class="tab_cadre_fixe">';
        echo '<tr><th>'.__('Type').'</th><th>'.__('Name').'</th><th>'.__('User').'</th><th>'.__('User status').'</th></tr>';
        foreach ($list as $eq) {
            echo '<tr>';
            echo '<td>'.htmlentities($eq['type']).'</td>';
            echo '<td><a href="/front/'.strtolower($eq['type']).'.form.php?id='.$eq['id'].'">'.htmlentities($eq['name']).'</a></td>';
            echo '<td>'.htmlentities($eq['username']).'</td>';
            echo '<td>'.(isset($eq['is_active']) ? ($eq['is_active'] ? __('Active') : __('Disabled')) : __('Unknown')).'</td>';
            echo '</tr>';
        }
        echo '</table>';
    }
}
