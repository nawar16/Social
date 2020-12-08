<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Post;
use App\User;

class NewPost extends Notification implements ShouldQueue
{
    use Queueable;

    protected $following;
    protected $post;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $following, Post $post)
    {
        $this->following = $following;
        $this->post = $post;
    }

    
    public function via($notifiable)
    {
        return ['database','broadcast'];
    }

    public function toArray($notifiable)
    {
        return [
            'id' => $this->id,
            'read_at' => null,
            'data' => [
                'following_id' => $this->following->id,
                'following_name' => $this->following->name,
                'post_id' => $this->post->id,
            ],
        ];
    }

    /**
     * Get the DB representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'following_id' => $this->following->id,
            'following_name' => $this->following->name,
            'post_id' => $this->post->id,
        ];
    }
}
