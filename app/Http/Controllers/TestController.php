<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Technology;
use App\Models\Team;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function test()
    {
        try {
            $tags = Tag::all();
            $technologies = Technology::all();
            $teams = Team::whereIn('status', ['recruiting', 'active'])->get();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'tags_count' => $tags->count(),
                    'technologies_count' => $technologies->count(),
                    'teams_count' => $teams->count(),
                    'tags' => $tags->pluck('name')->toArray(),
                    'technologies' => $technologies->pluck('name')->toArray(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}