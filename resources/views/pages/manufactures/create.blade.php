@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Tambah Manufactur" />

<x-common.component-card title="Tambah Manufactur">
    <x-flash-message.flash />

    <form method="POST" action="{{ route('manufactures.store') }}">
        @csrf
        @include('pages.manufactures.form', [
            'manufacture' => null,
        ])
    </form>
</x-common.component-card>
@endsection
