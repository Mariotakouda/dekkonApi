<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Enums\EmployeeStatus;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreEmployeeRequest;
use App\Http\Requests\Admin\UpdateEmployeeRequest;
use App\Http\Resources\Admin\EmployeeResource;
use App\Models\Employee;
use App\Models\User;
use App\Traits\ApiResponder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    use ApiResponder;

    public function index(Request $request): JsonResponse
    {
        $employees = Employee::with('user.role')
            ->when($request->query('status'), fn ($q, $status) => $q->where('status', $status))
            ->when($request->query('search'), function ($q, $search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('first_name', 'ILIKE', "%{$search}%")
                        ->orWhere('last_name', 'ILIKE', "%{$search}%")
                        ->orWhere('employee_number', 'ILIKE', "%{$search}%");
                });
            })
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return $this->paginated($employees, EmployeeResource::class);
    }

    public function store(StoreEmployeeRequest $request): JsonResponse
    {
        $employee = DB::transaction(function () use ($request) {
            $user = User::create([
                'role_id' => $request->role_id,
                'name' => $request->first_name . ' ' . $request->last_name,
                'code' => 'USR-' . Str::upper(Str::random(8)),
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'status' => UserStatus::ACTIVE,
                'email_verified_at' => now(),
                'is_active' => true,
            ]);

            $lastNumber = Employee::max('employee_number');
            $nextNumber = $lastNumber ? ((int) substr($lastNumber, 4)) + 1 : 1;

            return Employee::create([
                'user_id' => $user->id,
                'employee_number' => 'EMP-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT),
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'position' => $request->position,
                'date_hired_at' => $request->date_hired_at ?? now(),
                'gender' => $request->gender,
                'status' => EmployeeStatus::ACTIVE,
            ]);
        });

        return $this->success(new EmployeeResource($employee->load('user.role')), 'Employé créé.', 201);
    }

    public function show(Employee $employee): JsonResponse
    {
        return $this->success(new EmployeeResource($employee->load('user.role')));
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee): JsonResponse
    {
        $this->authorize('update', $employee);

        DB::transaction(function () use ($request, $employee) {
            $employee->user->update($request->only(['email', 'phone', 'role_id']));

            $employee->update($request->only(['first_name', 'last_name', 'position', 'gender', 'status']));

            // Synchronise le statut User avec le statut Employee (cohérence)
            if ($request->filled('status')) {
                $employee->user->update([
                    'is_active' => $request->status === 'ACTIVE',
                ]);
            }
        });

        return $this->success(new EmployeeResource($employee->fresh('user.role')), 'Employé mis à jour.');
    }

    public function destroy(Employee $employee): JsonResponse
    {
        $this->authorize('delete', $employee);

        // Désactivation plutôt que suppression physique (préserve l'historique ActivityLog, StockMovement...)
        $employee->update(['status' => EmployeeStatus::INACTIVE]);
        $employee->user->update(['is_active' => false, 'status' => UserStatus::INACTIVE]);

        return $this->success(message: 'Employé désactivé.');
    }
}
