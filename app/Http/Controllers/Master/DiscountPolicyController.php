<?php

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use App\Models\DiscountPolicy;
use App\Models\MasterDepartment;
use App\Http\Controllers\Controller;

class DiscountPolicyController extends Controller
{
    public function index(Request $request)
    {
        $query = DiscountPolicy::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('department_code', 'like', "%{$search}%")
                  ->orWhere('level_name', 'like', "%{$search}%")
                  ->orWhere('segment', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('department_code')) {
            $query->where('department_code', $request->department_code);
        }

        $discountPolicies = $query->latest()->paginate(15);

        /* ============================
        TABLE COLUMNS (KEY BASED)
        ============================ */
        $columns = [
            ['key' => 'department_code', 'label' => 'Department Code', 'type' => 'text'],
            ['key' => 'level_name', 'label' => 'Level Name', 'type' => 'text'],
            ['key' => 'segment', 'label' => 'Segment', 'type' => 'text'],
            ['key' => 'max_discount_percent', 'label' => 'Max Discount %', 'type' => 'text'],
            ['key' => 'notes', 'label' => 'Notes', 'type' => 'text'],
            ['key' => 'status', 'label' => 'Status', 'type' => 'badge'],
        ];

        $discountPolicy = $query->paginate(15);

        /* ============================
        FORMAT DATA FOR TABLE
        ============================ */
        $discountPolicyData = $discountPolicy->getCollection()->map(function ($policy) {
            return [
                'id' => $policy->id,
                'department_code' => $policy->department_code,
                'level_name' => $policy->level_name,
                'segment' => $policy->segment,
                'max_discount_percent' => number_format($policy->max_discount_percent, 2) . '%',
                'notes' => $policy->notes ?? '-',

                'status' => [
                    'value' => $policy->status,
                    'label' => ucfirst($policy->status),
                    'color' => match ($policy->status) {
                        'active' => 'active',
                        'inactive' => 'inactive',
                        default => 'gray',
                    }
                ],

                'actions' => [
                    'show' => route('master.discount-policy.show', $policy),
                    'edit' => route('master.discount-policy.edit', $policy),
                    'delete' => route('master.discount-policy.destroy', $policy),
                ],
            ];
        })->toArray();

        return view('pages.master.discount-policy.index', compact('discountPolicy', 'discountPolicyData', 'columns'));
    }

    public function create()
    {
        $departments = MasterDepartment::all();
        return view('pages.master.discount-policy.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'department_code' => 'required|string|max:255',
            'level_name' => 'required|string|max:255',
            'segment' => 'required|string|max:255',
            'max_discount_percent' => 'required|numeric|min:0|max:100',
            'notes' => 'nullable|string',
            'status' => 'required|string|in:active,inactive',
        ]);

        DiscountPolicy::create($validated);

        return redirect()->route('master.discount-policy.index')
            ->with('success', 'Discount Policy created successfully.');
    }

    public function show(DiscountPolicy $discountPolicy)
    {
        return view('pages.master.discount-policy.show', compact('discountPolicy'));
    }

    public function edit(DiscountPolicy $discountPolicy)
    {
        $departments = MasterDepartment::all();
        return view('pages.master.discount-policy.edit', compact('discountPolicy', 'departments'));
    }

    public function update(Request $request, DiscountPolicy $discountPolicy)
    {
        $validated = $request->validate([
            'department_code' => 'required|string|max:255',
            'level_name' => 'required|string|max:255',
            'segment' => 'required|string|max:255',
            'max_discount_percent' => 'required|numeric|min:0|max:100',
            'notes' => 'nullable|string',
            'status' => 'required|string|in:active,inactive',
        ]);

        $discountPolicy->update($validated);

        return redirect()->route('master.discount-policy.index')
            ->with('success', 'Discount Policy updated successfully.');
    }

    public function destroy(DiscountPolicy $discountPolicy)
    {
        try {
            $discountPolicy->delete();
            return redirect()->route('master.discount-policy.index')
                ->with('success', 'Discount Policy deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('master.discount-policy.index')
                ->with('error', 'Failed to delete discount policy. It may be associated with other records.');
        }
    }
}