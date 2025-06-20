<?php
// Cible de notification pour l'alerte d'informations manquantes
class PluginMorealertsNotificationTargetEquipmentMissingInfoAlert extends PluginMorealertsNotificationTarget {
    public function getEvents() {
        return ['equipmentmissinginfo' => __('Missing info alert', 'morealerts')];
    }
    public function getDatasForTemplate($event, $options = []) {
        $list = PluginMorealertsEquipmentMissingInfoAlert::getMissingInfoEquipments();
        return [
            'missinginfo_equipments' => $list,
            'count' => count($list)
        ];
    }
    public function getSubject($event, $options = []) {
        return __('Missing info alert', 'morealerts');
    }
    public function getBody($event, $options = []) {
        $data = $this->getDatasForTemplate($event, $options);
        if ($data['count'] == 0) {
            return __('No equipment with missing info', 'morealerts');
        }
        $body = __('Equipments with missing info:', 'morealerts') . "\n";
        foreach ($data['missinginfo_equipments'] as $eq) {
            $body .= $eq['type'] . ': ' . $eq['name'] . "\n";
        }
        return $body;
    }
}
