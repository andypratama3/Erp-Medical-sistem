@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Edit Vendors" />

<x-common.component-card title="Edit Vendors">
    <x-flash-message.flash />

    <form method="POST" action="{{ route('master.vendors.update', $vendor) }}">
        @csrf
        @method('PUT')
        @include('pages.master.vendors.form', ['vendor' => $vendor])
    </form>
</x-common.component-card>
@endsection

