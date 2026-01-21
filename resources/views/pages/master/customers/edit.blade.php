@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Edit Customers" />

<x-common.component-card title="Edit Customers">
    <x-flash-message.flash />

    <form method="POST" action="{{ route('master.customers.update', $customer) }}">
        @csrf
        @method('PUT')
        @include('pages.master.customers.form', ['customer' => $customer])
    </form>
</x-common.component-card>
@endsection

