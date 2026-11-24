@extends('layouts.app')

@section('title', 'Add Unit')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Add Unit of Measure</h4>
        <a href="{{ route('units.index') }}" class="btn btn-outline-secondary">Back to Units</a>
    </div>
    <div class="card border-0 shadow-sm"><div class="card-body">
        <form action="{{ route('units.store') }}" method="POST">
            @csrf
            @include('units._form', ['unit' => null, 'buttonLabel' => 'Create Unit'])
        </form>
    </div></div>
</div>
@endsection
