@extends('layouts.app')

@section('content')

<x-common.page-breadcrumb pageTitle="Role" />

<div class="space-y-6 sm:space-y-7">
    {{-- Flash Message --}}
    <x-flash-message.flash />

    {{-- Table Card --}}
    <x-common.component-card
        title="Role List"
        desc="Manage all roles in your system"
        link="{{ route('roles.create') }}">

        <x-table.table-component
            :data="$rolesData"
            :columns="$columns"
            :searchable="true"
            :filterable="false" />
    </x-common.component-card>

    {{-- Pagination --}}
    @if($roles->hasPages())
        <div class="flex justify-start gap-2">
            {{ $roles->links() }}
        </div>
    @endif
</div>

@endsection
