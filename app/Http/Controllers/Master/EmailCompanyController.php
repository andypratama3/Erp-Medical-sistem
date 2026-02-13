<?php

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use App\Models\EmailCompany;
use App\Models\MasterDepartment;
use App\Http\Controllers\Controller;

class EmailCompanyController extends Controller
{
    public function index(Request $request)
    {
        $query = EmailCompany::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('email_full', 'like', "%{$search}%")
                  ->orWhere('email_local', 'like', "%{$search}%")
                  ->orWhere('email_domain', 'like', "%{$search}%")
                  ->orWhere('dept_code', 'like', "%{$search}%")
                  ->orWhere('office_code', 'like', "%{$search}%");
            });
        }

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('scope_type')) {
            $query->where('scope_type', $request->scope_type);
        }

        if ($request->filled('dept_code')) {
            $query->where('dept_code', $request->dept_code);
        }

        if ($request->filled('is_primary')) {
            $query->where('is_primary', $request->is_primary);
        }

        $emailCompanies = $query->latest()->paginate(15);

        /* ============================
        TABLE COLUMNS (KEY BASED)
        ============================ */
        $columns = [
            ['key' => 'scope_type', 'label' => 'Scope Type', 'type' => 'text'],
            ['key' => 'dept_code', 'label' => 'Department', 'type' => 'text'],
            ['key' => 'office_code', 'label' => 'Office', 'type' => 'text'],
            ['key' => 'email_full', 'label' => 'Email', 'type' => 'text'],
            ['key' => 'is_primary', 'label' => 'Primary', 'type' => 'badge'],
            ['key' => 'status', 'label' => 'Status', 'type' => 'badge'],
        ];

        /* ============================
        FORMAT DATA FOR TABLE
        ============================ */
        $emailCompanyData = $emailCompanies->getCollection()->map(function ($email) {
            return [
                'id' => $email->id,
                'scope_type' => ucfirst($email->scope_type ?? '-'),
                'dept_code' => $email->dept_code ?? '-',
                'office_code' => $email->office_code ?? '-',
                'email_full' => $email->email_full,
                'email_local' => $email->email_local,
                'email_domain' => $email->email_domain,
                'note' => $email->note ?? '-',

                'is_primary' => [
                    'value' => $email->is_primary,
                    'label' => $email->is_primary ? 'Yes' : 'No',
                    'color' => $email->is_primary ? 'active' : 'inactive',
                ],

                'status' => [
                    'value' => $email->status,
                    'label' => ucfirst($email->status),
                    'color' => match ($email->status) {
                        'active' => 'active',
                        'inactive' => 'inactive',
                        default => 'gray',
                    }
                ],

                'actions' => [
                    'show' => route('master.email-company.show', $email),
                    'edit' => route('master.email-company.edit', $email),
                    'delete' => route('master.email-company.destroy', $email),
                ],
            ];
        })->toArray();

        return view('pages.master.email-company.index', compact('emailCompanies', 'emailCompanyData', 'columns'));
    }

    public function create()
    {
        return view('pages.master.email-company.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'scope_type' => 'required|string|max:255',
            'dept_code' => 'nullable|string|max:255',
            'office_code' => 'nullable|string|max:255',
            'email_local' => 'required|string|max:255',
            'email_domain' => 'required|string|max:255',
            'note' => 'nullable|string',
            'is_primary' => 'required|boolean',
            'status' => 'required|string|in:active,inactive',
        ]);

        // Combine email_local and email_domain to create email_full
        $validated['email_full'] = $validated['email_local'] . '@' . $validated['email_domain'];

        EmailCompany::create($validated);

        return redirect()->route('master.email-company.index')
            ->with('success', 'Email Company created successfully.');
    }

    public function show(EmailCompany $emailCompany)
    {
        return view('pages.master.email-company.show', compact('emailCompany'));
    }

    public function edit(EmailCompany $emailCompany)
    {
        return view('pages.master.email-company.edit', compact('emailCompany'));
    }

    public function update(Request $request, EmailCompany $emailCompany)
    {
        $validated = $request->validate([
            'scope_type' => 'required|string|max:255',
            'dept_code' => 'nullable|string|max:255',
            'office_code' => 'nullable|string|max:255',
            'email_local' => 'required|string|max:255',
            'email_domain' => 'required|string|max:255',
            'note' => 'nullable|string',
            'is_primary' => 'required|boolean',
            'status' => 'required|string|in:active,inactive',
        ]);

        // Combine email_local and email_domain to create email_full
        $validated['email_full'] = $validated['email_local'] . '@' . $validated['email_domain'];

        $emailCompany->update($validated);

        return redirect()->route('master.email-company.index')
            ->with('success', 'Email Company updated successfully.');
    }

    public function destroy(EmailCompany $emailCompany)
    {
        try {
            $emailCompany->delete();
            return redirect()->route('master.email-company.index')
                ->with('success', 'Email Company deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('master.email-company.index')
                ->with('error', 'Failed to delete email company. It may be associated with other records.');
        }
    }
}