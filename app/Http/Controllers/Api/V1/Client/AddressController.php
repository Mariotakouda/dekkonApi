<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StoreAddressRequest;
use App\Http\Requests\Client\UpdateAddressRequest;
use App\Http\Resources\Client\AddressResource;
use App\Models\Address;
use App\Traits\ApiResponder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    use ApiResponder;

    public function index(Request $request): JsonResponse
    {
        $addresses = $request->user()->customer->addresses()->latest()->get();

        return $this->success(AddressResource::collection($addresses));
    }

    public function store(StoreAddressRequest $request): JsonResponse
    {
        $customer = $request->user()->customer;

        $address = DB::transaction(function () use ($request, $customer) {
            if ($request->boolean('is_default')) {
                $customer->addresses()->update(['is_default' => false]);
            }

            return $customer->addresses()->create($request->validated());
        });

        return $this->success(new AddressResource($address), 'Adresse ajoutée.', 201);
    }

    public function show(Request $request, Address $address): JsonResponse
    {
        $this->authorize('view', $address);

        return $this->success(new AddressResource($address));
    }

    public function update(UpdateAddressRequest $request, Address $address): JsonResponse
    {
        $this->authorize('update', $address);

        DB::transaction(function () use ($request, $address) {
            if ($request->boolean('is_default')) {
                $address->customer->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
            }

            $address->update($request->validated());
        });

        return $this->success(new AddressResource($address->fresh()), 'Adresse mise à jour.');
    }

    public function destroy(Request $request, Address $address): JsonResponse
    {
        $this->authorize('delete', $address);

        $address->delete();

        return $this->success(message: 'Adresse supprimée.');
    }
}
