@extends('layouts.app')

@section('title', 'Edit Cost Centre')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Edit Cost Centre: {{ $cost_centre->code }}</h4>
        <a href="{{ route('cost-centres.index') }}" class="btn btn-outline-secondary">Back to Cost Centres</a>
    </div>
    <div class="card border-0 shadow-sm"><div class="card-body">
        <form action="{{ route('cost-centres.update', $cost_centre) }}" method="POST">
            @csrf
            @method('PUT')
            @include('cost_centres._form', ['centre' => $cost_centre, 'buttonLabel' => 'Save Changes'])
        </form>
    </div></div>
</div>
@endsection
