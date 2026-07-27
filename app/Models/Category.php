<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'name',
        'icon',
        'description',
    ];

    /**
     * Get the menus for this category.
     */
    public function menus(): HasMany
    {
        return $this->hasMany(MenuItem::class);
    }
}
