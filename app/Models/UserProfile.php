<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class UserProfile extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_profiles';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'preferred_flavors',
        'preferred_food_pairings',
        'preferred_price_min',
        'preferred_price_max',
        'preferred_types',
        'preferred_regions',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'preferred_flavors' => 'array',
        'preferred_food_pairings' => 'array',
        'preferred_types' => 'array',
        'preferred_regions' => 'array',
    ];

    /**
     * Get the past recommendations for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pastRecommendations()
    {
        return $this->hasMany(UserRecommendation::class)->latest()->take(5);
    }

    /**
     * Get wine ratings by the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function wineRatings()
    {
        return $this->hasMany(WineRating::class);
    }
} 