<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\DriverResource;
use App\Models\Driver;
use App\Models\Employee;
use App\Traits\ApiResponder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    use ApiResponder;

    public function index(Request $request): JsonResponse
    {
        $drivers = Driver::with('employee')
            ->withCount('deliveries')
            ->when($request->query('status'), fn ($q, $s) => $q->where('status', $s))
            ->get();

        return $this->success(DriverResource::collection($drivers));
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'employee_id' => ['required', 'uuid', 'exists:employees,id', 'unique:drivers,employee_id'],
            'vehicle_type' => ['nullable', 'string', 'max:100'],
            'vehicle_number' => ['nullable', 'string', 'max:50'],
        ]);

        $employee = Employee::findOrFail($request->employee_id);

        $driver = Driver::create([
            'employee_id' => $employee->id,
            'vehicle_type' => $request->vehicle_type,
            'vehicle_number' => $request->vehicle_number,
            'status' => \App\Enums\DriverStatus::AVAILABLE,
        ]);

        return $this->success(new DriverResource($driver->load('employee')), 'Livreur créé.', 201);
    }

    public function update(Request $request, Driver $driver): JsonResponse
    {
        $request->validate([
            'vehicle_type' => ['nullable', 'string', 'max:100'],
            'vehicle_number' => ['nullable', 'string', 'max:50'],
            'status' => ['nullable', 'string', 'in:AVAILABLE,BUSY,INACTIVE'],
        ]);

        $driver->update($request->only(['vehicle_type', 'vehicle_number', 'status']));

        return $this->success(new DriverResource($driver->fresh('employee')), 'Livreur mis à jour.');
    }
}
