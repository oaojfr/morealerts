<?php
// Cible de notification pour l'alerte périphérique non rattaché
class PluginMorealertsNotificationTargetPeripheralNotLinkedAlert extends PluginMorealertsNotificationTarget {
    public function getEvents() {
        return ['peripheralnotlinked' => __('Peripheral not linked alert', 'morealerts')];
    }
    public function getDatasForTemplate($event, $options = []) {
        $list = PluginMorealertsPeripheralNotLinkedAlert::getNotLinkedPeripherals();
        return [
            'notlinked_peripherals' => $list,
            'count' => count($list)
        ];
    }
    public function getSubject($event, $options = []) {
        return __('Peripheral not linked alert', 'morealerts');
    }
    public function getBody($event, $options = []) {
        $data = $this->getDatasForTemplate($event, $options);
        if ($data['count'] == 0) {
            return __('No peripheral not linked', 'morealerts');
        }
        $body = __('Peripherals not linked:', 'morealerts') . "\n";
        foreach ($data['notlinked_peripherals'] as $eq) {
            $body .= $eq['name'] . "\n";
        }
        return $body;
    }
}
