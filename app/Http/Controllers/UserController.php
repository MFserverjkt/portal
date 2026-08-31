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
        'HOSBY'  => 'HEAD OFFICE SURABAYA',
        'HOTNG'  => 'HEAD OFFICE TANGERANG',
        'MFBDK'  => 'Maison Feerie - Bidakara',
        'MFBX2'  => 'Maison Feerie - Bintaro Xchange Mall 2',
        'MFCPM'  => 'Maison Feerie - Central Park Mall',
        'MFGM3'  => 'Maison Feerie - Galaxy Mall 3',
        'MFHDH'  => 'Maison Feerie - Hokky Fruit Darmo Harapan',
        'MFHGF'  => 'Maison Feerie - Hokky Fruit Graha Family',
        'MFHMR'  => 'Maison Feerie - Hokky Fruit Merr',
        'MFKCH'  => 'Maison Feerie - KCIC Halim',
        'MFLMN'  => 'Maison Feerie - Lippo Mall Nusantara',
        'MFLPS'  => 'Maison Feerie - Lippo Plaza Sidoarjo',
        'MFLWS'  => 'Maison Feerie - Living World Alam Sutera',
        'MFPCM'  => 'Maison Feerie - Pakuwon City Mall',
        'MFPWM'  => 'Maison Feerie - Pakuwon Mall',
        'MFPMB'  => 'Maison Feerie - Pakuwon Mall Bekasi',
        'MFSBY'  => 'Maison Feerie - Siloam Surabaya',
        'MFSMB'  => 'Maison Feerie - Summarecon Mall Bekasi',
        'MFSPI'  => 'Maison Feerie - Supermall Pakuwon Indah',
        'MFTPZ'  => 'Maison Feerie - Tunjungan Plaza',
        'MFWCT'  => 'Maison Feerie - World Capital Tower',
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
        
        // Ambil daftar role dinamis dari DB, fallback jika tabel belum ada
        $roles = Schema::hasTable('roles') 
            ? DB::table('roles')->pluck('name')->toArray() 
            : ['ADMIN', 'IT', 'MAINTENANCE', 'OUTLET', 'HC', 'ASSET'];

        return view('users.create', compact('branches', 'roles'));
    }

    public function store(Request $request)
    {
        $existingRoles = Schema::hasTable('roles') 
            ? DB::table('roles')->pluck('name')->toArray() 
            : ['ADMIN', 'IT', 'MAINTENANCE', 'OUTLET', 'HC', 'ASSET'];

        $request->validate([
            'name'        => 'required|string|max:255',
            'username'    => 'required|string|unique:users,username',
            'password'    => 'required|string|min:6',
            'role'        => ['required', Rule::in($existingRoles)],
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
        
        $roles = Schema::hasTable('roles') 
            ? DB::table('roles')->pluck('name')->toArray() 
            : ['ADMIN', 'IT', 'MAINTENANCE', 'OUTLET', 'HC', 'ASSET'];

        return view('users.edit', compact('user', 'branches', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $existingRoles = Schema::hasTable('roles') 
            ? DB::table('roles')->pluck('name')->toArray() 
            : ['ADMIN', 'IT', 'MAINTENANCE', 'OUTLET', 'HC', 'ASSET'];

        $request->validate([
            'name'        => 'required|string|max:255',
            'username'    => ['required', 'string', Rule::unique('users', 'username')->ignore($user->id)],
            'role'        => ['required', Rule::in($existingRoles)],
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
        
        // Ambil list role secara dinamis dari tabel roles
        $roles = Schema::hasTable('roles') 
            ? DB::table('roles')->pluck('name')->toArray() 
            : ['ADMIN', 'IT', 'MAINTENANCE', 'OUTLET', 'HC', 'ASSET'];
        
        // Ambil seluruh permission
        $permissions = class_exists(Permission::class) 
            ? Permission::all()->groupBy('category') 
            : collect();
        
        // Ambil permission_id per nama role dengan mapping role_id -> roles.name
        $rolePermissions = [];
        if (Schema::hasTable('role_has_permissions') && Schema::hasTable('roles')) {
            $records = DB::table('role_has_permissions')
                ->join('roles', 'role_has_permissions.role_id', '=', 'roles.id')
                ->select('roles.name as role_name', 'role_has_permissions.permission_id')
                ->get();

            foreach ($records as $row) {
                $rolePermissions[$row->role_name][] = $row->permission_id;
            }
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

        $roleName = $request->input('role');
        $permissions = $request->input('permissions', []);

        try {
            DB::beginTransaction();

            // 1. Ambil atau buat record role di tabel 'roles' untuk mendapatkan role_id
            $roleRecord = DB::table('roles')->where('name', $roleName)->first();

            if (!$roleRecord) {
                $roleId = DB::table('roles')->insertGetId([
                    'name'       => $roleName,
                    'guard_name' => 'web',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $roleId = $roleRecord->id;
            }

            // 2. Hapus semua permission lama menggunakan 'role_id'
            DB::table('role_has_permissions')->where('role_id', $roleId)->delete();

            // 3. Simpan permission baru jika ada yang dicentang
            if (!empty($permissions)) {
                $dataToInsert = [];

                foreach ($permissions as $perm) {
                    $permissionId = null;

                    if (is_numeric($perm)) {
                        $permissionId = (int) $perm;
                    } else {
                        // Cari atau buat permission baru jika belum ada
                        $permissionRecord = DB::table('permissions')->where('name', $perm)->first();
                        
                        if ($permissionRecord) {
                            $permissionId = $permissionRecord->id;
                        } else {
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
                            'permission_id' => $permissionId,
                            'role_id'       => $roleId,
                        ];
                    }
                }

                if (!empty($dataToInsert)) {
                    DB::table('role_has_permissions')->insert($dataToInsert);
                }
            }

            DB::commit();

            return redirect()->back()->with('success', "Hak akses menu untuk role {$roleName} berhasil diperbarui!");
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Gagal memperbarui hak akses: ' . $e->getMessage());
        }
    }

    /**
     * Menyimpan Role Baru dari Modal View
     */
    public function storeRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:roles,name',
        ]);

        $roleName = strtoupper(trim($request->name));

        // 1. Simpan ke tabel roles
        DB::table('roles')->insert([
            'name'       => $roleName,
            'guard_name' => 'web',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Update definisi ENUM kolom role pada tabel users agar role baru valid di DB
        if (Schema::hasColumn('users', 'role')) {
            $existingRoles = DB::table('roles')->pluck('name')->toArray();
            if (!empty($existingRoles)) {
                $enumList = "'" . implode("','", $existingRoles) . "'";
                DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM({$enumList}) NOT NULL DEFAULT 'OUTLET'");
            }
        }

        return redirect()->back()->with('success', "Role {$roleName} berhasil ditambahkan!");
    }
}