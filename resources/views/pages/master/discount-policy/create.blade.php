@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Create Discount Policy" />

<x-common.component-card title="Create New Discount Policy">
    <x-flash-message.flash />

    <form method="POST" action="{{ route('master.discount-policy.store') }}">
        @csrf
        @include('pages.master.discount-policy.form')
    </form>
</x-common.component-card>
@endsection
