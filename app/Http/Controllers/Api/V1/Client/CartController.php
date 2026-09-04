<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Actions\Cart\AddItemToCartAction;
use App\Actions\Cart\RemoveCartItemAction;
use App\Actions\Cart\UpdateCartItemAction;
use App\Enums\CartStatus;
use App\Exceptions\Cart\CartItemNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\AddToCartRequest;
use App\Http\Requests\Client\UpdateCartItemRequest;
use App\Http\Resources\Client\CartResource;
use App\Models\Cart;
use App\Models\ProductVariant;
use App\Traits\ApiResponder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    use ApiResponder;

    public function show(Request $request): JsonResponse
    {
        $cart = $this->getOrCreateActiveCart($request);

        return $this->success(new CartResource($cart));
    }

    public function addItem(AddToCartRequest $request, AddItemToCartAction $action): JsonResponse
    {
        $customer = $request->user()->customer;
        $variant = ProductVariant::findOrFail($request->product_variant_id);

        $action->execute($customer, $variant, $request->quantity);

        $cart = $this->getOrCreateActiveCart($request);

        return $this->success(new CartResource($cart), 'Produit ajouté au panier.', 201);
    }

    public function updateItem(UpdateCartItemRequest $request, string $itemId, UpdateCartItemAction $action): JsonResponse
    {
        $item = $this->findCartItemOrFail($request, $itemId);

        $action->execute($item, $request->quantity);

        $cart = $this->getOrCreateActiveCart($request);

        return $this->success(new CartResource($cart), 'Panier mis à jour.');
    }

    public function removeItem(Request $request, string $itemId, RemoveCartItemAction $action): JsonResponse
    {
        $item = $this->findCartItemOrFail($request, $itemId);

        $action->execute($item);

        $cart = $this->getOrCreateActiveCart($request);

        return $this->success(new CartResource($cart), 'Article retiré du panier.');
    }

    private function getOrCreateActiveCart(Request $request): Cart
    {
        return Cart::firstOrCreate(
            ['customer_id' => $request->user()->customer->id, 'status' => CartStatus::ACTIVE],
        );
    }

    private function findCartItemOrFail(Request $request, string $itemId)
    {
        $cart = $this->getOrCreateActiveCart($request);
        $item = $cart->items()->find($itemId);

        if (! $item) {
            throw new CartItemNotFoundException();
        }

        return $item;
    }
}