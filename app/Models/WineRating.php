<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WineRating extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_profile_id',
        'wine_id',
        'rating', // 1-5 stars
        'comment',
    ];

    /**
     * Get the user profile that owns the rating.
     */
    public function userProfile()
    {
        return $this->belongsTo(UserProfile::class);
    }

    /**
     * Get the wine that was rated.
     */
    public function wine()
    {
        return $this->belongsTo(Wine::class);
    }
} 