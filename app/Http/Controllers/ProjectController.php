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
        // Временно убираем middleware для диагностики
        // $this->middleware('auth')->except(['index', 'show']);
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
            'user_name' => auth()->user() ? auth()->user()->name : 'Guest'
        ]);

        try {
            $tags = Tag::all();
            \Log::info('Tags loaded', ['count' => $tags->count()]);
            
            $technologies = Technology::all();
            \Log::info('Technologies loaded', ['count' => $technologies->count()]);
            
            $teams = Team::whereIn('status', ['recruiting', 'active'])->get();
            \Log::info('Teams loaded', ['count' => $teams->count()]);

            \Log::info('Returning view projects.create');
            return view('projects.create', compact('tags', 'technologies', 'teams'));
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

        $validated['created_by'] = Auth::id();
        $validated['status'] = 'active';

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('projects', 'public');
        }

        $project = Project::create($validated);

        if ($request->has('tags')) {
            $project->tags()->attach($request->tags);
        }

        if ($request->has('technologies')) {
            $project->technologies()->attach($request->technologies);
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
}
