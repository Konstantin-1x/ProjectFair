<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectTeamController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Показать команды проекта
     */
    public function index(Project $project)
    {
        // Проверяем, что пользователь является создателем проекта, лидером команды или администратором
        if ($project->created_by !== Auth::id() && 
            !$project->teams->where('leader_id', Auth::id())->count() && 
            !Auth::user()->isAdmin()) {
            abort(403, 'Только создатель проекта, лидер команды или администратор может управлять командами проекта.');
        }

        $project->load(['teams', 'team']);
        
        // Если пользователь - администратор, показываем все активные команды
        if (Auth::user()->isAdmin()) {
            $availableTeams = Team::where('status', 'active')
                ->whereNotIn('id', $project->teams->pluck('id'))
                ->get();
        } else {
            $availableTeams = Team::where('status', 'active')
                ->where('leader_id', Auth::id())
                ->whereNotIn('id', $project->teams->pluck('id'))
                ->get();
        }

        return view('projects.teams.index', compact('project', 'availableTeams'));
    }

    /**
     * Показать форму добавления команды
     */
    public function create(Project $project)
    {
        // Проверяем, что пользователь является создателем проекта или администратором
        if ($project->created_by !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'Только создатель проекта или администратор может добавлять команды.');
        }

        // Если пользователь - администратор, показываем все активные команды
        if (Auth::user()->isAdmin()) {
            $availableTeams = Team::where('status', 'active')
                ->whereNotIn('id', $project->teams->pluck('id'))
                ->get();
        } else {
            $availableTeams = Team::where('status', 'active')
                ->where('leader_id', Auth::id())
                ->whereNotIn('id', $project->teams->pluck('id'))
                ->get();
        }

        return view('projects.teams.create', compact('project', 'availableTeams'));
    }

    /**
     * Добавить команду к проекту
     */
    public function store(Request $request, Project $project)
    {
        // Проверяем, что пользователь является создателем проекта или администратором
        if ($project->created_by !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'Только создатель проекта или администратор может добавлять команды.');
        }

        $validated = $request->validate([
            'team_id' => 'required|exists:teams,id',
            'role_description' => 'nullable|string|max:500',
            'deadline' => 'nullable|date|after:today',
        ]);

        // Проверяем, что команда не уже добавлена
        if ($project->teams->contains($validated['team_id'])) {
            return back()->with('error', 'Эта команда уже участвует в проекте.');
        }

        $project->addTeam(
            $validated['team_id'],
            $validated['role_description'] ?? null,
            $validated['deadline'] ?? null
        );

        return redirect()->route('projects.teams.index', $project)
            ->with('success', 'Команда успешно добавлена к проекту!');
    }

    /**
     * Удалить команду из проекта
     */
    public function destroy(Project $project, Team $team)
    {
        // Проверяем, что пользователь является создателем проекта или администратором
        if ($project->created_by !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'Только создатель проекта или администратор может удалять команды.');
        }

        // Проверяем, что команда участвует в проекте
        if (!$project->teams->contains($team->id)) {
            return back()->with('error', 'Эта команда не участвует в проекте.');
        }

        $project->removeTeam($team->id);

        return redirect()->route('projects.teams.index', $project)
            ->with('success', 'Команда удалена из проекта.');
    }

    /**
     * Обновить статус команды в проекте
     */
    public function updateStatus(Request $request, Project $project, Team $team)
    {
        // Проверяем, что пользователь является создателем проекта или администратором
        if ($project->created_by !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'Только создатель проекта или администратор может изменять статус команд.');
        }

        $validated = $request->validate([
            'status' => 'required|in:active,completed,withdrawn',
        ]);

        $project->updateTeamStatus($team->id, $validated['status']);

        return redirect()->route('projects.teams.index', $project)
            ->with('success', 'Статус команды обновлен.');
    }
}