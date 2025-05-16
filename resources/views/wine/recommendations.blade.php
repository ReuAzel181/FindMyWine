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
                <p class="lead">
                    Based on your preferences, we recommend the following wines:
                </p>
                
                <div class="mb-4">
                    <h5>Your Search Criteria:</h5>
                    <ul class="list-group list-group-flush">
                        @if(!empty($criteria['types']))
                            <li class="list-group-item">
                                <strong>Wine Types:</strong> {{ implode(', ', (array)$criteria['types']) }}
                            </li>
                        @endif
                        
                        @if(!empty($criteria['flavors']))
                            <li class="list-group-item">
                                <strong>Flavors:</strong> {{ is_array($criteria['flavors']) ? implode(', ', $criteria['flavors']) : $criteria['flavors'] }}
                            </li>
                        @endif
                        
                        @if(!empty($criteria['food_pairings']))
                            <li class="list-group-item">
                                <strong>Food Pairings:</strong> {{ is_array($criteria['food_pairings']) ? implode(', ', $criteria['food_pairings']) : $criteria['food_pairings'] }}
                            </li>
                        @endif
                        
                        @if(!empty($criteria['price_min']) || !empty($criteria['price_max']))
                            <li class="list-group-item">
                                <strong>Price Range:</strong> 
                                @if(!empty($criteria['price_min']) && !empty($criteria['price_max']))
                                    ${{ number_format($criteria['price_min'], 2) }} - ${{ number_format($criteria['price_max'], 2) }}
                                @elseif(!empty($criteria['price_min']))
                                    ${{ number_format($criteria['price_min'], 2) }} and up
                                @else
                                    Up to ${{ number_format($criteria['price_max'], 2) }}
                                @endif
                            </li>
                        @endif
                        
                        @if(!empty($criteria['regions']))
                            <li class="list-group-item">
                                <strong>Regions:</strong> {{ implode(', ', (array)$criteria['regions']) }}
                            </li>
                        @endif
                        
                        @if($randomMode)
                            <li class="list-group-item">
                                <strong>Mode:</strong> Random Selection
                            </li>
                        @endif
                    </ul>
                </div>
                
                <div class="row">
                    @forelse($recommendations as $wine)
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 wine-card">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $wine->name }}</h5>
                                    <h6 class="card-subtitle mb-2 text-muted">
                                        {{ $wine->type ?? 'N/A' }} | {{ $wine->vintage ?? 'N/A' }}
                                    </h6>
                                    
                                    <p class="card-text">
                                        <strong>Price:</strong> ${{ number_format($wine->price, 2) }}<br>
                                        <strong>Region:</strong> {{ $wine->region ?? 'N/A' }}, {{ $wine->country ?? 'N/A' }}<br>
                                        <strong>Grape Variety:</strong> {{ $wine->grape_variety ?? 'N/A' }}
                                    </p>
                                    
                                    <div class="mb-3">
                                        <strong>Tasting Notes:</strong>
                                        <p class="small">{{ $wine->tasting_notes ?? 'No tasting notes available.' }}</p>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <strong>Food Pairings:</strong>
                                        <p class="small">{{ $wine->food_pairings ?? 'No food pairing information available.' }}</p>
                                    </div>
                                    
                                    <!-- Rating Section -->
                                    <div class="mt-3 pt-3 border-top">
                                        <h6>Rate this wine:</h6>
                                        <div class="star-rating mb-2" data-wine-id="{{ $wine->id }}">
                                            @for($i = 1; $i <= 5; $i++)
                                                <span class="star" data-rating="{{ $i }}">★</span>
                                            @endfor
                                        </div>
                                        <textarea class="form-control form-control-sm mb-2 rating-comment" placeholder="Add a comment (optional)"></textarea>
                                        <button class="btn btn-sm btn-outline-primary save-rating" data-wine-id="{{ $wine->id }}">
                                            Save Rating
                                        </button>
                                        <div class="rating-status small mt-1"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-warning">
                                No wines matched your criteria. Please try adjusting your preferences.
                            </div>
                        </div>
                    @endforelse
                </div>
                
                <div class="text-center mt-4">
                    <a href="{{ route('wine.index') }}" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i> Try Another Search
                    </a>
                    
                    @if($userProfile)
                        <a href="{{ route('wine.profile.export', ['username' => $userProfile->username]) }}" class="btn btn-outline-primary ms-2">
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
        // Username from form
        const username = "{{ $userProfile->username }}";
        
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
            const wineId = $(this).data('wine-id');
            const starsContainer = $(`.star-rating[data-wine-id="${wineId}"]`);
            const rating = starsContainer.attr('data-selected-rating');
            const comment = $(this).closest('.card-body').find('.rating-comment').val();
            const statusElement = $(this).closest('.card-body').find('.rating-status');
            
            if (!rating) {
                statusElement.html('<span class="text-danger">Please select a rating.</span>');
                return;
            }
            
            // Show loading status
            statusElement.html('<span class="text-info">Saving your rating...</span>');
            
            // Submit rating via AJAX
            $.ajax({
                url: "{{ route('wine.rate') }}",
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    username: username,
                    wine_id: wineId,
                    rating: rating,
                    comment: comment
                },
                success: function(response) {
                    statusElement.html('<span class="text-success">Rating saved!</span>');
                    setTimeout(() => {
                        statusElement.html('');
                    }, 3000);
                },
                error: function(xhr) {
                    statusElement.html('<span class="text-danger">Error saving rating. Please try again.</span>');
                }
            });
        });
    });
</script>
@endsection 