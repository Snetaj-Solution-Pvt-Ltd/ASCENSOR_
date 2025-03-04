<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;

class TasksController extends Controller
{
    public function create(Request $request) {
        try {
            $validateReq = Validator::make($request->all(), 
            [
                'user_id' => 'required|integer',
                'title' => 'required',
                'description' => 'required',
                'deadline' => 'required|date_format:Y-m-d',
                'category_id' => 'required|integer',
                'status' => 'required',
            ]);

            if($validateReq->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateReq->errors()
                ], 401);
            }

            $task = Task::create([
                'user_id' => $request->user_id,
                'title' => $request->title,
                'description' => $request->description,
                'deadline' => $request->deadline,
                'category_id' => $request->category_id,
                'status' => $request->status,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Task Created Successfully',
                'task' => $task
            ], 200);

            
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function listTasks(Request $request) {
        $uid = Auth::id();
        $category = $request->category;
        $status = $request->status;

        $tasks = Task::with('category')->orderBy('updated_at','desc');
        if ($request->has('category') && $request->category != '') {
            $tasks = $tasks->where('category_id', $category);
        }

        if ($request->has('status') && $request->status != '') {
            $tasks = $tasks->where(function ($q) use ($status) {
                $q->where('status', $status);
            });
        }
        $tasks = $tasks->where('user_id', $uid);
        $tasksRes = $tasks->get();
        if ($tasksRes && Count($tasksRes) > 0) {
            return response()->json([
                'status' => true,
                'message' => 'Task List',
                'task' => $tasksRes
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No Task found for given criteria.',
            ], 401);
        }   
    }

    public function updateTask(Request $request) {
        $validateReq = Validator::make($request->all(), 
            [
                'id' => 'required|integer',
                'user_id' => 'required|integer',
                'title' => 'required',
                'description' => 'required',
                'deadline' => 'required|date_format:Y-m-d',
                'category_id' => 'required|integer',
                'status' => 'required',
            ]);

            if($validateReq->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateReq->errors()
                ], Response::HTTP_BAD_REQUEST);
            }

            $task = Task::with('category')->find($request->id);
            if (!$task) {
                return response()->json([
                    'status' => false,
                    'message' => 'Task not found.',
                ], Response::HTTP_NOT_FOUND);
            }

            $task->user_id = $request->user_id;
            $task->title = $request->title;
            $task->description = $request->description;
            $task->deadline = $request->deadline;
            $task->category_id = $request->category_id;
            $task->status = $request->status;
            if ($task->save()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Task updated successfully.',
                    'task' => $task
                ], Response::HTTP_OK);
            }

            return response()->json([
                'status' => false,
                'message' => 'Unable to update task.',
            ], Response::HTTP_BAD_REQUEST);

    }

    public function deleteTask(Request $request) {
        $validateReq = Validator::make($request->all(), 
            [
                'id' => 'required|integer'
            ]);

            if($validateReq->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateReq->errors()
                ], Response::HTTP_BAD_REQUEST);
            }

            $uid = Auth::id();
            $task = Task::with('category')->where('id', $request->id)->where('user_id', $uid)->get();
            if (!$task) {
                return response()->json([
                    'status' => false,
                    'message' => 'Task not found.',
                ], Response::HTTP_NOT_FOUND);
            }

            Task::where('id',$request->id)->where('user_id',$uid)->delete();
            return response()->json([
                'status' => true,
                'message' => 'Task Deleted.',
                'task' => $task
            ], Response::HTTP_OK);
    }
}
