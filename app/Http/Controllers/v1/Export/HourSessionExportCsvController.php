<?php 
declare(strict_types=1);
namespace App\Http\Controllers\v1\Export;

use App\Exceptions\SalaryNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\ExportRequest;
use App\Services\ExportCsvService\ExportCsvService;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

class HourSessionExportCsvController extends Controller
{
    public function __construct(
        private readonly ExportCsvService $exportCsvService
    ) {}

    public function __invoke(ExportRequest $request) 
    {
      
            $month = (int)$request->query('month');
            $year = (int)$request->query('year');

            try {

            return $this->exportCsvService->exportCsv(
                $request->user()->employee,
                $month,
                $year
            );

        } catch (SalaryNotFoundException| \Exception $e) {
            throw new HttpResponseException(response()->json(['error' => $e->getMessage()],$e->getCode()));
        }
    }
}