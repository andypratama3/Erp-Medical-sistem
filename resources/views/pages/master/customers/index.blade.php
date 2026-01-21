@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Master Customers" />

<div class="space-y-6 sm:space-y-7">
    <x-flash-message.flash />

    <x-common.component-card
        title="Office List"
        desc="Manage all Customers in your system"
        link="{{ route('master.customers.create') }}">

        <x-table.table-component
            :data="$customersData"
            :columns="$columns"
            :searchable="true"
            :filterable="false" />
    </x-common.component-card>

    @if($customers->hasPages())
        <div class="flex justify-start gap-2">
            {{ $customers->links() }}
        </div>
    @endif
</div>
@endsection
