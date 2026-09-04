<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\ForgotPasswordRequest;
use App\Http\Requests\Client\LoginRequest;
use App\Http\Requests\Client\RegisterRequest;
use App\Http\Requests\Client\ResetPasswordRequest;
use App\Http\Requests\Client\VerifyAccountRequest;
use App\Http\Resources\Client\ProfileResource;
use App\Services\Auth\AuthenticationService;
use App\Traits\ApiResponder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use ApiResponder;

    public function __construct(
        private readonly AuthenticationService $authService
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register($request);

        return $this->success([
            'user' => new ProfileResource($result['user']),
            'token' => $result['token'],
        ], 'Inscription réussie. Un code de vérification a été envoyé par email.', 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->email, $request->password);

        return $this->success([
            'user' => new ProfileResource($result['user']),
            'token' => $result['token'],
        ], 'Connexion réussie.');
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return $this->success(message: 'Déconnexion réussie.');
    }

    public function me(Request $request): JsonResponse
    {
        return $this->success(
            new ProfileResource($request->user()->load(['customer', 'employee', 'role.permissions', 'directPermissions']))
        );
    }

    public function sendVerification(Request $request): JsonResponse
    {
        $user = $request->user();

        abort_if($user->email_verified_at, 422, 'Ce compte est déjà vérifié.');

        $this->authService->sendVerificationCode($user);

        return $this->success(message: 'Code de vérification envoyé.');
    }

    public function verifyAccount(VerifyAccountRequest $request): JsonResponse
    {
        $verified = $this->authService->verifyAccount($request->user(), $request->code);

        if (! $verified) {
            return $this->error('Code invalide ou expiré.', 422);
        }

        return $this->success(new ProfileResource($request->user()->fresh()), 'Compte vérifié avec succès.');
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $this->authService->sendPasswordResetCode($request->email);

        return $this->success(message: 'Un code de réinitialisation a été envoyé par email.');
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $success = $this->authService->resetPassword($request->email, $request->token, $request->password);

        if (! $success) {
            return $this->error('Code invalide ou expiré.', 422);
        }

        return $this->success(message: 'Mot de passe réinitialisé avec succès.');
    }
}
