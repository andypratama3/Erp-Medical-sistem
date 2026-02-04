@extends('layouts.app')

@section('content')

<x-common.page-breadcrumb pageTitle="Tambah Permission" />

<form method="POST" action="{{ route('management.permissions.store') }}">
    @csrf

    <x-common.component-card title="Tambah Permission">
        @include('pages.permissions.fields')
        <div class="mt-6 flex justify-end">
            <button class="btn btn-primary dark:text-white dark:bg-white/5">
                Simpan
            </button>
        </div>
    </x-common.component-card>
</form>

@endsection
