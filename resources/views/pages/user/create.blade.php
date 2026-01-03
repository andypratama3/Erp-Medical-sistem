@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Tambah User" />

<x-common.component-card title="Tambah User">
    <x-flash-message.flash />

    <form method="POST" action="{{ route('users.store') }}">
        @csrf
        @include('pages.user.form', [
            'roles' => $roles,
            'selectedRoles' => []
        ])
    </form>
</x-common.component-card>
@endsection
