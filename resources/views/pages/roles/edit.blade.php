@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Edit Role" />

<x-common.component-card title="Edit Role">
    <x-flash-message.flash />
    <form method="POST" action="{{ route('roles.update', $role->id) }}">
        @csrf
        @method('PUT')

        @include('pages.roles.form', [
            'role' => $role,
            'permissions' => $permissions,
            'selectedPermissions' => $role->permissions->pluck('name')->toArray()
        ])
    </form>
</x-common.component-card>
@endsection
