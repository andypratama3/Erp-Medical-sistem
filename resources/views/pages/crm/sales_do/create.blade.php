@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Create Sales DO" />

<x-common.component-card title="Create New Sales Delivery Order">
    <x-flash-message.flash />

    <form method="POST" action="{{ route('crm.sales-do.store') }}" id="salesDoForm">
        @csrf
        @include('pages.crm.sales_do.form')
    </form>
</x-common.component-card>
@endsection
