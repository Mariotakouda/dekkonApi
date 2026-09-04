<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Traits\ApiResponder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Support\MediaUrl;
use Illuminate\Support\Facades\Storage;

class ProductImageController extends Controller
{
    use ApiResponder;

    public function store(Request $request, Product $product): JsonResponse
    {
        $request->validate([
            'image' => ['required', 'image', 'max:4096'], // 4 Mo max
            'alt_text' => ['nullable', 'string', 'max:255'],
            'is_primary' => ['nullable', 'boolean'],
        ]);

        $path = $request->file('image')->store('products', 'public');
        $url = MediaUrl::to($path);

        $image = DB::transaction(function () use ($request, $product, $url) {
            if ($request->boolean('is_primary')) {
                $product->images()->update(['is_primary' => false]);
            }

            return $product->images()->create([
                'url' => $url,
                'alt_text' => $request->input('alt_text', $product->name),
                'sort_order' => $product->images()->max('sort_order') + 1,
                'is_primary' => $request->boolean('is_primary', $product->images()->count() === 0),
            ]);
        });

        return $this->success([
            'id' => $image->id,
            'url' => $image->url,
            'is_primary' => $image->is_primary,
        ], 'Image ajoutée.', 201);
    }

    public function destroy(Product $product, ProductImage $image): JsonResponse
    {
        abort_if($image->product_id !== $product->id, 404, 'Image introuvable pour ce produit.');

        // Supprime le fichier physique du disque avant l'enregistrement DB
        $relativePath = MediaUrl::toRelativePath($image->url);
        Storage::disk('public')->delete($relativePath);

        $image->delete();

        return $this->success(message: 'Image supprimée.');
    }

    public function setPrimary(Product $product, ProductImage $image): JsonResponse
    {
        abort_if($image->product_id !== $product->id, 404, 'Image introuvable pour ce produit.');

        DB::transaction(function () use ($product, $image) {
            $product->images()->update(['is_primary' => false]);
            $image->update(['is_primary' => true]);
        });

        return $this->success(message: 'Image définie comme principale.');
    }
}
