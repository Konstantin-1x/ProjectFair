<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectTaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Показать задачи проекта
     */
    public function index(Project $project)
    {
        // Загружаем команду проекта
        $project->load('team');
        
        // Проверяем права доступа: администратор или лидер команды проекта
        if (!Auth::user()->isAdmin() && $project->team->leader_id !== Auth::id()) {
            abort(403, 'Только администратор или лидер команды может управлять задачами проекта.');
        }

        $tasks = $project->tasks()->orderBy('order')->get();
        $progress = $project->getTasksProgress();

        return view('projects.tasks.index', compact('project', 'tasks', 'progress'));
    }

    /**
     * Показать форму создания задачи
     */
    public function create(Project $project)
    {
        // Загружаем команду проекта
        $project->load('team');
        
        // Проверяем права доступа: администратор или лидер команды проекта
        if (!Auth::user()->isAdmin() && $project->team->leader_id !== Auth::id()) {
            abort(403, 'Только администратор или лидер команды может создавать задачи проекта.');
        }

        return view('projects.tasks.create', compact('project'));
    }

    /**
     * Сохранить новую задачу
     */
    public function store(Request $request, Project $project)
    {
        // Загружаем команду проекта
        $project->load('team');
        
        // Проверяем права доступа: администратор или лидер команды проекта
        if (!Auth::user()->isAdmin() && $project->team->leader_id !== Auth::id()) {
            abort(403, 'Только администратор или лидер команды может создавать задачи проекта.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'nullable|string|max:100',
            'difficulty' => 'required|in:easy,medium,hard',
            'deadline' => 'nullable|date|after:today',
        ]);

        // Определяем порядок задачи (между базовыми задачами)
        $order = $project->tasks()->where('is_basic_task', false)->max('order') + 1;

        $task = $project->tasks()->create(array_merge($validated, [
            'created_by' => Auth::id(),
            'status' => 'open',
            'is_basic_task' => false,
            'order' => $order,
        ]));

        return redirect()->route('projects.tasks.index', $project)
            ->with('success', 'Задача успешно создана!');
    }

    /**
     * Показать задачу
     */
    public function show(Project $project, Task $task)
    {
        // Проверяем, что задача принадлежит проекту
        if ($task->project_id !== $project->id) {
            abort(404);
        }

        // Загружаем команду проекта
        $project->load('team');
        
        // Проверяем права доступа: администратор или лидер команды проекта
        if (!Auth::user()->isAdmin() && $project->team->leader_id !== Auth::id()) {
            abort(403, 'Только администратор или лидер команды может просматривать задачи проекта.');
        }

        $task->load('files');
        return view('projects.tasks.show', compact('project', 'task'));
    }

    /**
     * Завершить задачу
     */
    public function complete(Request $request, Project $project, Task $task)
    {
        // Проверяем, что задача принадлежит проекту
        if ($task->project_id !== $project->id) {
            abort(404);
        }

        // Загружаем команду проекта
        $project->load('team');
        
        // Проверяем права доступа: администратор или лидер команды проекта
        if (!Auth::user()->isAdmin() && $project->team->leader_id !== Auth::id()) {
            abort(403, 'Только администратор или лидер команды может завершать задачи проекта.');
        }

        $validated = $request->validate([
            'completion_text' => 'nullable|string|max:1000',
            'completion_file' => 'nullable|file|mimes:pdf,doc,docx,txt,zip,rar,jpg,jpeg,png,gif|max:10240', // 10MB max
            'completion_files' => 'nullable|array|max:10', // максимум 10 файлов
            'completion_files.*' => 'file|mimes:pdf,doc,docx,txt,zip,rar,jpg,jpeg,png,gif|max:10240', // 10MB max per file
        ]);

        $updateData = [
            'status' => 'completed',
            'completed_at' => now(),
            'completion_text' => $validated['completion_text'] ?? null,
        ];

        // Обработка старого формата (одиночный файл)
        if ($request->hasFile('completion_file')) {
            $file = $request->file('completion_file');
            $filename = 'task_completion_' . $task->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('task_completions', $filename, 'public');
            $updateData['completion_file'] = $path;
        }

        $task->update($updateData);

        // Обработка множественных файлов
        if ($request->hasFile('completion_files')) {
            foreach ($request->file('completion_files') as $file) {
                $filename = 'task_file_' . $task->id . '_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('task_completions', $filename, 'public');
                
                $task->files()->create([
                    'filename' => $filename,
                    'original_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        return redirect()->route('projects.tasks.index', $project)
            ->with('success', 'Задача отмечена как выполненная!');
    }

    /**
     * Удалить задачу
     */
    public function destroy(Project $project, Task $task)
    {
        // Проверяем, что задача принадлежит проекту
        if ($task->project_id !== $project->id) {
            abort(404);
        }

        // Загружаем команду проекта
        $project->load('team');
        
        // Проверяем права доступа: администратор или лидер команды проекта
        if (!Auth::user()->isAdmin() && $project->team->leader_id !== Auth::id()) {
            abort(403, 'Только администратор или лидер команды может удалять задачи проекта.');
        }

        // Нельзя удалять базовые задачи
        if ($task->is_basic_task) {
            return back()->with('error', 'Нельзя удалять базовые задачи проекта.');
        }

        $task->delete();

        return redirect()->route('projects.tasks.index', $project)
            ->with('success', 'Задача успешно удалена!');
    }
}