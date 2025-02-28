<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Task;
use Illuminate\Routing\Controller;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create()
    {
        return view('tasks.create');
    }
    public function index(Request $request)
    {
        $sort = $request->query('sort', 'title');
        $order = $request->query('order', 'asc');
        
        $tasks = Task::where('user_id', Auth::id())
                     ->orderBy($sort, $order)
                     ->get();
        
        return view('tasks.index', compact('tasks', 'sort', 'order'));
    }

    public function starredTasks()
    {
        $tasks = Task::where('starred', true)->get();
        return view('tasks.starred', compact('tasks'));
    }
    
    public function edit($id)
    {
        $task = Task::where('id', $id)->where('user_id', Auth::id())->first();
    
        if (!$task) {
            return redirect()->route('tasks.index')->with('error', 'Can not access!');
        }
    
        return view('tasks.edit', compact('task'));
    }
    
    public function show($id)
    {
        $task = Task::where('id', $id)->where('user_id', Auth::id())->first();

        if (!$task) {
            return redirect()->route('tasks.index')->with('error', 'Can not access!');
        }

        return view('tasks.show', compact('task'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required',
            'content' => 'required',
            'starred' => 'nullable|boolean',
        ]);

        $validatedData['starred'] = $request->boolean('starred');
        $validatedData['user_id'] = Auth::id();

        Task::create($validatedData);

        return redirect()->route('tasks.index')->with('success', 'Task created successfully.');
    }
    public function update(Request $request, $id)
    {
        $task = Task::where('id', $id)->where('user_id', Auth::id())->first();
        if (!$task) {
            return redirect()->route('tasks.index')->with('error', 'Bạn không có quyền cập nhật Task này.');
        }
    
        $request->validate([
            'title' => 'required',
            'content' => 'required',
            'starred' => 'nullable|boolean',
        ]);
    
        $task->update([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'starred' => $request->boolean('starred'),
        ]);
    
        return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
    }
    

    public function destroy($id)
    {
        $task = Task::where('id', $id)->where('user_id', Auth::id())->first();
        if (!$task) {
            return redirect()->route('tasks.index')->with('error', 'Bạn không thể xóa Task này.');
        }

        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
    }

    public function toggleStarred($id)
    {
        $task = Task::where('id', $id)->where('user_id', Auth::id())->first();
        if (!$task) {
            return response()->json(['error' => 'Bạn không có quyền sửa Task này'], 403);
        }

        $task->update(['starred' => !$task->starred]);
        return response()->json(['starred' => $task->starred]);
    }
}
