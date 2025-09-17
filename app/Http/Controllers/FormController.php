<?php

namespace App\Http\Controllers;
use App\Models\Form;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Resources\FormResource;

class FormController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->has('slug')) {
            // Find the form(s) matching the slug
            $forms = Form::where('slug', $request->slug)->get();

            // Eager load relationships ONLY when fetching by slug
            $forms->load('sections.fields'); 
        } else {
            $forms = Form::all();
        }

        // Use the resource to format the collection of forms
        return FormResource::collection($forms);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // TO DO: Add validation logic
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:forms,slug',
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ]);

        $form = Form::create($validated);

        return response()->json($form, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Form $form)
    {
        // return $form->load('sections.fields');
        $form->load('sections.fields');
        return new FormResource($form);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Form $form)
    {
        // TO DO: Add validation logic
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|required|string|unique:forms,slug,' . $form->id,
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ]);

        $form->update($validated);

        return response()->json($form);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Form $form)
    {
        $form->delete();

        return response()->noContent();
    }
}
