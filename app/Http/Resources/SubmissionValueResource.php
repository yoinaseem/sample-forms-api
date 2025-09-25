<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\FormField;

class SubmissionValueResource extends JsonResource
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
            'submission_id' => $this->submission_id,
            'form_field_id' => $this->form_field_id,
            'value' => $this->value,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'field' => $this->when($this->field, function () {
                return [
                    'id' => $this->field->id,
                    'name' => $this->field->name,
                    'label' => $this->field->label,
                    'type' => $this->field->type,
                ];
            }, function () {
                // If the field relationship is null, try to get it directly
                $field = FormField::find($this->form_field_id);
                if ($field) {
                    return [
                        'id' => $field->id,
                        'name' => $field->name,
                        'label' => $field->label,
                        'type' => $field->type,
                    ];
                }
                return null;
            }),
        ];
    }
}