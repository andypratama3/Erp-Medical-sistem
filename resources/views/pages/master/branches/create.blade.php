@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Create Branch" />

<x-common.component-card title="Create Branch">
    <x-flash-message.flash />

    <form method="POST" action="{{ route('master.branches.store') }}" enctype="multipart/form-data">
        @csrf
        @method('POST')
        @include('pages.master.branches.form', [
            'managers' => $managers
        ])
    </form>
</x-common.component-card>
@endsection
