@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Master Discount Policy" />

<div class="space-y-6 sm:space-y-7">
    <x-flash-message.flash />

    <x-common.component-card
        title="Discount Policy List"
        desc="Manage all discount policy in your system"
        link="{{ route('master.discount-policy.create') }}">

        <x-table.table-component
            :data="$discountPolicyData"
            :columns="$columns"
            :searchable="true"
            :filterable="false"
            :pagination="$discountPolicy" />
    </x-common.component-card>
</div>
@endsection
