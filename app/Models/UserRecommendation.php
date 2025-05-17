<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRecommendation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_profile_id',
        'wine_id',
        'recommended_at',
        'criteria_used',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'criteria_used' => 'array',
        'recommended_at' => 'datetime',
    ];

    /**
     * Get the user profile that owns the recommendation.
     */
    public function userProfile()
    {
        return $this->belongsTo(UserProfile::class);
    }

    /**
     * Get the wine that was recommended.
     */
    public function wine()
    {
        return $this->belongsTo(Wine::class);
    }
} 