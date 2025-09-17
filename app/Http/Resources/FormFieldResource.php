<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormFieldResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'name' => $this->name,
            'type' => $this->type,
            'is_required' => $this->is_required,
            'options' => $this->options,
            'validation_rules' => $this->validation_rules,
            'order' => $this->order,
            'class_name' => $this->class_name,
            'placeholder' => $this->placeholder,
            'depends_on_field_id' => $this->depends_on_field_id,
            'depends_on_value' => $this->depends_on_value,
        ];
    }
}