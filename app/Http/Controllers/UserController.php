<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Schema;

class UserController extends Controller
{
    // Daftar Cabang Resmi Maison Feerie
    private $branches = [
        'HOTNG' => 'HEAD OFFICE TANGERANG',
        'MFLW'  => 'MAISON FEERIE LIVING WORLD',
        'MFBX'  => 'MAISON FEERIE BINTARO EXCHANGE',
        'MFCP'  => 'MAISON FEERIE CENTRAL PARK',
        'MFLMN' => 'MAISON FEERIE LIPPO MALL NUSANTARA',
        'MFWCT' => 'MAISON FEERIE WORLD CAPITAL TOWER',
        'MFBDK' => 'MAISON FEERIE BIDAKARA 2',
        'MFKCH' => 'MAISON FEERIE KERETA CEPAT HALIM',
        'MFPMB' => 'MAISON FEERIE PAKUWON MALL BEKASI',
        'MFSMB' => 'MAISON FEERIE SUMMARECON MALL BEKASI',
    ];

    // Method/fungsi index untuk menampilkan daftar user (dengan dukungan filter Branch)
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('branch')) {
            $query->where('branch_code', $request->branch);
        }

        $users = $query->latest()->paginate(10);
        $branches = $this->branches;

        return view('users.index', compact('users', 'branches'));
    }

    public function create()
    {
        $branches = $this->branches;
        $roles = ['ADMIN', 'IT', 'MAINTENANCE', 'OUTLET'];

        return view('users.create', compact('branches', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'username'    => 'required|string|unique:users,username',
            'password'    => 'required|string|min:6',
            'role'        => 'required|in:ADMIN,IT,MAINTENANCE,OUTLET',
            'branch_code' => 'required|string',
        ]);

        User::create([
            'name'        => $request->name,
            'username'    => $request->username,
            'password'    => Hash::make($request->password),
            'role'        => $request->role,
            'branch_code' => $request->branch_code,
            'branch_name' => $this->branches[$request->branch_code] ?? $request->branch_code,
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $branches = $this->branches;
        $roles = ['ADMIN', 'IT', 'MAINTENANCE', 'OUTLET'];

        return view('users.edit', compact('user', 'branches', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'        => 'required|string|max:255',
            'username'    => ['required', 'string', Rule::unique('users', 'username')->ignore($user->id)],
            'role'        => 'required|in:ADMIN,IT,MAINTENANCE,OUTLET',
            'branch_code' => 'required|string',
            'password'    => 'nullable|string|min:6',
        ]);

        $data = [
            'name'        => $request->name,
            'username'    => $request->username,
            'role'        => $request->role,
            'branch_code' => $request->branch_code,
            'branch_name' => $this->branches[$request->branch_code] ?? $request->branch_code,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'Data user berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Mencegah user menghapus akun sendiri yang sedang dipakai login
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }

    // Menampilkan Tampilan Matrix & Form Checkbox Hak Akses Menu
    public function roles(Request $request)
    {
        $users = User::all();
        $roles = ['ADMIN', 'IT', 'MAINTENANCE', 'OUTLET'];
        
        // Ambil seluruh permission dan kelompokkan berdasarkan kategori
        $permissions = class_exists(Permission::class) 
            ? Permission::all()->groupBy('category') 
            : collect();
        
        // Ambil permission_id yang dimiliki oleh masing-masing role
        $rolePermissions = [];
        if (Schema::hasTable('role_has_permissions')) {
            $rolePermissions = DB::table('role_has_permissions')
                ->get()
                ->groupBy('role')
                ->map(function ($items) {
                    return $items->pluck('permission_id')->toArray();
                })
                ->toArray();
        }

        return view('users.roles', compact('users', 'roles', 'permissions', 'rolePermissions'));
    }

    // Menyimpan/Memperbarui Pilihan Checkbox Menu per Role
    public function updateRolePermissions(Request $request)
    {
        $request->validate([
            'role'        => 'required|string',
            'permissions' => 'nullable|array',
        ]);

        $role = $request->input('role');
        $permissions = $request->input('permissions', []);

        try {
            DB::beginTransaction();

            // 1. Hapus semua permission lama untuk role terkait
            DB::table('role_has_permissions')->where('role', $role)->delete();

            // 2. Jika ada permission yang dicentang, proses dan simpan
            if (!empty($permissions)) {
                $dataToInsert = [];

                foreach ($permissions as $perm) {
                    $permissionId = null;

                    // Jika input berupa angka ID (misal: 1, 2, 3)
                    if (is_numeric($perm)) {
                        $permissionId = (int) $perm;
                    } else {
                        // Jika input berupa nama string (misal: 'users.index'), cari atau buat ID-nya
                        $permissionId = DB::table('permissions')->where('name', $perm)->value('id');

                        if (!$permissionId) {
                            $permissionId = DB::table('permissions')->insertGetId([
                                'name'       => $perm,
                                'guard_name' => 'web',
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }

                    if ($permissionId) {
                        $dataToInsert[] = [
                            'role'          => $role,
                            'permission_id' => $permissionId,
                        ];
                    }
                }

                if (!empty($dataToInsert)) {
                    DB::table('role_has_permissions')->insert($dataToInsert);
                }
            }

            DB::commit();

            return redirect()->back()->with('success', "Hak akses menu untuk role {$role} berhasil diperbarui!");
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Gagal memperbarui hak akses: ' . $e->getMessage());
        }
    }
}