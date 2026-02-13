@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Edit Employee" />

<x-common.component-card title="Edit Employee">
    <x-flash-message.flash />

    <form method="POST" action="{{ route('master.employees.update', $employee) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('pages.master.employees.form', [
            'employee' => $employee,
            'departments' => $departments,
            'offices' => $offices,
        ])
    </form>
</x-common.component-card>
@endsection
