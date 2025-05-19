@extends('layouts.app')

@section('title', 'Admin - Dataset Management')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-database me-2 text-gold"></i>Wine Dataset Management</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title">Dataset Information</h5>
                                    <p class="card-text">
                                        <strong>Total Wines:</strong> {{ $wineCount }}
                                    </p>
                                    <p class="text-muted small">
                                        The wine dataset is stored locally and does not require internet connectivity for operation.
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title">Dataset Management</h5>
                                    
                                    <h6 class="mt-3 mb-2 text-dark">Import Options:</h6>
                                    
                                    <a href="{{ route('admin.dataset.import.form') }}" class="btn btn-primary w-100 text-start py-2 mb-2" style="background-color: #0d6efd;">
                                        <i class="fas fa-file-csv me-2"></i> Import CSV
                                    </a>
                                    
                                    <a href="{{ route('admin.dataset.import.form') }}?type=additional" class="btn btn-primary w-100 text-start py-2 mb-2" style="background-color: #0d6efd;">
                                        <i class="fas fa-star me-2"></i> Import Additional Wines
                                    </a>
                                    
                                    <a href="{{ url('/artisan/command/seed:additional-wines') }}" 
                                       onclick="return confirm('Are you sure you want to run the additional wines seeder?');"
                                       class="btn btn-primary w-100 text-start py-2 mb-3" style="background-color: #0d6efd;">
                                        <i class="fas fa-seedling me-2"></i> Run Additional Wines Seeder
                                    </a>
                                    
                                    <h6 class="mt-4 mb-2 text-dark">Other Options:</h6>
                                    
                                    <a href="{{ route('admin.dataset.export') }}" class="btn btn-outline-primary w-100 text-start py-2 mb-2">
                                        <i class="fas fa-file-export me-2"></i> Export Wines to CSV
                                    </a>
                                    <a href="{{ route('admin.dataset.list') }}" class="btn btn-outline-primary w-100 text-start py-2">
                                        <i class="fas fa-list me-2"></i> View & Manage Wines
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <h5><i class="fas fa-info-circle me-2"></i>About the Dataset</h5>
                        <p>
                            The Wine Recommender uses a curated, verified dataset of wines that is stored locally in your database.
                            As an administrator, you can:
                        </p>
                        <ul>
                            <li>Import wines from a CSV file</li>
                            <li>Add additional wines without replacing existing ones</li>
                            <li>Export the current dataset to a CSV file</li>
                            <li>Manually add, edit, or remove wines</li>
                        </ul>
                        <p class="mb-0">
                            <strong>Note:</strong> The system does not require internet connectivity to operate, 
                            as all wine data is stored locally in your database.
                        </p>
                    </div>
                    
                    <div class="text-center mt-4">
                        <a href="{{ route('wine.home') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Wine Recommender
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 