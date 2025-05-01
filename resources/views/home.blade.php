@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('Tasks') }}</h5>
                                    <p class="card-text">{{ __('View and manage your tasks') }}</p>
                                    <a href="{{ route('tasks.index') }}" class="btn btn-primary">{{ __('View Tasks') }}</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('Create Task') }}</h5>
                                    <p class="card-text">{{ __('Create a new task') }}</p>
                                    <a href="{{ route('decipherExport.createDecipherTask') }}" class="btn btn-success">{{ __('Create Task') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('Profile') }}</h5>
                                    <p class="card-text">{{ __('Manage your account settings') }}</p>
                                    <a href="{{ route('profile') }}" class="btn btn-info">{{ __('View Profile') }}</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('Security') }}</h5>
                                    <p class="card-text">{{ __('Change your password') }}</p>
                                    <a href="{{ route('password.request') }}" class="btn btn-warning">{{ __('Change Password') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
