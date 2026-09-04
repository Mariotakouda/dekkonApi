<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\PermissionResource;
use App\Models\Permission;
use App\Traits\ApiResponder;
use Illuminate\Http\JsonResponse;

class PermissionController extends Controller
{
    use ApiResponder;

    public function index(): JsonResponse
    {
        $permissions = Permission::orderBy('code')->get();

        return $this->success(PermissionResource::collection($permissions));
    }
}
