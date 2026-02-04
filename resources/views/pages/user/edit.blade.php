@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Edit User" />

<x-common.component-card title="Edit User">
    <x-flash-message.flash />

    <form method="POST" action="{{ route('management.users.update', $user->id) }}">
        @csrf
        @method('PUT')

        @include('pages.user.form', [
            'user' => $user,
            'roles' => $roles,
            'selectedRoles' => $selectedRoles
        ])
    </form>
</x-common.component-card>
@endsection
