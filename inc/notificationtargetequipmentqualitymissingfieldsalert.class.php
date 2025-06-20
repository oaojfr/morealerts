<?php
class PluginMorealertsNotificationTargetEquipmentQualityMissingFieldsAlert extends PluginMorealertsNotificationTarget {
    public function getEvents() {
        return ['equipmentqualitymissingfields' => __('Missing or inconsistent required fields', 'morealerts')];
    }
    public function getDatasForTemplate($event, $options = []) {
        $list = PluginMorealertsEquipmentQualityMissingFieldsAlert::getEquipmentsWithMissingFields();
        return [
            'equipments' => $list,
            'count' => count($list)
        ];
    }
    public function getSubject($event, $options = []) {
        return __('Missing or inconsistent required fields', 'morealerts');
    }
    public function getBody($event, $options = []) {
        $data = $this->getDatasForTemplate($event, $options);
        if ($data['count'] == 0) {
            return __('No equipment with missing or inconsistent required fields', 'morealerts');
        }
        $body = __('Equipments with missing or inconsistent required fields:', 'morealerts') . "\n";
        foreach ($data['equipments'] as $eq) {
            $body .= $eq['type'] . ': ' . $eq['name'] . ' (ID: ' . $eq['id'] . ")\n";
        }
        return $body;
    }
}
