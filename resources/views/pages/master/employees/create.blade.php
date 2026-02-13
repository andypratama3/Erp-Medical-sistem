@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Create Employee" />

<x-common.component-card title="Create Employee">
    <x-flash-message.flash />

    <form method="POST" action="{{ route('master.employees.store') }}" enctype="multipart/form-data">
        @csrf
        @include('pages.master.employees.form',[
            'employee' => null,
            'departments' => $departments,
            'offices' => $offices,  
        ])
    </form>
</x-common.component-card>
@endsection
