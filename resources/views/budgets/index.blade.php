
@extends('layouts.app')

@section('title', 'Budget Management')

@section('content')

<div class="container-fluid">

    <h4 class="mb-4">Budget Management</h4>

    {{-- Add / Update Budget --}}
    <div class="card shadow-sm mb-4">

        <div class="card-header">
            <strong>Set Cost Centre Budget</strong>
        </div>

        <div class="card-body">

            <form action="{{ route('budgets.store') }}" method="POST">
                @csrf

                <div class="row">

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Cost Centre *</label>

                        <select name="cost_centre_id"
                                class="form-select"
                                required>

                            <option value="">Select Cost Centre</option>

                            @foreach($costCentres as $centre)
                                <option value="{{ $centre->id }}"
                                    {{ old('cost_centre_id') == $centre->id ? 'selected' : '' }}>
                                    {{ $centre->code }} - {{ $centre->name }}
                                </option>
                            @endforeach

                        </select>

                        @error('cost_centre_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Financial Year *</label>

                        <input type="text"
                               name="financial_year"
                               class="form-control"
                               placeholder="2026-27"
                               value="{{ old('financial_year', now()->month >= 4 ? now()->format('Y') . '-' . now()->addYear()->format('y') : now()->subYear()->format('Y') . '-' . now()->format('y')) }}"
                               pattern="[0-9]{4}-[0-9]{2}"
                               title="Example: 2026-27"
                               required>

                        @error('financial_year')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror

                        <small class="text-muted">
                            Financial year: April to March (Example: 2026-27)
                        </small>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Budget Amount (₹) *</label>

                        <input type="number"
                               name="budget_amount"
                               class="form-control"
                               min="0"
                               step="0.01"
                               value="{{ old('budget_amount') }}"
                               placeholder="Enter budget amount"
                               required>

                        @error('budget_amount')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                </div>

                <button type="submit" class="btn btn-primary">
                    Save Budget
                </button>

            </form>

        </div>
    </div>

    {{-- Budget List --}}
    <div class="card shadow-sm">

        <div class="card-header">
            <strong>Cost Centre Budget Details</strong>
        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle">

                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Cost Centre</th>
                            <th>Financial Year</th>
                            <th class="text-end">Budget (₹)</th>
                            <th class="text-end">Committed (₹)</th>
                            <th class="text-end">Spent (₹)</th>
                            <th class="text-end">Available (₹)</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($budgets as $budget)

                            @php
                                $available = (float) $budget->budget_amount
                                    - (float) $budget->committed_amount
                                    - (float) $budget->spent_amount;
                            @endphp

                            <tr>
                                <td>{{ $loop->iteration }}</td>

                                <td>
                                    {{ $budget->costCentre->code ?? '-' }}
                                    -
                                    {{ $budget->costCentre->name ?? '-' }}
                                </td>

                                <td>{{ $budget->financial_year }}</td>

                                <td class="text-end">
                                    {{ number_format((float) $budget->budget_amount, 2) }}
                                </td>

                                <td class="text-end">
                                    {{ number_format((float) $budget->committed_amount, 2) }}
                                </td>

                                <td class="text-end">
                                    {{ number_format((float) $budget->spent_amount, 2) }}
                                </td>

                                <td class="text-end fw-bold {{ $available < 0 ? 'text-danger' : 'text-success' }}">
                                    {{ number_format($available, 2) }}
                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    No budgets found.
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
