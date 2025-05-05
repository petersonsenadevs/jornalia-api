<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RegisterNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(private readonly User $user)
    {
        Log::info('RegisterNotification constructor', [
            'user_id' => $user->id,
            'email' => $user->email
        ]);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bienvenido a ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        $logoPath = Storage::disk('public')->path('jorn.png');
        $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));


        Log::info('Preparando contenido del email', [
            'user_id' => $this->user->id,
            'template' => 'emails.register_notification'
        ]);

        return new Content(
            view: 'emails.register_notification',
            with: [
                'name' => $this->user->employee->name ?? $this->user->email,
                'email' => $this->user->email,
                'company' => $this->user->employee->company_name ?? config('app.name'),
                'logo' => $logoBase64,
            ]
        );
    }
}