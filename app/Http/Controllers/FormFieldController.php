<?php

namespace App\Http\Controllers;

use App\Models\FormField;
use App\Http\Resources\FormFieldResource;
use Illuminate\Validation\Rule;
use App\Models\FormSection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FormFieldController extends Controller
{
    /**
     * Store a newly created field for a given section.
     */
    public function store(Request $request, FormSection $formSection)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'name' => 'required|string|max:255|alpha_dash', // No spaces allowed
            'type' => ['required', Rule::in(['text', 'select', 'radio', 'checkbox', 'textarea', 'email', 'tel', 'number', 'date', 'url'])],
            'is_required' => 'sometimes|boolean',
            'order' => 'required|integer|min:1',
            'class_name' => 'nullable|string|max:255',
            'placeholder' => 'nullable|string|max:255',
            'options' => 'nullable|array',
            'validation_rules' => 'nullable|array',
            'depends_on_field_id' => 'nullable|exists:form_fields,id',
            'depends_on_value' => 'nullable|string',
        ]);

        $field = $formSection->fields()->create($validated);

        return new FormFieldResource($field);
    }

    /**
     * Display the specified field.
     */
    public function show(FormField $formField)
    {
        return new FormFieldResource($formField);
    }

    /**
     * Update the specified field in storage.
     */
    public function update(Request $request, FormField $formField)
    {
        $validated = $request->validate([
            'label' => 'sometimes|required|string|max:255',
            'name' => 'sometimes|required|string|max:255|alpha_dash',
            'type' => ['sometimes', 'required', Rule::in(['text', 'select', 'radio', 'checkbox', 'textarea', 'email', 'tel', 'number', 'date', 'url'])],
            'is_required' => 'sometimes|boolean',
            'order' => 'sometimes|required|integer|min:1',
            'class_name' => 'nullable|string|max:255',
            'placeholder' => 'nullable|string|max:255',
            'options' => 'nullable|array',
            'validation_rules' => 'nullable|array',
            'depends_on_field_id' => 'nullable|exists:form_fields,id',
            'depends_on_value' => 'nullable|string',
        ]);

        $formField->update($validated);

        return new FormFieldResource($formField);
    }

    /**
     * Remove the specified field from storage.
     */
    public function destroy(FormField $formField)
    {
        $formField->delete();

        return response()->noContent();
    }
}