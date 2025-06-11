<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Appointment;

class AppointmentStatusChanged extends Notification
{
    use Queueable;

    protected $appointment;
    protected $oldStatus;
    protected $newStatus;
    protected $by;

    /**
     * Create a new notification instance.
     */
    public function __construct(Appointment $appointment, $oldStatus, $newStatus, $by = null)
    {
        $this->appointment = $appointment;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->by = $by;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subject = 'Cập nhật trạng thái lịch hẹn - ' . $this->appointment->TitleAppoint;
        $byText = $this->by ? " bởi {$this->by}" : '';
        
        return (new MailMessage)
                    ->subject($subject)
                    ->greeting('Xin chào ' . $notifiable->Name . '!')
                    ->line("Trạng thái lịch hẹn của bạn đã được cập nhật{$byText}.")
                    ->line('**Tiêu đề:** ' . $this->appointment->TitleAppoint)
                    ->line('**Trạng thái cũ:** ' . $this->oldStatus)
                    ->line('**Trạng thái mới:** ' . $this->newStatus)
                    ->line('**Thời gian bắt đầu:** ' . \Carbon\Carbon::parse($this->appointment->AppointmentDateStart)->format('d/m/Y H:i'))
                    ->line('**Thời gian kết thúc:** ' . \Carbon\Carbon::parse($this->appointment->AppointmentDateEnd)->format('d/m/Y H:i'))
                    ->action('Xem chi tiết lịch hẹn', url('/appointments/' . $this->appointment->AppointmentID))
                    ->line('Cảm ơn bạn đã sử dụng dịch vụ của chúng tôi!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $byText = $this->by ? " ({$this->by})" : '';

        return [
            'appointment_id' => $this->appointment->AppointmentID,
            'title' => $this->appointment->TitleAppoint,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'property_title' => $this->appointment->property->Title ?? 'N/A',
            'appointment_date_start' => $this->appointment->AppointmentDateStart,
            'appointment_date_end' => $this->appointment->AppointmentDateEnd,
            'by' => $this->by,
            'message' => "Lịch hẹn '{$this->appointment->TitleAppoint}' đã được cập nhật từ '{$this->oldStatus}' thành '{$this->newStatus}'{$byText}"
        ];
    }
}
