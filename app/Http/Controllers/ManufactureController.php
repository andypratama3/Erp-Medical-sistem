<?php

namespace App\Http\Controllers;

use App\Models\Manufacture;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ManufactureController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view_manufactures', only: ['index', 'show']),
            new Middleware('permission:create_manufacture', only: ['create', 'store']),
            new Middleware('permission:edit_manufacture', only: ['edit', 'update']),
            new Middleware('permission:delete_manufacture', only: ['destroy']),
        ];
    }

    public function getColumns()
    {
        return [
            [
                'key' => 'name',
                'label' => 'Nama',
                'type' => 'text',
            ],
            [
                'key' => 'code',
                'label' => 'Kode',
                'type' => 'text',
            ],
            [
                'key' => 'country',
                'label' => 'Negara',
                'type' => 'text',
            ],
            [
                'key' => 'email',
                'label' => 'Email',
                'type' => 'text',
            ],
            [
                'key' => 'phone',
                'label' => 'Telepon',
                'type' => 'text',
            ],
            [
                'key' => 'address',
                'label' => 'Alamat',
                'type' => 'text',
            ],
            [
                'key' => 'status',
                'label' => 'Status',
                'type' => 'badge',
                'options' => [
                    'active' => 'success',
                    'inactive' => 'danger',
                ],
            ],
        ];
    }

    public function index(Request $request)
    {
        $columns = $this->getColumns();
        
        $query = Manufacture::query();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%')
                  ->orWhere('country', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('country')) {
            $query->where('country', $request->country);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $manufactures = $query->latest()->paginate(12)->withQueryString();
        $countries = Manufacture::distinct()->pluck('country')->filter();

        return view('pages.manufactures.index', compact('columns', 'manufactures', 'countries'));
    }

    public function create()
    {
        return view('pages.manufactures.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:manufactures,code',
            'country' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        Manufacture::create($validated);

        return redirect()->route('manufactures.index')
            ->with('success', 'Manufacture berhasil ditambahkan.');
    }

    public function show(Manufacture $manufacture)
    {
        return view('pages.manufactures.show', compact('manufacture'));
    }

    public function edit(Manufacture $manufacture)
    {
        return view('pages.manufactures.edit', compact('manufacture'));
    }

    public function update(Request $request, Manufacture $manufacture)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:manufactures,code,' . $manufacture->id,
            'country' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $manufacture->update($validated);

        return redirect()->route('manufactures.index')
            ->with('success', 'Manufacture berhasil diupdate.');
    }

    public function destroy(Manufacture $manufacture)
    {
        $manufacture->delete();

        return redirect()->route('manufactures.index')
            ->with('success', 'Manufacture berhasil dihapus.');
    }
}
