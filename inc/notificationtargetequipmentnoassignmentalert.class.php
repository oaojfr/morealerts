<?php
// Cible de notification pour l'alerte d'équipement sans affectation
class PluginMorealertsNotificationTargetEquipmentNoAssignmentAlert extends PluginMorealertsNotificationTarget {
    public function getEvents() {
        return ['equipmentnoassignment' => __('No assignment alert', 'morealerts')];
    }
    public function getDatasForTemplate($event, $options = []) {
        $list = PluginMorealertsEquipmentNoAssignmentAlert::getNoAssignmentEquipments();
        return [
            'noassignment_equipments' => $list,
            'count' => count($list)
        ];
    }
    public function getSubject($event, $options = []) {
        return __('No assignment alert', 'morealerts');
    }
    public function getBody($event, $options = []) {
        $data = $this->getDatasForTemplate($event, $options);
        if ($data['count'] == 0) {
            return __('No equipment without assignment', 'morealerts');
        }
        $body = __('Equipments without assignment:', 'morealerts') . "\n";
        foreach ($data['noassignment_equipments'] as $eq) {
            $body .= $eq['type'] . ': ' . $eq['name'] . "\n";
        }
        return $body;
    }
}
