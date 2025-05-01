@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2 class="text-center mb-4 text-primary">User Profile</h2>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0" data-bs-theme="auto">
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">User's email:</label>
                        <div class="form-control border-0 bg-transparent text-body">{{ $user->email }}</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Teams & Roles:</label>
                        <div class="form-control border-0 bg-transparent text-body">
                            @foreach($user->teams as $team)
                                {{ $team->name }} - 
                                @foreach($user->roles as $role)
                                    {{ $role->name }}@if(!$loop->last), @endif
                                @endforeach
                                <br/>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">API Keys:</label>
                        <div class="form-control border-0 bg-transparent text-body">
                            @foreach($user->keys as $key)
                                <div>
                                    <strong>{{ $key->host }}:</strong> {{ $key->value }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a href="#" class="btn btn-primary">Edit Profile</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection