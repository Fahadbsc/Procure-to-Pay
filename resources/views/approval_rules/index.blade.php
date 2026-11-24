
@extends('layouts.app')

@section('title', 'Approval Rules')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">Approval Rules</h3>
            <p class="text-muted mb-0">Manage document approval workflows</p>
        </div>

        <a href="{{ route('approval-rules.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add Approval Rule
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="60">#</th>
                            <th>Document Type</th>
                            <th>Cost Centre</th>
                            <th>Category</th>
                            <th>Minimum Amount</th>
                            <th>Maximum Amount</th>
                            <th>Approval Steps</th>
                            <th>Status</th>
                            <th width="150">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($rules as $rule)
                            <tr>
                                <td>{{ $loop->iteration }}</td>

                                <td>
                                    {{ $rule->document_type }}
                                </td>

                                <td>
                                    {{ $rule->costCentre->name ?? 'All Cost Centres' }}
                                </td>
                                <td>{{ $rule->category ?? 'Any' }}</td>

                                <td>
                                    {{ number_format($rule->min_amount, 2) }}
                                </td>

                                <td>
                                    {{ $rule->max_amount !== null
                                        ? number_format($rule->max_amount, 2)
                                        : 'No Limit' }}
                                </td>

                                <td>
                                    @foreach($rule->steps as $step)
                                        <div>
                                            {{ $step->step_order }}.
                                            {{ $step->approver->name ?? 'Unknown' }}
                                        </div>
                                    @endforeach
                                </td>

                                <td>
                                    @if($rule->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>

                                <td>
                                    <a href="{{ route('approval-rules.edit', $rule->id) }}"
                                       class="btn btn-sm btn-warning">
                                        Edit
                                    </a>

                                    <form action="{{ route('approval-rules.destroy', $rule->id) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Delete this approval rule?')">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="btn btn-sm btn-danger">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    No approval rules found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>

@endsection

