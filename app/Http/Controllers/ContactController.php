<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function show()
    {
        return view('contact');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'school'      => 'required|string|max:255',
            'city'        => 'required|string|max:100',
            'email'       => 'required|email|max:255',
            'phone'       => 'nullable|string|max:30',
            'students'    => 'nullable|string',
            'message'     => 'nullable|string|max:2000',
        ]);

        // In production this would send an email or save to DB.
        // For now we redirect back with a success flash.
        return redirect()->route('contact')
            ->with('success', true)
            ->with('contact_name', $request->name);
    }
}
