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
        $query = Ticket::with(['asset', 'user', 'bast', 'bast.technician'])
            ->where('department', $department) // Disesuaikan: Hanya mengecek kolom 'department'
            ->latest();

        // Terapkan filter branch jika dipilih (Mengecek di tabel tickets & relasi user)
        if (!empty($branch)) {
            $query->where(function ($q) use ($branch) {
                // 1. Cek dari kolom branch_code atau branch_name pada tiket
                $q->where('branch_code', $branch)
                  ->orWhere('branch_name', $branch)
                  // 2. Fallback: Cek dari branch_code akun pelapor
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
        // Ambil daftar branch unik dari tiket
        $ticketBranches = Ticket::whereNotNull('branch_code')
            ->pluck('branch_code')
            ->merge(Ticket::whereNotNull('branch_name')->pluck('branch_name'));
        
        // Ambil daftar branch unik dari data users
        $userBranches = User::whereNotNull('branch_code')->pluck('branch_code');

        // Penggabungan, pembersihan, dan pengurutan daftar branch
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

    // Laporan Corrective khusus IT
    public function reportIt(Request $request)
    {
        $selectedBranch = $request->input('branch');
        $tickets = $this->getTicketsByDepartmentAndBranch('IT', $selectedBranch);
        $branches = $this->getBranchList();

        return view('reports.it', compact('tickets', 'branches'));
    }

    // Export Excel Laporan IT (Mendukung Filter Branch)
    public function exportItExcel(Request $request)
    {
        $selectedBranch = $request->input('branch');
        
        // Mengirimkan filter branch dan department ('IT') ke ReportCorrectiveExport
        $fileName = 'Report_Corrective_IT' . ($selectedBranch ? '_' . $selectedBranch : '') . '_' . date('Ymd_His') . '.xlsx';

        return Excel::download(new ReportCorrectiveExport($selectedBranch, 'IT'), $fileName);
    }

    // ------------------------------------------------------------------------
    // LAPORAN MAINTENANCE
    // ------------------------------------------------------------------------

    // Laporan Corrective khusus MAINTENANCE
    public function reportMaintenance(Request $request)
    {
        $selectedBranch = $request->input('branch');
        $tickets = $this->getTicketsByDepartmentAndBranch('MAINTENANCE', $selectedBranch);
        $branches = $this->getBranchList();

        return view('reports.maintenance', compact('tickets', 'branches'));
    }

    // Export Excel Laporan Maintenance (Mendukung Filter Branch)
    public function exportMaintenanceExcel(Request $request)
    {
        $selectedBranch = $request->input('branch');
        
        // Mengirimkan filter branch dan department ('MAINTENANCE') ke ReportCorrectiveExport
        $fileName = 'Report_Corrective_Maintenance' . ($selectedBranch ? '_' . $selectedBranch : '') . '_' . date('Ymd_His') . '.xlsx';

        return Excel::download(new ReportCorrectiveExport($selectedBranch, 'MAINTENANCE'), $fileName);
    }
}