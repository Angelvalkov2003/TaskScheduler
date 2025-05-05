@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2 class="text-center mb-4">Edit Profile</h2>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Name Edit Form -->
    <div class="card mb-4">
        <div class="card-header">
            <h4 class="mb-0">Edit Name</h4>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('profile.update.name') }}">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                           id="name" name="name" value="{{ old('name', $user->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">Update Name</button>
            </form>
        </div>
    </div>

    <!-- Password Change Form -->
    <div class="card mb-4">
        <div class="card-header">
            <h4 class="mb-0">Change Password</h4>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('profile.update.password') }}">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="current_password" class="form-label">Current Password</label>
                    <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                           id="current_password" name="current_password">
                    @error('current_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="new_password" class="form-label">New Password</label>
                    <input type="password" class="form-control @error('new_password') is-invalid @enderror" 
                           id="new_password" name="new_password">
                    @error('new_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                    <input type="password" class="form-control" 
                           id="new_password_confirmation" name="new_password_confirmation">
                </div>

                <button type="submit" class="btn btn-primary">Update Password</button>
            </form>
        </div>
    </div>

    <!-- API Keys Form -->
    <div class="card mb-4">
        <div class="card-header">
            <h4 class="mb-0">Manage API Keys</h4>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('profile.update.keys') }}">
                @csrf
                @method('PUT')
                <div id="keys-container">
                    @foreach($user->keys as $index => $key)
                        <div class="key-entry mb-3">
                            <div class="row">
                                <div class="col-md-5">
                                    <input type="text" class="form-control @error('keys.'.$index.'.host') is-invalid @enderror" 
                                           name="keys[{{ $index }}][host]" 
                                           value="{{ $key->host }}" 
                                           placeholder="Host">
                                    @error('keys.'.$index.'.host')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-5">
                                    <input type="text" class="form-control @error('keys.'.$index.'.value') is-invalid @enderror" 
                                           name="keys[{{ $index }}][value]" 
                                           value="{{ $key->value }}" 
                                           placeholder="Value">
                                    @error('keys.'.$index.'.value')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-danger remove-key">Remove</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <button type="button" class="btn btn-secondary mb-3" id="add-key">Add New Key</button>
                <div>
                    <button type="submit" class="btn btn-primary">Save API Keys</button>
                </div>
            </form>
        </div>
    </div>

    <div class="text-center">
        <a href="{{ route('profile') }}" class="btn btn-secondary">Back to Profile</a>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const keysContainer = document.getElementById('keys-container');
    const addKeyButton = document.getElementById('add-key');
    let keyCount = {{ count($user->keys) }};

    addKeyButton.addEventListener('click', function() {
        const keyEntry = document.createElement('div');
        keyEntry.className = 'key-entry mb-3';
        keyEntry.innerHTML = `
            <div class="row">
                <div class="col-md-5">
                    <input type="text" class="form-control" 
                           name="keys[${keyCount}][host]" 
                           placeholder="Host">
                </div>
                <div class="col-md-5">
                    <input type="text" class="form-control" 
                           name="keys[${keyCount}][value]" 
                           placeholder="Value">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger remove-key">Remove</button>
                </div>
            </div>
        `;
        keysContainer.appendChild(keyEntry);
        keyCount++;
    });

    keysContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-key')) {
            e.target.closest('.key-entry').remove();
        }
    });
});
</script>
@endpush
@endsection
