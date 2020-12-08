<?php

namespace App\Events;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use App\User as User;

class MyEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    
      public $user;
      public $message;
      
      public function __construct($user,$message)
      {
          $this->user = $user;
          $this->message = $message;
      }
    
      public function broadcastOn()
      {
          return new PrivateChannel('usr_'.$this->user->id);
      }
    
      public function broadcastAs()
      {
          return 'my-event';
      }
}
