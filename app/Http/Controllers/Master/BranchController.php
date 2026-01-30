<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;


class BranchController extends Controller
{
    public function __consruct(AuditLogService $auditLog)
    {
        $this->auditLog = $auditLog;
    }

    public function index(Request $request)
    {
        $query = Branch::with('manager');

        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                ->orWhere('name', 'like', "%{$search}%")
                ->orWhere('city', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $branches = $query->latest()->paginate(15);

        /* ============================
        TABLE COLUMNS (KEY BASED)
        ============================ */
        $columns = [
            ['key' => 'code', 'label' => 'Code', 'type' => 'text'],
            ['key' => 'name', 'label' => 'Name', 'type' => 'text'],
            ['key' => 'city', 'label' => 'City', 'type' => 'text'],
            ['key' => 'phone', 'label' => 'Phone', 'type' => 'text'],
            ['key' => 'manager', 'label' => 'Manager', 'type' => 'text'],
            ['key' => 'status', 'label' => 'Status', 'type' => 'badge'],
        ];

        /* ============================
        FORMAT DATA FOR TABLE
        ============================ */
        $branchesData = $branches->getCollection()->map(function ($branch) {
            return [
                'id' => $branch->id,
                'code' => $branch->code,
                'name' => $branch->name,
                'city' => $branch->city ?? '-',
                'phone' => $branch->phone ?? '-',
                'manager' => $branch->manager?->name ?? '-',

                'status' => [
                    'value' => $branch->status,
                    'label' => ucfirst($branch->status),
                    'color' => match ($branch->status) {
                        'active' => 'active',
                        'inactive' => 'inactive',
                        default => 'gray',
                    }
                ],

                'actions' => [
                    'show' => route('master.branches.show', $branch),
                    'edit' => route('master.branches.edit', $branch),
                    'delete' => route('master.branches.destroy', $branch),
                ],
            ];
        })->toArray();

        $managers = User::all();

        return view('pages.master.branches.index', compact(
            'columns',
            'branches',
            'branchesData',
            'managers'
        ));
    }

    public function create()
    {
        $managers = User::all();
        return view('pages.master.branches.create', compact('managers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:master_branches,code',
            'name' => 'required|string|max:200',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'manager_id' => 'nullable|exists:users,id',
            'status' => 'required|in:active,inactive',
        ]);

        $branch = Branch::create($validated);

        $this->auditLog->logCreate('master', $branch);

        return redirect()->route('master.branches.index')
            ->with('success', 'Branch created successfully.');
    }

    public function show(Branch $branch)
    {
        $branch->load('manager', 'users');
        return view('pages.master.branches.show', compact('branch'));
    }

    public function edit(Branch $branch)
    {
        $managers = User::all();
        return view('pages.master.branches.edit', compact('branch', 'managers'));
    }

    public function update(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:master_branches,code,' . $branch->id,
            'name' => 'required|string|max:200',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'manager_id' => 'nullable|exists:users,id',
            'status' => 'required|in:active,inactive',
        ]);

        $branch->update($validated);

        return redirect()->route('master.branches.index')
            ->with('success', 'Branch updated successfully.');
    }

    public function destroy(Branch $branch)
    {
        $branch->delete();

        return redirect()->route('master.branches.index')
            ->with('success', 'Branch deleted successfully.');
    }

    /**
     * Switch current branch for authenticated user
     */
     public function switchBranch(Request $request): JsonResponse
    {
        try {
            // Validate input
            $validated = $request->validate([
                'branch_id' => 'required|integer|min:1|exists:master_branches,id',
            ]);

            $user = auth()->user();
            $branchId = $validated['branch_id'];

            // Verify the branch exists and is active
            $branch = Branch::find($branchId);
            if (!$branch) {
                return response()->json([
                    'success' => false,
                    'message' => 'Branch not found'
                ], 404);
            }

            if (!$branch->isActive()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This branch is not active'
                ], 422);
            }

            // Check if user can access this branch
            // This uses the User model's switchBranch method which checks permissions
            if (!$user->switchBranch($branchId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have access to this branch'
                ], 403);
            }

            // Log the branch switch for audit trail
            \Log::info('Branch switched', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'from_branch_id' => $user->getOriginal('current_branch_id'),
                'to_branch_id' => $branchId,
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Branch switched successfully',
                'data' => [
                    'branch_id' => $branchId,
                    'branch_name' => $branch->name,
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid branch ID provided',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            \Log::error('Error switching branch', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while switching branches'
            ], 500);
        }
    }
}
