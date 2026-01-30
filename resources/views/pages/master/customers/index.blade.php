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
            :filterable="false"
            :pagination="$customers" />
    </x-common.component-card>
</div>
@endsection
