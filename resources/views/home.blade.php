@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <p>{{ __('You are logged in!') }}</p>
                    
                    <a href="{{ route('wine.home') }}" class="btn btn-primary">
                        <i class="fas fa-wine-glass-alt me-2"></i> Go to Wine Recommender
                    </a>
                </div>
            </div>
            
            @if(isset($ratedWines) && $ratedWines->count() > 0)
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-star text-warning me-2"></i>{{ __('Your Rated Wines') }}
                </div>

                <div class="card-body">
                    <div class="list-group">
                        @foreach($ratedWines as $rating)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-1">{{ $rating->wine->name }}</h5>
                                    <div class="rated-stars">
                                        @for($i = 1; $i <= 5; $i++)
                                            <span class="star {{ $i <= $rating->rating ? 'filled' : '' }}">★</span>
                                        @endfor
                                    </div>
                                </div>
                                <p class="mb-1">
                                    <span class="badge bg-primary me-2">{{ $rating->wine->type ?? 'N/A' }}</span>
                                    <span class="badge bg-secondary">{{ $rating->wine->vintage ?? 'N/A' }}</span>
                                </p>
                                @if($rating->comment)
                                    <p class="mb-0 small text-muted">{{ $rating->comment }}</p>
                                @endif
                                <small class="text-muted">Rated {{ $rating->updated_at->diffForHumans() }}</small>
                            </div>
                        @endforeach
                    </div>
                    
                    @if(Auth::user()->profile && Auth::user()->profile->wineRatings()->count() > 5)
                        <div class="text-center mt-3">
                            <a href="{{ route('wine.home') }}" class="btn btn-sm btn-outline-primary">
                                View All Rated Wines
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .rated-stars {
        color: #ccc;
        font-size: 1.2rem;
    }
    
    .rated-stars .star.filled {
        color: #DAA520;
    }
</style>
@endsection
