@extends('layouts.app')

@section('content')

<x-common.page-breadcrumb pageTitle="Manufacture" />

<div class="space-y-6 sm:space-y-7">
    {{-- Flash Message --}}
    <x-flash-message.flash />

    {{-- Table Card --}}
    <x-common.component-card
        title="Manufacture List"
        desc="Manage all manufactures in your system"
        link="{{ route('manufactures.create') }}">

        <x-table.table-component
            :data="$manufactures"
            :columns="$columns"
            :searchable="true"
            :filterable="false" />
    </x-common.component-card>

    {{-- Pagination --}}
    @if($manufactures->hasPages())
        <div class="flex justify-start gap-2">
            {{ $manufactures->links() }}
        </div>
    @endif
</div>

@endsection
