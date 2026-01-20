@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Tambah Product" />

<x-common.component-card title="Tambah Product">
    <x-flash-message.flash />

    <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
        @csrf
        @include('products.form', [
            'categories' => $categories,
            'manufactures' => $manufactures,
            'productGroups' => $productGroups
        ])
    </form>
</x-common.component-card>
@endsection
