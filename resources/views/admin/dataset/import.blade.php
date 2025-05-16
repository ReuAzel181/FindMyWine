@extends('layouts.app')

@section('title', 'Admin - Import Wine Dataset')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><i class="fas fa-file-import me-2"></i>Import Wine Dataset</h4>
            </div>
            <div class="card-body">
                <p class="lead">
                    Import wines from a CSV file into the database.
                </p>
                
                <form action="{{ route('admin.dataset.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="wine_file" class="form-label">Select CSV File</label>
                        <input type="file" class="form-control @error('wine_file') is-invalid @enderror" 
                            id="wine_file" name="wine_file" accept=".csv,.txt" required>
                        
                        @error('wine_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        
                        <div class="form-text">
                            The CSV file should contain headers and wine data in comma-separated format.
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="clear_existing" name="clear_existing" value="1">
                            <label class="form-check-label" for="clear_existing">
                                Clear existing wines before import
                            </label>
                        </div>
                        <div class="form-text text-danger">
                            Warning: This will delete all existing wines from the database.
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">CSV Format Guidelines</h5>
                            </div>
                            <div class="card-body">
                                <p>Your CSV file should include the following columns (header names are flexible):</p>
                                <ul>
                                    <li><strong>name</strong> - The wine name (required)</li>
                                    <li><strong>type</strong> - Wine type (e.g., Red, White, Rosé, Sparkling)</li>
                                    <li><strong>vintage</strong> - Year of production</li>
                                    <li><strong>price</strong> - Price in numerical format</li>
                                    <li><strong>grape_variety</strong> - Grape variety</li>
                                    <li><strong>region</strong> - Wine region</li>
                                    <li><strong>country</strong> - Country of origin</li>
                                    <li><strong>flavor_profile</strong> - Descriptive flavor notes</li>
                                    <li><strong>food_pairings</strong> - Recommended food pairings</li>
                                    <li><strong>tasting_notes</strong> - Detailed tasting notes</li>
                                    <li><strong>alcohol_content</strong> - Alcohol percentage</li>
                                    <li><strong>image_path</strong> - Path to image (optional)</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload me-2"></i>Upload and Import
                        </button>
                        <a href="{{ route('admin.dataset.index') }}" class="btn btn-outline-secondary ms-2">
                            <i class="fas fa-arrow-left me-2"></i>Back to Dataset Management
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 