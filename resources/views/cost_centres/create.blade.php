@extends('layouts.app')

@section('title', 'Add Cost Centre')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Add Cost Centre</h4>
        <a href="{{ route('cost-centres.index') }}" class="btn btn-outline-secondary">Back to Cost Centres</a>
    </div>
    <div class="card border-0 shadow-sm"><div class="card-body">
        <form action="{{ route('cost-centres.store') }}" method="POST">
            @csrf
            @include('cost_centres._form', ['centre' => null, 'buttonLabel' => 'Create Cost Centre'])
        </form>
    </div></div>
</div>
@endsection
