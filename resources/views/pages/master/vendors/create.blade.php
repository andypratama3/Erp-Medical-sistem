@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Create Vendors" />

<x-common.component-card title="Create New Vendors">
    <x-flash-message.flash />

    <form method="POST" action="{{ route('master.vendors.store') }}">
        @csrf
        @include('pages.master.vendors.form')
    </form>
</x-common.component-card>
@endsection
