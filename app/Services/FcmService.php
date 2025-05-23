<?php
namespace App\Services;

use App\Models\User;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Laravel\Firebase\Facades\Firebase;

class FcmService
{
    protected $messaging;
    
    public function __construct()
    {
        $this->messaging = Firebase::messaging();
    }
    
    /**
     * Send notification to a specific user
     */
    public function sendToUser(User $user, string $title, string $body, array $data = [])
    {
        $tokens = $user->fcmTokens()->pluck('token')->toArray();
        
        if (empty($tokens)) {
            return false;
        }
        
        return $this->sendToTokens($tokens, $title, $body, $data);
    }
    
    /**
     * Send notification to multiple users
     */
    public function sendToUsers($users, string $title, string $body, array $data = [])
    {
        $tokens = [];
        
        foreach ($users as $user) {
            $userTokens = $user->fcmTokens()->pluck('token')->toArray();
            $tokens = array_merge($tokens, $userTokens);
        }
        
        if (empty($tokens)) {
            return false;
        }
        
        return $this->sendToTokens($tokens, $title, $body, $data);
    }
    
    /**
     * Send notification to users with specific role or job position
     */
    public function sendToRole(string $role, string $title, string $body, array $data = [])
    {
        $users = User::whereHas('roles', function($query) use ($role) {
            $query->where('name', $role);
        })->get();
        
        return $this->sendToUsers($users, $title, $body, $data);
    }
    
    /**
     * Send notification to users with higher ranking positions
     */
    public function sendToHigherRanking(array $higherRoles, string $title, string $body, array $data = [])
    {
        $users = User::whereHas('role', function($query) use ($higherRoles) {
            $query->whereIn('id', $higherRoles);
        })->get();
        
        return $this->sendToUsers($users, $title, $body, $data);
    }
    
    /**
     * Send notification to tokens
     */
    protected function sendToTokens(array $tokens, string $title, string $body, array $data = [])
    {
        if (empty($tokens)) {
            return false;
        }
        
        $notification = Notification::create($title, $body);
        
        $message = CloudMessage::new()
            ->withNotification($notification)
            ->withData($data);
        
        // Send to multiple tokens (up to 500)
        $chunks = array_chunk($tokens, 500);
        $results = [];
        
        foreach ($chunks as $chunk) {
            try {
                $results[] = $this->messaging->sendMulticast($message, $chunk);
            } catch (\Exception $e) {
                // Log error
                \Log::error('FCM Send Error: ' . $e->getMessage());
            }
        }
        
        return $results;
    }
}

