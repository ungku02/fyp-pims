<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class UserWorkspaceNotification extends Notification
{
    use Queueable;

    protected $user;
    protected $workspace;

    public function __construct($user, $workspace)
    {
        $this->user = $user;
        $this->workspace = $workspace;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('You have been added to a workspace: ' . $this->workspace->title)
                    ->action('View Profile', url('/profile/' . $this->user->id))
                    ->line('Thank you for using our application!');
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'You have been added to a workspace: ' . $this->workspace->title,
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
        ];
    }
}

