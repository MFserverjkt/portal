<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use App\Exports\ReportCorrectiveExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Helper method untuk query tiket berdasarkan divisi & branch
     */
    private function getTicketsByDepartmentAndBranch(string $department, ?string $branch)
    {
        $query = Ticket::with([
                'asset', 
                'user', 
                'bast', 
                'bast.technician'
            ])
            ->where('department', $department)
            ->latest();

        // Filter Branch jika dipilih (Mengecek di tabel tickets & relasi user)
        if (!empty($branch)) {
            $query->where(function ($q) use ($branch) {
                $q->where('branch_code', $branch)
                  ->orWhere('branch_name', $branch)
                  ->orWhereHas('user', function ($userQuery) use ($branch) {
                      $userQuery->where('branch_code', $branch);
                  });
            });
        }

        return $query->get();
    }

    /**
     * Helper method untuk mengambil daftar branch unik dari database (Tickets & Users)
     */
    private function getBranchList()
    {
        $ticketBranches = Ticket::whereNotNull('branch_code')
            ->pluck('branch_code')
            ->merge(Ticket::whereNotNull('branch_name')->pluck('branch_name'));
        
        $userBranches = User::whereNotNull('branch_code')->pluck('branch_code');

        return $ticketBranches
            ->merge($userBranches)
            ->filter()
            ->unique()
            ->sort()
            ->values();
    }

    // ------------------------------------------------------------------------
    // LAPORAN IT
    // ------------------------------------------------------------------------

    public function reportIt(Request $request)
    {
        $selectedBranch = $request->input('branch');
        $tickets = $this->getTicketsByDepartmentAndBranch('IT', $selectedBranch);
        $branches = $this->getBranchList();

        return view('reports.it', compact('tickets', 'branches'));
    }

    public function exportItExcel(Request $request)
    {
        $selectedBranch = $request->input('branch');
        $fileName = 'Report_Corrective_IT' . ($selectedBranch ? '_' . $selectedBranch : '') . '_' . date('Ymd_His') . '.xlsx';

        return Excel::download(new ReportCorrectiveExport($selectedBranch, 'IT'), $fileName);
    }

    // ------------------------------------------------------------------------
    // LAPORAN MAINTENANCE
    // ------------------------------------------------------------------------

    public function reportMaintenance(Request $request)
    {
        $selectedBranch = $request->input('branch');
        $tickets = $this->getTicketsByDepartmentAndBranch('MAINTENANCE', $selectedBranch);
        $branches = $this->getBranchList();

        return view('reports.maintenance', compact('tickets', 'branches'));
    }

    public function exportMaintenanceExcel(Request $request)
    {
        $selectedBranch = $request->input('branch');
        $fileName = 'Report_Corrective_Maintenance' . ($selectedBranch ? '_' . $selectedBranch : '') . '_' . date('Ymd_His') . '.xlsx';

        return Excel::download(new ReportCorrectiveExport($selectedBranch, 'MAINTENANCE'), $fileName);
    }
}