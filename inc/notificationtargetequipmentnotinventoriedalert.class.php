<?php
// Cible de notification pour l'alerte d'équipement non inventorié
class PluginMorealertsNotificationTargetEquipmentNotInventoriedAlert extends PluginMorealertsNotificationTarget {
    public function getEvents() {
        return ['equipmentnotinventoried' => __('Not inventoried since X days', 'morealerts')];
    }
    public function getDatasForTemplate($event, $options = []) {
        $list = PluginMorealertsEquipmentNotInventoriedAlert::getNotInventoriedEquipments();
        return [
            'notinventoried_equipments' => $list,
            'count' => count($list)
        ];
    }
    public function getSubject($event, $options = []) {
        return __('Not inventoried since X days', 'morealerts');
    }
    public function getBody($event, $options = []) {
        $data = $this->getDatasForTemplate($event, $options);
        if ($data['count'] == 0) {
            return __('No equipment not inventoried since X days', 'morealerts');
        }
        $body = __('Equipments not inventoried since X days:', 'morealerts') . "\n";
        foreach ($data['notinventoried_equipments'] as $eq) {
            $body .= $eq['type'] . ': ' . $eq['name'] . ' (' . $eq['last_inventory_update'] . ")\n";
        }
        return $body;
    }
}
