<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Factories;

/**
 * Description of UserFactory
 *
 * @author Soo Yu Hung
 */
use App\Models\User;

class UserFactory {

    public function definition(): array {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static {
        return $this->state(fn(array $attributes) => [
                    'email_verified_at' => null,
        ]);
    }

    public static function createUser($type) {
        $user = new User();

        switch ($type) {
            case 'admin':
                $user->role = 'admin';
                break;
            case 'manager':
                $user->role = 'manager';
                break;
            default:
                $user->role = 'user';
                break;
        }

        return $user;
    }
}
