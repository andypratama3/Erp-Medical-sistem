@extends('layouts.app')

@section('content')

<x-common.page-breadcrumb pageTitle="Permission" />

<div class="space-y-6 sm:space-y-7">
   <x-flash-message.flash />
    {{-- Table Card --}}
    <x-common.component-card
        title="Permission List"
        desc="Manage all permissions in your system"
        link="{{ route('permissions.create') }}">

        <x-table.table-component
            :data="$permissionsData"
            :columns="$columns"
            :searchable="true"
            :filterable="false" />
    </x-common.component-card>

    {{-- Pagination --}}
    @if($permissions->hasPages())
        <div class="flex justify-start gap-2 ">
            {{ $permissions->links() }}
        </div>
    @endif
</div>

@endsection
