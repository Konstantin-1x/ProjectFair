<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Project;
use App\Models\Team;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the user's profile.
     */
    public function show(User $user = null)
    {
        // Если пользователь не указан, показываем профиль текущего пользователя
        if (!$user) {
            $user = Auth::user();
        }

        // Получаем проекты пользователя
        $projects = Project::with(['tags', 'technologies', 'team'])
            ->where('created_by', $user->id)
            ->latest()
            ->paginate(6);

        // Получаем команды пользователя
        $teams = $user->teams()->with(['leader', 'projects'])->get();

        // Получаем команды, которыми руководит пользователь
        $ledTeams = $user->ledTeams()->with(['members', 'projects'])->get();

        return view('profile.show', compact('user', 'projects', 'teams', 'ledTeams'));
    }

    /**
     * Show the form for editing the user's profile.
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Update the user's profile.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'institute' => 'nullable|string|max:100',
            'course' => 'nullable|integer|min:1|max:6',
            'group' => 'nullable|string|max:50',
            'bio' => 'nullable|string|max:1000',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Обработка загрузки аватара
        if ($request->hasFile('avatar')) {
            // Удаляем старый аватар, если он есть
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($validated);

        return redirect()->route('profile.show')
            ->with('success', 'Профиль успешно обновлен!');
    }

    /**
     * Show user's projects.
     */
    public function projects(User $user = null)
    {
        if (!$user) {
            $user = Auth::user();
        }

        // Получаем проекты, созданные пользователем
        $createdProjects = Project::with(['tags', 'technologies', 'team'])
            ->where('created_by', $user->id)
            ->latest();

        // Получаем проекты, в которых участвует пользователь через команды
        $participatingProjects = Project::with(['tags', 'technologies', 'teams'])
            ->whereHas('teams', function($query) use ($user) {
                $query->whereHas('members', function($memberQuery) use ($user) {
                    $memberQuery->where('user_id', $user->id);
                });
            })
            ->latest();

        // Объединяем и убираем дубликаты
        $allProjects = $createdProjects->union($participatingProjects)->latest()->paginate(12);

        return view('profile.projects', compact('user') + ['projects' => $allProjects]);
    }

    /**
     * Show user's teams.
     */
    public function teams(User $user = null)
    {
        if (!$user) {
            $user = Auth::user();
        }

        // Получаем команды, где пользователь является участником, но не руководителем проекта
        $teams = $user->teamsAsMember()->with(['leader', 'members', 'projects'])->get();
        $ledTeams = $user->ledTeams()->with(['members', 'projects'])->get();

        return view('profile.teams', compact('user', 'teams', 'ledTeams'));
    }
}
