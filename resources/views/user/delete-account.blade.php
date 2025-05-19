@extends('layouts.app')

@section('title', 'Delete Account')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Delete Account</h4>
                </div>
                
                <div class="card-body">
                    <div class="alert alert-warning">
                        <strong>Warning:</strong> This action cannot be undone. All your data will be permanently deleted.
                    </div>
                    
                    <p>This will delete your account and all associated data including:</p>
                    <ul>
                        <li>Your profile information</li>
                        <li>Wine ratings and comments</li>
                        <li>Recommendation history</li>
                        <li>Saved preferences</li>
                    </ul>
                    
                    <form method="POST" action="{{ route('user.delete-account') }}" class="mt-4">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Confirm your password to continue</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="{{ route('user.account-settings') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash-alt me-1"></i> Permanently Delete Account
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 