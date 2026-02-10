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
        $query = DiscountPolicy::with('department');

        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('department_code', 'like', "%{$search}%")
                  ->orWhere('level_name', 'like', "%{$search}%")
                  ->orWhere('segment', 'like', "%{$search}%");
            });
        }


        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        $discountPolicy = $query->latest()->paginate(15);

        /* ============================
        TABLE COLUMNS (KEY BASED)
        ============================ */
        $columns = [
            ['key' => 'department_code', 'label' => 'Department Code', 'type' => 'text'],
            ['key' => 'level_name', 'label' => 'Level Name', 'type' => 'text'],
            ['key' => 'segment', 'label' => 'Segment', 'type' => 'text'],
            ['key' => 'max_discount_percent', 'label' => 'Max Discount Percent', 'type' => 'text'],
            ['key' => 'notes', 'label' => 'Notes', 'type' => 'text'],
            ['key' => 'status', 'label' => 'Status', 'type' => 'badge'],
        ];

        /* ============================
        FORMAT DATA FOR TABLE
        ============================ */
        $discountPolicyData = $discountPolicy->getCollection()->map(function ($department) {
            return [
                'id' => $department->id,
                'department_code' => $department->department_code,
                'level_name' => $department->level_name,
                'segment' => $department->segment,
                'max_discount_percent' => $department->max_discount_percent,
                'notes' => $department->notes,

                'status' => [
                    'value' => $department->status,
                    'label' => ucfirst($department->status),
                    'color' => $department->status ,
                ],

                'actions' => [
                    'show' => route('master.discount-policy.show', $department),
                    'edit' => route('master.discount-policy.edit', $department),
                    'delete' => route('master.discount-policy.destroy', $department),
                ],
            ];
        })->toArray();

        $departments = MasterDepartment::active()->get();

        
        return view('pages.master.discount-policy.index', compact('discountPolicy', 'discountPolicyData', 'columns','departments'));
    }

    public function create()
    {
        $departments = MasterDepartment::active()->get();
        return view('pages.master.discount-policy.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'department_id' => 'required',
            'level_name' => 'required',
            'segment' => 'required',
            'max_discount_percent' => 'required',
            'notes' => 'required',
            'status' => 'required',
        ]);

        DiscountPolicy::create($request->all());

        return redirect()->route('master.discount-policy.index')->with('success', 'Discount Policy created successfully');
    }
}
