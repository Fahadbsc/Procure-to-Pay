@extends('layouts.app')
@section('title',$editing?'Edit User':'Add User')
@section('content')<div class="container-fluid"><h3 class="mb-4">{{ $editing?'Edit':'Add' }} Company User</h3>
<form method="POST" action="{{ $editing?route('users.update',$user):route('users.store') }}">@csrf @if($editing)@method('PUT')@endif
<div class="row"><div class="col-md-6 mb-3"><label class="form-label">Name</label><input class="form-control" name="name" value="{{ old('name',$user->name) }}" required></div><div class="col-md-6 mb-3"><label class="form-label">Email</label><input type="email" class="form-control" name="email" value="{{ old('email',$user->email) }}" required></div>
<div class="col-md-6 mb-3"><label class="form-label">{{ $editing?'New password (leave blank to keep current)':'Password' }}</label><input type="password" class="form-control" name="password" {{ $editing?'':'required' }} minlength="8"></div>
<div class="col-md-6 mb-3"><label class="form-label">Manager</label><select class="form-select" name="manager_id"><option value="">No manager</option>@foreach($managers as $manager)<option value="{{ $manager->id }}" @selected(old('manager_id',$user->manager_id)==$manager->id)>{{ $manager->name }}</option>@endforeach</select></div>
<div class="col-12 mb-3"><label class="form-label">Roles</label><select class="form-select" name="roles[]" multiple required size="9">@foreach($roles as $role)<option value="{{ $role->id }}" @selected(in_array($role->id,old('roles',$selectedRoles)))>{{ $role->name }}</option>@endforeach</select><small class="text-muted">Hold Ctrl to select more than one role.</small></div></div>
<button class="btn btn-success">Save</button> <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a></form></div>@endsection
