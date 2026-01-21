<?php

namespace App\Services;

class StateMachineService
{
    protected array $transitions = [
        'crm_to_wqs' => ['wqs_ready'],
        'wqs_ready' => ['scm_ready'],
        'scm_ready' => ['scm_on_delivery'],
        'scm_on_delivery' => ['scm_delivered'],
        'scm_delivered' => ['act_tukar_faktur'],
        'act_tukar_faktur' => ['act_invoiced'],
        'act_invoiced' => ['fin_on_collect'],
        'fin_on_collect' => ['fin_paid'],
    ];

    public function canTransition(string $currentStatus, string $targetStatus): bool
    {
        return in_array($targetStatus, $this->transitions[$currentStatus] ?? []);
    }

    public function getNextStatuses(string $currentStatus): array
    {
        return $this->transitions[$currentStatus] ?? [];
    }
}
