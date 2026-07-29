<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
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

    // Laporan Corrective khusus MAINTENANCE
    public function reportMaintenance()
    {
        $tickets = Ticket::with(['asset', 'user', 'bast.technician'])
            ->where('department', 'MAINTENANCE')
            ->latest()
            ->get();

        return view('reports.maintenance', compact('tickets'));
    }
}