<?php

namespace App\Http\Controllers;

use App\Models\SubjectModel;
use Illuminate\Http\Request;

class SubjectController extends Controller
{

    public function getAllSubjects()
    {
        try {
            $subjects = SubjectModel::all();

            return response()->json([
                'subjects' => $subjects
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve subjects.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }


}