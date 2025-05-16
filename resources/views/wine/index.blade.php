@extends('layouts.app')

@section('title', 'Wine Recommender')

@section('content')
<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><i class="fas fa-wine-glass-alt me-2"></i>Wine Recommender</h4>
            </div>
            <div class="card-body">
                <p class="lead">Find your perfect wine match by entering your preferences below.</p>
                <p>Remember, you must enter at least one preference (flavor, food pairing, or price range).</p>
                
                <form action="{{ route('wine.recommend') }}" method="POST">
                    @csrf
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="username" class="form-label">Your Name/Username</label>
                            <input type="text" class="form-control @error('username') is-invalid @enderror" 
                                id="username" name="username" required 
                                value="{{ old('username', session('username', '')) }}">
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">This will be used to save your preferences and recommendations.</div>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-light text-dark">
                                    <h5 class="mb-0">Wine Preferences</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="types" class="form-label">Wine Types</label>
                                        <select class="form-select" id="types" name="types[]" multiple>
                                            @if(isset($wineAttributes['types']))
                                                @foreach($wineAttributes['types'] as $type)
                                                    <option value="{{ $type }}" {{ in_array($type, old('types', [])) ? 'selected' : '' }}>
                                                        {{ $type }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <div class="form-text">Hold Ctrl/Cmd to select multiple types.</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="flavors" class="form-label">Preferred Flavors</label>
                                        <input type="text" class="form-control" id="flavors" name="flavors" 
                                            value="{{ old('flavors') }}" placeholder="e.g., fruity, oaky, spicy">
                                        <div class="form-text">Enter flavors separated by commas.</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="food_pairings" class="form-label">Food Pairings</label>
                                        <input type="text" class="form-control" id="food_pairings" name="food_pairings" 
                                            value="{{ old('food_pairings') }}" placeholder="e.g., steak, fish, pasta">
                                        <div class="form-text">Enter food pairings separated by commas.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-light text-dark">
                                    <h5 class="mb-0">Additional Criteria</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Price Range</label>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" class="form-control" id="price_min" name="price_min" 
                                                        value="{{ old('price_min') }}" min="0" step="0.01" placeholder="Min">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" class="form-control" id="price_max" name="price_max" 
                                                        value="{{ old('price_max') }}" min="0" step="0.01" placeholder="Max">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="regions" class="form-label">Regions</label>
                                        <select class="form-select" id="regions" name="regions[]" multiple>
                                            @if(isset($wineAttributes['regions']))
                                                @foreach($wineAttributes['regions'] as $region)
                                                    <option value="{{ $region }}" {{ in_array($region, old('regions', [])) ? 'selected' : '' }}>
                                                        {{ $region }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <div class="form-text">Hold Ctrl/Cmd to select multiple regions.</div>
                                    </div>
                                    
                                    <div class="form-check mt-3">
                                        <input class="form-check-input" type="checkbox" id="random_mode" name="random_mode" value="1" 
                                            {{ old('random_mode') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="random_mode">
                                            <strong>Random Mode</strong> - Get random recommendations within your price range
                                        </label>
                                        <div class="form-text">Useful if you want to discover new wines.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-search me-2"></i>Get Wine Recommendations
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    @php
        $userProfile = App\Models\UserProfile::where('username', session('username', ''))->first();
        $pastRecommendations = $userProfile ? $userProfile->pastRecommendations()->with('wine')->get() : collect();
    @endphp
    
    @if($userProfile && $pastRecommendations->isNotEmpty())
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><i class="fas fa-history me-2"></i>Your Past Recommendations</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($pastRecommendations as $recommendation)
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 wine-card">
                            <div class="card-body">
                                <h5 class="card-title">{{ $recommendation->wine->name }}</h5>
                                <h6 class="card-subtitle mb-2 text-muted">
                                    {{ $recommendation->wine->type }} | {{ $recommendation->wine->vintage }}
                                </h6>
                                <p class="card-text">
                                    <strong>Price:</strong> ${{ number_format($recommendation->wine->price, 2) }}<br>
                                    <strong>Region:</strong> {{ $recommendation->wine->region }}, {{ $recommendation->wine->country }}
                                </p>
                                <p class="small text-muted">
                                    Recommended on {{ $recommendation->recommended_at->format('M d, Y') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <div class="text-center mt-3">
                    <a href="{{ route('wine.profile.export', ['username' => $userProfile->username]) }}" class="btn btn-outline-primary">
                        <i class="fas fa-download me-1"></i> Export Your Profile
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    // Split comma-separated inputs into arrays
    function splitCommaSeparatedInput(input) {
        return input.split(',')
            .map(item => item.trim())
            .filter(item => item !== '');
    }
    
    // Handle form submission
    document.querySelector('form').addEventListener('submit', function(e) {
        // Get flavor input and convert to array
        const flavorsInput = document.getElementById('flavors');
        if (flavorsInput.value) {
            const flavorsArray = splitCommaSeparatedInput(flavorsInput.value);
            flavorsInput.value = flavorsArray.join(',');
        }
        
        // Get food pairings input and convert to array
        const foodPairingsInput = document.getElementById('food_pairings');
        if (foodPairingsInput.value) {
            const foodPairingsArray = splitCommaSeparatedInput(foodPairingsInput.value);
            foodPairingsInput.value = foodPairingsArray.join(',');
        }
        
        // Ensure at least one preference is set
        const hasTypes = document.getElementById('types').selectedOptions.length > 0;
        const hasFlavors = flavorsInput.value !== '';
        const hasFoodPairings = foodPairingsInput.value !== '';
        const hasPriceMin = document.getElementById('price_min').value !== '';
        const hasPriceMax = document.getElementById('price_max').value !== '';
        const hasRegions = document.getElementById('regions').selectedOptions.length > 0;
        
        if (!hasTypes && !hasFlavors && !hasFoodPairings && !hasPriceMin && !hasPriceMax && !hasRegions) {
            e.preventDefault();
            alert('Please enter at least one preference (e.g., flavor, food pairing, or price range).');
        }
    });
</script>
@endsection 