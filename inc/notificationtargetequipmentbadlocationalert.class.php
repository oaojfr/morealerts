<?php
// Cible de notification pour l'alerte localisation obsolète
class PluginMorealertsNotificationTargetEquipmentBadLocationAlert extends PluginMorealertsNotificationTarget {
    public function getEvents() {
        return ['equipmentbadlocation' => __('Bad location alert', 'morealerts')];
    }
    public function getDatasForTemplate($event, $options = []) {
        $list = PluginMorealertsEquipmentBadLocationAlert::getBadLocationEquipments();
        return [
            'badlocation_equipments' => $list,
            'count' => count($list)
        ];
    }
    public function getSubject($event, $options = []) {
        return __('Bad location alert', 'morealerts');
    }
    public function getBody($event, $options = []) {
        $data = $this->getDatasForTemplate($event, $options);
        if ($data['count'] == 0) {
            return __('No equipment with bad location', 'morealerts');
        }
        $body = __('Equipments with bad location:', 'morealerts') . "\n";
        foreach ($data['badlocation_equipments'] as $eq) {
            $body .= $eq['type'] . ': ' . $eq['name'] . "\n";
        }
        return $body;
    }
}
