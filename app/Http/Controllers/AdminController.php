<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use App\Models\Tag;
use App\Models\Technology;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        $stats = [
            'users' => User::count(),
            'projects' => Project::count(),
            'teams' => Team::count(),
            'tasks' => Task::count(),
            'active_projects' => Project::where('status', 'active')->count(),
            'completed_projects' => Project::where('status', 'completed')->count(),
        ];

        $recent_projects = Project::with(['creator', 'tags'])
            ->latest()
            ->take(5)
            ->get();

        $recent_users = User::latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_projects', 'recent_users'));
    }

    public function analytics()
    {
        // Статистика по институтам
        $institute_stats = Project::select('institute', DB::raw('count(*) as count'))
            ->whereNotNull('institute')
            ->groupBy('institute')
            ->orderBy('count', 'desc')
            ->get();

        // Статистика по курсам
        $course_stats = Project::select('course', DB::raw('count(*) as count'))
            ->whereNotNull('course')
            ->groupBy('course')
            ->orderBy('course')
            ->get();

        // Статистика по технологиям
        $technology_stats = DB::table('project_technology')
            ->join('technologies', 'project_technology.technology_id', '=', 'technologies.id')
            ->select('technologies.name', DB::raw('count(*) as count'))
            ->groupBy('technologies.name')
            ->orderBy('count', 'desc')
            ->take(10)
            ->get();

        // Статистика по тегам
        $tag_stats = DB::table('project_tag')
            ->join('tags', 'project_tag.tag_id', '=', 'tags.id')
            ->select('tags.name', DB::raw('count(*) as count'))
            ->groupBy('tags.name')
            ->orderBy('count', 'desc')
            ->take(10)
            ->get();

        // Статистика по месяцам (SQLite совместимо)
        $monthly_stats = Project::select(
                DB::raw('strftime("%Y-%m", created_at) as month'),
                DB::raw('count(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('admin.analytics', compact(
            'institute_stats',
            'course_stats', 
            'technology_stats',
            'tag_stats',
            'monthly_stats'
        ));
    }

    public function users()
    {
        $users = User::withCount(['projects', 'teams'])
            ->latest()
            ->paginate(20);

        return view('admin.users', compact('users'));
    }

    public function projects()
    {
        $projects = Project::with(['creator', 'tags', 'technologies'])
            ->latest()
            ->paginate(20);

        return view('admin.projects', compact('projects'));
    }

    public function teams()
    {
        $teams = Team::with(['leader', 'members'])
            ->withCount('members')
            ->latest()
            ->paginate(20);

        return view('admin.teams', compact('teams'));
    }

    public function tasks()
    {
        $tasks = Task::with(['creator', 'assignedTeam'])
            ->latest()
            ->paginate(20);

        return view('admin.tasks', compact('tasks'));
    }

    public function editUser(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,user',
            'institute' => 'nullable|string|max:100',
            'course' => 'nullable|integer|min:1|max:6',
            'group' => 'nullable|string|max:50',
            'bio' => 'nullable|string|max:1000',
        ]);

        $user->update($validated);

        return redirect()->route('admin.users')
            ->with('success', 'Пользователь успешно обновлен!');
    }

    public function toggleAdmin(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users')
                ->with('error', 'Вы не можете изменить свою собственную роль!');
        }

        $user->update([
            'role' => $user->role === 'admin' ? 'user' : 'admin'
        ]);

        $message = $user->role === 'admin' ? 'назначен администратором' : 'лишен прав администратора';
        
        return redirect()->route('admin.users')
            ->with('success', "Пользователь {$user->name} {$message}!");
    }
}
