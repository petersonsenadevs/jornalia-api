<?php

namespace App\Jobs;

use App\Mail\HourWorkedReport;
use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendHourWorkedReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private Employee $employee,
        private string $pdfContent,
        private string $filename,
        private int $month,
        private int $year
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->employee->user->email)
            ->send(new HourWorkedReport(
                $this->pdfContent,
                $this->filename,
                $this->month,
                $this->year
            ));
    }
}
