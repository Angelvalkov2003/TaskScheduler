<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

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


}