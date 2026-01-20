@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Edit Product" />

<x-common.component-card title="Edit Product">
    <x-flash-message.flash />

    <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('products.form', [
            'product' => $product,
            'categories' => $categories,
            'manufactures' => $manufactures,
            'productGroups' => $productGroups
        ])
    </form>
</x-common.component-card>
@endsection
