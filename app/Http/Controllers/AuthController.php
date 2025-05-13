<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use SweetAlert2\Laravel\Swal;

class AuthController extends Controller
{
    public function registerPage()
    {
        return view('pages.register');
    }

    public function registerProcess(Request $request)
    {
        $validation = $request->validate(
            [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'username' => 'required|string|max:255|unique:users,username',
                'email' => 'required|email:rfc,dns|max:255|unique:users,email',
                'password' => 'required|min:8',
            ],
            [
                'first_name.required' => 'First name is required',
                'first_name.string' => 'First name must be a string',
                'first_name.max' => 'First name may not be greater than 255 characters',
                'last_name.required' => 'Last name is required',
                'last_name.string' => 'Last name must be a string',
                'last_name.max' => 'Last name may not be greater than 255 characters',
                'username.required' => 'Username is required',
                'username.string' => 'Username must be a string',
                'username.max' => 'Username may not be greater than 255 characters',
                'username.unique' => 'Username already taken',
                'email.required' => 'Email is required',
                'email.email' => 'Email must be a valid email address',
                'email.max' => 'Email may not be greater than 255 characters',
                'email.unique' => 'Email already taken',
                'password.required' => 'Password is required',
                'password.min' => 'Password must be at least 8 characters',
            ],
        );
        try {
            $validation['password'] = bcrypt($validation['password']);
            $validation['fullname'] = $request->first_name . ' ' . $request->last_name;
            $validation['username'] = strtolower($validation['username']);
            $validation['id'] = substr(md5(uniqid()), 0, 8);

            User::create($validation);

            return redirect()->route('login')->with('success', 'Registration successfully');
        } catch (\Exception $e) {
            \Log::error('Registration Error: ' . $e->getMessage());

            return redirect()->back()->with('error', 'An error occurred during registration. Please try again.');
        }
    }

    public function loginPage()
    {
        return view('pages.login');
    }

    public function loginProcess(Request $request)
    {
        $validation = $request->validate(
            [
                'email' => 'required|email:rfc,dns|max:255',
                'password' => 'required|min:8',
            ],
            [
                'email.required' => 'Email is required',
                'email.email' => 'Email must be a valid email address',
                'email.max' => 'Email may not be greater than 255 characters',
                'password.required' => 'Password is required',
                'password.min' => 'Password must be at least 8 characters',
            ],
        );
        try {
            if (auth()->attempt($validation)) {
                $request->session()->regenerate();
                return redirect()->route('beranda')->with('success', 'Login successfully');
            }

            return back()->with('error', 'Email or password is incorrect');
        } catch (\Exception $e) {
            \Log::error('Login Error: ' . $e->getMessage());

            return back()->with('error', 'An error occurred during login. Please try again.');
        }
    }

    public function logout(Request $request)
    {
        try {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('beranda')->with('success', 'Logout successfully');
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Logout Error: ' . $e->getMessage());

            // Redirect back with an error message
            return redirect()->route('beranda')->with('error', 'An error occurred during logout. Please try again.');
        }
    }
}
