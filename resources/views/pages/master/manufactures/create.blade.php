@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Create Manufactur" />

<x-common.component-card title="Create Manufactur">
    <x-flash-message.flash />

    <form method="POST" action="{{ route('master.manufactures.store') }}">
        @csrf
        @include('pages.master.manufactures.form', [
            'manufacture' => null,
        ])
    </form>
</x-common.component-card>
@endsection
