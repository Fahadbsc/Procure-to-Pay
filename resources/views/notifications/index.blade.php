@extends('layouts.app')
@section('title','Notifications')
@section('content')
<div class="container-fluid"><h3 class="mb-4">Notifications</h3><div class="list-group">
@forelse($notifications as $notification)<div class="list-group-item d-flex justify-content-between align-items-center {{ $notification->read_at ? '' : 'list-group-item-info' }}"><span>{{ $notification->data['message'] ?? 'Workflow update' }} <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small></span>
@if(!$notification->read_at)<form method="POST" action="{{ route('notifications.read',$notification->id) }}">@csrf<button class="btn btn-sm btn-outline-primary">Open and mark read</button></form>@elseif(!empty($notification->data['url']))<a class="btn btn-sm btn-outline-secondary" href="{{ $notification->data['url'] }}">Open</a>@endif</div>@empty<div class="list-group-item">No notifications.</div>@endforelse
</div>{{ $notifications->links() }}</div>
@endsection
