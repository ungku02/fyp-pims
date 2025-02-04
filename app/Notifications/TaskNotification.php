<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class TaskNotification extends Notification
{
    use Queueable;

    protected $user;
    protected $card;
    protected $type; // Tambah type

    public function __construct($user, $card, $type)
    {
        $this->card = $card;
        $this->user = $user;
        $this->type = $type; // Simpan type
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $logoPath = public_path('img/logo4.png'); // Ensure path is correct
        if (file_exists($logoPath)) {
            $logoBase64 = base64_encode(file_get_contents($logoPath));
            $logoSrc = 'data:image/png;base64,' . $logoBase64;
        } else {
            $logoSrc = ''; // Provide a fallback if the file is missing
        }

        return (new MailMessage)
            ->subject($this->type === 'new_task' ? 'New Task Assigned' : ($this->type === 'overdue' ? 'Task Overdue Alert' : ($this->type === 'swap_request' ? 'Task Swap Request' : 'Task Swap Notification')))
            ->view('emails.task-email', [
                'user' => $this->user,
                'card' => $this->card,
                'type' => $this->type,
                'logoSrc' => $logoSrc, // Send Base64 to Blade
            ]);
    }

    public function toArray($notifiable)
    {
        return [
            'message' => $this->type === 'new_task' 
                ? "You have new assigned task: " . $this->card->title . "\nLet's get started!"
                : ($this->type === 'overdue' 
                    ? "Your task is overdue: " . $this->card->title . " (" . Carbon::parse($this->card->due_date)->format('d-m-Y') . ")\nPlease complete it as soon as possible!"
                    : ($this->type === 'swap_request'
                        ? "You have a new task swap request for task: " . $this->card->title
                        : "Task swap request: " . $this->card->title . " has been " . ($this->type === 'swap_accepted' ? "accepted" : "rejected"))),
            'task_id' => $this->card->id,
            'user_id' => $this->user->id,
            'type' => $this->type, // Simpan type dalam database
        ];
    }   
}
