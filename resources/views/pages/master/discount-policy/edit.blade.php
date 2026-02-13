@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Edit Discount Policy" />

<x-common.component-card title="Edit Discount Policy">
    <x-flash-message.flash />

    <form method="POST" action="{{ route('master.discount-policy.update', $discountPolicy) }}">
        @csrf
        @method('PUT')
        @include('pages.master.discount-policy.form',[
            'departments' => $departments,
            'discountPolicy' => $discountPolicy,
            'departments' => $departments,
        ])
    </form>
</x-common.component-card>
@endsection
