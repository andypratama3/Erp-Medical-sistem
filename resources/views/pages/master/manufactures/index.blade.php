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
        link="{{ route('master.manufactures.create') }}">

        <x-table.table-component
            :data="$manufacturesData"
            :columns="$columns"
            :searchable="true"
            :filterable="true"
            :pagination="$manufactures" />
    </x-common.component-card>
</div>

@endsection
