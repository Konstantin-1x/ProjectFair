<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Team;
use App\Models\TeamProjectApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamProjectApplicationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Показать форму подачи заявки командой на проект
     */
    public function create(Project $project)
    {
        $user = Auth::user();
        
        // Получаем команды, где пользователь является лидером
        $userTeams = Team::where('leader_id', $user->id)
            ->whereIn('status', ['active', 'recruiting'])
            ->get();

        // Фильтруем команды, которые могут подать заявку
        $availableTeams = $userTeams->filter(function ($team) use ($project) {
            return $project->canTeamApply($team->id);
        });

        if ($availableTeams->isEmpty()) {
            return redirect()->back()->with('error', 'У вас нет команд, которые могут подать заявку на этот проект.');
        }

        return view('projects.teams.apply', compact('project', 'availableTeams'));
    }

    /**
     * Подать заявку командой на проект
     */
    public function store(Request $request, Project $project)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'team_id' => 'required|exists:teams,id',
            'message' => 'nullable|string|max:1000',
        ]);

        $team = Team::find($validated['team_id']);

        // Проверяем, что пользователь является лидером команды
        if ($team->leader_id !== $user->id) {
            return redirect()->back()->with('error', 'Вы не являетесь лидером этой команды.');
        }

        // Проверяем, может ли команда подать заявку
        if (!$project->canTeamApply($team->id)) {
            return redirect()->back()->with('error', 'Эта команда не может подать заявку на этот проект.');
        }

        try {
            TeamProjectApplication::createApplication(
                $team->id,
                $project->id,
                $user->id,
                $validated['message']
            );

            return redirect()->route('projects.show', $project)
                ->with('success', 'Заявка команды на участие в проекте подана успешно!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Произошла ошибка при подаче заявки.');
        }
    }

    /**
     * Показать заявки команд на проект (для создателя проекта и администраторов)
     */
    public function index(Project $project)
    {
        $user = Auth::user();
        
        // Проверяем права доступа
        if ($project->created_by !== $user->id && !$user->isAdmin()) {
            abort(403, 'Только создатель проекта или администратор может просматривать заявки команд.');
        }

        $applications = $project->teamApplications()
            ->with(['team', 'appliedBy'])
            ->orderBy('applied_at', 'desc')
            ->get();

        return view('projects.teams.applications', compact('project', 'applications'));
    }

    /**
     * Одобрить заявку команды
     */
    public function approve(Project $project, TeamProjectApplication $application)
    {
        $user = Auth::user();
        
        // Проверяем права доступа
        if ($project->created_by !== $user->id && !$user->isAdmin()) {
            abort(403, 'Только создатель проекта или администратор может одобрять заявки.');
        }

        if ($application->project_id !== $project->id) {
            abort(404, 'Заявка не найдена для этого проекта.');
        }

        if (!$application->isPending()) {
            return redirect()->back()->with('error', 'Эта заявка уже была обработана.');
        }

        $application->approve($user->id);

        return redirect()->back()->with('success', 'Заявка команды одобрена. Команда добавлена к проекту.');
    }

    /**
     * Отклонить заявку команды
     */
    public function reject(Project $project, TeamProjectApplication $application)
    {
        $user = Auth::user();
        
        // Проверяем права доступа
        if ($project->created_by !== $user->id && !$user->isAdmin()) {
            abort(403, 'Только создатель проекта или администратор может отклонять заявки.');
        }

        if ($application->project_id !== $project->id) {
            abort(404, 'Заявка не найдена для этого проекта.');
        }

        if (!$application->isPending()) {
            return redirect()->back()->with('error', 'Эта заявка уже была обработана.');
        }

        $application->reject($user->id);

        return redirect()->back()->with('success', 'Заявка команды отклонена.');
    }

    /**
     * Отозвать заявку команды (для лидера команды)
     */
    public function withdraw(Project $project, TeamProjectApplication $application)
    {
        $user = Auth::user();
        
        // Проверяем, что пользователь является лидером команды
        if ($application->team->leader_id !== $user->id) {
            abort(403, 'Только лидер команды может отозвать заявку.');
        }

        if ($application->project_id !== $project->id) {
            abort(404, 'Заявка не найдена для этого проекта.');
        }

        if (!$application->isPending()) {
            return redirect()->back()->with('error', 'Эта заявка уже была обработана.');
        }

        $application->withdraw();

        return redirect()->back()->with('success', 'Заявка отозвана.');
    }

    /**
     * Показать заявки команд пользователя (где он лидер)
     */
    public function myApplications()
    {
        $user = Auth::user();
        
        $applications = TeamProjectApplication::whereHas('team', function ($query) use ($user) {
            $query->where('leader_id', $user->id);
        })
        ->with(['project', 'team'])
        ->orderBy('applied_at', 'desc')
        ->get();

        return view('projects.teams.my-applications', compact('applications'));
    }
}
