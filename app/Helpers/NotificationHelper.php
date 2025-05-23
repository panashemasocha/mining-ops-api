<?php
namespace App\Helpers;

use App\Services\FcmService;
use App\Models\User;

class NotificationHelper
{
    protected $fcmService;

    public function __construct(FcmService $fcmService)
    {
        $this->fcmService = $fcmService;
    }

    /**
     * Send notification based on user roles and hierarchy
     */
    public function notifyByHierarchy($event, $data, $excludeUserId = null)
    {
        switch ($event) {
            case 'ore_submitted':
                $this->fcmService->sendToHigherRanking(
                    ['manager', 'supervisor', 'admin'],
                    'New Ore Data',
                    'New ore data has been submitted.',
                    array_merge($data, ['notification_type' => 'new_ore'])
                );
                break;

            case 'dispatch_created':
                $this->fcmService->sendToHigherRanking(
                    ['manager', 'admin'],
                    'New Dispatch',
                    'A new dispatch has been created.',
                    array_merge($data, ['notification_type' => 'new_dispatch'])
                );
                break;

            case 'trip_assigned':
                if (isset($data['driver_ids'])) {
                    $drivers = User::whereIn('id', $data['driver_ids'])->get();
                    $this->fcmService->sendToUsers(
                        $drivers,
                        'Trip Assignment',
                        'You have been assigned to a new trip.',
                        array_merge($data, ['notification_type' => 'driver_assignment'])
                    );
                }
                break;
        }
    }
}