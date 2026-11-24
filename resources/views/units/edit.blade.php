@extends('layouts.app')

@section('title', 'Edit Unit')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Edit Unit: {{ $unit->code }}</h4>
        <a href="{{ route('units.index') }}" class="btn btn-outline-secondary">Back to Units</a>
    </div>
    <div class="card border-0 shadow-sm"><div class="card-body">
        <form action="{{ route('units.update', $unit) }}" method="POST">
            @csrf
            @method('PUT')
            @include('units._form', ['buttonLabel' => 'Save Changes'])
        </form>
    </div></div>
</div>
@endsection
