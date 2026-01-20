@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Tambah Category" />

<x-common.component-card title="Tambah Category">
    <x-flash-message.flash />

    <form method="POST" action="{{ route('categories.store') }}">
        @csrf
        @include('categories.form')
    </form>
</x-common.component-card>
@endsection
