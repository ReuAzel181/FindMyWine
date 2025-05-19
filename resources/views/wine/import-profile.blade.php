@extends('layouts.app')

@section('title', 'Import Profile')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><i class="fas fa-upload me-2"></i>Import User Profile</h4>
            </div>
            <div class="card-body">
                <p class="lead">
                    You can import a previously exported user profile to restore your preferences and ratings.
                </p>
                
                <form action="{{ route('wine.profile.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="profile_file" class="form-label">Select Profile File</label>
                        <input type="file" class="form-control @error('profile_file') is-invalid @enderror" 
                            id="profile_file" name="profile_file" accept=".json" required>
                        
                        @error('profile_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        
                        <div class="form-text">
                            Please select a valid profile file (JSON format) that was previously exported from the Wine Recommender.
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Note:</strong> Importing a profile will restore your preferences, past recommendations, and wine ratings.
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload me-2"></i>Import Profile
                        </button>
                        <a href="{{ route('wine.home') }}" class="btn btn-outline-secondary ms-2">
                            <i class="fas fa-arrow-left me-2"></i>Back to Home
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 