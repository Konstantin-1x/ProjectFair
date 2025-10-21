<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use App\Models\Tag;
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
    public function index(Request $request)
    {
        $query = Team::with(['leader', 'members', 'projects', 'tags']);

        // Фильтр по статусу
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Поиск по названию команды или лидеру
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('leader', function ($leaderQuery) use ($search) {
                      $leaderQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Фильтр по максимальному размеру команды
        if ($request->filled('max_members')) {
            $query->where('max_members', $request->max_members);
        }

        $teams = $query->latest()->paginate(12);

        return view('teams.index', compact('teams'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tags = Tag::all();
        return view('teams.create', compact('tags'));
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
            'recruitment_start' => 'nullable|date',
            'recruitment_end' => 'nullable|date|after:recruitment_start',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'new_tags' => 'nullable|string',
        ]);

        $validated['leader_id'] = Auth::id();
        $validated['status'] = 'recruiting';

        $team = Team::create($validated);

        // Добавляем создателя как лидера команды
        $team->members()->attach(Auth::id(), [
            'role' => 'leader',
            'joined_at' => now()
        ]);

        // Обрабатываем существующие теги
        if ($request->has('tags')) {
            $team->tags()->attach($request->tags);
        }

        // Обрабатываем новые теги
        if ($request->filled('new_tags')) {
            $newTagNames = array_filter(array_map('trim', explode(',', $request->new_tags)));
            
            foreach ($newTagNames as $tagName) {
                if (!empty($tagName)) {
                    // Проверяем, существует ли тег
                    $tag = Tag::firstOrCreate(
                        ['name' => $tagName],
                        ['color' => '#6c757d', 'description' => 'Создан при создании команды']
                    );
                    
                    // Добавляем тег к команде, если его еще нет
                    if (!$team->tags()->where('tag_id', $tag->id)->exists()) {
                        $team->tags()->attach($tag->id);
                    }
                }
            }
        }

        return redirect()->route('teams.show', $team)
            ->with('success', 'Команда успешно создана!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Team $team)
    {
        $team->load(['leader', 'members', 'projects', 'vacancies', 'tags']);
        
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

        // Удаляем пользователя из команды
        $team->members()->detach(Auth::id());

        // Очищаем заявку пользователя (если есть)
        $team->applications()
            ->where('user_id', Auth::id())
            ->delete();

        return back()->with('success', 'Вы покинули команду.');
    }

    /**
     * Remove member from team (admin only)
     */
    public function removeMember(Request $request, Team $team, User $user)
    {
        // Проверяем, что пользователь является администратором
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Только администратор может удалять участников из команды.');
        }

        // Проверяем, что пользователь в команде
        if (!$team->members->contains($user->id)) {
            return back()->with('error', 'Пользователь не состоит в этой команде.');
        }

        // Проверяем, что удаляемый пользователь не лидер
        if ($team->leader_id === $user->id) {
            return back()->with('error', 'Нельзя удалить лидера команды. Сначала передайте лидерство другому участнику.');
        }

        $reason = $request->get('reason', 'Удален администратором');
        
        $team->kickMember($user->id, $reason);

        return back()->with('success', "Пользователь {$user->name} удален из команды.");
    }

    public function excludeMember(Request $request, Team $team, User $user)
    {
        // Проверяем, что пользователь является лидером команды
        if ($team->leader_id !== Auth::id()) {
            abort(403, 'Только лидер команды может исключать участников.');
        }
        
        if (!$team->members->contains($user->id)) {
            return back()->with('error', 'Пользователь не состоит в этой команде.');
        }
        
        if ($team->leader_id === $user->id) {
            return back()->with('error', 'Нельзя исключить лидера команды. Сначала передайте лидерство другому участнику.');
        }
        
        $reason = $request->get('reason', 'Исключен лидером команды');
        $result = $team->excludeMember($user->id, $reason);
        
        if ($result) {
            return back()->with('success', "Пользователь {$user->name} исключен из команды.");
        } else {
            return back()->with('error', 'Не удалось исключить пользователя из команды.');
        }
    }
}
