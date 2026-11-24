
@extends('layouts.app')

@section('title', 'My Approvals')

@section('content')

<div class="container-fluid">

    <div class="mb-4">
        <h3>My Pending Approvals</h3>
        <p class="text-muted mb-0">
            Documents waiting for your approval
        </p>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">

                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Document Type</th>
                            <th>Document ID</th>
                            <th>Requested By</th>
                            <th>Approval Step</th>
                            <th>Requested Date</th>
                            <th width="280">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($approvals as $step)

                            @php
                                $approval = $step->approvalRequest;
                            @endphp

                            <tr>
                                <td>{{ $loop->iteration }}</td>

                                <td>
                                    {{ $approval->document_type === 'PR'
                                        ? 'Purchase Requisition'
                                        : 'Purchase Order' }}
                                </td>

                                <td>{{ $approval->document_id }}</td>

                                <td>
                                    {{ $approval->requester->name ?? 'Unknown' }}
                                </td>

                                <td>
                                    Step {{ $step->step_order }}
                                </td>

                                <td>
                                    {{ $approval->created_at->format('d-m-Y h:i A') }}
                                </td>

                                <td>
                                    <form action="{{ route('my-approvals.approve', $approval->id) }}"
                                          method="POST"
                                          class="mb-2">
                                        @csrf

                                        <input type="text"
                                               name="comments"
                                               class="form-control form-control-sm mb-2"
                                               placeholder="Approval comments (optional)">

                                        <button type="submit"
                                                class="btn btn-sm btn-success"
                                                onclick="return confirm('Approve this document?')">
                                            Approve
                                        </button>
                                    </form>

                                    <form action="{{ route('my-approvals.reject', $approval->id) }}"
                                          method="POST">
                                        @csrf

                                        <input type="text"
                                               name="comments"
                                               class="form-control form-control-sm mb-2"
                                               placeholder="Rejection reason (required)"
                                               required
                                               minlength="5">

                                        <button type="submit"
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirm('Reject this document?')">
                                            Reject
                                        </button>
                                    </form>
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    No pending approvals found.
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
