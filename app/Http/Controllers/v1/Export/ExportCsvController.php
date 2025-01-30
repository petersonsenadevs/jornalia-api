<?php 

namespace App\Http\Controllers\v1\Export;

use App\Exports\HourWorkedExport;
use App\Http\Controllers\Controller;
use App\Models\HourWorked;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportCsvController extends Controller
{
    public function __construct(){}
   public function __invoke(Request $request) {

  $month=  $request->query('month');
   $year= $request->query('year');
   $employee = $request->user()->employee;
$namePath = str_replace(' ', '_', $employee->id) . '_' . $month . '_' . $year . '.xlsx';
   $export = new HourWorkedExport($month, $year, $employee);
   $path = storage_path('app/public/' . $namePath);
   Excel::store($export, 'public/' . $namePath);

    return response()->download($path);

   }
}