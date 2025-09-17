<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormField extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_section_id',
        'label',
        'name',
        'type',
        'options',
        'validation_rules',
        'is_required',
        'order',
        'class_name',
        'placeholder',
        'depends_on_field_id',
        'depends_on_value',
    ];

     protected $casts = [
        'options' => AsArrayObject::class,
        'validation_rules' => AsArrayObject::class,
        'is_required' => 'boolean',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(FormSection::class);
    }
}
