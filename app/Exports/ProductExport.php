<?php

namespace App\Exports;

use App\Product;
use Excel;
use Maatwebsite\Excel\ExcelServiceProvider;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ProductExport implements FromQuery, WithHeadings, ShouldAutoSize, WithMapping, WithColumnFormatting, WithEvents
{
    use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct($start, $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    public function query()
    {
        return Product::query()->whereBetween('created_at', [$this->start, $this->end]);
    }

    public function map($item): array
    {
        return [
            $item->id,
            $item->name,
            $item->quantity,
            $item->price
        ];
    }

    public function headings(): array
    {
        return [
            'Item Code',
            'Product Name',
            'Quantity',
            'Price'
        ];
    }

    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }

    public function registerEvents(): array
    {

        $styleArray0 = [
            'font' => [
               'bold' => true,
               'size'      =>  12
            ]
        ];

         $styleArray1 = [
             'font' => [
                'bold' => true
             ]
         ];

        $styleArray2 = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ]
        ];
          

        return [
            // Handle by a closure.

            AfterSheet::class => function (AfterSheet $event) use ($styleArray1, $styleArray2, $styleArray0) {

                $count = $event->sheet->getHighestRow() + 1;
                
                $event->sheet->insertNewRowBefore(1, 1);
                $event->sheet->insertNewColumnBefore('A', 1);

                $event->sheet->setCellValue('B1', 'Report Between '.$this->start.' and '.$this->end.'')->mergeCells('B1:E1');
                $event->sheet->getStyle('B1:E1')->applyFromArray($styleArray0);
                $event->sheet->getStyle('B1:E1')->applyFromArray($styleArray2);

                $event->sheet->getStyle('B2:E2')->applyFromArray($styleArray1)->getAlignment()->setHorizontal('center');
                $event->sheet->getStyle('B2:E'.$count.'')->applyFromArray($styleArray2);

                $event->sheet->getStyle('B1:E1')->applyFromArray($styleArray0)->getAlignment()->setHorizontal('center');

                $event->sheet->getStyle('B1:E1')->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setARGB('FCF700');
            },

        ];
    }
}
