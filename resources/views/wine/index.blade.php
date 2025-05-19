@extends('layouts.app')

@section('title', 'Wine Recommender')

@section('content')
<div class="row g-3">
    <div class="col-md-9">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h4 class="mb-0 text-center"><i class="fas fa-wine-glass-alt me-2"></i>Wine Recommender</h4>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <h2 class="display-5 mb-3">Find Your Perfect Wine</h2>
                    <p class="lead">Tell us your preferences, and we'll recommend wines that match your taste.</p>
                    <p class="text-muted">Enter at least one preference (flavor, food pairing, or price range) to get started.</p>
                    
                    <!-- Random Wine Button -->
                    <form action="{{ route('wine.recommend') }}" method="POST" class="mt-3 mb-4">
                        @csrf
                        <input type="hidden" name="random_mode" value="1">
                        <button type="submit" class="btn btn-lg btn-dark pulse-button">
                            <i class="fas fa-random me-2" style="font-size: 1.2rem !important;"></i><span style="font-size: 1.1rem;">Surprise Me with Random Wines</span>
                        </button>
                    </form>
                </div>
                
                <form action="{{ route('wine.recommend') }}" method="POST">
                    @csrf
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-header bg-light text-dark border-0">
                                    <h5 class="mb-0 text-center">
                                        <i class="fas fa-wine-bottle me-2"></i>Wine Preferences
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-4">
                                        <label for="types" class="form-label">
                                            <i class="fas fa-wine-glass-alt text-gold"></i>
                                            Wine Types
                                        </label>
                                        <select class="form-select custom-select" id="types" name="types[]" multiple>
                                            @if(isset($wineAttributes['types']))
                                                @foreach($wineAttributes['types'] as $type)
                                                    <option value="{{ $type }}" {{ in_array($type, old('types', [])) ? 'selected' : '' }}>
                                                        {{ $type }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <div class="form-text">
                                            <i class="fas fa-info-circle"></i>
                                            Hold Ctrl/Cmd to select multiple types
                                        </div>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label for="flavors" class="form-label">
                                            <i class="fas fa-wine-glass text-gold"></i>
                                            Preferred Flavors
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-wine-glass-alt"></i></span>
                                            <input type="text" class="form-control" id="flavors" name="flavors" 
                                                value="{{ old('flavors') }}" 
                                                placeholder="e.g., fruity, oaky, spicy">
                                        </div>
                                        <div class="form-text">
                                            <i class="fas fa-info-circle"></i>
                                            Enter flavors separated by commas
                                        </div>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label for="food_pairings" class="form-label">
                                            <i class="fas fa-utensils text-gold"></i>
                                            Food Pairings
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-utensils"></i></span>
                                            <input type="text" class="form-control" id="food_pairings" name="food_pairings" 
                                                value="{{ old('food_pairings') }}" 
                                                placeholder="e.g., steak, fish, pasta">
                                        </div>
                                        <div class="form-text">
                                            <i class="fas fa-info-circle"></i>
                                            Enter food pairings separated by commas
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-header bg-light text-dark border-0">
                                    <h5 class="mb-0 text-center">
                                        <i class="fas fa-sliders-h me-2"></i>Additional Criteria
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-4">
                                        <label class="form-label">
                                            <i class="fas fa-tag text-gold"></i>
                                            Price Range
                                        </label>
                                        <div class="row g-2">
                                            <div class="col-md-6">
                                                <div class="input-group">
                                                    <span class="input-group-text">₱</span>
                                                    <input type="number" class="form-control" id="price_min" name="price_min" 
                                                        value="{{ old('price_min') }}" min="0" step="0.01" placeholder="Min">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="input-group">
                                                    <span class="input-group-text">₱</span>
                                                    <input type="number" class="form-control" id="price_max" name="price_max" 
                                                        value="{{ old('price_max') }}" min="0" step="0.01" placeholder="Max">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-text">
                                            <i class="fas fa-info-circle"></i>
                                            Optional: Set your budget range
                                        </div>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label for="regions" class="form-label">
                                            <i class="fas fa-globe-europe text-gold"></i>
                                            Wine Regions
                                        </label>
                                        <select class="form-select custom-select" id="regions" name="regions[]" multiple>
                                            @if(isset($wineAttributes['regions']))
                                                @foreach($wineAttributes['regions'] as $region)
                                                    <option value="{{ $region }}" {{ in_array($region, old('regions', [])) ? 'selected' : '' }}>
                                                        {{ $region }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <div class="form-text">
                                            <i class="fas fa-info-circle"></i>
                                            Hold Ctrl/Cmd to select multiple regions
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary btn-lg px-5">
                            <i class="fas fa-search me-2"></i>Find My Perfect Wine
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        @php
            $user = Auth::user();
            $ratedWines = collect();
            
            // Check if user has a profile
            if ($user && $user->profile) {
                // Get the user's rated wines
                $ratedWines = \App\Models\WineRating::where('user_profile_id', $user->profile->id)
                    ->with('wine')
                    ->orderBy('updated_at', 'desc')
                    ->take(6)
                    ->get();
            }
        @endphp
        
        @if($ratedWines->count() > 0)
        <div class="card border-0 shadow-sm h-100 rated-wines-container">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-center align-items-center">
                    <span><i class="fas fa-star text-warning me-2"></i>{{ __('Your Rated Wines') }}</span>
                </div>
            </div>

            <div class="card-body p-0 rated-wines-list">
                <div class="list-group list-group-flush">
                    @foreach($ratedWines as $rating)
                        <div class="list-group-item border-0 rated-wine-item" data-rating-id="{{ $rating->id }}">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-1">{{ $rating->wine->name }}</h5>
                                <div class="rated-stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span class="star edit-rating" data-rating="{{ $i }}" data-rating-id="{{ $rating->id }}" data-wine-id="{{ $rating->wine->id }}">
                                            <i class="fas fa-star {{ $i <= $rating->rating ? 'filled' : '' }}"></i>
                                        </span>
                                    @endfor
                                    <i class="fas fa-pencil-alt ms-2 edit-comment-btn" title="Edit comment" data-rating-id="{{ $rating->id }}"></i>
                                </div>
                            </div>
                            <p class="mb-1">
                                <span class="badge bg-primary me-2">{{ $rating->wine->type ?? 'N/A' }}</span>
                                <span class="badge bg-secondary">{{ $rating->wine->vintage ?? 'N/A' }}</span>
                            </p>
                            <div class="comment-section" id="comment-section-{{ $rating->id }}">
                                @if($rating->comment)
                                    <p class="mb-0 small text-muted comment-text" id="comment-text-{{ $rating->id }}">{{ $rating->comment }}</p>
                                @else
                                    <p class="mb-0 small text-muted comment-text" id="comment-text-{{ $rating->id }}"><em>No comment</em></p>
                                @endif
                                <div class="edit-comment-form d-none" id="edit-form-{{ $rating->id }}">
                                    <textarea class="form-control form-control-sm mt-2 mb-2" id="comment-input-{{ $rating->id }}">{{ $rating->comment }}</textarea>
                                    <div class="d-flex justify-content-end">
                                        <button class="btn btn-sm btn-secondary me-2 cancel-edit" data-rating-id="{{ $rating->id }}">Cancel</button>
                                        <button class="btn btn-sm btn-primary save-comment" data-rating-id="{{ $rating->id }}" data-wine-id="{{ $rating->wine->id }}">Save</button>
                                    </div>
                                </div>
                            </div>
                            <small class="text-muted">Rated {{ $rating->updated_at->diffForHumans() }}</small>
                        </div>
                    @endforeach
                </div>
            </div>
            
            @if($user->profile && $user->profile->wineRatings()->count() > 6)
                <div class="card-footer bg-white border-0 text-center p-3">
                    <a href="#" class="btn btn-sm btn-outline-primary">
                        View All Rated Wines
                    </a>
                </div>
            @endif
        </div>
        @else
        <div class="card border-0 shadow-sm h-100 rated-wines-container">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-center align-items-center">
                    <span><i class="fas fa-star text-warning me-2"></i>{{ __('Your Rated Wines') }}</span>
                </div>
            </div>

            <div class="card-body d-flex flex-column justify-content-center align-items-center text-center p-4">
                <div class="empty-ratings-icon mb-3">
                    <i class="fas fa-wine-glass-alt fa-3x text-muted"></i>
                </div>
                <h5 class="mb-2">No Rated Wines Yet</h5>
                <p class="text-muted mb-4">Start rating wines to build your personal collection of favorites.</p>
                <div class="empty-star-rating mb-3">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="fas fa-star text-muted me-1"></i>
                    @endfor
                </div>
                <p class="small fst-italic">Your rated wines will appear here after you receive and rate recommendations.</p>
            </div>
            
            <div class="card-footer bg-white border-0 text-center p-3">
                <p class="text-muted small mb-0">Fill out your preferences on the left to discover new wines</p>
            </div>
        </div>
        @endif
    </div>
    
    @php
        $userProfile = Auth::user()->profile;
        $pastRecommendations = $userProfile ? $userProfile->pastRecommendations()->with('wine')->get() : collect();
    @endphp
    
    @if($userProfile && $pastRecommendations->isNotEmpty())
    <div class="col-md-12 mt-3">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h4 class="mb-0 text-center">
                    <i class="fas fa-history me-2"></i>Your Past Recommendations
                </h4>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @foreach($pastRecommendations as $recommendation)
                    <div class="col-md-4">
                        <div class="card h-100 wine-card border-0 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">{{ $recommendation->wine->name }}</h5>
                                <h6 class="card-subtitle mb-3">
                                    <span class="badge bg-primary me-2">{{ $recommendation->wine->type }}</span>
                                    <span class="badge bg-secondary">{{ $recommendation->wine->vintage }}</span>
                                </h6>
                                <div class="wine-attributes">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <strong class="text-wine-burgundy">Price:</strong>
                                        <span class="badge bg-success">₱{{ number_format($recommendation->wine->price, 2) }}</span>
                                    </div>
                                    <div>
                                        <strong class="text-wine-burgundy">Region:</strong>
                                        {{ $recommendation->wine->region }}, {{ $recommendation->wine->country }}
                                    </div>
                                </div>
                                <p class="small text-muted mt-2 mb-0">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    Recommended on {{ $recommendation->recommended_at->format('M d, Y') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <div class="text-center mt-4">
                    <a href="{{ route('wine.profile.export') }}" 
                        class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-download me-2"></i>Export Your Profile
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
$(document).ready(function() {
    // Custom styling for the multi-select options
    $('.custom-select option').each(function() {
        const optionText = $(this).text().trim();
        // Add icon based on content
        if (optionText === 'Red' || optionText === 'White' || optionText === 'Rosé' || optionText === 'Sparkling') {
            $(this).attr('data-icon', 'wine-glass-alt');
        } else if (optionText === 'Dessert') {
            $(this).attr('data-icon', 'cookie');
        } else {
            $(this).attr('data-icon', 'map-marker-alt');
        }
    });
    
    // Add animation to the submit button
    $('form').on('submit', function() {
        const button = $(this).find('button[type="submit"]');
        button.prop('disabled', true)
            .html('<span class="loading me-2"></span>Finding your perfect wine...');
    });
    
    // Function to show status message
    function showStatusMessage(element, message) {
        // Remove any existing status messages first
        element.find('.status-message').remove();
        
        // Add the new message
        const statusMessage = $(`<div class="status-message">${message}</div>`);
        element.append(statusMessage);
        
        // Message will automatically fade out via CSS animation
    }
    
    // Edit rating functionality
    $('.edit-rating').on('click', function() {
        const ratingValue = $(this).data('rating');
        const ratingId = $(this).data('rating-id');
        const wineId = $(this).data('wine-id');
        const ratedWineItem = $(this).closest('.rated-wine-item');
        
        // Update visual stars immediately
        $(this).closest('.rated-stars').find('.fa-star').removeClass('filled');
        $(this).closest('.rated-stars').find('.edit-rating').each(function() {
            if ($(this).data('rating') <= ratingValue) {
                $(this).find('.fa-star').addClass('filled');
            }
        });
        
        // Update rating via AJAX
        $.ajax({
            url: "{{ route('wine.rate') }}",
            method: "POST",
            data: {
                wine_id: wineId,
                rating: ratingValue,
                // Keep existing comment if there is one
                comment: $(`#comment-input-${ratingId}`).val() || $(`#comment-text-${ratingId}`).text(),
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                if (response.success) {
                    showStatusMessage(ratedWineItem, "Rating updated!");
                }
            },
            error: function(xhr) {
                console.error('Error updating rating:', xhr.responseText);
                alert('There was an error updating your rating. Please try again.');
            }
        });
    });
    
    // Edit comment
    $('.edit-comment-btn').on('click', function() {
        const ratingId = $(this).data('rating-id');
        $(`#comment-text-${ratingId}`).addClass('d-none');
        $(`#edit-form-${ratingId}`).removeClass('d-none');
    });
    
    // Cancel edit
    $('.cancel-edit').on('click', function() {
        const ratingId = $(this).data('rating-id');
        $(`#edit-form-${ratingId}`).addClass('d-none');
        $(`#comment-text-${ratingId}`).removeClass('d-none');
    });
    
    // Save comment
    $('.save-comment').on('click', function() {
        const ratingId = $(this).data('rating-id');
        const wineId = $(this).data('wine-id');
        const comment = $(`#comment-input-${ratingId}`).val();
        const commentText = $(`#comment-text-${ratingId}`);
        const ratedWineItem = $(this).closest('.rated-wine-item');
        
        // Get the current rating from the filled stars
        const currentRating = ratedWineItem.find('.fa-star.filled').length;
        
        $.ajax({
            url: "{{ route('wine.rate') }}",
            method: "POST",
            data: {
                wine_id: wineId,
                rating: currentRating, 
                comment: comment,
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                if (response.success) {
                    // Update comment text and hide form
                    if (comment) {
                        commentText.text(comment);
                    } else {
                        commentText.html("<em>No comment</em>");
                    }
                    
                    $(`#edit-form-${ratingId}`).addClass('d-none');
                    commentText.removeClass('d-none');
                    
                    showStatusMessage(ratedWineItem, "Comment updated!");
                }
            },
            error: function(xhr) {
                console.error('Error updating comment:', xhr.responseText);
                alert('There was an error updating your comment. Please try again.');
            }
        });
    });
});
</script>
@endsection

@section('styles')
<style>
    /* Remove excess space */
    body {
        background-color: #f8f9fa;
    }
    
    .card {
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .card-header {
        border-bottom: none;
    }
    
    .shadow-sm {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
    }
    
    /* Star styling */
    .rated-stars {
        display: flex;
        align-items: center;
    }
    
    .rated-stars .star {
        margin-right: 5px;
        cursor: pointer;
    }
    
    .rated-stars .fa-star {
        color: #ccc;
        font-size: 1.1rem;
        transition: color 0.2s ease;
    }
    
    .rated-stars .fa-star.filled {
        color: #DAA520;
    }
    
    .rated-stars .star:hover .fa-star {
        color: #DAA520;
    }
    
    .rated-wine-item {
        transition: background-color 0.2s ease;
        padding: 0.75rem 1rem;
        position: relative;
    }
    
    .rated-wine-item:hover {
        background-color: #f8f9fa;
    }
    
    .edit-comment-btn {
        cursor: pointer;
        color: #6c757d;
        font-size: 0.8rem;
    }
    
    .edit-comment-btn:hover {
        color: #495057;
    }
    
    /* Wine attributes */
    .text-wine-burgundy {
        color: #722F37;
    }
    
    .text-gold {
        color: #DAA520;
    }
    
    /* Remove scrollbars */
    ::-webkit-scrollbar {
        width: 0px;
        background: transparent;
    }
    
    /* Status messages */
    .status-message {
        position: absolute;
        top: 10px;
        right: 10px;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 0.75rem;
        background-color: rgba(40, 167, 69, 0.2);
        color: #28a745;
        z-index: 1;
        opacity: 0;
        transform: translateY(-10px);
        animation: fadeInOut 2s ease forwards;
    }
    
    @keyframes fadeInOut {
        0% { opacity: 0; transform: translateY(-10px); }
        15% { opacity: 1; transform: translateY(0); }
        85% { opacity: 1; transform: translateY(0); }
        100% { opacity: 0; transform: translateY(-10px); }
    }
    
    /* Fixed height for rated wines section */
    .rated-wines-container {
        max-height: 100%;
        display: flex;
        flex-direction: column;
    }
    
    .rated-wines-list {
        flex-grow: 1;
        overflow-y: auto;
        max-height: calc(100vh - 300px);
    }
    
    /* Edit comment section */
    .comment-section {
        margin-bottom: 0.5rem;
    }
    
    textarea.form-control-sm {
        min-height: 60px;
        resize: vertical;
    }

    /* Multi-select styling with orange highlight */
    .form-select option:checked,
    .form-select option:focus,
    .form-select option:hover {
        background-color: #fd7e14 !important;
        color: white !important;
    }
    
    .form-select option:checked {
        background: linear-gradient(0deg, #fd7e14 0%, #fd7e14 100%);
    }
    
    /* Override default focus color */
    .form-select:focus {
        border-color: #fd7e14;
        box-shadow: 0 0 0 0.25rem rgba(253, 126, 20, 0.25);
    }
    
    /* Pulse animation for random wine button */
    .pulse-button {
        position: relative;
        box-shadow: 0 0 0 0 rgba(253, 126, 20, 0.7);
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(253, 126, 20, 0.7);
        }
        70% {
            box-shadow: 0 0 0 10px rgba(253, 126, 20, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(253, 126, 20, 0);
        }
    }
    
    /* Change button hover effect */
    .pulse-button:hover {
        transform: scale(1.05);
        transition: transform 0.3s ease;
        animation: none;
    }
</style>
@endsection 