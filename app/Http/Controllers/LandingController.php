<?php
namespace App\Http\Controllers;

use App\Models\{School, Post};

class LandingController extends Controller
{
    public function show()
    {
        if (auth()->check()) {
            return match(auth()->user()->role) {
                'admin'            => redirect()->route('admin.dashboard'),
                'director'         => redirect()->route('dashboard'),
                default            => redirect()->route('feed'),
            };
        }
        $schools      = School::where('is_active', true)->take(6)->pluck('name');
        $previewPosts = Post::with('user')->latest()->take(2)->get();
        return view('landing', compact('schools', 'previewPosts'));
    }
}
