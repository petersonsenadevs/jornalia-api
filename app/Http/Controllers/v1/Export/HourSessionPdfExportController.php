<?php

namespace App\Http\Controllers\v1\Export;

use App\Exceptions\PdfGenerateException;
use App\Exceptions\SalaryNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\ExportRequest;
use App\Services\Export\PdfExportService;
use Illuminate\Http\Exceptions\HttpResponseException;

class HourSessionPdfExportController extends Controller
{
    public function __construct(
        private PdfExportService $pdfExportService
    ) {}

    public function __invoke(ExportRequest $request)
    {
      
            $month = $request->query('month');
            $year = $request->query('year');
            $sendEmail = filter_var($request->query('send_email', false), FILTER_VALIDATE_BOOLEAN);

            try {

            $result = $this->pdfExportService->generatePdf(
                $request->user()->employee,
                $month,
                $year,
                $sendEmail
            );

            return response($result['content'])
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $result['filename'] . '"');

        } catch (SalaryNotFoundException| PdfGenerateException | \Exception $e) {
            throw new HttpResponseException(response()->json(['error' => $e->getMessage()],$e->getCode()));
        }
    }
}