<?php

namespace App\Http\Controllers;

use App\Models\Research;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResearchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'Title' => 'required|string|max:255',
            'Abstract' => 'required|string'
        ]);

        $research = Auth::user()->researches()->create($validated);

        return response()->json([
            'message' => 'Research added successfully',
            'research' => $research
        ], 201);
    }
}