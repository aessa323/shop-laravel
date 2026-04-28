<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * GET /api/cart - جلب سلة التسوق
     */
    public function index(Request $request): JsonResponse
    {
        $items = CartItem::with(['product.images'])
                         ->where('user_id', $request->user()->id)
                         ->get();

        $total = $items->sum(fn($item) => $item->product->current_price * $item->quantity);

        return response()->json([
            'status' => true,
            'items'  => $items,
            'total'  => $total,
            'count'  => $items->sum('quantity'),
        ]);
    }

    /**
     * POST /api/cart - إضافة منتج للسلة
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1|max:10',
        ]);

        $product = Product::findOrFail($request->product_id);

        if (!$product->is_in_stock) {
            return response()->json(['status' => false, 'message' => 'المنتج غير متوفر'], 422);
        }

        $cartItem = CartItem::updateOrCreate(
            ['user_id' => $request->user()->id, 'product_id' => $request->product_id],
            ['quantity' => DB::raw("quantity + {$request->quantity}")]
        );

        return response()->json([
            'status'  => true,
            'message' => 'تم إضافة المنتج للسلة',
            'item'    => $cartItem->load('product'),
        ]);
    }

    /**
     * PUT /api/cart/{id} - تعديل الكمية
     */
    public function update(Request $request, CartItem $cartItem): JsonResponse
    {
        if ($cartItem->user_id !== $request->user()->id) {
            return response()->json(['status' => false, 'message' => 'غير مصرح'], 403);
        }

        $request->validate(['quantity' => 'required|integer|min:1|max:10']);

        $cartItem->update(['quantity' => $request->quantity]);

        return response()->json(['status' => true, 'message' => 'تم التحديث', 'item' => $cartItem]);
    }

    /**
     * DELETE /api/cart/{id} - حذف من السلة
     */
    public function destroy(Request $request, CartItem $cartItem): JsonResponse
    {
        if ($cartItem->user_id !== $request->user()->id) {
            return response()->json(['status' => false, 'message' => 'غير مصرح'], 403);
        }
        $cartItem->delete();

        return response()->json(['status' => true, 'message' => 'تم الحذف من السلة']);
    }
}
