<?php
// Cible de notification pour l'alerte de garantie expirée
class PluginMorealertsNotificationTargetEquipmentWarrantyAlert extends PluginMorealertsNotificationTarget {
    public function getEvents() {
        return ['equipmentwarrantyexpired' => __('Warranty expired alert', 'morealerts')];
    }

    public function getDatasForTemplate($event, $options = []) {
        $expired = PluginMorealertsEquipmentWarrantyAlert::getExpiredWarrantyEquipments();
        return [
            'expired_equipments' => $expired,
            'count' => count($expired)
        ];
    }

    public function getSubject($event, $options = []) {
        return __('Warranty expired alert', 'morealerts');
    }

    public function getBody($event, $options = []) {
        $data = $this->getDatasForTemplate($event, $options);
        if ($data['count'] == 0) {
            return __('No equipment with expired warranty', 'morealerts');
        }
        $body = __('Equipments with expired warranty:', 'morealerts') . "\n";
        foreach ($data['expired_equipments'] as $eq) {
            $body .= $eq['type'] . ': ' . $eq['name'] . ' (' . $eq['warranty_date'] . ")\n";
        }
        return $body;
    }
}
