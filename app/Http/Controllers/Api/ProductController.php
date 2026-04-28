<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    /**
     * GET /api/products
     * جلب المنتجات مع فلترة وبحث
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::with(['category', 'images'])
                        ->active();

        // فلترة حسب الفئة
        if ($request->category_id) {
            $query->byCategory($request->category_id);
        }

        // فلترة حسب العلامة التجارية
        if ($request->brand) {
            $query->where('brand', $request->brand);
        }

        // فلترة حسب السعر
        if ($request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }

        // البحث
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%");
            });
        }

        // الترتيب
        match ($request->sort ?? 'newest') {
            'price_asc'  => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'rating'     => $query->orderBy('rating_avg', 'desc'),
            'sales'      => $query->orderBy('sales_count', 'desc'),
            default      => $query->latest(),
        };

        $products = $query->paginate($request->per_page ?? 12);

        return response()->json([
            'status'  => true,
            'data'    => $products,
        ]);
    }

    /**
     * GET /api/products/featured
     * المنتجات المقترحة للصفحة الرئيسية
     */
    public function featured(): JsonResponse
    {
        $products = Product::with(['category', 'images'])
                           ->active()
                           ->featured()
                           ->take(8)
                           ->get();

        return response()->json(['status' => true, 'data' => $products]);
    }

    /**
     * GET /api/products/best-sellers
     * الأفضل مبيعاً
     */
    public function bestSellers(): JsonResponse
    {
        $products = Product::with(['category', 'images'])
                           ->active()
                           ->bestSeller()
                           ->orderBy('sales_count', 'desc')
                           ->take(6)
                           ->get();

        return response()->json(['status' => true, 'data' => $products]);
    }

    /**
     * GET /api/products/flash-sale
     * عروض Flash Sale
     */
    public function flashSale(): JsonResponse
    {
        $products = Product::with(['category', 'images'])
                           ->active()
                           ->flashSale()
                           ->take(4)
                           ->get();

        return response()->json(['status' => true, 'data' => $products]);
    }

    /**
     * GET /api/products/{slug}
     * تفاصيل منتج معين
     */
    public function show(string $slug): JsonResponse
    {
        $product = Product::with(['category', 'images', 'reviews.user'])
                          ->where('slug', $slug)
                          ->active()
                          ->firstOrFail();

        // زيادة عدد المشاهدات
        $product->increment('views');

        // منتجات مشابهة
        $related = Product::with(['images'])
                          ->active()
                          ->byCategory($product->category_id)
                          ->where('id', '!=', $product->id)
                          ->take(4)
                          ->get();

        return response()->json([
            'status'  => true,
            'data'    => $product,
            'related' => $related,
        ]);
    }
}
 