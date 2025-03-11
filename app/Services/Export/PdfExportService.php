<?php

namespace App\Services\Export;

use App\Jobs\SendHourWorkedReportJob;
use App\Models\Employee;
use App\Models\HourSession;
use Illuminate\Support\Facades\Http;

class PdfExportService
{
    /**
     * Genera y devuelve el PDF de horas trabajadas
     */
    public function generatePdf(Employee $employee, int $month, int $year, bool $sendEmail = false): array
    {
        // Aumentar el límite de memoria si es necesario
        ini_set('memory_limit', '1024M');

        $hourSessions = $this->getHourSessions($employee, $month, $year);
        $totals = $this->calculateTotals($hourSessions);
        $data = $this->prepareViewData($employee, $month, $year, $hourSessions, $totals);
        
        $pdfContent = $this->generatePdfContent($data);
        $filename = $this->generateFilename($month, $year);

        if ($sendEmail) {
            SendHourWorkedReportJob::dispatch(
                $employee,
                $pdfContent,
                $filename,
                $month,
                $year
            );
        }

        return [
            'content' => $pdfContent,
            'filename' => $filename
        ];
    }

    /**
     * Obtiene las sesiones de horas trabajadas
     */
    private function getHourSessions(Employee $employee, int $month, int $year): \Illuminate\Support\Collection
    {
        return HourSession::where('employee_id', $employee->id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->leftJoin('hours_worked', 'hour_sessions.id', '=', 'hours_worked.hour_session_id')
            ->get(['hour_sessions.date', 'hours_worked.normal_hours', 'hours_worked.overtime_hours', 'hours_worked.holiday_hours'])
            ->map(function ($hourSession) {
                return [
                    'date' => $hourSession->date,
                    'normal_hours' => $hourSession->normal_hours ?? null,
                    'overtime_hours' => $hourSession->overtime_hours ?? null,
                    'holiday_hours' => $hourSession->holiday_hours ?? null,
                ];
            });
    }

    /**
     * Calcula los totales de horas
     */
    private function calculateTotals(\Illuminate\Support\Collection $hourSessions): array
    {
        $totalNormalHours = $hourSessions->sum('normal_hours');
        $totalOvertimeHours = $hourSessions->sum('overtime_hours');
        $totalHolidayHours = $hourSessions->sum('holiday_hours');
        
        return [
            'normalHours' => $totalNormalHours,
            'overtimeHours' => $totalOvertimeHours,
            'holidayHours' => $totalHolidayHours,
            'totalHours' => $totalNormalHours + $totalOvertimeHours + $totalHolidayHours
        ];
    }

    /**
     * Prepara los datos para la vista
     */
    private function prepareViewData(Employee $employee, int $month, int $year, \Illuminate\Support\Collection $hourSessions, array $totals): array
    {
        $logoPath = public_path('storage/logo.png');
        $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));

        return [
            'month' => $month,
            'year' => $year,
            'employee' => $employee,
            'hourWorkedData' => $hourSessions->sortBy('date'),
            'logoPath' => $logoBase64,
            'totalNormalHours' => $totals['normalHours'],
            'totalOvertimeHours' => $totals['overtimeHours'],
            'totalHolidayHours' => $totals['holidayHours'],
            'totalHours' => $totals['totalHours']
        ];
    }

    /**
     * Genera el contenido del PDF usando PDFShift
     */
    private function generatePdfContent(array $data): string
    {
        $htmlContent = view('exports.hourworked_pdf', $data)->render();

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->withBasicAuth('api', env('PDFSHIFT_API_KEY'))
          ->post('https://api.pdfshift.io/v3/convert/pdf', [
            'source' => $htmlContent,
            'landscape' => false,
            'use_print' => false,
        ]);

        if (!$response->successful()) {
            throw new \Exception('Error al generar el PDF: ' . json_encode($response->json()));
        }

        return $response->body();
    }

    /**
     * Genera el nombre del archivo PDF
     */
    private function generateFilename(int $month, int $year): string
    {
        return "hour_worked_report_{$month}_{$year}.pdf";
    }
}
