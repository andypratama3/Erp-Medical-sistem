@extends('layouts.app')

@section('content')

<x-common.page-breadcrumb pageTitle="User" />

<div class="space-y-6 sm:space-y-7">
    <x-flash-message.flash />

    <x-common.component-card
        title="User List"
        desc="Manage all users in your system"
        link="{{ route('users.create') }}">

        <x-table.table-component
            :data="$usersData"
            :columns="$columns"
            :searchable="true"
            :filterable="false" />
    </x-common.component-card>

    @if($users->hasPages())
        <div class="flex justify-start gap-2">
            {{ $users->links() }}
        </div>
    @endif
</div>

@endsection
