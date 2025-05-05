<?php 
declare(strict_types=1);
namespace App\Services\ExportCsvService;

use App\Exceptions\SalaryNotFoundException;
use App\Jobs\SendHourWorkedReportJob;
use App\Models\Employee;
use App\Models\HourSession;
use App\Services\Salary\FindSalaryByMonthService;
use App\Traits\TimeConverterTrait;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\HourWorkedExport;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportCsvService
{
    use TimeConverterTrait;

    public function __construct(
        private readonly FindSalaryByMonthService $findSalaryByMonthService,
    ) {}

    public function exportCsv(Employee $employee, int $month, int $year): BinaryFileResponse
    {
        try {
            ini_set('memory_limit', '1024M');

            $hourSessions = $this->getHourSessions($employee, $month, $year);
            $totals = $this->calculateTotals($hourSessions);
            $filename = $this->generateFilename($month, $year);

            // Preparar los datos para la vista Excel
            $data = $this->prepareViewData($employee, $month, $year, $hourSessions, $totals);
            
            // Crear el directorio temporal si no existe
            Storage::makeDirectory('temp');
            
            // Generar el Excel y guardarlo temporalmente
            Excel::store(new HourWorkedExport($data), "temp/{$filename}", 'local');

            Log::info('Excel report generated and saved', [
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

                Log::info('Excel report email job dispatched', [
                    'employee_id' => $employee->id,
                    'email' => $employee->user->email,
                    'month' => $month,
                    'year' => $year
                ]);

            } catch (\Exception $e) {
                Log::error('Error queueing Excel report email', [
                    'employee_id' => $employee->id,
                    'email' => $employee->user->email,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                // Limpiar archivo temporal en caso de error
                Storage::disk('local')->delete("temp/{$filename}");
                throw $e;
            }

            return Excel::download(new HourWorkedExport($data), $filename);

        } catch (\Exception $e) {
            Log::error('Error generating Excel report', [
                'employee_id' => $employee->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    private function getHourSessions(Employee $employee, int $month, int $year): Collection
    {
        return HourSession::where('employee_id', $employee->id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->leftJoin('hours_worked', 'hour_sessions.id', '=', 'hours_worked.hour_session_id')
            ->get(['hour_sessions.date', 'hours_worked.normal_hours', 'hours_worked.overtime_hours','hours_worked.night_hours', 'hours_worked.holiday_hours'])
            ->map(function ($hourSession) {
                return [
                    'date' => $hourSession->date,
                    'normal_hours' => (float)($hourSession->normal_hours ?? 0),
                    'overtime_hours' => (float)($hourSession->overtime_hours ?? 0),
                    'holiday_hours' => (float)($hourSession->holiday_hours ?? 0),
                    'night_hours' => (float)($hourSession->night_hours ?? 0),
                ];
            });
    }

    private function calculateTotals(Collection $hourSessions): array
    {
        $totalNormalHours = (float)$hourSessions->sum('normal_hours');
        $totalOvertimeHours = (float)$hourSessions->sum('overtime_hours');
        $totalHolidayHours = (float)$hourSessions->sum('holiday_hours');
        $totalNightHours = (float)$hourSessions->sum('night_hours');
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

    private function prepareViewData(Employee $employee, int $month, int $year,Collection $hourSessions, array $totals): array
    {
        try {
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
                    'normal_hours' => $session['normal_hours'] > 0 ? $this->convertDecimalToHoursAndMinutes((float)$session['normal_hours']) : null,
                    'overtime_hours' => $session['overtime_hours'] > 0 ? $this->convertDecimalToHoursAndMinutes((float)$session['overtime_hours']) : null,
                    'holiday_hours' => $session['holiday_hours'] > 0 ? $this->convertDecimalToHoursAndMinutes((float)$session['holiday_hours']) : null,
                    'night_hours' => $session['night_hours'] > 0 ? $this->convertDecimalToHoursAndMinutes((float)$session['night_hours']) : null,
                ];
            });

            return [
                'month' => $month,
                'year' => $year,
                'employee' => $employee,
                'hourWorkedData' => $formattedHourSessions->sortBy('date'),
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
        } catch (\Exception $e) {
            Log::error('Error preparing view data', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    private function generateFilename(int $month, int $year): string
    {
        return "hour_worked_report_{$month}_{$year}.xlsx";
    }
}