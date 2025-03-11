<?php

namespace App\Http\Controllers\v1\Export;

use App\Http\Controllers\Controller;
use App\Services\Export\PdfExportService;
use Illuminate\Http\Request;

class PdfController extends Controller
{
    public function __construct(
        private PdfExportService $pdfExportService
    ) {}

    public function __invoke(Request $request)
    {
        try {
            $month = $request->query('month');
            $year = $request->query('year');
            $sendEmail = $request->query('send_email', false);

            $result = $this->pdfExportService->generatePdf(
                $request->user()->employee,
                $month,
                $year,
                $sendEmail
            );

            return response($result['content'])
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $result['filename'] . '"');

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al generar el PDF',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}