@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Login') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}" id="login-form">
                        @csrf

                        <div class="row mb-3">
                            <label for="username" class="col-md-4 col-form-label text-md-end">{{ __('Username') }}</label>

                            <div class="col-md-6">
                                <input id="username" type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" required autocomplete="username" autofocus>

                                @error('username')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-wine px-4">
                                    {{ __('Login') }}
                                </button>

                                @if (Route::has('password.request'))
                                    <a class="btn btn-link text-wine" href="{{ route('password.request') }}">
                                        {{ __('Forgot Password?') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>

                    @if(isset($users) && count($users) > 0)
                    <hr class="my-4 gold-hr">
                    <div class="available-accounts">
                        <h5 class="mb-3 text-center">Or select an account</h5>
                        <div class="account-list">
                            @foreach($users as $user)
                            <div class="mb-2 position-relative">
                                <button type="button" class="btn w-100 user-select-btn" 
                                    data-username="{{ $user->username }}">
                                    <div class="d-flex align-items-center">
                                        <div class="account-icon">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div class="account-details">
                                            <span class="account-name">{{ $user->name }}</span>
                                            <span class="account-username">{{ $user->username }}</span>
                                        </div>
                                    </div>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger delete-account-btn position-absolute" 
                                    style="top: 50%; right: 15px; transform: translateY(-50%); z-index: 10;"
                                    data-username="{{ $user->username }}" 
                                    data-name="{{ $user->name }}"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#deleteAccountModal">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Account Modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteAccountModalLabel"><i class="fas fa-exclamation-triangle me-2"></i>Delete Account</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="fw-bold">Are you sure you want to delete this account?</p>
                <p>Account: <span id="accountToDelete"></span></p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    This action cannot be undone. All data associated with this account will be permanently deleted.
                </div>
                <form id="deleteAccountForm" method="POST" action="{{ route('user.delete-from-login') }}">
                    @csrf
                    <input type="hidden" name="username" id="usernameInput">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="deleteAccountForm" class="btn btn-danger">
                    <i class="fas fa-trash-alt me-1"></i> Delete Account
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .btn-wine {
        background-color: #8B2332;
        border-color: #8B2332;
        color: white;
    }
    
    .btn-wine:hover {
        background-color: #6d1a27;
        border-color: #6d1a27;
        color: white;
    }
    
    .text-wine {
        color: #8B2332 !important;
    }
    
    .gold-hr {
        border-top: 1px solid #DAA520;
        opacity: 0.5;
    }
    
    .account-list {
        max-width: 400px;
        margin: 0 auto;
    }
    
    .user-select-btn {
        background-color: #f9f6f0;
        border: 1px solid #e5e0d5;
        border-radius: 8px;
        padding: 10px 15px;
        transition: all 0.2s ease;
        text-align: left;
    }
    
    .user-select-btn:hover {
        background-color: #f5f1e8;
        border-color: #DAA520;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.05);
    }
    
    .account-icon {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background-color: #DAA520;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
        flex-shrink: 0;
    }
    
    .account-details {
        display: flex;
        flex-direction: column;
    }
    
    .account-name {
        font-weight: 600;
        color: #333;
    }
    
    .account-username {
        color: #777;
        font-size: 0.85rem;
    }
    
    .available-accounts h5 {
        color: #8B2332;
        font-weight: 600;
    }
</style>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Handle user selection
    $('.user-select-btn').on('click', function() {
        const username = $(this).data('username');
        $('#username').val(username);
        $('#password').focus();
    });
    
    // Stop propagation to prevent selecting the account when clicking the delete button
    $('.delete-account-btn').on('click', function(e) {
        e.stopPropagation();
        const username = $(this).data('username');
        const name = $(this).data('name');
        
        // Set the username in the modal and form
        $('#accountToDelete').text(name + ' (' + username + ')');
        $('#usernameInput').val(username);
    });
    
    // Ensure modal backdrop is removed when modal is closed
    $('#deleteAccountModal').on('hidden.bs.modal', function () {
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open');
        $('body').css('padding-right', '');
    });
});
</script>
@endsection
