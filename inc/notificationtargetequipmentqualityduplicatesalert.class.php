<?php
class PluginMorealertsNotificationTargetEquipmentQualityDuplicatesAlert extends PluginMorealertsNotificationTarget {
    public function getEvents() {
        return ['equipmentqualityduplicates' => __('Detected duplicates', 'morealerts')];
    }
    public function getDatasForTemplate($event, $options = []) {
        $list = PluginMorealertsEquipmentQualityDuplicatesAlert::getEquipmentsWithDuplicates();
        return [
            'equipments' => $list,
            'count' => count($list)
        ];
    }
    public function getSubject($event, $options = []) {
        return __('Detected duplicates', 'morealerts');
    }
    public function getBody($event, $options = []) {
        $data = $this->getDatasForTemplate($event, $options);
        if ($data['count'] == 0) {
            return __('No equipment with detected duplicates', 'morealerts');
        }
        $body = __('Equipments with detected duplicates:', 'morealerts') . "\n";
        foreach ($data['equipments'] as $eq) {
            $body .= $eq['type'] . ': ' . $eq['name'] . ' (ID: ' . $eq['id'] . ")\n";
        }
        return $body;
    }
}
