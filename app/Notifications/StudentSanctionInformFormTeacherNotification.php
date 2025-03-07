<?php

namespace App\Notifications;

use App\Models\Sanction;
use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StudentSanctionInformFormTeacherNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $student;
    protected $sanction;

    /**
     * Create a new notification instance.
     *
     * @param Student $student
     * @param Sanction $sanction
     */
    public function __construct(Student $student, Sanction $sanction)
    {
        $this->student = $student;
        $this->sanction = $sanction;
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
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Notificare Nouă Sancțiune Elev')
            ->greeting('Bună ziua ' . $notifiable->name . ',')
            ->line('Vă informăm că ' . ($this->student->sex_id == Student::MALE ? 'elevul ' : 'eleva ') . $this->student->full_name . ' a primit o nouă sancțiune.')
            ->line('Detalii Sancțiune:')
            ->line('Descriere: ' . $this->sanction->long_description)
            ->line('Data Sancțiunii: ' . now()->format('d-m-Y'))
            ->line('Vă mulțumim pentru atenție!');
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
            'student_id' => $this->student->id,
            'sanction_id' => $this->sanction->id,
        ];
    }
}
