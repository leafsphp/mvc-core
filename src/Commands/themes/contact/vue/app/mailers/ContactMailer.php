<?php

namespace App\Mailers;

class ContactMailer
{
    /**
     * A message submitted through the contact form,
     * delivered to your team inbox (CONTACT_EMAIL in .env)
     */
    public static function message(array $data)
    {
        return mailer()->create([
            'subject' => "New contact form message from {$data['name']}",
            'body' => "{$data['message']}\n\n---\nFrom: {$data['name']} <{$data['email']}>",
            'recipientEmail' => _env('CONTACT_EMAIL', _env('MAIL_SENDER_EMAIL')),
            'recipientName' => _env('APP_NAME', 'Team'),
            'replyToEmail' => $data['email'],
            'replyToName' => $data['name'],
        ]);
    }
}
