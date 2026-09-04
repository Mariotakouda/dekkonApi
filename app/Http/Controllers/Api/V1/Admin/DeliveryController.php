<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Actions\Delivery\AssignDriverAction;
use App\Actions\Delivery\UpdateDeliveryStatusAction;
use App\Enums\DeliveryStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AssignDeliveryRequest;
use App\Http\Resources\Admin\DeliveryResource;
use App\Models\Delivery;
use App\Models\Driver;
use App\Traits\ApiResponder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    use ApiResponder;

    public function index(Request $request): JsonResponse
    {
        $deliveries = Delivery::with(['order', 'driver.employee'])
            ->when($request->query('status'), fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate($request->integer('per_page', 20));

        return $this->paginated($deliveries, DeliveryResource::class);
    }

    public function assign(AssignDeliveryRequest $request, Delivery $delivery, AssignDriverAction $action): JsonResponse
    {
        $driver = Driver::findOrFail($request->driver_id);

        $delivery = $action->execute($delivery, $driver);

        return $this->success(new DeliveryResource($delivery), 'Livreur affecté.');
    }

    public function updateStatus(Request $request, Delivery $delivery, UpdateDeliveryStatusAction $action): JsonResponse
    {
        $request->validate([
            'status' => ['required', 'string'],
            'failure_reason' => ['nullable', 'string'],
        ]);

        $delivery = $action->execute(
            $delivery,
            DeliveryStatus::from($request->status),
            $request->input('failure_reason'),
        );

        return $this->success(new DeliveryResource($delivery), 'Statut de livraison mis à jour.');
    }
}
