@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Master Vendor" />

<div class="space-y-6 sm:space-y-7">
    <x-flash-message.flash />

    <x-common.component-card
        title="Vendor List"
        desc="Manage all vendors in your system"
        link="{{ route('master.vendors.create') }}">

        <x-table.table-component
            :data="$vendorsData"
            :columns="$columns"
            :searchable="true"
            :filterable="false"
            :pagination="$vendors" />
    </x-common.component-card>
</div>
@endsection
