<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    /**
     * GET /api/categories
     * جلب كل الفئات الرئيسية مع الفرعية
     */
    public function index(): JsonResponse
    {
        $categories = Category::with('children')
                              ->active()
                              ->parent()
                              ->orderBy('sort_order')
                              ->get();

        return response()->json([
            'status' => true,
            'data'   => $categories,
        ]);
    }

    /**
     * GET /api/categories/{slug}
     * تفاصيل فئة معينة مع منتجاتها
     */
    public function show(string $slug): JsonResponse
    {
        $category = Category::with(['children', 'products' => function ($q) {
                                $q->active()->with('images')->latest();
                            }])
                            ->where('slug', $slug)
                            ->active()
                            ->firstOrFail();

        return response()->json([
            'status' => true,
            'data'   => $category,
        ]);
    }
}
 