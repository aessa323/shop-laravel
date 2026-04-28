<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReviewController extends Controller
{
    /**
     * GET /api/products/{id}/reviews
     * جلب تقييمات منتج معين
     */
    public function index(int $id): JsonResponse
    {
        $product = Product::findOrFail($id);

        $reviews = Review::with('user:id,name')
                         ->where('product_id', $id)
                         ->where('is_approved', true)
                         ->latest()
                         ->paginate(10);

        // إحصائيات التقييمات
        $stats = Review::where('product_id', $id)
                       ->where('is_approved', true)
                       ->selectRaw('
                           COUNT(*) as total,
                           AVG(rating) as average,
                           SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five_star,
                           SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as four_star,
                           SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as three_star,
                           SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as two_star,
                           SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as one_star
                       ')
                       ->first();

        return response()->json([
            'status'  => true,
            'data'    => $reviews,
            'stats'   => $stats,
        ]);
    }

    /**
     * POST /api/products/{id}/reviews
     * إضافة تقييم جديد (يحتاج تسجيل دخول)
     */
    public function store(Request $request, int $id): JsonResponse
    {
        $product = Product::findOrFail($id);

        // تحقق إذا المستخدم قيّم هذا المنتج مسبقاً
        $existing = Review::where('product_id', $id)
                          ->where('user_id', $request->user()->id)
                          ->first();

        if ($existing) {
            return response()->json([
                'status'  => false,
                'message' => 'لقد قمت بتقييم هذا المنتج مسبقاً',
            ], 422);
        }

        $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        // تحقق إذا اشترى المنتج فعلاً
        $isPurchased = $request->user()
                               ->orders()
                               ->whereHas('items', fn($q) => $q->where('product_id', $id))
                               ->exists();

        $review = Review::create([
            'product_id'  => $id,
            'user_id'     => $request->user()->id,
            'rating'      => $request->rating,
            'comment'     => $request->comment,
            'is_verified' => $isPurchased,
        ]);

        // تحديث متوسط التقييم في جدول المنتجات
        $avg   = Review::where('product_id', $id)->avg('rating');
        $count = Review::where('product_id', $id)->count();

        $product->update([
            'rating_avg'   => round($avg, 2),
            'rating_count' => $count,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'تم إضافة تقييمك بنجاح',
            'data'    => $review->load('user:id,name'),
        ], 201);
    }

    /**
     * DELETE /api/reviews/{id}
     * حذف تقييم (للمستخدم نفسه فقط)
     */
    public function destroy(Request $request, Review $review): JsonResponse
    {
        if ($review->user_id !== $request->user()->id) {
            return response()->json([
                'status'  => false,
                'message' => 'غير مصرح لك بحذف هذا التقييم',
            ], 403);
        }

        $productId = $review->product_id;
        $review->delete();

        // تحديث المتوسط بعد الحذف
        $avg   = Review::where('product_id', $productId)->avg('rating') ?? 0;
        $count = Review::where('product_id', $productId)->count();

        Product::where('id', $productId)->update([
            'rating_avg'   => round($avg, 2),
            'rating_count' => $count,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'تم حذف التقييم',
        ]);
    }
}
 