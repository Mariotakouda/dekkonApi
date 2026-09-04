<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Http\Resources\Admin\CategoryResource;
use App\Models\Category;
use App\Traits\ApiResponder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    use ApiResponder;

    public function index(): JsonResponse
    {
        $categories = Category::withCount('products')
            ->with('children')
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();

        return $this->success(CategoryResource::collection($categories));
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        $category = Category::create($data);

        return $this->success(new CategoryResource($category), 'Catégorie créée.', 201);
    }

    public function show(Category $category): JsonResponse
    {
        return $this->success(new CategoryResource(
            $category->load(['children', 'attributes'])->loadCount('products')
        ));
    }

    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $category->update($request->validated());

        return $this->success(new CategoryResource($category->fresh('children')), 'Catégorie mise à jour.');
    }

    public function destroy(Category $category): JsonResponse
    {
        abort_if($category->products()->exists(), 409, 'Impossible de supprimer une catégorie contenant des produits.');
        abort_if($category->children()->exists(), 409, 'Impossible de supprimer une catégorie ayant des sous-catégories.');

        $category->delete();

        return $this->success(message: 'Catégorie supprimée.');
    }
}
