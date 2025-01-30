<?php 
namespace App\Exports;

use App\Models\Employee;
use App\Models\HourSession;
use App\Models\HourWorked;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\View\View;

class HourWorkedExport implements FromView, WithHeadings
{
    protected $month;
    protected $year;
    protected $employee;

    public function __construct($month, $year, Employee $employee)
    {
        $this->month = $month;
        $this->year = $year;
        $this->employee = $employee;
    }

    public function view(): View
    {
        // Consulta para obtener las horas trabajadas
        $hourSessions = HourSession::where('employee_id', $this->employee->id)
            ->whereMonth('date', $this->month)
            ->whereYear('date', $this->year)
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

        // Renderiza la vista Blade y pasa los datos
        return view('exports.hourworked', [
            'month' => $this->month,
            'year' => $this->year,
            'employee' => $this->employee,
            'hourWorkedData' => $hourSessions
        ]);
    }

    public function headings(): array
    {
        return [
            'Date',
            'Normal Hours',
            'Overtime Hours',
            'Holiday Hours',
        ];
    }
}
