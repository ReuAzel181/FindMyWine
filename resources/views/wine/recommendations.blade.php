@extends('layouts.app')

@section('title', 'Your Wine Recommendations')

@section('content')
<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="fas fa-wine-glass-alt me-2"></i>
                    Your Wine Recommendations
                </h4>
            </div>
            <div class="card-body">
                <p class="lead text-center mb-4">
                    Based on your preferences, we've curated these exceptional wines just for you.
                </p>
                
                <div class="mb-4">
                    <div class="wine-attributes">
                        <h5 class="mb-3">Your Search Criteria:</h5>
                        <dl class="row mb-0">
                            @if(!empty($criteria['types']))
                                <dt class="col-sm-3">Wine Types:</dt>
                                <dd class="col-sm-9">{{ implode(', ', (array)$criteria['types']) }}</dd>
                            @endif
                            
                            @if(!empty($criteria['flavors']))
                                <dt class="col-sm-3">Flavors:</dt>
                                <dd class="col-sm-9">{{ is_array($criteria['flavors']) ? implode(', ', $criteria['flavors']) : $criteria['flavors'] }}</dd>
                            @endif
                            
                            @if(!empty($criteria['food_pairings']))
                                <dt class="col-sm-3">Food Pairings:</dt>
                                <dd class="col-sm-9">{{ is_array($criteria['food_pairings']) ? implode(', ', $criteria['food_pairings']) : $criteria['food_pairings'] }}</dd>
                            @endif
                            
                            @if(!empty($criteria['price_min']) || !empty($criteria['price_max']))
                                <dt class="col-sm-3">Price Range:</dt>
                                <dd class="col-sm-9">
                                    @if(!empty($criteria['price_min']) && !empty($criteria['price_max']))
                                        ₱{{ number_format($criteria['price_min'], 2) }} - ₱{{ number_format($criteria['price_max'], 2) }}
                                    @elseif(!empty($criteria['price_min']))
                                        ₱{{ number_format($criteria['price_min'], 2) }} and up
                                    @else
                                        Up to ₱{{ number_format($criteria['price_max'], 2) }}
                                    @endif
                                </dd>
                            @endif
                            
                            @if(!empty($criteria['regions']))
                                <dt class="col-sm-3">Regions:</dt>
                                <dd class="col-sm-9">{{ implode(', ', (array)$criteria['regions']) }}</dd>
                            @endif
                            
                            @if($randomMode)
                                <dt class="col-sm-3">Mode:</dt>
                                <dd class="col-sm-9">Random Selection</dd>
                            @endif
                        </dl>
                    </div>
                </div>
                
                <div class="row">
                    @forelse($recommendations as $wine)
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 wine-card">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $wine->name }}</h5>
                                    <h6 class="card-subtitle mb-3">
                                        <span class="badge bg-primary me-2">{{ $wine->type ?? 'N/A' }}</span>
                                        <span class="badge bg-secondary">{{ $wine->vintage ?? 'N/A' }}</span>
                                    </h6>
                                    
                                    <div class="wine-attributes mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <strong class="text-wine-burgundy">Price:</strong>
                                            <span class="badge bg-success">₱{{ number_format($wine->price, 2) }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <strong class="text-wine-burgundy">Region:</strong>
                                            {{ $wine->region ?? 'N/A' }}, {{ $wine->country ?? 'N/A' }}
                                        </div>
                                        <div>
                                            <strong class="text-wine-burgundy">Grape:</strong>
                                            {{ $wine->grape_variety ?? 'N/A' }}
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <h6 class="text-wine-burgundy mb-2">Tasting Notes:</h6>
                                        <p class="small">{{ $wine->tasting_notes ?? 'No tasting notes available.' }}</p>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <h6 class="text-wine-burgundy mb-2">Food Pairings:</h6>
                                        <p class="small">{{ $wine->food_pairings ?? 'No food pairing information available.' }}</p>
                                    </div>
                                    
                                    <!-- Rating Section -->
                                    <div class="mt-3 pt-3 border-top">
                                        <h6 class="mb-2">Rate this wine:</h6>
                                        <div class="star-rating mb-2" data-wine-id="{{ $wine->id }}">
                                            @for($i = 1; $i <= 5; $i++)
                                                <span class="star" data-rating="{{ $i }}" title="{{ $i }} star{{ $i > 1 ? 's' : '' }}">★</span>
                                            @endfor
                                        </div>
                                        <textarea class="form-control form-control-sm mb-2 rating-comment" 
                                            placeholder="Share your thoughts about this wine (optional)"
                                            rows="2"></textarea>
                                        <button class="btn btn-sm btn-outline-primary save-rating" data-wine-id="{{ $wine->id }}">
                                            <i class="fas fa-save me-1"></i> Save Rating
                                        </button>
                                        <div class="rating-status small mt-1"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                No wines matched your criteria. Please try adjusting your preferences.
                            </div>
                        </div>
                    @endforelse
                </div>
                
                <div class="text-center mt-4">
                    <a href="{{ route('wine.home') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-search me-1"></i> Try Another Search
                    </a>
                    
                    @if($userProfile)
                        <a href="{{ route('wine.profile.export') }}" 
                            class="btn btn-outline-primary btn-lg ms-2">
                            <i class="fas fa-download me-1"></i> Export Your Profile
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Handle star rating selection
    $('.star').on('mouseover', function() {
        const rating = $(this).data('rating');
        const stars = $(this).parent().find('.star');
        
        stars.removeClass('filled');
        for (let i = 0; i < rating; i++) {
            $(stars[i]).addClass('filled');
        }
    });
    
    $('.star').on('mouseout', function() {
        const starsContainer = $(this).parent();
        const selectedRating = starsContainer.attr('data-selected-rating') || 0;
        const stars = starsContainer.find('.star');
        
        stars.removeClass('filled');
        for (let i = 0; i < selectedRating; i++) {
            $(stars[i]).addClass('filled');
        }
    });
    
    $('.star').on('click', function() {
        const rating = $(this).data('rating');
        const starsContainer = $(this).parent();
        
        starsContainer.attr('data-selected-rating', rating);
        
        // Trigger mouseout to update display
        $(this).mouseout();
    });
    
    // Handle rating submission
    $('.save-rating').on('click', function() {
        const button = $(this);
        const wineId = button.data('wine-id');
        const starsContainer = $(`.star-rating[data-wine-id="${wineId}"]`);
        const rating = starsContainer.attr('data-selected-rating');
        const comment = button.closest('.card-body').find('.rating-comment').val();
        const statusElement = button.closest('.card-body').find('.rating-status');
        
        if (!rating) {
            statusElement.html('<span class="text-danger">Please select a rating.</span>');
            return;
        }
        
        // Show loading status
        statusElement.html('<span class="text-info">Saving your rating...</span>');
        
        // CSRF token
        const token = $('meta[name="csrf-token"]').attr('content');
        
        // Send rating to server
        $.ajax({
            url: '{{ route('wine.rate') }}',
            type: 'POST',
            data: {
                _token: token,
                wine_id: wineId,
                rating: rating,
                comment: comment
            },
            success: function(response) {
                statusElement.html('<span class="text-success">Rating saved successfully!</span>');
                // Redirect to home page after short delay to show success message
                setTimeout(() => {
                    window.location.href = response.redirect || '{{ route('home') }}';
                }, 1500);
            },
            error: function(xhr) {
                let errorMessage = 'Failed to save rating. Please try again.';
                
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                    
                    // If not logged in, provide login link
                    if (errorMessage.includes('Unauthenticated') || errorMessage.includes('log in')) {
                        errorMessage = 'Please <a href="{{ route('login') }}">log in</a> to save your rating.';
                    }
                }
                
                statusElement.html(`<span class="text-danger">${errorMessage}</span>`);
            }
        });
    });
});
</script>
@endsection 