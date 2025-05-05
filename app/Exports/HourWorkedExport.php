<?php 
namespace App\Exports;

use App\Traits\TimeConverterTrait;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\View\View;

class HourWorkedExport implements FromView, WithHeadings
{
    use TimeConverterTrait;

    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('exports.hourworked', $this->data);
    }

    public function headings(): array
    {
        return [
            'Fecha',
            'Horas Normales',
            'Horas Extras',
            'Horas Nocturnas',
            'Horas Festivas',
        ];
    }
}
