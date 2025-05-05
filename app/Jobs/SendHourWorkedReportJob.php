<?php

namespace App\Jobs;

use App\Mail\HourWorkedReport;
use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SendHourWorkedReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [60, 180, 300];

    public function __construct(
        private Employee $employee,
        private string $filename,
        private int $month,
        private int $year
    ) {}

    public function handle(): void
    {
        try {
            if (!$this->employee->user || !$this->employee->user->email) {
                Log::error('SendHourWorkedReportJob: Employee has no associated user or email', [
                    'employee_id' => $this->employee->id
                ]);
                return;
            }

            $mailable = new HourWorkedReport(
                $this->filename,
                $this->month,
                $this->year
            );

            Mail::to($this->employee->user->email)->send($mailable);

            Log::info('Hour worked report sent successfully', [
                'employee_id' => $this->employee->id,
                'month' => $this->month,
                'year' => $this->year
            ]);

            // Limpiar el archivo temporal después de enviar el correo
            Storage::disk('local')->delete("temp/{$this->filename}");
            
        } catch (\Exception $e) {
            Log::error('Error sending hour worked report', [
                'employee_id' => $this->employee->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Asegurarse de limpiar el archivo temporal incluso en caso de error
            Storage::disk('local')->delete("temp/{$this->filename}");
            
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('SendHourWorkedReportJob failed', [
            'employee_id' => $this->employee->id,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
        
        // Limpiar el archivo temporal si el job falla
        Storage::disk('local')->delete("temp/{$this->filename}");
    }
}
