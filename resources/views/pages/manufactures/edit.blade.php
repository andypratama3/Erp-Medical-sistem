@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Edit Manufactur" />

<x-common.component-card title="Edit Manufactur">
    <x-flash-message.flash />
    <form method="POST" action="{{ route('manufactures.update', $manufacture->id) }}">
        @csrf
        @method('PUT')

        @include('pages.manufactures.form', [
            'manufacture' => $manufacture,
        ])
    </form>
</x-common.component-card>
@endsection
