@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Create Driver" />

<x-common.component-card title="Create New Driver">
    <x-flash-message.flash />

    <form method="POST" action="{{ route('scm.drivers.store') }}">
        @csrf
        @include('pages.scm.drivers.form')
    </form>
</x-common.component-card>
@endsection
