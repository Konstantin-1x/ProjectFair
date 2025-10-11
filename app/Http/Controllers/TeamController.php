<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TeamController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $teams = Team::with(['leader', 'members', 'projects'])
            ->where('status', 'recruiting')
            ->latest()
            ->paginate(12);

        return view('teams.index', compact('teams'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('teams.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'max_members' => 'required|integer|min:2|max:10',
            'institute' => 'nullable|string|max:100',
            'course' => 'nullable|integer|min:1|max:6',
            'recruitment_start' => 'nullable|date',
            'recruitment_end' => 'nullable|date|after:recruitment_start',
        ]);

        $validated['leader_id'] = Auth::id();
        $validated['status'] = 'recruiting';

        $team = Team::create($validated);

        // Добавляем создателя как лидера команды
        $team->members()->attach(Auth::id(), [
            'role' => 'leader',
            'joined_at' => now()
        ]);

        return redirect()->route('teams.show', $team)
            ->with('success', 'Команда успешно создана!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Team $team)
    {
        $team->load(['leader', 'members', 'projects', 'vacancies']);
        
        return view('teams.show', compact('team'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Team $team)
    {
        // Проверяем, что пользователь является лидером команды
        if ($team->leader_id !== Auth::id()) {
            abort(403, 'Только лидер команды может редактировать команду.');
        }

        return view('teams.edit', compact('team'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Team $team)
    {
        // Проверяем, что пользователь является лидером команды
        if ($team->leader_id !== Auth::id()) {
            abort(403, 'Только лидер команды может редактировать команду.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'max_members' => 'required|integer|min:2|max:10',
            'status' => 'required|in:recruiting,active,completed,disbanded',
            'recruitment_start' => 'nullable|date',
            'recruitment_end' => 'nullable|date|after:recruitment_start',
        ]);

        $team->update($validated);

        return redirect()->route('teams.show', $team)
            ->with('success', 'Команда успешно обновлена!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Team $team)
    {
        // Проверяем, что пользователь является лидером команды
        if ($team->leader_id !== Auth::id()) {
            abort(403, 'Только лидер команды может удалить команду.');
        }

        $team->delete();

        return redirect()->route('teams.index')
            ->with('success', 'Команда успешно удалена!');
    }

    /**
     * Join team
     */
    public function join(Team $team)
    {
        // Проверяем, что команда принимает участников
        if ($team->status !== 'recruiting') {
            return back()->with('error', 'Команда не принимает новых участников.');
        }

        // Проверяем, что команда не переполнена
        if ($team->members->count() >= $team->max_members) {
            return back()->with('error', 'Команда уже полная.');
        }

        // Проверяем, что пользователь еще не в команде
        if ($team->members->contains(Auth::id())) {
            return back()->with('error', 'Вы уже состоите в этой команде.');
        }

        $team->members()->attach(Auth::id(), [
            'role' => 'member',
            'joined_at' => now()
        ]);

        return back()->with('success', 'Вы успешно присоединились к команде!');
    }

    /**
     * Leave team
     */
    public function leave(Team $team)
    {
        // Проверяем, что пользователь в команде
        if (!$team->members->contains(Auth::id())) {
            return back()->with('error', 'Вы не состоите в этой команде.');
        }

        // Проверяем, что пользователь не лидер
        if ($team->leader_id === Auth::id()) {
            return back()->with('error', 'Лидер не может покинуть команду. Сначала передайте лидерство другому участнику.');
        }

        $team->members()->detach(Auth::id());

        return back()->with('success', 'Вы покинули команду.');
    }
}
