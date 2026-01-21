@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Master Offices" />

<div class="space-y-6 sm:space-y-7">
    <x-flash-message.flash />

    <x-common.component-card
        title="Office List"
        desc="Manage all offices in your system"
        link="{{ route('master.offices.create') }}">

        <x-table.table-component
            :data="$departmentsData"
            :columns="$columns"
            :searchable="true"
            :filterable="false" />
    </x-common.component-card>

    @if($departments->hasPages())
        <div class="flex justify-start gap-2">
            {{ $departments->links() }}
        </div>
    @endif
</div>
@endsection
