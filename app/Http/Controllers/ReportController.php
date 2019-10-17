<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\Exports\ProductExport;

class ReportController extends Controller
{
    public function generate($start, $end)
    {
        return Product::whereBetween('created_at', [$start, $end])->get();
    }

        public function export($start, $end) 
    {
        return (new ProductExport($start, $end))->download('reports.xlsx');
    }
}
