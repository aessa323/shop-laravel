<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WishlistController extends Controller
{
    /**
     * GET /api/wishlist
     * جلب قائمة المفضلة للمستخدم
     */
    public function index(Request $request): JsonResponse
    {
        $items = Wishlist::with(['product' => function ($q) {
                              $q->with('images')->active();
                          }])
                         ->where('user_id', $request->user()->id)
                         ->latest()
                         ->get();

        return response()->json([
            'status' => true,
            'data'   => $items,
            'count'  => $items->count(),
        ]);
    }

    /**
     * POST /api/wishlist/{productId}
     * إضافة أو إزالة من المفضلة (Toggle)
     */
    public function toggle(Request $request, int $productId): JsonResponse
    {
        $existing = Wishlist::where('user_id', $request->user()->id)
                            ->where('product_id', $productId)
                            ->first();

        if ($existing) {
            // موجود → احذفه
            $existing->delete();
            return response()->json([
                'status'  => true,
                'action'  => 'removed',
                'message' => 'تم الإزالة من المفضلة',
            ]);
        }

        // مش موجود → أضفه
        Wishlist::create([
            'user_id'    => $request->user()->id,
            'product_id' => $productId,
        ]);

        return response()->json([
            'status'  => true,
            'action'  => 'added',
            'message' => 'تم الإضافة للمفضلة',
        ], 201);
    }

    /**
     * DELETE /api/wishlist/clear
     * مسح كل المفضلة
     */
    public function clear(Request $request): JsonResponse
    {
        Wishlist::where('user_id', $request->user()->id)->delete();

        return response()->json([
            'status'  => true,
            'message' => 'تم مسح المفضلة',
        ]);
    }
}
 