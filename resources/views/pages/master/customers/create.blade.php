@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Create Customers" />

<x-common.component-card title="Create New Customers">
    <x-flash-message.flash />

    <form method="POST" action="{{ route('master.customers.store') }}">
        @csrf
        @include('pages.master.customers.form')
    </form>
</x-common.component-card>
@endsection
