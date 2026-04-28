<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * POST /api/orders - إنشاء طلب جديد
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'shipping_name'    => 'required|string',
            'shipping_phone'   => 'required|string',
            'shipping_city'    => 'required|string',
            'shipping_address' => 'required|string',
            'payment_method'   => 'required|in:cash,card,wallet',
            'notes'            => 'nullable|string',
        ]);

        $cartItems = CartItem::with('product')
                             ->where('user_id', $request->user()->id)
                             ->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['status' => false, 'message' => 'السلة فارغة'], 422);
        }

        DB::beginTransaction();
        try {
            $subtotal = $cartItems->sum(fn($item) => $item->product->current_price * $item->quantity);

            $order = Order::create([
                'user_id'          => $request->user()->id,
                'subtotal'         => $subtotal,
                'shipping_fee'     => 10,
                'total'            => $subtotal + 10,
                'payment_method'   => $request->payment_method,
                'shipping_name'    => $request->shipping_name,
                'shipping_phone'   => $request->shipping_phone,
                'shipping_city'    => $request->shipping_city,
                'shipping_address' => $request->shipping_address,
                'notes'            => $request->notes,
            ]);

            foreach ($cartItems as $item) {
                $order->items()->create([
                    'product_id'   => $item->product_id,
                    'product_name' => $item->product->name,
                    'price'        => $item->product->current_price,
                    'quantity'     => $item->quantity,
                    'total'        => $item->product->current_price * $item->quantity,
                ]);

                $item->product->decrement('stock', $item->quantity);
                $item->product->increment('sales_count', $item->quantity);
            }

            CartItem::where('user_id', $request->user()->id)->delete();

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'تم إنشاء الطلب بنجاح',
                'order'   => $order->load('items'),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'حدث خطأ، يرجى المحاولة مجدداً'], 500);
        }
    }

    /**
     * GET /api/orders - طلباتي
     */
    public function index(Request $request): JsonResponse
    {
        $orders = Order::with('items.product')
                       ->where('user_id', $request->user()->id)
                       ->latest()
                       ->paginate(10);

        return response()->json(['status' => true, 'data' => $orders]);
    }

    /**
     * GET /api/orders/{order} - تفاصيل طلب
     */
    public function show(Request $request, Order $order): JsonResponse
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['status' => false, 'message' => 'غير مصرح'], 403);
        }

        return response()->json(['status' => true, 'data' => $order->load('items.product')]);
    }
}
