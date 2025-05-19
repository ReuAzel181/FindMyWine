@extends('layouts.app')

@section('title', 'Account Settings')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-white">
                    <h4 class="mb-0"><i class="fas fa-user-cog me-2"></i>Account Settings</h4>
                </div>
                
                <div class="card-body">
                    <h5 class="border-bottom pb-2">Your Information</h5>
                    
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <p class="fw-bold mb-1">Name:</p>
                        </div>
                        <div class="col-md-8">
                            <p>{{ Auth::user()->name }}</p>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <p class="fw-bold mb-1">Username:</p>
                        </div>
                        <div class="col-md-8">
                            <p>{{ Auth::user()->username }}</p>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <p class="fw-bold mb-1">Email:</p>
                        </div>
                        <div class="col-md-8">
                            <p>{{ Auth::user()->email ?? 'Not provided' }}</p>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <p class="fw-bold mb-1">Account Created:</p>
                        </div>
                        <div class="col-md-8">
                            <p>{{ Auth::user()->created_at->format('F j, Y') }}</p>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <h5 class="text-danger border-bottom pb-2">Danger Zone</h5>
                    
                    <div class="mt-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="mb-1">Delete Account</h6>
                                <p class="mb-0 text-muted">Permanently delete your account and all data</p>
                            </div>
                            <a href="{{ route('user.delete-account') }}" class="btn btn-outline-danger">
                                Delete Account
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 