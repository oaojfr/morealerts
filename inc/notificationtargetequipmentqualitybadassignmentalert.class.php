<?php
class PluginMorealertsNotificationTargetEquipmentQualityBadAssignmentAlert extends PluginMorealertsNotificationTarget {
    public function getEvents() {
        return ['equipmentqualitybadassignment' => __('Assignment to disabled/nonexistent user or service', 'morealerts')];
    }
    public function getDatasForTemplate($event, $options = []) {
        $list = PluginMorealertsEquipmentQualityBadAssignmentAlert::getEquipmentsWithBadAssignment();
        return [
            'equipments' => $list,
            'count' => count($list)
        ];
    }
    public function getSubject($event, $options = []) {
        return __('Assignment to disabled/nonexistent user or service', 'morealerts');
    }
    public function getBody($event, $options = []) {
        $data = $this->getDatasForTemplate($event, $options);
        if ($data['count'] == 0) {
            return __('No equipment assigned to disabled/nonexistent user or service', 'morealerts');
        }
        $body = __('Equipments assigned to disabled/nonexistent user or service:', 'morealerts') . "\n";
        foreach ($data['equipments'] as $eq) {
            $body .= $eq['type'] . ': ' . $eq['name'] . ' (ID: ' . $eq['id'] . ")\n";
        }
        return $body;
    }
}
