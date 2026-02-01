@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Drivers" />

<div class="space-y-6 sm:space-y-7">
    <x-flash-message.flash />

    <x-common.component-card
        title="Driver List"
        desc="Manage all Drivers"
        link="{{ route('scm.drivers.create') }}">

        <x-table.table-component
            :data="$driversData"
            :columns="$columns"
            :searchable="true"
            :filterable="false" />
    </x-common.component-card>

    @if($drivers->hasPages())
        <div class="flex justify-start gap-2">
            {{ $departments->links() }}
        </div>
    @endif
</div>
@endsection
