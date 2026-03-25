<?php

namespace App\Http\Controllers;

use App\Models\Operator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OperatorController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'keyword' => trim((string) $request->input('keyword', '')),
            'page_size' => max(1, min(100, (int) $request->input('page_size', 10))),
        ];

        $query = Operator::query();
        if ($filters['keyword'] !== '') {
            $keyword = $filters['keyword'];
            $query->where(function ($sub) use ($keyword) {
                $sub->where('employee_id', 'like', '%' . $keyword . '%')
                    ->orWhere('name', 'like', '%' . $keyword . '%')
                    ->orWhere('department', 'like', '%' . $keyword . '%');
            });
        }

        $operators = $query
            ->latest('id')
            ->paginate($filters['page_size'])
            ->withQueryString();

        return view('operators.index', compact('operators', 'filters'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'string', 'max:50', 'unique:operators,employee_id'],
            'name' => ['required', 'string', 'max:150'],
            'department' => ['required', 'string', 'max:100'],
        ]);

        Operator::query()->create($validated);

        return redirect()
            ->route('operators.index')
            ->with('success', 'Data operator berhasil ditambahkan.');
    }

    public function destroy(Operator $operator): RedirectResponse
    {
        $operator->delete();

        return redirect()
            ->route('operators.index')
            ->with('success', 'Data operator berhasil dihapus.');
    }

    public function preview(Request $request): JsonResponse
    {
        $employeeId = trim((string) $request->query('employee_id', ''));
        if ($employeeId === '') {
            return response()->json([
                'message' => 'Nomor ID operator wajib diisi.',
            ], 422);
        }

        $operator = Operator::query()->where('employee_id', $employeeId)->first();
        if (!$operator) {
            return response()->json([
                'message' => 'Nomor ID operator tidak ditemukan.',
            ], 422);
        }

        return response()->json([
            'employee_id' => $operator->employee_id,
            'name' => $operator->name,
            'department' => $operator->department,
        ]);
    }
}

