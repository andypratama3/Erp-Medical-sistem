@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Tambah Role" />

<x-common.component-card title="Tambah Role">
    <x-flash-message.flash />

    <form method="POST" action="{{ route('management.roles.store') }}">
        @csrf
        @include('pages.roles.form', [
            'permissions' => $permissions,
            'selectedPermissions' => []
        ])
    </form>
</x-common.component-card>
@endsection
