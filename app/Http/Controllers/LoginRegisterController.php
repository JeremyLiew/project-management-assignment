<?php

//Soo Yu Hung

namespace App\Http\Controllers;

use App\Decorators\AuthLogDecorator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log; 

class LoginRegisterController extends Controller
{
    public function __construct()
    {
        // Applying middleware
        $this->middleware('guest')->except(['dashboard', 'logout']);
        $this->middleware('auth')->only(['dashboard', 'logout', 'createUsers']);
    }

    public function register(): View
    {
        return view('auth.register');
    }
    
    public function store(Request $request): RedirectResponse
    {
        $authLogger = new AuthLogDecorator($request);

        try {
            $request->validate([
                'name' => 'required|string|max:250',
                'email' => 'required|string|email:rfc,dns|max:250|unique:users,email',
                'password' => 'required|string|min:8|confirmed'
            ]);
    
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);
    
            $credentials = $request->only('email', 'password');
            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();
                $authLogger->logAction('Registration Successful', [
                    'email' => $request->input('email'),
                ]);
                return redirect()->route('dashboard')
                    ->withSuccess('You have successfully registered & logged in!');
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Check if the validation error is due to the email already existing
            if ($e->errors() && array_key_exists('email', $e->errors())) {
                $authLogger->logAction('Registration Attempt with Existing Email', [
                    'email' => $request->input('email'),
                    'error' => 'Email already exists',
                ]);
            }
    
            throw $e;  // Re-throw the validation exception
        } catch (\Exception $e) {
            $authLogger->logAction('Registration Failed', [
                'error' => $e->getMessage(),
                'email' => $request->input('email'),
            ]);
            return redirect()->back()->with('error', 'Registration failed.');
        }
    }

    public function login(): View
    {
        return view('auth.login');
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $authLogger = new AuthLogDecorator($request);

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $authLogger->logAction('Login Successful', [
                'email' => $request->input('email'),
            ]);

            $user = Auth::user(); // Retrieve the authenticated user
            $request->session()->put('user_email', $user->email);

            return redirect()->route('dashboard')
                 ->withSuccess('Login successful');
        } else {
            $authLogger->logAction('Login Failed', [
                'email' => $request->input('email'),
            ]);
            return back()->withErrors([
                'email' => 'Your provided credentials do not match our records.',
            ])->onlyInput('email');
        }
    }
    
    public function dashboard(): View
    {
        return view('dashboard.index');
    } 
    
    public function logout(Request $request): RedirectResponse
    {
        $authLogger = new AuthLogDecorator($request);
        $authLogger->logAction('Logout Successful', [
            'user_id' => auth()->id(),
        ]);
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')
            ->withSuccess('You have logged out successfully!');
    }
}
