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

        $query = User::query();

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
            ->back()
            ->with('success', 'New user added successfully.');
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'string', Rule::in(User::normalAdminRoles())],
        ]);

        $user->update([
            'role' => $validated['role'],
        ]);

        return redirect()
            ->back()
            ->with('success', 'User role updated successfully.');
    }

    public function edit(User $user): View
    {
        $availableRoles = User::normalAdminRoleLabels();
        return view('admin.users.edit', compact('user', 'availableRoles'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', Rule::unique('users', 'username')->ignore($user->id)],
            'role' => ['required', 'string', Rule::in(User::normalAdminRoles())],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        $data = ['name' => $validated['name'], 'username' => $validated['username'], 'role' => $validated['role']];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()
            ->back()
            ->with('success', 'User data updated successfully.');
    }

    public function deactivate(User $user): RedirectResponse
    {
        $user->update(['status' => 'inactive']);

        return redirect()
            ->back()
            ->with('success', 'User account deactivated successfully.');
    }

    public function activate(User $user): RedirectResponse
    {
        $user->update(['status' => 'active']);

        return redirect()
            ->back()
            ->with('success', 'User account activated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()
            ->back()
            ->with('success', 'User deleted successfully.');
    }
}
