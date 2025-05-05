<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showRegister()
    {
        return view("auth.register");
    }

    public function showLogin()
    {
        return view("auth.login");
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed', //avtomatichno vijda passowrd_confirmation i go sravnqva s password
        ]);//avtomatichno hashira i usloqva

        $user = User::create($validated);

        Auth::login($user);
        return redirect()->route('tasks.index')->with('success', 'User registered!');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($validated)) {
            $request->session()->regenerate();

            return redirect()->route('tasks.index')->with('success', 'Logged in!');
        }

        throw ValidationException::withMessages(['credentials' => 'Incorrect credetials']);

    }

    public function logout(Request $request)
    {
        Auth::logout(); //mahame samo login 

        $request->session()->invalidate(); //Mahame vsqkakva data ot session

        $request->session()->regenerateToken(); //regenerira crfs tokena za slevasha sesiq

        return redirect()->route('tasks.index')->with('success', 'Logout!');
    }

    public function profile()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'You must be logged in to view this page.');
        }

        $user->load([
            'roles', // Зарежда ролите
            'teams', // Зарежда отборите
            'keys',  // Зарежда ключовете
        ]);

        return view('auth.profile', compact('user'));
    }

    public function updateName(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user->name = $validated['name'];
        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Name updated successfully');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user->password = Hash::make($validated['new_password']);
        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Password updated successfully');
    }

    public function updateKeys(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'keys' => 'array',
            'keys.*.host' => 'required|string',
            'keys.*.value' => 'required|string',
        ]);

        // Delete existing keys
        $user->keys()->delete();
        
        // Create new keys
        if (isset($validated['keys'])) {
            foreach ($validated['keys'] as $keyData) {
                $user->keys()->create([
                    'host' => $keyData['host'],
                    'value' => $keyData['value']
                ]);
            }
        }

        return redirect()->route('profile.edit')->with('success', 'API keys updated successfully');
    }
}