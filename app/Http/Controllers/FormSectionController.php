<?php

namespace App\Http\Controllers;

use App\Http\Resources\FormSectionResource;
use App\Models\Form;
use App\Models\FormSection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FormSectionController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Form $form) 
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'order' => 'required|integer|min:1',
        ]);

        $formSection = $form->sections()->create($request->all());

        return response()->json($formSection, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(FormSection $formSection)
    {
        $formSection->load('fields');
        return new FormSectionResource($formSection);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FormSection $formSection)
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'order' => 'sometimes|required|integer|min:1',
        ]);

        $formSection->update($validated);
        return response()->json($formSection);
    }

    /**
     * Remove the specified section from storage.
     */
    public function destroy(FormSection $formSection)
    {
        $formSection->delete();

        return response()->noContent();
    }
}
