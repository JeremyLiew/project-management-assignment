<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Http\Controllers;

/**
 * Description of RegisterController
 *
 * @author garys
 */
use App\Http\Controllers\Controller;
use App\Factories\UserFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationEmail;

class RegisterController extends Controller {

    public function register(Request $request) {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|min:10|max:15|unique:users',
            'type' => 'required|string|in:admin,manager,user'
        ]);

        $type = $request->input('type');
        $user = UserFactory::createUser($type);

        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->phone = $request->input('phone');
        $user->password = Hash::make($request->input('password'));
        $user->save();

        // Send verification email
        Mail::to($user->email)->send(new VerificationEmail($user));

        // Or send phone verification
        // Twilio::message($user->phone, 'Your verification code is: ...');

        return response()->json(['message' => 'User registered successfully'], 201);
    }
}
