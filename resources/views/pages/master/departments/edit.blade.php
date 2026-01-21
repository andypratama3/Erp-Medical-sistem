@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Edit Departments" />

<x-common.component-card title="Edit Departments">
    <x-flash-message.flash />

    <form method="POST" action="{{ route('master.departments.update', $department) }}">
        @csrf
        @method('PUT')
        @include('pages.master.departments.form', ['department' => $department])
    </form>
</x-common.component-card>
@endsection

