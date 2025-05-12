<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LessonPlan;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\LessonPlanController;

class LessonPlanController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'lesson_plan_no' => 'required|string',
            'grade_level' => 'required|string',
            'section' => 'required|string',
            'category' => 'required|string',
            'link' => 'required|url',
        ]);

        $lessonPlan = Auth::user()->lessonPlans()->create($validated);

        return response()->json($lessonPlan, 201);
    }

    public function index(Request $request)
    {
        $lessonPlans = Auth::user()->lessonPlans()->get();
        return response()->json($lessonPlans);
    }

    public function update(Request $request, $id)
    {
        \Log::info('Update request data:', $request->all());
        
        $lessonPlan = LessonPlan::where('LessonPlan_ID', $id)->firstOrFail();
        
        $validated = $request->validate([
            'lesson_plan_no' => 'sometimes|numeric',
            'grade_level' => 'sometimes|string',
            'section' => 'sometimes|string',
            'category' => 'sometimes|string',
            'link' => 'sometimes|url',
        ]);

        // Only convert if present in request
        if (isset($validated['lesson_plan_no'])) {
            $validated['lesson_plan_no'] = (string)$validated['lesson_plan_no'];
        }

        $lessonPlan->update($validated);
        return response()->json($lessonPlan);
    }

    public function destroy(LessonPlan $lessonPlan)
    {
        $this->authorize('delete', $lessonPlan);
        $lessonPlan->delete();
        return response()->json(null, 204);
    }
}