<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LoginNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $loginTime;

    public function __construct()
    {
        $this->loginTime = now();
    }

    public function via($notifiable)
    {
        return ['mail']; // استخدام قناة البريد الإلكتروني كما مطلوب
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('تنبيه أمني: تسجيل دخول جديد لحسابك')
                    ->greeting('مرحباً ' . $notifiable->name)
                    ->line('تم تسجيل الدخول إلى حسابك بنجاح.')
                    ->line('وقت تسجيل الدخول: ' . $this->loginTime)
                    ->line('إذا لم تكن أنت من قام بهذا الإجراء، يرجى تأمين حسابك فوراً.')
                    ->salutation('مع تحيات إدارة المنصة');
    }
}