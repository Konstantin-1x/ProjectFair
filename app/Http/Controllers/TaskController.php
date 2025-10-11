<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Task::with(['creator', 'assignedTeam']);

        // Фильтр по статусу
        $status = $request->get('status', 'open');
        if ($status) {
            $query->where('status', $status);
        }

        // Поиск по названию и описанию
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Фильтр по институту
        if ($request->filled('institute')) {
            $query->where('institute', $request->get('institute'));
        }

        // Фильтр по курсу
        if ($request->filled('course')) {
            $query->where('course', $request->get('course'));
        }

        // Фильтр по сложности
        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->get('difficulty'));
        }

        $tasks = $query->latest()->paginate(12);

        return view('tasks.index', compact('tasks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $teams = Team::where('status', 'active')->get();
        return view('tasks.create', compact('teams'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'requirements' => 'nullable|string',
            'difficulty' => 'required|in:easy,medium,hard',
            'type' => 'nullable|string|max:100',
            'institute' => 'nullable|string|max:100',
            'course' => 'nullable|integer|min:1|max:6',
            'max_team_size' => 'required|integer|min:1|max:10',
            'deadline' => 'nullable|date|after:today',
            'assigned_team_id' => 'nullable|exists:teams,id',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['status'] = 'open';

        $task = Task::create($validated);

        // Если задача назначена команде, обновляем статус
        if ($request->filled('assigned_team_id')) {
            $task->update([
                'assigned_at' => now(),
                'status' => 'in_progress'
            ]);
        }

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Задача успешно создана!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        $task->load(['creator', 'assignedTeam']);
        
        return view('tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        // Проверяем, что пользователь является создателем задачи
        if ($task->created_by !== Auth::id()) {
            abort(403, 'Только создатель задачи может редактировать задачу.');
        }

        $teams = Team::where('status', 'active')->get();
        
        return view('tasks.edit', compact('task', 'teams'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        // Проверяем, что пользователь является создателем задачи
        if ($task->created_by !== Auth::id()) {
            abort(403, 'Только создатель задачи может редактировать задачу.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'requirements' => 'nullable|string',
            'difficulty' => 'required|in:easy,medium,hard',
            'status' => 'required|in:open,in_progress,completed,closed',
            'type' => 'nullable|string|max:100',
            'institute' => 'nullable|string|max:100',
            'course' => 'nullable|integer|min:1|max:6',
            'max_team_size' => 'required|integer|min:1|max:10',
            'deadline' => 'nullable|date|after:today',
            'assigned_team_id' => 'nullable|exists:teams,id',
        ]);

        $task->update($validated);

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Задача успешно обновлена!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        // Проверяем, что пользователь является создателем задачи
        if ($task->created_by !== Auth::id()) {
            abort(403, 'Только создатель задачи может удалить задачу.');
        }

        $task->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'Задача успешно удалена!');
    }

    /**
     * Assign task to team
     */
    public function assign(Task $task, Team $team)
    {
        // Проверяем, что пользователь является создателем задачи
        if ($task->created_by !== Auth::id()) {
            abort(403, 'Только создатель задачи может назначить задачу команде.');
        }

        // Проверяем, что задача еще не назначена
        if ($task->assigned_team_id) {
            return back()->with('error', 'Задача уже назначена команде.');
        }

        $task->update([
            'assigned_team_id' => $team->id,
            'assigned_at' => now(),
            'status' => 'in_progress'
        ]);

        return back()->with('success', 'Задача успешно назначена команде!');
    }

    /**
     * Complete task
     */
    public function complete(Task $task)
    {
        // Проверяем, что пользователь является создателем задачи или участником назначенной команды
        $canComplete = $task->created_by === Auth::id() || 
                      ($task->assignedTeam && $task->assignedTeam->members->contains(Auth::id()));

        if (!$canComplete) {
            abort(403, 'Вы не можете завершить эту задачу.');
        }

        $task->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);

        return back()->with('success', 'Задача отмечена как выполненная!');
    }
}
