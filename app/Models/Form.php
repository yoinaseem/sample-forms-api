<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Form extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
    ];

     protected $casts = [
        'is_active' => 'boolean',
    ];

    public function sections(): HasMany
    {
        return $this->hasMany(FormSection::class)->orderBy('order');
    }
    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }
}
