@extends('layouts.app')

@section('content')

<x-common.page-breadcrumb pageTitle="Notification" />

<div class="space-y-6 sm:space-y-7">
    {{-- Flash Message --}}
    <x-flash-message.flash />

    {{-- Table Card --}}
    <x-common.component-card
        title="Role List"
        desc="all Notificaiton">

        <x-table.table-component
            :data="$notificationsData"
            :columns="$columns"
            :searchable="true"
            :filterable="false" />
    </x-common.component-card>

    {{-- Pagination --}}
    @if($notifications->hasPages())
        <div class="flex justify-start gap-2">
            {{ $notifications->links() }}
        </div>
    @endif
</div>

@endsection
