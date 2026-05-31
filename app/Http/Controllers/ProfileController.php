<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller {
    public function show(User $user) {
        $posts = $user->posts()->with(['user','attachments.poll.options','reactions','hashtags'])->latest()->paginate(10);
        return view('profile.show', compact('user','posts'));
    }
    public function edit(Request $request) {
        return view('profile.edit', ['user' => $request->user()]);
    }
    public function update(Request $request) {
        $user = $request->user();
        $request->validate(['name'=>'required|string|max:255','bio'=>'nullable|string|max:1000','location'=>'nullable|string|max:100']);
        $user->update($request->only('name','bio','location'));
        return redirect()->route('profile.show', ['user' => $user])->with('success', 'Profil mis à jour');
    }
    public function updateAvatar(Request $request) {
        $request->validate(['avatar'=>'required|image|max:5120']);
        $user = $request->user();
        if($user->avatar_path) Storage::disk('public')->delete($user->avatar_path);
        $path = $request->file('avatar')->store('avatars','public');
        $user->update(['avatar_path'=>$path]);
        return back()->with('success','Avatar mis à jour');
    }
    public function destroy(Request $request) {
        $user = $request->user();
        Auth::logout();
        $user->delete();
        $request->session()->invalidate();
        return redirect('/');
    }
}
