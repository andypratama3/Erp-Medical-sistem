@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Edit Category" />

<x-common.component-card title="Edit Category">
    <x-flash-message.flash />

    <form method="POST" action="{{ route('categories.update', $category) }}">
        @csrf
        @method('PUT')
        @include('categories.form', ['category' => $category])
    </form>
</x-common.component-card>
@endsection
