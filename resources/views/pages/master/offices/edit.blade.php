
@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Edit Office" />

<x-common.component-card title="Edit Office">
    <x-flash-message.flash />

    <form method="POST" action="{{ route('master.offices.update', $office) }}">
        @csrf
        @method('PUT')
        @include('pages.master.offices.form', ['office' => $office])
    </form>
</x-common.component-card>
@endsection
