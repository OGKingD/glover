<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class UserCreatedNotification extends Notification
{
    use Queueable;

    private string $token;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($token)
    {
        //
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->greeting("Hi There!, ")
            ->line('This is to inform you that you have successfully being registerd.')
            ->line(new HtmlString("<h2 style='text-align: center'>YOUR TOKEN ⚠️</h2>
                                         <i><b style='color: red'> " . $this->token . "  </b></i>
                                         <br> Do not share your token with anyone! Please keep this token safe at all times 🍻 👍
                                         "))
            ->salutation(new HtmlString("<br><h3 style='color: lawngreen'>💖 With Love Maker Checker Service!💖 </h3>"));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
