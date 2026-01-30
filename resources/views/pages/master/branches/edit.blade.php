@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Edit Branch" />

<x-common.component-card title="Edit Branch">
    <x-flash-message.flash />

    <form method="POST" action="{{ route('master.branches.update', $branch) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('pages.master.branches.form', [
            'branch' => $branch,
            'managers' => $managers
        ])
    </form>
</x-common.component-card>
@endsection
