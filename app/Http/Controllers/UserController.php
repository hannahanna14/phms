<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        $users = User::select('id', 'username', 'full_name', 'email', 'role')
            ->orderBy('full_name')
            ->get();
            
        return Inertia::render('User/Index', [
            'users' => $users
        ]);
    }

    public function create()
    {
        return Inertia::render('User/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|unique:users,username',
            'full_name' => 'required',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,nurse,teacher'
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('users.index')
            ->with('message', 'User created successfully');
    }

    public function edit(User $user)
    {
        return Inertia::render('User/Edit', [
            'user' => $user->only('id', 'username', 'full_name', 'email', 'role')
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'username' => 'required|unique:users,username,'.$user->id,
            'full_name' => 'required',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'role' => 'required|in:admin,nurse,teacher',
            'password' => 'nullable|min:6'
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('users.index')
            ->with('message', 'User updated successfully');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')
            ->with('message', 'User deleted successfully');
    }
}
