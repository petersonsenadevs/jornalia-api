<?php
declare(strict_types=1);
namespace App\Services\Export;

use App\Exceptions\PdfGenerateException;
use App\Jobs\SendHourWorkedReportJob;
use App\Mail\HourWorkedReport;
use App\Models\Employee;
use App\Models\HourSession;
use App\Services\Salary\FindSalaryByMonthService;
use App\Traits\TimeConverterTrait;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class PdfExportService
{
    use TimeConverterTrait;

    public function __construct(
        private readonly FindSalaryByMonthService $findSalaryByMonthService,
    ) {}

    /**
     * Genera y devuelve el PDF de horas trabajadas
     */
    public function generatePdf(Employee $employee, int $month, int $year, bool $sendEmail = false): array
    {
        try {
            // Aumentar el límite de memoria si es necesario
            ini_set('memory_limit', '1024M');

            $hourSessions = $this->getHourSessions($employee, $month, $year);
            $totals = $this->calculateTotals($hourSessions);
            $data = $this->prepareViewData($employee, $month, $year, $hourSessions, $totals);
            
            $pdfContent = $this->generatePdfContent($data);
            $filename = $this->generateFilename($month, $year);

            // Guardar el PDF temporalmente
            Storage::makeDirectory('temp');
            Storage::disk('local')->put("temp/{$filename}", $pdfContent);

            Log::info('PDF report generated and saved', [
                'employee_id' => $employee->id,
                'filename' => $filename
            ]);

                try {
                    // Enviar el correo electrónico
                    SendHourWorkedReportJob::dispatch(
                        $employee,
                        $filename,
                        $month,
                        $year
                    );

                    Log::info('PDF report email job dispatched', [
                        'employee_id' => $employee->id,
                        'email' => $employee->user->email,
                        'month' => $month,
                        'year' => $year
                    ]);

                } catch (\Exception $e) {
                    Log::error('Error queueing PDF report email', [
                        'employee_id' => $employee->id,
                        'email' => $employee->user->email,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    
                    // Asegurarse de limpiar el archivo temporal en caso de error
                    Storage::disk('local')->delete("temp/{$filename}");
                    throw $e;
                }
            

            return [
                'content' => $pdfContent,
                'filename' => $filename
            ];
        } catch (PdfGenerateException $e) {
            Log::error('Error generating PDF report', [
                'employee_id' => $employee->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new PdfGenerateException();
        }
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
            ->get(['hour_sessions.date', 'hours_worked.normal_hours', 'hours_worked.overtime_hours','hours_worked.night_hours', 'hours_worked.holiday_hours'])
            ->map(function ($hourSession) {
                return [
                    'date' => $hourSession->date,
                    'normal_hours' => $hourSession->normal_hours ?? null,
                    'overtime_hours' => $hourSession->overtime_hours ?? null,
                    'holiday_hours' => $hourSession->holiday_hours ?? null,
                    'night_hours' => $hourSession->night_hours ?? null,
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
        $totalNightHours = $hourSessions->sum('night_hours');
        $totalHours = $totalNormalHours + $totalOvertimeHours + $totalHolidayHours + $totalNightHours;

        return [
            'normalHours' => $this->convertDecimalToHoursAndMinutes($totalNormalHours),
            'overtimeHours' => $this->convertDecimalToHoursAndMinutes($totalOvertimeHours),
            'holidayHours' => $this->convertDecimalToHoursAndMinutes($totalHolidayHours),
            'nightHours' => $this->convertDecimalToHoursAndMinutes($totalNightHours),
            'totalHours' => $this->convertDecimalToHoursAndMinutes($totalHours),
            'decimalNormalHours' => $totalNormalHours,
            'decimalOvertimeHours' => $totalOvertimeHours,
            'decimalHolidayHours' => $totalHolidayHours,
            'decimalNightHours' => $totalNightHours,
            'decimalTotalHours' => $totalHours
        ];
    }

    /**
     * Prepara los datos para la vista
     */
    private function prepareViewData(Employee $employee, int $month, int $year, \Illuminate\Support\Collection $hourSessions, array $totals): array
    {
    
            $logoPath = Storage::disk('public')->path('jorn.png');
            if (!file_exists($logoPath)) {
                Log::warning('Logo file not found', ['path' => $logoPath]);
                $logoBase64 = null;
            }
            
            if (filesize($logoPath) > 0) {
                $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
            }

            // Obtener información del salario
            $salary = $this->findSalaryByMonthService->execute(
                $employee->id,
                str_pad((string)$month, 2, '0', STR_PAD_LEFT),
                (string)$year
            );

            // Convertir las horas de cada sesión a formato horas:minutos
            $formattedHourSessions = $hourSessions->map(function ($session) {
                return [
                    'date' => $session['date'],
                    'normal_hours' => $session['normal_hours'] ? $this->convertDecimalToHoursAndMinutes((float)$session['normal_hours']) : null,
                    'overtime_hours' => $session['overtime_hours'] ? $this->convertDecimalToHoursAndMinutes((float)$session['overtime_hours']) : null,
                    'holiday_hours' => $session['holiday_hours'] ? $this->convertDecimalToHoursAndMinutes((float)$session['holiday_hours']) : null,
                    'night_hours' => $session['night_hours'] ? $this->convertDecimalToHoursAndMinutes((float)$session['night_hours']) : null,
                ];
            });

            return [
                'month' => $month,
                'year' => $year,
                'employee' => $employee,
                'hourWorkedData' => $formattedHourSessions->sortBy('date'),
                'logoPath' => $logoBase64,
                'totalNormalHours' => $totals['normalHours'],
                'totalOvertimeHours' => $totals['overtimeHours'],
                'totalNightHours' => $totals['nightHours'],
                'totalHolidayHours' => $totals['holidayHours'],
                'totalHours' => $totals['totalHours'],
                'decimalTotals' => [
                    'normalHours' => $totals['decimalNormalHours'],
                    'overtimeHours' => $totals['decimalOvertimeHours'],
                    'holidayHours' => $totals['decimalHolidayHours'],
                    'nightHours' => $totals['decimalNightHours'],
                    'totalHours' => $totals['decimalTotalHours']
                ],
                'salary' => $salary
            ];
            
        } 
    

    /**
     * Genera el contenido del PDF usando PDFShift
     */
    private function generatePdfContent(array $data): string
    {
        $htmlContent = view('exports.hourworked_pdf', $data)->render();
        $pdfshiftApiKey = 'sk_b2a85309c894df62be2638beb3abb7bd1ef644a6';
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->withBasicAuth('api', $pdfshiftApiKey)
          ->post('https://api.pdfshift.io/v3/convert/pdf', [
            'source' => $htmlContent,
            'landscape' => false,
            'use_print' => false,
            'format' => 'A4',
            'margin' => '2cm',
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
