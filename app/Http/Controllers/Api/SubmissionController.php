<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\Submission;
use App\Models\FormField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\SubmissionResource;

class SubmissionController extends Controller
{
    /**
     * Display a listing of submissions.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
   public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'form_id' => 'sometimes|exists:forms,id',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'page' => 'sometimes|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $query = Submission::with(['form', 'values.field']);
        
        // Filter by form if provided
        if ($request->has('form_id')) {
            $query->forForm($request->form_id);
        }

        // Apply search filters if provided
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('values', function ($q) use ($search) {
                $q->where('value', 'like', "%{$search}%");
            });
        }

        // Apply field filters if provided
        if ($request->has('filters')) {
            $filters = $request->filters;
            foreach ($filters as $fieldName => $fieldValue) {
                $query->withFieldValue($fieldName, $fieldValue);
            }
        }

        // Order by latest by default
        $query->latest();

        // Paginate results
        $perPage = $request->input('per_page', 15);
        $submissions = $query->paginate($perPage);
        
        return response()->json([
            'data' => SubmissionResource::collection($submissions->items()),
            'meta' => [
                'current_page' => $submissions->currentPage(),
                'from' => $submissions->firstItem(),
                'last_page' => $submissions->lastPage(),
                'path' => $request->url(),
                'per_page' => $submissions->perPage(),
                'to' => $submissions->lastItem(),
                'total' => $submissions->total(),
            ],
        ]);
    }

    /**
     * Store a newly created submission in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'formId' => 'required|exists:forms,id',
            'data' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $form = Form::findOrFail($request->formId);
        // Validate the data against the form structure
        $validationErrors = $this->validateSubmissionData($form, $request->data);
        
        if (!empty($validationErrors)) {
            return response()->json(['errors' => $validationErrors], 422);
        }

        DB::beginTransaction();
        
        try {
            // Create the submission
            $submission = Submission::create([
                'form_id' => $form->id,
            ]);

            // Create submission values for each field
            foreach ($request->data as $fieldName => $fieldValue) {
                // Find the field by name across all sections
                $field = null;
                foreach ($form->sections as $section) {
                    $foundField = $section->fields->firstWhere('name', $fieldName);
                    if ($foundField) {
                        $field = $foundField;
                        break;
                    }
                }
                
                if ($field) {
                    // Only create a value if the field is visible based on dependencies
                    if ($this->isFieldVisible($field, $request->data)) {
                        // dd($field);
                        $submission->values()->create([
                            'form_field_id' => $field->id,
                            'value' => $fieldValue,
                        ]);
                    }
                }
            }            
            DB::commit();
            
            return response()->json([
                'message' => 'Submission created successfully',
                'submission' => $submission->load('values.field'),
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'message' => 'Failed to create submission',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified submission.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $submission = Submission::with(['form', 'values.field'])->findOrFail($id);
        
        return response()->json([
            'submission' => $submission,
            'data' => $submission->data, // Using the accessor to get data as an array
        ]);
    }

    /**
     * Update the specified submission in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $submission = Submission::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'data' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        // Validate the data against the form structure
        $validationErrors = $this->validateSubmissionData($submission->form, $request->data);
        
        if (!empty($validationErrors)) {
            return response()->json(['errors' => $validationErrors], 422);
        }
        
        DB::beginTransaction();
        
        try {
            // Delete existing values
            $submission->values()->delete();
            
            // Create new submission values
            foreach ($request->data as $fieldName => $fieldValue) {
                $field = $submission->form->fields()->where('name', $fieldName)->first();
                
                if ($field) {
                    // Only create a value if the field is visible based on dependencies
                    if ($this->isFieldVisible($field, $request->data)) {
                        $submission->values()->create([
                            'form_field_id' => $field->id,
                            'value' => $fieldValue,
                        ]);
                    }
                }
            }
            
            DB::commit();
            
            return response()->json([
                'message' => 'Submission updated successfully',
                'submission' => $submission->load('values.field'),
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'message' => 'Failed to update submission',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified submission from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $submission = Submission::findOrFail($id);
        
        DB::beginTransaction();
        
        try {
            // Delete submission values first (foreign key constraint)
            $submission->values()->delete();
            
            // Delete the submission
            $submission->delete();
            
            DB::commit();
            
            return response()->json([
                'message' => 'Submission deleted successfully',
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'message' => 'Failed to delete submission',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Validate submission data against form structure.
     *
     * @param  \App\Models\Form  $form
     * @param  array  $data
     * @return array
     */
    private function validateSubmissionData(Form $form, array $data)
    {
        $errors = [];
        
        foreach ($form->sections as $section) {
            foreach ($section->fields as $field) {
                // Skip validation if field is not visible based on dependencies
                if (!$this->isFieldVisible($field, $data)) {
                    continue;
                }
                
                $fieldName = $field->name;
                $fieldValue = $data[$fieldName] ?? null;
                
                // Required field validation
                if ($field->is_required && empty($fieldValue)) {
                    $errors[$fieldName] = "{$field->label} is required";
                    continue;
                }
                
                // Type-specific validation
                if (!empty($fieldValue)) {
                    switch ($field->type) {
                        case 'email':
                            if (!filter_var($fieldValue, FILTER_VALIDATE_EMAIL)) {
                                $errors[$fieldName] = "Please enter a valid email address";
                            }
                            break;
                            
                        case 'url':
                            if (!filter_var($fieldValue, FILTER_VALIDATE_URL)) {
                                $errors[$fieldName] = "Please enter a valid URL";
                            }
                            break;
                            
                        case 'tel':
                            if (!preg_match('/^[\d\s\-+()]+$/', $fieldValue)) {
                                $errors[$fieldName] = "Please enter a valid phone numbe";
                            }
                            break;
                            
                        case 'number':
                            if (!is_numeric($fieldValue)) {
                                $errors[$fieldName] = "Please enter a valid number";
                            }
                            break;
                    }
                }
            }
        }
        
        return $errors;
    }
    
    /**
     * Check if a field should be visible based on dependencies.
     *
     * @param  \App\Models\FormField  $field
     * @param  array  $data
     * @return bool
     */
    private function isFieldVisible(FormField $field, array $data)
    {
        // If no dependency, field is always visible
        if (!$field->depends_on_field_id) {
            return true;
        }
        
        // Find the dependent field
        $dependentField = FormField::find($field->depends_on_field_id);
        if (!$dependentField) {
            return true; // Fallback if dependent field not found
        }
        
        // Get the value of the dependent field
        $dependentValue = $data[$dependentField->name] ?? null;
        
        // Handle different field types
        if ($dependentField->type === 'checkbox') {
            return is_array($dependentValue) && in_array($field->depends_on_value, $dependentValue);
        }
        
        return $dependentValue === $field->depends_on_value;
    }
}