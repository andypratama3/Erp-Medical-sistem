@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Create Office" />

<x-common.component-card title="Create New Office">
    <x-flash-message.flash />

    <form method="POST" action="{{ route('master.offices.store') }}">
        @csrf
        @include('pages.master.offices.form')
    </form>
</x-common.component-card>
@endsection
