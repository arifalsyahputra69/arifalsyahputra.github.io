<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Exports\SalesExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    /**
     * Export penjualan ke Excel.
     */
    public function export(Request $request)
    {
        $type = $request->query('type', 'daily');

        // Default harian = hari ini
        if ($type === 'daily') {
            $start = now()->format('Y-m-d');
            $end = null;
            $fileName = "penjualan_harian_{$start}.xlsx";
        }
        // Bulanan = bulan sekarang
        elseif ($type === 'monthly') {
            $start = now()->startOfMonth()->format('Y-m-d');
            $end = now()->endOfMonth()->format('Y-m-d');
            $fileName = "penjualan_bulanan_" . now()->format('Y_m') . ".xlsx";
        } else {
            $start = $request->query('start');
            $end = $request->query('end');
            $fileName = "penjualan.xlsx";
        }

        return Excel::download(new SalesExport($type, $start, $end), $fileName);
    }
}