@extends('layouts.app')

@section('title', 'Admin - Wine List')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="fas fa-wine-bottle me-2"></i>Wine Database</h4>
                <a href="{{ route('admin.dataset.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus me-1"></i> Add New Wine
                </a>
            </div>
            <div class="card-body">
                <!-- Search and Filter -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <form action="{{ route('admin.dataset.list') }}" method="GET" class="d-flex">
                            <input type="text" name="search" class="form-control me-2" 
                                placeholder="Search wines..." value="{{ $search ?? '' }}">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="{{ route('admin.dataset.list') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-sync-alt me-1"></i> Reset Filters
                        </a>
                    </div>
                </div>
                
                <!-- Wine Table -->
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>
                                    <a href="{{ route('admin.dataset.list', ['sort_by' => 'name', 'sort_dir' => $sortField == 'name' && $sortDirection == 'asc' ? 'desc' : 'asc', 'search' => $search]) }}" class="text-white text-decoration-none">
                                        Name
                                        @if($sortField == 'name')
                                            <i class="fas fa-sort-{{ $sortDirection == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('admin.dataset.list', ['sort_by' => 'type', 'sort_dir' => $sortField == 'type' && $sortDirection == 'asc' ? 'desc' : 'asc', 'search' => $search]) }}" class="text-white text-decoration-none">
                                        Type
                                        @if($sortField == 'type')
                                            <i class="fas fa-sort-{{ $sortDirection == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('admin.dataset.list', ['sort_by' => 'vintage', 'sort_dir' => $sortField == 'vintage' && $sortDirection == 'asc' ? 'desc' : 'asc', 'search' => $search]) }}" class="text-white text-decoration-none">
                                        Vintage
                                        @if($sortField == 'vintage')
                                            <i class="fas fa-sort-{{ $sortDirection == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('admin.dataset.list', ['sort_by' => 'price', 'sort_dir' => $sortField == 'price' && $sortDirection == 'asc' ? 'desc' : 'asc', 'search' => $search]) }}" class="text-white text-decoration-none">
                                        Price
                                        @if($sortField == 'price')
                                            <i class="fas fa-sort-{{ $sortDirection == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>Region</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($wines as $wine)
                                <tr>
                                    <td>{{ $wine->name }}</td>
                                    <td>{{ $wine->type ?? 'N/A' }}</td>
                                    <td>{{ $wine->vintage ?? 'N/A' }}</td>
                                    <td>${{ number_format($wine->price, 2) }}</td>
                                    <td>{{ $wine->region ?? 'N/A' }}</td>
                                    <td>
                                        <a href="{{ route('admin.dataset.edit', $wine->id) }}" class="btn btn-sm btn-outline-primary me-1">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.dataset.delete', $wine->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                onclick="return confirm('Are you sure you want to delete this wine?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="alert alert-info mb-0">
                                            No wines found. <a href="{{ route('admin.dataset.create') }}">Add a wine</a> or 
                                            <a href="{{ route('admin.dataset.import.form') }}">import from CSV</a>.
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $wines->appends(['search' => $search, 'sort_by' => $sortField, 'sort_dir' => $sortDirection])->links() }}
                </div>
                
                <div class="text-center mt-4">
                    <a href="{{ route('admin.dataset.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Dataset Management
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 