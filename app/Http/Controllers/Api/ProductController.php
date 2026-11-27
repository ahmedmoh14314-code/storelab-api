<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function index(): JsonResponse
    {
        $products = Product::where('is_active', true)->get();

        return response()->json([
            'data' => $products,
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $product = Product::where('is_active', true)
            ->findOrFail($id);

        return response()->json([
            'data' => $product,
        ]);
    }

    public function sho4w(int $id): JsonResponse
    {
        $product = Product::where('is_active', true)
            ->findOrFail($id);

        return response()->json([
            'data' => $product,
        ]);
    }
}
