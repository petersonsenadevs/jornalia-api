<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class HourWorkedReport extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        private readonly string $filename,
        private readonly int $month,
        private readonly int $year
    ) {
        // Verificar que el archivo existe antes de continuar
        if (!Storage::exists("temp/{$this->filename}")) {
            throw new \RuntimeException("El archivo temporal no existe: {$this->filename}");
        }
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Reporte de Horas Trabajadas - {$this->month}/{$this->year}"
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.hour_worked_report',
            with: [
                'month' => $this->month,
                'year' => $this->year
            ]
        );
    }

    public function attachments(): array
    {
        // Verificar que el archivo existe en el storage
        if (!Storage::exists("temp/{$this->filename}")) {
            throw new \RuntimeException("El archivo temporal no existe: {$this->filename}");
        }

        return [
            Attachment::fromStorage("temp/{$this->filename}")
                ->as($this->filename)
                ->withMime('application/pdf')
        ];
    }
}