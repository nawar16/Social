<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\User;

class UserFollowed extends Notification implements ShouldQueue
{
    use Queueable;

    protected $follower;
    
    public function __construct(User $follower)
    {
        $this->follower = $follower;
    }

    
    public function via($notifiable)
    {
        return ['database','broadcast'];
    }
 
    //for broadcast
    public function toArray($notifiable)
    {
        return [
            'id' => $this->id,
            'read_at' => null,
            'data' => [
                'follower_id' => $this->follower->id,
                'follower_name' => $this->follower->name,
            ],
        ];
    }

    /**
     * Get the DB representation of the notification.
    */
    public function toDatabase($notifiable)
    {
        return [
            'follower_id' => $this->follower->id,
            'follower_name' => $this->follower->name,
        ];
    }
}
