<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use Illuminate\View\View;
use Illuminate\Http\Request;

class GradesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $grades = Grade::with([
            'teacher:id,first_name,last_name,subject_id',
            'teacher.subject:id,name',
            'student:id,first_name,last_name,class_id',
            'student.class:id,name',
        ])->latest()->paginate(10);

        return view('grades.index', compact('grades'));
    }
    

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
