@extends('layouts.app')

@section('content')

<x-common.page-breadcrumb pageTitle="User" />

<div class="space-y-6 sm:space-y-7">
    <x-flash-message.flash />

    <x-common.component-card
        title="User List"
        desc="Manage all users in your system"
        link="{{ route('master.users.create') }}">

        <x-table.table-component
            :data="$usersData"
            :columns="$columns"
            :searchable="true"
            :filterable="false"
            :pagination="$users" />
    </x-common.component-card>
</div>

@endsection
