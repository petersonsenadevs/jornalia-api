<?php

namespace App\Http\Controllers\v1\Export;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\HourSession;
use App\Models\HourWorked;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class PdfController extends Controller
{
    public function __invoke(Request $request)
    {
        // Aumentar el límite de memoria si es necesario
        ini_set('memory_limit', '1024M');
    
        // Obtener el empleado
        $employee = $request->user()->employee;
    
        $month = $request->query('month');
        $year = $request->query('year');
    
        // Obtener las horas trabajadas
        $hourSessions = HourSession::where('employee_id', $employee->id)
            ->whereMonth('date', $request->query('month'))
            ->whereYear('date', $request->query('year'))
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
            //Suma de todas las horas trabajadas
            $totalNormalHours = $hourSessions->sum('normal_hours');
            $totalOvertimeHours = $hourSessions->sum('overtime_hours');
            $totalHolidayHours = $hourSessions->sum('holiday_hours');
            $totalHours = $totalNormalHours + $totalOvertimeHours + $totalHolidayHours;
            $logoPath = public_path('storage/logo.png');
$logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
    
        // Datos para la vista
        $data = [
            'month' => $month,
            'year' => $year,
            'employee' => $employee,
            'hourWorkedData' => $hourSessions->sortBy('date'),
            'logoPath' => $logoBase64,
            'totalNormalHours' => $totalNormalHours,
            'totalOvertimeHours' => $totalOvertimeHours,
            'totalHolidayHours' => $totalHolidayHours,
            'totalHours' => $totalHours,
             // Ruta del logo
        ];
    
        // Renderizar la vista Blade a HTML
        $htmlContent = view('exports.hourworked_pdf', $data)->render();
    
        // Llamar a la API de PDFShift para generar el PDF
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->withBasicAuth('api', env('PDFSHIFT_API_KEY')) // Autenticación básica
          ->post('https://api.pdfshift.io/v3/convert/pdf', [
            'source' => $htmlContent,
            'landscape' => false,
            'use_print' => false,
        ]);
    
        // Verificar si la solicitud fue exitosa
        if ($response->successful()) {
            // Descargar el PDF
            return response($response->body())
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="hour_worked_report.pdf"');
        } else {
            // Manejar el error
            return response()->json([
                'error' => 'Error al generar el PDF',
                'details' => $response->json(), // Mostrar detalles del error en formato JSON
            ], status: $response->status());
        
        }
    }
}