@extends('layouts.app')

@section('title', 'Admin - Edit Wine')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Wine</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.dataset.update', $wine->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Wine Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                id="name" name="name" value="{{ old('name', $wine->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-3">
                            <label for="type" class="form-label">Type</label>
                            <input type="text" class="form-control @error('type') is-invalid @enderror" 
                                id="type" name="type" value="{{ old('type', $wine->type) }}">
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-3">
                            <label for="vintage" class="form-label">Vintage</label>
                            <input type="text" class="form-control @error('vintage') is-invalid @enderror" 
                                id="vintage" name="vintage" value="{{ old('vintage', $wine->vintage) }}">
                            @error('vintage')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="price" class="form-label">Price</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                    id="price" name="price" value="{{ old('price', $wine->price) }}" step="0.01" min="0">
                            </div>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-3">
                            <label for="grape_variety" class="form-label">Grape Variety</label>
                            <input type="text" class="form-control @error('grape_variety') is-invalid @enderror" 
                                id="grape_variety" name="grape_variety" value="{{ old('grape_variety', $wine->grape_variety) }}">
                            @error('grape_variety')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-3">
                            <label for="region" class="form-label">Region</label>
                            <input type="text" class="form-control @error('region') is-invalid @enderror" 
                                id="region" name="region" value="{{ old('region', $wine->region) }}">
                            @error('region')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-3">
                            <label for="country" class="form-label">Country</label>
                            <input type="text" class="form-control @error('country') is-invalid @enderror" 
                                id="country" name="country" value="{{ old('country', $wine->country) }}">
                            @error('country')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="flavor_profile" class="form-label">Flavor Profile</label>
                            <textarea class="form-control @error('flavor_profile') is-invalid @enderror" 
                                id="flavor_profile" name="flavor_profile" rows="3">{{ old('flavor_profile', $wine->flavor_profile) }}</textarea>
                            @error('flavor_profile')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="food_pairings" class="form-label">Food Pairings</label>
                            <textarea class="form-control @error('food_pairings') is-invalid @enderror" 
                                id="food_pairings" name="food_pairings" rows="3">{{ old('food_pairings', $wine->food_pairings) }}</textarea>
                            @error('food_pairings')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label for="tasting_notes" class="form-label">Tasting Notes</label>
                            <textarea class="form-control @error('tasting_notes') is-invalid @enderror" 
                                id="tasting_notes" name="tasting_notes" rows="4">{{ old('tasting_notes', $wine->tasting_notes) }}</textarea>
                            @error('tasting_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="alcohol_content" class="form-label">Alcohol Content</label>
                                <input type="text" class="form-control @error('alcohol_content') is-invalid @enderror" 
                                    id="alcohol_content" name="alcohol_content" value="{{ old('alcohol_content', $wine->alcohol_content) }}">
                                @error('alcohol_content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="image_path" class="form-label">Image Path</label>
                                <input type="text" class="form-control @error('image_path') is-invalid @enderror" 
                                    id="image_path" name="image_path" value="{{ old('image_path', $wine->image_path) }}">
                                @error('image_path')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save Changes
                        </button>
                        <a href="{{ route('admin.dataset.list') }}" class="btn btn-outline-secondary ms-2">
                            <i class="fas fa-arrow-left me-2"></i>Back to Wine List
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 