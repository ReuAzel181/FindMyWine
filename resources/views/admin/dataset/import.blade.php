@extends('layouts.app')

@section('title', isset($title) ? $title : 'Import Wine Dataset')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-white">
                    <h4 class="mb-0"><i class="fas fa-file-import me-2 text-gold"></i>{{ isset($title) ? $title : 'Import Wine Dataset' }}</h4>
                </div>
                <div class="card-body">
                    <p class="mb-4">
                        {{ isset($description) ? $description : 'Upload a CSV file containing wine data. This will replace all existing wines.' }}
                    </p>
                    
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    <form action="{{ route('admin.dataset.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <input type="hidden" name="import_type" value="{{ $type ?? 'standard' }}">
                        
                        <div class="mb-4">
                            <label for="csv_file" class="form-label">
                                <i class="fas fa-file-csv me-1 text-gold"></i> CSV File
                            </label>
                            <input type="file" class="form-control @error('csv_file') is-invalid @enderror" id="csv_file" name="csv_file">
                            @error('csv_file')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                            
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Accepted format: CSV with headers. 
                                @if(($type ?? 'standard') === 'additional')
                                    Will add wines to the existing database.
                                @else
                                    Will replace all wines in the database.
                                @endif
                            </div>
                        </div>
                        
                        @if(($type ?? 'standard') === 'standard')
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="clear_existing" name="clear_existing" value="1" checked>
                            <label class="form-check-label" for="clear_existing">
                                Clear existing wines before import
                            </label>
                            <div class="form-text text-danger">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                Warning: This will remove all existing wines and ratings!
                            </div>
                        </div>
                        @endif
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.dataset.list') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Back to List
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-file-import me-1"></i> Import Wines
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2 text-gold"></i>CSV Format Requirements</h5>
                </div>
                <div class="card-body">
                    <p>Your CSV file should include the following columns:</p>
                    <ul>
                        <li><strong>name</strong> - Wine name</li>
                        <li><strong>type</strong> - Wine type (Red, White, Rosé, Sparkling, Dessert)</li>
                        <li><strong>variety</strong> - Grape variety</li>
                        <li><strong>origin</strong> - Country or region of origin</li>
                        <li><strong>price</strong> - Price in ₱ (Philippine Peso)</li>
                        <li><strong>flavor_profile</strong> - Flavor descriptions (comma separated)</li>
                        <li><strong>food_pairings</strong> - Food pairings (comma separated)</li>
                        <li><strong>description</strong> - Wine description</li>
                        <li><strong>image_url</strong> - URL to wine image (optional)</li>
                        <li><strong>source_url</strong> - URL to source information (optional)</li>
                    </ul>
                    <div class="alert alert-info">
                        <i class="fas fa-lightbulb me-2"></i>
                        <strong>Tip:</strong> You can download the current dataset as a CSV file to see the expected format.
                        <div class="mt-2">
                            <a href="{{ route('admin.dataset.export') }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-download me-1"></i> Download Current Dataset
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 