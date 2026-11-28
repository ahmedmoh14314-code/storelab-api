<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $cart = $request->user()
            ->cart()
            ->with('items.product')
            ->first();

        return response()->json([
            'data' => [
                'items' => $cart?->items ?? [],
            ],
        ]);
    }

    public function addItem(Request $request): JsonResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $product = Product::findOrFail($data['product_id']);

        if (! $product->is_active) {
            return response()->json([
                'message' => 'This product is not available, Try agin later.',
            ], 422);
        }

        if ($data['quantity'] > $product->stock_quantity) {
            return response()->json([
                'message' => 'Not enough stock available.',
            ], 422);
        }

        $cart = $request->user()->cart()->firstOrCreate([]);

        $cartItem = $cart->items()
            ->where('product_id', $product->id)
            ->first();

        $newQuantity = ($cartItem?->quantity ?? 0) + $data['quantity'];

        if ($newQuantity > $product->stock_quantity) {
            return response()->json([
                'message' => 'Not enough stock available.',
            ], 422);
        }

        if ($cartItem) {
            $cartItem->quantity = $newQuantity;
            $cartItem->save();
        } else {
            $cartItem = new CartItem;
            $cartItem->product_id = $product->id;
            $cartItem->quantity = $newQuantity;

            $cart->items()->save($cartItem);
        }

        return response()->json([
            'message' => 'Product added to cart successfully.',
            'data' => $cartItem->load('product'),
        ], $cartItem->wasRecentlyCreated ? 201 : 200);
    }

    public function updateItem(Request $request, int $item): JsonResponse
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $cart = $request->user()->cart()->firstOrFail();

        $cartItem = $cart->items()
            ->with('product')
            ->findOrFail($item);

        if (! $cartItem->product->is_active) {
            return response()->json([
                'message' => 'This product is not available.',
            ], 422);
        }

        if ($data['quantity'] > $cartItem->product->stock_quantity) {
            return response()->json([
                'message' => 'Not enough stock available.',
            ], 422);
        }

        $cartItem->quantity = $data['quantity'];
        $cartItem->save();

        return response()->json([
            'message' => 'Cart item updated successfully.',
            'data' => $cartItem->load('product'),
        ]);
    }

    public function removeItem(Request $request, int $item): JsonResponse
    {
        $cart = $request->user()->cart()->firstOrFail();

        $cartItem = $cart->items()->findOrFail($item);

        $cartItem->delete();

        return response()->json([
            'message' => 'Item removed from cart successfully.',
        ]);
    }
}
