<?php

namespace App\Services;

use CodeIgniter\Email\Email;
use Config\Email as EmailConfig;

class EmailService
{
    protected $email;

    public function __construct()
    {
        $this->email = new Email();
        $this->initializeEmail();
    }

    private function initializeEmail()
    {
        $config = new EmailConfig();
        
        $this->email->initialize([
            'protocol' => 'smtp',
            'SMTPHost' => getenv('SMTP_HOST'),
            'SMTPUser' => getenv('SMTP_USER'),
            'SMTPPass' => getenv('SMTP_PASS'),
            'SMTPPort' => getenv('SMTP_PORT'),
            'SMTPCrypto' => 'tls',
            'mailType' => 'html',
            'charset' => 'utf-8',
            'wordWrap' => true,
            'validate' => true,
        ]);
    }

    public function sendBookingConfirmation($booking, $user)
    {
        $subject = 'Booking Confirmation - ' . $booking['title'];
        $message = view('emails/booking_confirmation', [
            'booking' => $booking,
            'user' => $user
        ]);

        return $this->sendEmail($user['email'], $subject, $message);
    }

    public function sendBookingStatusUpdate($booking, $user)
    {
        $subject = 'Booking Status Update - ' . $booking['title'];
        $message = view('emails/booking_status_update', [
            'booking' => $booking,
            'user' => $user
        ]);

        return $this->sendEmail($user['email'], $subject, $message);
    }

    public function sendBookingReminder($booking, $user)
    {
        $subject = 'Booking Reminder - ' . $booking['title'];
        $message = view('emails/booking_reminder', [
            'booking' => $booking,
            'user' => $user
        ]);

        return $this->sendEmail($user['email'], $subject, $message);
    }

    private function sendEmail($to, $subject, $message)
    {
        $this->email->setFrom(getenv('MAIL_FROM'), getenv('MAIL_FROM_NAME'));
        $this->email->setTo($to);
        $this->email->setSubject($subject);
        $this->email->setMessage($message);

        if (!$this->email->send()) {
            log_message('error', 'Failed to send email: ' . $this->email->printDebugger(['headers']));
            return false;
        }

        return true;
    }
}
