@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Create Manajemen Stock" />

<x-common.component-card title="Create New Manajemen Stock">
    <x-flash-message.flash />

    <form method="POST" action="{{ route('wqs.inventory.store') }}" id="stockCheckForm">
        @csrf
        @include('pages.wqs.inventory.form')
    </form>
</x-common.component-card>
@endsection
