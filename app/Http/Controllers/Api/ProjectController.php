<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Http\Controllers\Api;

/**
 * Description of ProjectController
 *
 * @author garys
 */
use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller {

    public function index() {
        return response()->json(Project::all());
    }

    public function show($id) {
        return response()->json(Project::find($id));
    }

    public function store(Request $request) {
        $project = Project::create($request->all());
        return response()->json($project);
    }

    public function update(Request $request, $id) {
        $project = Project::find($id);
        $project->update($request->all());
        return response()->json($project);
    }

    public function destroy($id) {
        Project::destroy($id);
        return response()->json(['message' => 'Project deleted successfully']);
    }
}
