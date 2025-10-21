<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\TeamApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamApplicationController extends Controller
{
    /**
     * Показать форму подачи заявки
     */
    public function create(Team $team)
    {
        $user = Auth::user();
        
        // Проверяем, может ли пользователь подать заявку
        if (!$team->canUserApply($user->id)) {
            return redirect()->back()->with('error', 'Вы не можете подать заявку в эту команду.');
        }

        return view('teams.apply', compact('team'));
    }

    /**
     * Подать заявку на вступление в команду
     */
    public function store(Request $request, Team $team)
    {
        $user = Auth::user();
        
        // Проверяем, может ли пользователь подать заявку
        if (!$team->canUserApply($user->id)) {
            return redirect()->back()->with('error', 'Вы не можете подать заявку в эту команду.');
        }

        $request->validate([
            'message' => 'nullable|string|max:1000',
        ]);

        try {
            TeamApplication::createApplication($team->id, $user->id, $request->message);
            
            return redirect()->route('teams.show', $team)
                ->with('success', 'Заявка на вступление в команду подана успешно!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Произошла ошибка при подаче заявки.');
        }
    }

    /**
     * Показать заявки команды (для лидера)
     */
    public function index(Team $team)
    {
        $user = Auth::user();
        
        // Проверяем, является ли пользователь лидером команды
        if ($team->leader_id !== $user->id) {
            return redirect()->back()->with('error', 'У вас нет прав для просмотра заявок этой команды.');
        }

        $applications = $team->applications()
            ->with('user')
            ->orderBy('applied_at', 'desc')
            ->paginate(10);

        return view('teams.applications', compact('team', 'applications'));
    }

    /**
     * Одобрить заявку
     */
    public function approve(Team $team, TeamApplication $application)
    {
        $user = Auth::user();
        
        // Проверяем, является ли пользователь лидером команды
        if ($team->leader_id !== $user->id) {
            return redirect()->back()->with('error', 'У вас нет прав для одобрения заявок этой команды.');
        }

        // Проверяем, что заявка в ожидании
        if (!$application->isPending()) {
            return redirect()->back()->with('error', 'Эта заявка уже обработана.');
        }

        // Проверяем, не превышен ли лимит участников
        if ($team->approvedMembers()->count() >= $team->max_members) {
            return redirect()->back()->with('error', 'Команда уже набрана.');
        }

        try {
            $application->approve($user->id);
            
            return redirect()->back()->with('success', 'Заявка одобрена! Пользователь добавлен в команду.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Произошла ошибка при одобрении заявки.');
        }
    }

    /**
     * Отклонить заявку
     */
    public function reject(Request $request, Team $team, TeamApplication $application)
    {
        $user = Auth::user();
        
        // Проверяем, является ли пользователь лидером команды
        if ($team->leader_id !== $user->id) {
            return redirect()->back()->with('error', 'У вас нет прав для отклонения заявок этой команды.');
        }

        // Проверяем, что заявка в ожидании
        if (!$application->isPending()) {
            return redirect()->back()->with('error', 'Эта заявка уже обработана.');
        }

        $request->validate([
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        try {
            $application->reject($user->id, $request->rejection_reason);
            
            return redirect()->back()->with('success', 'Заявка отклонена.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Произошла ошибка при отклонении заявки.');
        }
    }

    /**
     * Отозвать заявку
     */
    public function withdraw(Team $team, TeamApplication $application)
    {
        $user = Auth::user();
        
        // Проверяем, что пользователь является автором заявки
        if ($application->user_id !== $user->id) {
            return redirect()->back()->with('error', 'Вы можете отозвать только свои заявки.');
        }

        // Проверяем, что заявка в ожидании
        if (!$application->isPending()) {
            return redirect()->back()->with('error', 'Эта заявка уже обработана.');
        }

        try {
            $application->withdraw();
            
            return redirect()->back()->with('success', 'Заявка отозвана.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Произошла ошибка при отзыве заявки.');
        }
    }

    /**
     * Показать мои заявки
     */
    public function myApplications()
    {
        $user = Auth::user();
        $applications = $user->teamApplications()
            ->with('team')
            ->orderBy('applied_at', 'desc')
            ->paginate(10);

        return view('teams.my-applications', compact('applications'));
    }
}
