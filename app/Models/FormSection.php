<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormSection extends Model
{
    use HasFactory;
    protected $fillable = [
        'form_id',
        'title',
        'order',
    ];

    public function fields(): HasMany
    {
        return $this->hasMany(FormField::class)->orderBy('order');
    }
}
