<?php 
declare(strict_types=1);
namespace App\Services\ExportCsvService;

use App\Models\Employee;
use App\Models\HourSession;
use App\Models\HourWorked;
use Maatwebsite\Excel\Facades\Excel;

class ExportCsvService
{
    public function exportCsv($month, $year, Employee $employee)
    {
        $data = HourSession::where('employee_id', $employee->id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get(['date'])
            ->map(function ($hourSession) {
                return [
                    'date' => $hourSession->date,
                    'normal_hours' => $hourSession->hourWorked->normal_hours,
                    'overtime_hours' => $hourSession->hourWorked->overtime_hours,
                    'holiday_hours' => $hourSession->hourWorked->holiday_hours,
                ];
            }) ;

        return Excel::download($data, 'hour_worked.csv');
    }
}