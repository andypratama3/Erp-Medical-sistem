@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Create Departments" />

<x-common.component-card title="Create New Departments">
    <x-flash-message.flash />

    <form method="POST" action="{{ route('master.departments.store') }}">
        @csrf
        @include('pages.master.departments.form')
    </form>
</x-common.component-card>
@endsection
