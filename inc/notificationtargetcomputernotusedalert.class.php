<?php
// Cible de notification pour l'alerte ordinateur non utilisé
class PluginMorealertsNotificationTargetComputerNotUsedAlert extends PluginMorealertsNotificationTarget {
    public function getEvents() {
        return ['computernotused' => __('Computer not used since X days', 'morealerts')];
    }
    public function getDatasForTemplate($event, $options = []) {
        $list = PluginMorealertsComputerNotUsedAlert::getNotUsedComputers();
        return [
            'notused_computers' => $list,
            'count' => count($list)
        ];
    }
    public function getSubject($event, $options = []) {
        return __('Computer not used since X days', 'morealerts');
    }
    public function getBody($event, $options = []) {
        $data = $this->getDatasForTemplate($event, $options);
        if ($data['count'] == 0) {
            return __('No computer not used since X days', 'morealerts');
        }
        $body = __('Computers not used since X days:', 'morealerts') . "\n";
        foreach ($data['notused_computers'] as $eq) {
            $body .= $eq['name'] . ' (' . $eq['last_login'] . ")\n";
        }
        return $body;
    }
}
