<?php
// Cible de notification pour l'alerte maintenance
class PluginMorealertsNotificationTargetEquipmentMaintenanceAlert extends PluginMorealertsNotificationTarget {
    public function getEvents() {
        return ['equipmentmaintenance' => __('Maintenance alert', 'morealerts')];
    }
    public function getDatasForTemplate($event, $options = []) {
        $list = PluginMorealertsEquipmentMaintenanceAlert::getMaintenanceEquipments();
        return [
            'maintenance_equipments' => $list,
            'count' => count($list)
        ];
    }
    public function getSubject($event, $options = []) {
        return __('Maintenance alert', 'morealerts');
    }
    public function getBody($event, $options = []) {
        $data = $this->getDatasForTemplate($event, $options);
        if ($data['count'] == 0) {
            return __('No equipment with maintenance issue', 'morealerts');
        }
        $body = __('Equipments with maintenance issue:', 'morealerts') . "\n";
        foreach ($data['maintenance_equipments'] as $eq) {
            $body .= $eq['type'] . ': ' . $eq['name'] . "\n";
        }
        return $body;
    }
}
