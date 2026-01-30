@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Master departments" />

<div class="space-y-6 sm:space-y-7">
    <x-flash-message.flash />

    <x-common.component-card
        title="Office List"
        desc="Manage all departments in your system"
        link="{{ route('master.departments.create') }}">

        <x-table.table-component
            :data="$departmentsData"
            :columns="$columns"
            :searchable="true"
            :filterable="false"
            :pagination="$departments" />
    </x-common.component-card>
</div>
@endsection
