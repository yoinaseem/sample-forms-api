<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\SubmissionValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class SubmissionValueController extends Controller
{
    /**
     * Display a listing of submission values.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'submission_id' => 'sometimes|exists:submissions,id',
            'form_field_id' => 'sometimes|exists:form_fields,id',
            'per_page' => 'sometimes|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $query = SubmissionValue::with(['submission', 'field']);

        // Filter by submission if provided
        if ($request->has('submission_id')) {
            $query->where('submission_id', $request->submission_id);
        }

        // Filter by field if provided
        if ($request->has('form_field_id')) {
            $query->where('form_field_id', $request->form_field_id);
        }

        // Search by value if provided
        if ($request->has('search')) {
            $query->where('value', 'like', "%{$request->search}%");
        }

        // Order by latest by default
        $query->latest();

        // Paginate results
        $perPage = $request->input('per_page', 15);
        $values = $query->paginate($perPage);

        return response()->json([
            'data' => $values->items(),
            'meta' => [
                'current_page' => $values->currentPage(),
                'from' => $values->firstItem(),
                'last_page' => $values->lastPage(),
                'path' => $request->url(),
                'per_page' => $values->perPage(),
                'to' => $values->lastItem(),
                'total' => $values->total(),
            ],
        ]);
    }

    /**
     * Display the specified submission value.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $value = SubmissionValue::with(['submission', 'field'])->findOrFail($id);
        
        return response()->json([
            'submission_value' => $value,
            'typed_value' => $value->typed_value, // Using the accessor
        ]);
    }

    /**
     * Update the specified submission value in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $value = SubmissionValue::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'value' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $value->value = $request->value;
        $value->save();
        
        return response()->json([
            'message' => 'Submission value updated successfully',
            'submission_value' => $value->fresh(['submission', 'field']),
        ]);
    }

    /**
     * Remove the specified submission value from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $value = SubmissionValue::findOrFail($id);
        $value->delete();
        
        return response()->json([
            'message' => 'Submission value deleted successfully',
        ]);
    }
}