<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Exports\ReportCorrectiveExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    // Laporan Corrective khusus IT
    public function reportIt()
    {
        $tickets = Ticket::with(['asset', 'user', 'bast.technician'])
            ->where('department', 'IT')
            ->latest()
            ->get();

        return view('reports.it', compact('tickets'));
    }

    // Export Excel Laporan IT
    public function exportItExcel()
    {
        $tickets = Ticket::with(['asset', 'user', 'bast.technician'])
            ->where('department', 'IT')
            ->latest()
            ->get();

        return Excel::download(new ReportCorrectiveExport($tickets), 'Report_Corrective_IT.xlsx');
    }

    // Laporan Corrective khusus MAINTENANCE
    public function reportMaintenance()
    {
        $tickets = Ticket::with(['asset', 'user', 'bast.technician'])
            ->where('department', 'MAINTENANCE')
            ->latest()
            ->get();

        return view('reports.maintenance', compact('tickets'));
    }

    // Export Excel Laporan Maintenance
    public function exportMaintenanceExcel()
    {
        $tickets = Ticket::with(['asset', 'user', 'bast.technician'])
            ->where('department', 'MAINTENANCE')
            ->latest()
            ->get();

        return Excel::download(new ReportCorrectiveExport($tickets), 'Report_Corrective_Maintenance.xlsx');
    }
}