<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wine extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'type',
        'vintage',
        'price',
        'grape_variety',
        'region',
        'country',
        'flavor_profile',
        'food_pairings',
        'tasting_notes',
        'alcohol_content',
        'image_path',
    ];
} 