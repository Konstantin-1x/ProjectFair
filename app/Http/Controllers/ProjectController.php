<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Tag;
use App\Models\Technology;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Project::with(['creator', 'team', 'tags', 'technologies']);

        // Фильтр по статусу (по умолчанию показываем активные)
        $status = $request->get('status', 'active');
        if ($status) {
            $query->where('status', $status);
        }

        // Поиск по названию и описанию
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%");
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

        $projects = $query->latest()->paginate(12);

        return view('projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        \Log::info('ProjectController@create method called', [
            'user_id' => auth()->id(),
            'user_name' => auth()->user() ? auth()->user()->name : 'Guest',
            'authenticated' => auth()->check(),
            'url' => request()->url(),
            'method' => request()->method()
        ]);

        try {
            // Проверяем, что пользователь является лидером хотя бы одной команды
            $userTeams = Team::where('leader_id', Auth::id())->whereIn('status', ['active', 'recruiting'])->get();
            if ($userTeams->isEmpty()) {
                return redirect()->route('teams.create')
                    ->with('error', 'Для создания проекта необходимо быть лидером команды. Сначала создайте команду.');
            }

            $tags = Tag::all();
            \Log::info('Tags loaded', ['count' => $tags->count()]);
            
            $technologies = Technology::all();
            \Log::info('Technologies loaded', ['count' => $technologies->count()]);

            \Log::info('Returning view projects.create');
            return view('projects.create', compact('tags', 'technologies', 'userTeams'));
        } catch (\Exception $e) {
            \Log::error('Error in ProjectController@create', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'type' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'demo_url' => 'nullable|url',
            'github_url' => 'nullable|url',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'new_tags' => 'nullable|string',
            'technologies' => 'nullable|array',
            'technologies.*' => 'exists:technologies,id',
            'new_technologies' => 'nullable|string',
            'project_deadline' => 'nullable|date|after:today',
            'team_join_deadline' => 'nullable|date|after:today',
            'individual_join_deadline' => 'nullable|date|after:today',
            'allows_individual_join' => 'nullable|boolean',
            'deadline_description' => 'nullable|string|max:1000',
            'team_id' => 'required|exists:teams,id',
        ]);

        // Проверяем, что пользователь является лидером выбранной команды
        $selectedTeam = Team::find($validated['team_id']);
        if (!$selectedTeam || $selectedTeam->leader_id !== Auth::id()) {
            return redirect()->back()
                ->with('error', 'Вы не являетесь лидером выбранной команды.')
                ->withInput();
        }

        $validated['created_by'] = Auth::id();
        $validated['status'] = 'active';

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('projects', 'public');
        }

        $project = Project::create($validated);

        // Автоматически добавляем команду к проекту через новую систему связей
        $project->teams()->attach($selectedTeam->id, [
            'role_description' => 'Основная команда проекта',
            'status' => 'active',
            'joined_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Создаем базовые задачи для проекта
        $project->createBasicTasks();

        // Обрабатываем существующие теги
        if ($request->has('tags')) {
            $project->tags()->attach($request->tags);
        }

        // Обрабатываем новые теги
        if ($request->filled('new_tags')) {
            $newTagNames = array_filter(array_map('trim', explode(',', $request->new_tags)));
            
            foreach ($newTagNames as $tagName) {
                if (!empty($tagName)) {
                    $tag = Tag::firstOrCreate(
                        ['name' => $tagName],
                        ['color' => '#6c757d', 'description' => 'Создан при создании проекта']
                    );
                    
                    if (!$project->tags()->where('tag_id', $tag->id)->exists()) {
                        $project->tags()->attach($tag->id);
                    }
                }
            }
        }

        // Обрабатываем существующие технологии
        if ($request->has('technologies')) {
            $project->technologies()->attach($request->technologies);
        }

        // Обрабатываем новые технологии
        if ($request->filled('new_technologies')) {
            $newTechNames = array_filter(array_map('trim', explode(',', $request->new_technologies)));
            
            foreach ($newTechNames as $techName) {
                if (!empty($techName)) {
                    $technology = Technology::firstOrCreate(
                        ['name' => $techName],
                        ['description' => 'Создана при создании проекта']
                    );
                    
                    if (!$project->technologies()->where('technology_id', $technology->id)->exists()) {
                        $project->technologies()->attach($technology->id);
                    }
                }
            }
        }

        return redirect()->route('projects.show', $project)
            ->with('success', 'Проект успешно создан!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        $project->load(['creator', 'team.members', 'tags', 'technologies']);

        return view('projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        $this->authorize('update', $project);

        $tags = Tag::all();
        $technologies = Technology::all();
        $teams = Team::where('status', 'active')->get();

        return view('projects.edit', compact('project', 'tags', 'technologies', 'teams'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'type' => 'nullable|string|max:100',
            'institute' => 'nullable|string|max:100',
            'course' => 'nullable|integer|min:1|max:6',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'demo_url' => 'nullable|url',
            'github_url' => 'nullable|url',
            'team_id' => 'nullable|exists:teams,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'technologies' => 'nullable|array',
            'technologies.*' => 'exists:technologies,id',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('projects', 'public');
        }

        $project->update($validated);

        if ($request->has('tags')) {
            $project->tags()->sync($request->tags);
        } else {
            $project->tags()->detach();
        }

        if ($request->has('technologies')) {
            $project->technologies()->sync($request->technologies);
        } else {
            $project->technologies()->detach();
        }

        return redirect()->route('projects.show', $project)
            ->with('success', 'Проект успешно обновлен!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);

        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Проект успешно удален!');
    }

    /**
     * Завершить проект с файлом
     */
    public function complete(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'completion_file' => 'required|file|mimes:pdf,doc,docx,txt,zip,rar|max:10240', // 10MB max
        ]);

        // Сохраняем файл
        $file = $request->file('completion_file');
        $filename = 'completion_' . $project->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('project_completions', $filename, 'public');

        // Обновляем проект
        $project->update([
            'status' => 'completed',
            'completed_at' => now(),
            'completion_file' => $path,
        ]);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Проект успешно завершен! Файл отчета прикреплен.');
    }
}
