<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\UpdateProfileRequest;
use App\Http\Resources\Client\ProfileResource;
use App\Traits\ApiResponder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    use ApiResponder;

    public function show(Request $request): JsonResponse
    {
        return $this->success(
            new ProfileResource($request->user()->load('customer'))
        );
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();

        DB::transaction(function () use ($request, $user) {
            $user->update($request->only(['email', 'phone']));

            $user->customer?->update($request->only([
                'first_name', 'last_name', 'date_of_birth', 'gender',
            ]));
        });

        return $this->success(
            new ProfileResource($user->fresh('customer')),
            'Profil mis à jour.'
        );
    }
}
