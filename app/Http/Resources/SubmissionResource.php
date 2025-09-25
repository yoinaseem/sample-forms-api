<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubmissionResource extends JsonResource
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
            'form_id' => $this->form_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'form' => [
                'id' => $this->form->id,
                'name' => $this->form->name,
                'slug' => $this->form->slug,
                'description' => $this->form->description,
                'is_active' => $this->form->is_active,
            ],
            'values' => SubmissionValueResource::collection($this->values),
        ];
    }
}