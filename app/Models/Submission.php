<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SubmissionValue;

class Submission extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_id',
    ];

    protected $with = ['values.field']; // Eager load values and their fields by default

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function values()
    {
        return $this->hasMany(SubmissionValue::class);
    }

    public function getDataAttribute(){
        $data = [];

        foreach($this->values as $value){
            $fieldName = $value->field->name;
            $data[$fieldName] = $value->field->typed_value;
        }

        return $data;

    }

    /**
     * Scope a query to only include submissions for a specific form.
     */
    public function scopeForForm($query, $formId)
    {
        return $query->where('form_id', $formId);
    }

    /**
     * Scope a query to find submissions with a specific field value.
     */
    public function scopeWithFieldValue($query, $fieldName, $fieldValue)
    {
        return $query->whereHas('values', function ($q) use ($fieldName, $fieldValue) {
            $q->where('value', $fieldValue)
              ->whereHas('field', function ($fieldQuery) use ($fieldName) {
                  $fieldQuery->where('name', $fieldName);
              });
        });
    }


    // add required ,ethods
}
