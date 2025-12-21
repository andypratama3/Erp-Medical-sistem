@extends('layouts.app')

@section('content')

<x-common.page-breadcrumb pageTitle="Edit Permission" />

<form method="POST" action="{{ route('permissions.update', $permission->id) }}">
    @csrf
    @method('PUT')

    <x-common.component-card title="Edit Permission">
        @include('pages.permissions.fields', ['permission' => $permission])

        <div class="mt-6 flex justify-end">
            <button class="btn btn-primary dark:text-white dark:bg-white/5">
                Simpan
            </button>
        </div>
    </x-common.component-card>
</form>

@endsection
