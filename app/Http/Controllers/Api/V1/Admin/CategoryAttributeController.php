<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryAttributeRequest;
use App\Http\Requests\Admin\UpdateCategoryAttributeRequest;
use App\Http\Resources\Admin\CategoryAttributeResource;
use App\Models\Category;
use App\Models\CategoryAttribute;
use App\Traits\ApiResponder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class CategoryAttributeController extends Controller
{
    use ApiResponder;

    public function index(Category $category): JsonResponse
    {
        return $this->success(CategoryAttributeResource::collection($category->attributes));
    }

    public function store(StoreCategoryAttributeRequest $request, Category $category): JsonResponse
    {
        $data = $request->validated();
        $data['key'] = Str::slug($data['key'], '_');

        $attribute = $category->attributes()->create($data);

        return $this->success(new CategoryAttributeResource($attribute), 'Attribut créé.', 201);
    }

    public function update(UpdateCategoryAttributeRequest $request, Category $category, CategoryAttribute $attribute): JsonResponse
    {
        abort_if($attribute->category_id !== $category->id, 404, 'Attribut introuvable pour cette catégorie.');

        $attribute->update($request->validated());

        return $this->success(new CategoryAttributeResource($attribute->fresh()), 'Attribut mis à jour.');
    }

    public function destroy(Category $category, CategoryAttribute $attribute): JsonResponse
    {
        abort_if($attribute->category_id !== $category->id, 404, 'Attribut introuvable pour cette catégorie.');

        $hasValues = $attribute->values()->exists();
        abort_if($hasValues, 409, 'Impossible de supprimer un attribut déjà utilisé par des produits.');

        $attribute->delete();

        return $this->success(message: 'Attribut supprimé.');
    }
}
