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
        // Загружаем команды проекта
        $project->load('teams.members');
        
        // Проверяем права доступа: преподаватели или участники команд проекта
        $isAdmin = Auth::user()->isAdmin();
        $isProjectMember = false;
        
        // Проверяем, является ли пользователь участником любой команды проекта
        foreach ($project->teams as $team) {
            if ($team->members()->where('user_id', Auth::id())->exists()) {
                $isProjectMember = true;
                break;
            }
        }
        
        if (!$isAdmin && !$isProjectMember) {
            abort(403, 'Только преподаватели или участники команд проекта могут просматривать задачи.');
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
        
        // Проверяем права доступа: только преподаватели
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Только преподаватели могут создавать задачи проекта.');
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
        
        // Проверяем права доступа: только преподаватели
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Только преподаватели могут создавать задачи проекта.');
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

        // Загружаем команды проекта
        $project->load('teams.members');
        
        // Проверяем права доступа: преподаватели или участники команд проекта
        $isAdmin = Auth::user()->isAdmin();
        $isProjectMember = false;
        
        // Проверяем, является ли пользователь участником любой команды проекта
        foreach ($project->teams as $team) {
            if ($team->members()->where('user_id', Auth::id())->exists()) {
                $isProjectMember = true;
                break;
            }
        }
        
        if (!$isAdmin && !$isProjectMember) {
            abort(403, 'Только преподаватели или участники команд проекта могут просматривать задачи.');
        }

        $task->load(['files', 'comments.user']);
        return view('projects.tasks.show', compact('project', 'task'));
    }

    /**
     * Показать форму редактирования задачи
     */
    public function edit(Project $project, Task $task)
    {
        // Проверяем, что задача принадлежит проекту
        if ($task->project_id !== $project->id) {
            abort(404);
        }

        // Загружаем команду проекта
        $project->load('team');
        
        // Проверяем права доступа: только преподаватели
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Только преподаватели могут редактировать задачи проекта.');
        }

        return view('projects.tasks.edit', compact('project', 'task'));
    }

    /**
     * Обновить задачу
     */
    public function update(Request $request, Project $project, Task $task)
    {
        // Проверяем, что задача принадлежит проекту
        if ($task->project_id !== $project->id) {
            abort(404);
        }

        // Загружаем команду проекта
        $project->load('team');
        
        // Проверяем права доступа: только преподаватели
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Только преподаватели могут редактировать задачи проекта.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'nullable|string|max:100',
            'difficulty' => 'required|in:easy,medium,hard',
            'deadline' => 'nullable|date|after:today',
            'status' => 'required|in:open,in_progress,completed,closed',
        ]);

        $task->update($validated);

        return redirect()->route('projects.tasks.index', $project)
            ->with('success', 'Задача успешно обновлена!');
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

        // Загружаем команды проекта
        $project->load('teams.members');
        
        // Проверяем права доступа: преподаватели или участники команд проекта
        $isAdmin = Auth::user()->isAdmin();
        $isProjectMember = false;
        
        // Проверяем, является ли пользователь участником любой команды проекта
        foreach ($project->teams as $team) {
            if ($team->members()->where('user_id', Auth::id())->exists()) {
                $isProjectMember = true;
                break;
            }
        }
        
        if (!$isAdmin && !$isProjectMember) {
            abort(403, 'Только преподаватели или участники команд проекта могут завершать задачи.');
        }

        $validated = $request->validate([
            'completion_text' => 'required|string|max:1000',
            'completion_file' => 'nullable|file|mimes:pdf,doc,docx,txt,zip,rar,jpg,jpeg,png,gif|max:10240', // 10MB max
            'completion_files' => 'nullable|array|max:10', // максимум 10 файлов
            'completion_files.*' => 'file|mimes:pdf,doc,docx,txt,zip,rar,jpg,jpeg,png,gif|max:10240', // 10MB max per file
        ]);

        $updateData = [
            'status' => 'completed',
            'completed_at' => now(),
            'completion_text' => $validated['completion_text'] ?? null,
            'is_rejected' => false, // Сбрасываем статус отклонения при повторном завершении
            'rejection_reason' => null,
            'rejected_at' => null,
            'rejected_by' => null,
        ];

        // Обработка старого формата (одиночный файл)
        if ($request->hasFile('completion_file')) {
            $file = $request->file('completion_file');
            $filename = 'task_completion_' . $task->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('task_completions', $filename, 'public');
            $updateData['completion_file'] = $path;
        }

        $task->update($updateData);

        // Создаем комментарий о завершении
        $attachedFiles = [];
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

                $attachedFiles[] = [
                    'filename' => $filename,
                    'original_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                ];
            }
        }

        // Создаем комментарий о завершении задачи
        $task->comments()->create([
            'user_id' => Auth::id(),
            'comment' => $validated['completion_text'],
            'type' => 'completion',
            'attached_files' => $attachedFiles,
        ]);

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
        
        // Проверяем права доступа: только преподаватели
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Только преподаватели могут удалять задачи проекта.');
        }

        // Нельзя удалять базовые задачи
        if ($task->is_basic_task) {
            return back()->with('error', 'Нельзя удалять базовые задачи проекта.');
        }

        $task->delete();

        return redirect()->route('projects.tasks.index', $project)
            ->with('success', 'Задача успешно удалена!');
    }

    /**
     * Отклонить выполнение задачи
     */
    public function reject(Request $request, Project $project, Task $task)
    {
        // Проверяем, что задача принадлежит проекту
        if ($task->project_id !== $project->id) {
            abort(404);
        }

        // Проверяем права доступа: только преподаватели
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Только преподаватели могут отклонять выполнение задач.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
            'rejection_files' => 'nullable|array|max:10',
            'rejection_files.*' => 'file|mimes:pdf,doc,docx,txt,zip,rar,jpg,jpeg,png,gif|max:10240',
        ]);

        $attachedFiles = [];
        if ($request->hasFile('rejection_files')) {
            foreach ($request->file('rejection_files') as $file) {
                $filename = 'rejection_file_' . $task->id . '_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('task_completions', $filename, 'public');
                
                $attachedFiles[] = [
                    'filename' => $filename,
                    'original_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                ];
            }
        }

        $task->reject($validated['rejection_reason'], Auth::id(), $attachedFiles);

        return redirect()->route('projects.tasks.index', $project)
            ->with('success', 'Выполнение задачи отклонено. Задача возвращена в статус "Открыта".');
    }

    /**
     * Одобрить выполнение задачи
     */
    public function approve(Project $project, Task $task)
    {
        // Проверяем, что задача принадлежит проекту
        if ($task->project_id !== $project->id) {
            abort(404);
        }

        // Проверяем права доступа: только преподаватели
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Только преподаватели могут одобрять выполнение задач.');
        }

        $task->approve();

        return redirect()->route('projects.tasks.index', $project)
            ->with('success', 'Выполнение задачи одобрено.');
    }

    /**
     * Добавить комментарий к задаче
     */
    public function addComment(Request $request, Project $project, Task $task)
    {
        // Проверяем, что задача принадлежит проекту
        if ($task->project_id !== $project->id) {
            abort(404);
        }

        // Проверяем права доступа: только преподаватели
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Только преподаватели могут добавлять комментарии к задачам.');
        }

        $validated = $request->validate([
            'comment' => 'required|string|max:1000',
            'comment_files' => 'nullable|array|max:10',
            'comment_files.*' => 'file|mimes:pdf,doc,docx,txt,zip,rar,jpg,jpeg,png,gif|max:10240',
        ]);

        $attachedFiles = [];
        if ($request->hasFile('comment_files')) {
            foreach ($request->file('comment_files') as $file) {
                $filename = 'comment_file_' . $task->id . '_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('task_completions', $filename, 'public');
                
                $attachedFiles[] = [
                    'filename' => $filename,
                    'original_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                ];
            }
        }

        $task->comments()->create([
            'user_id' => Auth::id(),
            'comment' => $validated['comment'],
            'type' => 'approval',
            'attached_files' => $attachedFiles,
        ]);

        return redirect()->route('projects.tasks.show', [$project, $task])
            ->with('success', 'Комментарий добавлен.');
    }
}