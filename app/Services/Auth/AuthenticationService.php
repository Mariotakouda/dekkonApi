<?php

namespace App\Services\Auth;

use App\Enums\UserStatus;
use App\Exceptions\Auth\AccountSuspendedException;
use App\Exceptions\Auth\InvalidCredentialsException;
use App\Http\Requests\Client\RegisterRequest;
use App\Mail\ResetPasswordMail;
use App\Mail\VerifyAccountMail;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthenticationService
{
    public function register(RegisterRequest $request): array
    {
        return DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->first_name . ' ' . $request->last_name,
                'code' => 'USR-' . Str::upper(Str::random(8)),
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'status' => UserStatus::ACTIVE,
                'is_active' => true,
            ]);

            Customer::create([
                'user_id' => $user->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
            ]);

            $this->sendVerificationCode($user);

            $token = $user->createToken('mobile')->plainTextToken;

            return [
                'user' => $user->fresh(['customer', 'employee', 'role.permissions', 'directPermissions']),
                'token' => $token,
            ];
        });
    }

    public function login(string $email, string $password): array
    {
        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            throw new InvalidCredentialsException();
        }

        if ($user->status !== UserStatus::ACTIVE || ! $user->is_active) {
            throw new AccountSuspendedException();
        }

        $user->update(['last_login_at' => now()]);

        $token = $user->createToken('mobile')->plainTextToken;

        return [
            'user' => $user->fresh(['customer', 'employee', 'role.permissions', 'directPermissions']),
            'token' => $token,
        ];
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }

    public function logoutFromAllDevices(User $user): void
    {
        $user->tokens()->delete();
    }

    /**
     * Génère et envoie un code de vérification à 6 chiffres (section 4 : "vérification du compte").
     */
    public function sendVerificationCode(User $user): void
    {
        $code = (string) random_int(100000, 999999);

        $user->update([
            'verification_code' => $code,
            'verification_code_expires_at' => now()->addMinutes(15),
        ]);

        Mail::to($user->email)->queue(new VerifyAccountMail($user, $code));
    }

    public function verifyAccount(User $user, string $code): bool
    {
        if ($user->verification_code !== $code) {
            return false;
        }

        if (! $user->verification_code_expires_at || now()->gt($user->verification_code_expires_at)) {
            return false;
        }

        $user->update([
            'email_verified_at' => now(),
            'verification_code' => null,
            'verification_code_expires_at' => null,
        ]);

        return true;
    }

    /**
     * Envoie un code de réinitialisation, stocké haché dans password_reset_tokens (comme le fait Laravel nativement).
     */
    public function sendPasswordResetCode(string $email): void
    {
        $user = User::where('email', $email)->firstOrFail();
        $code = (string) random_int(100000, 999999);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            ['token' => Hash::make($code), 'created_at' => now()]
        );

        Mail::to($email)->queue(new ResetPasswordMail($user, $code));
    }

    public function resetPassword(string $email, string $token, string $newPassword): bool
    {
        $record = DB::table('password_reset_tokens')->where('email', $email)->first();

        if (! $record || ! Hash::check($token, $record->token)) {
            return false;
        }

        // Expiration : 60 minutes
        if (now()->diffInMinutes($record->created_at) > 60) {
            return false;
        }

        $user = User::where('email', $email)->firstOrFail();
        $user->update(['password' => Hash::make($newPassword)]);

        // Invalide tous les tokens Sanctum existants par sécurité
        $user->tokens()->delete();

        DB::table('password_reset_tokens')->where('email', $email)->delete();

        return true;
    }
}
