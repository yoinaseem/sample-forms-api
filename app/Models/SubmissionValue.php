<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubmissionValue extends Model
{
    protected $fillable = [
        'submission_id',
        'form_field_id',
        'value',
    ];

    protected $casts = [
        'value' => 'string',
    ];

    public function field()
    {
        return $this->belongsTo(FormField::class);
    }

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

    public function getTypedValueAttribute()
    {
        if (!$this->value){
            return $this->value;
        }

        switch ($this->field->type) 
        {
            case 'checkbox':
                return $this->array_value;
            case 'number':
                return $this->numeric_value;
            case 'boolean':
                return $this->boolean_value;
            default:
                return $this->value;
        }
    }

    /**
    * Get the value as an array (for checkboxes).
    */
    public function getArrayValueAttribute()
    {
        return json_decode($this->value, true) ?? [];
    }

    /**
     * Get the value as a number.
     */
    public function getNumericValueAttribute()
    {
        return is_numeric($this->value) ? floatval($this->value) : null;
    }

    /**
     * Get the value as a boolean.
     */
    public function getBooleanValueAttribute()
    {
        return filter_var($this->value, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Set the value, handling arrays appropriately.
     */
    public function setValueAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['value'] = json_encode($value);
        } else {
            $this->attributes['value'] = (string) $value;
        }
    }
}
