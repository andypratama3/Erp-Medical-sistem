@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Edit Sales DO" />

<x-common.component-card title="Edit Sales Delivery Order">
    <x-flash-message.flash />

    <form method="POST" action="{{ route('crm.sales-do.update', $salesDo) }}">
        @csrf
        @method('PUT')
        @include('pages.crm.sales_do.form', ['salesDo' => $salesDo])
    </form>
</x-common.component-card>
@endsection
