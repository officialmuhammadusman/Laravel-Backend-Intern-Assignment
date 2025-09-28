<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            
            if ($user->isAdmin()) {
                $projects = Project::with(['owner', 'users'])->get();
            } else {
                $projects = $user->projects()->with(['owner', 'users'])->get();
            }

            return response()->json([
                'success' => true,
                'message' => 'Projects retrieved successfully',
                'projects' => $projects
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve projects',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            if (!$request->user()->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 400);
            }

            $project = new Project();
            $project->name = $request->name;
            $project->description = $request->description;
            $project->owner_id = $request->user()->id;
            $project->save();

            return response()->json([
                'success' => true,
                'message' => 'Project created successfully',
                'project' => $project->load(['owner', 'users'])
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Project creation failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function addMember(Request $request, Project $project)
    {
        try {
            if (!$request->user()->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 400);
            }

            $user = User::find($request->user_id);

            if ($project->users()->where('user_id', $user->id)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is already assigned to this project'
                ], 400);
            }

            $project->users()->attach($user->id);

            return response()->json([
                'success' => true,
                'message' => 'User added to project successfully',
                'project' => $project->load(['owner', 'users'])
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add user to project',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}