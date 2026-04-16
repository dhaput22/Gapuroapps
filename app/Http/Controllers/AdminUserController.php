<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(Request $request): View
    {
        $availableRoles = User::normalAdminRoleLabels();
        $requestedRole = (string) $request->input('role', '');
        $roleFilter = array_key_exists($requestedRole, $availableRoles) ? $requestedRole : '';

        $filters = [
            'keyword' => trim((string) $request->input('keyword', '')),
            'role' => $roleFilter,
            'page_size' => max(1, min(100, (int) $request->input('page_size', 10))),
        ];

        $query = User::query()->where('role', '!=', User::ROLE_SUPER_ADMIN);

        if ($filters['role'] !== '') {
            $query->where('role', $filters['role']);
        }

        if ($filters['keyword'] !== '') {
            $keyword = $filters['keyword'];
            $query->where(function ($builder) use ($keyword) {
                $builder->where('name', 'like', '%' . $keyword . '%')
                    ->orWhere('username', 'like', '%' . $keyword . '%');
            });
        }

        $users = $query
            ->orderByDesc('id')
            ->paginate($filters['page_size'])
            ->withQueryString();

        return view('admin.users.index', compact('users', 'filters', 'availableRoles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $normalRoles = User::normalAdminRoles();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', Rule::unique('users', 'username')],
            'role' => ['required', 'string', Rule::in($normalRoles)],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        User::query()->create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'role' => $validated['role'],
            'status' => 'active',
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Admin baru berhasil ditambahkan.');
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        if ($user->isSuperAdmin()) {
            return redirect()
                ->route('admin.users.index')
                ->withErrors(['role' => 'Role super admin tidak bisa diubah dari halaman ini.']);
        }

        $validated = $request->validate([
            'role' => ['required', 'string', Rule::in(User::normalAdminRoles())],
        ]);

        $user->update([
            'role' => $validated['role'],
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Role admin berhasil diperbarui.');
    }
}
