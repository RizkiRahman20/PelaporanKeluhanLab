<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Models\Contracts\FilamentUser;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    protected static string $view = 'filament.pages.auth.login';

    public function authenticate(): ?LoginResponse
    {
        $data = $this->form->getState();

        // Validasi input kosong / format email
        $this->validate([
            'data.email' => ['required', 'email'],
            'data.password' => ['required'],
        ], [
            'data.email.required' => 'Email wajib diisi.',
            'data.email.email' => 'Format email tidak valid.',
            'data.password.required' => 'Password wajib diisi.',
        ]);

        // Cek email terdaftar atau tidak
        $user = User::where('email', $data['email'])->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => 'Email tidak terdaftar.',
            ]);
        }

        // Cek password benar atau salah
        if (! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'password' => 'Password salah.',
            ]);
        }

        // Opsional: cek user aktif
        if (isset($user->status_aktif) && $user->status !== 'aktif') {
            throw ValidationException::withMessages([
                'email' => 'Akun ini tidak aktif.',
            ]);
        }

        // Opsional: cek apakah user boleh akses panel Filament
        if (
            $user instanceof FilamentUser &&
            ! $user->canAccessPanel(Filament::getCurrentPanel())
        ) {
            throw ValidationException::withMessages([
                'email' => 'Akun ini tidak memiliki akses ke dashboard.',
            ]);
        }

        // Login user
        Filament::auth()->login($user, $data['remember'] ?? false);

        session()->regenerate();

        return app(LoginResponse::class);
    }
}