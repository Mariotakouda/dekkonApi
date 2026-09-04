<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\Client\CategoryResource;
use App\Services\Catalog\CategoryTreeService;
use App\Traits\ApiResponder;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    use ApiResponder;

    public function __construct(
        private readonly CategoryTreeService $categoryTreeService
    ) {}

    public function index(): JsonResponse
    {
        $categories = $this->categoryTreeService->tree();

        return $this->success(CategoryResource::collection($categories));
    }
}
