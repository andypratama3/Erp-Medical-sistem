@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Edit Driver" />

<x-common.component-card title="Edit Driver">
    <x-flash-message.flash />

    <form method="POST" action="{{ route('scm.drivers.update', $driver) }}">
        @csrf
        @method('PUT')
        @include('pages.scm.drivers.form', ['driver' => $driver])
    </form>
</x-common.component-card>
@endsection

