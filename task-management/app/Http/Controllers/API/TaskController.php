<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    public function index(Request $request, Project $project)
    {
        $user = $request->user();
        
        // Check if user has access to this project
        if (!$user->isAdmin() && !$project->users()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. You are not assigned to this project.'
            ], 403);
        }

        $tasks = $project->tasks()->with(['assignedUser'])->get();

        return response()->json([
            'success' => true,
            'message' => 'Tasks retrieved successfully',
            'tasks' => $tasks
        ]);
    }

    public function store(Request $request, Project $project)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed',
            'due_date' => 'nullable|date',
            'assigned_to' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 400);
        }

        // Check if assigned user is part of the project
        if (!$project->users()->where('user_id', $request->assigned_to)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Assigned user is not part of this project'
            ], 400);
        }

        $task = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status,
            'due_date' => $request->due_date,
            'project_id' => $project->id,
            'assigned_to' => $request->assigned_to,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Task created successfully',
            'task' => $task->load(['assignedUser', 'project'])
        ], 201);
    }

    public function update(Request $request, Project $project, Task $task)
    {
        $user = $request->user();

        // Check if task belongs to the project
        if ($task->project_id !== $project->id) {
            return response()->json([
                'success' => false,
                'message' => 'Task does not belong to this project'
            ], 400);
        }

        // Members can only update status, admins can update everything
        if ($user->isMember()) {
            // Check if user is assigned to this task
            if ($task->assigned_to !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only update tasks assigned to you'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'status' => 'required|in:pending,in_progress,completed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 400);
            }

            $task->update(['status' => $request->status]);
        } else {
            // Admin can update everything
            $validator = Validator::make($request->all(), [
                'title' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'status' => 'sometimes|required|in:pending,in_progress,completed',
                'due_date' => 'nullable|date',
                'assigned_to' => 'sometimes|required|exists:users,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 400);
            }

            $task->update($request->only(['title', 'description', 'status', 'due_date', 'assigned_to']));
        }

        return response()->json([
            'success' => true,
            'message' => 'Task updated successfully',
            'task' => $task->fresh()->load(['assignedUser', 'project'])
        ]);
    }

    public function destroy(Request $request, Project $project, Task $task)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.'
            ], 403);
        }

        // Check if task belongs to the project
        if ($task->project_id !== $project->id) {
            return response()->json([
                'success' => false,
                'message' => 'Task does not belong to this project'
            ], 400);
        }

        $task->delete();

        return response()->json([
            'success' => true,
            'message' => 'Task deleted successfully'
        ]);
    }
}