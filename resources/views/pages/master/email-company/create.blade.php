@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Create Email Company" />

<x-common.component-card title="Create Email Company">
    <x-flash-message.flash />

    <form method="POST" action="{{ route('master.email-company.store') }}" enctype="multipart/form-data">
        @csrf
        @include('pages.master.email-company.form',[
            'emailCompany' => null,
            'departments' => $departments,
        ])
    </form>
</x-common.component-card>
@endsection
