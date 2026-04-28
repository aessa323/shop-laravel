<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Models\Review;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'     => 'مدير المتجر',
            'email'    => 'admin@store.com',
            'password' => bcrypt('password123'),
            'role'     => 'admin',
        ]);

        $user = User::create([
            'name'     => 'أحمد محمد',
            'email'    => 'ahmed@example.com',
            'password' => bcrypt('password123'),
        ]);

        $smartphones = Category::create(['name' => 'الهواتف الذكية', 'name_en' => 'Smartphones', 'icon' => '📱', 'slug' => 'smartphones', 'sort_order' => 1]);
        $computers   = Category::create(['name' => 'أجهزة الكمبيوتر', 'name_en' => 'Computers', 'icon' => '💻', 'slug' => 'computers', 'sort_order' => 2]);
        $home        = Category::create(['name' => 'الأجهزة المنزلية', 'name_en' => 'Home Appliances', 'icon' => '🏠', 'slug' => 'home-appliances', 'sort_order' => 3]);
        $accessories = Category::create(['name' => 'أكسسوارات', 'name_en' => 'Accessories', 'icon' => '🎧', 'slug' => 'accessories', 'sort_order' => 4]);

        $products = [
            [
                'name' => 'سامسونج جالاكسي S22 الترا', 'slug' => 'samsung-galaxy-s22-ultra', 'brand' => 'Samsung',
                'price' => 1780.00, 'stock' => 50, 'category_id' => $smartphones->id,
                'is_featured' => true, 'is_best_seller' => true, 'rating_avg' => 4.8, 'rating_count' => 124, 'sales_count' => 320,
                'thumbnail' => 'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?w=400&h=400&fit=crop',
                'description' => 'هاتف سامسونج المميز بشاشة كبيرة وأداء قوي وكاميرا احترافية',
                'specs' => ['الشاشة' => '6.8 بوصة AMOLED', 'المعالج' => 'Snapdragon 8 Gen 1', 'الذاكرة' => '12GB / 256GB', 'الكاميرا' => '108MP', 'البطارية' => '5000mAh'],
            ],
            [
                'name' => 'كامون 30 بريميير', 'slug' => 'camon-30-premier', 'brand' => 'Tecno',
                'price' => 1200.00, 'stock' => 30, 'category_id' => $smartphones->id,
                'is_featured' => true, 'rating_avg' => 4.5, 'rating_count' => 67, 'sales_count' => 150,
                'thumbnail' => 'https://images.unsplash.com/photo-1512054502232-10a0a035d672?w=400&h=400&fit=crop',
                'description' => 'هاتف تيكنو الرائد بكاميرا متطورة وشاشة عالية التحديث',
                'specs' => ['الشاشة' => '6.78 بوصة 144Hz', 'المعالج' => 'Dimensity 8200', 'الذاكرة' => '16GB / 512GB', 'الكاميرا' => '50MP', 'البطارية' => '5000mAh'],
            ],
            [
                'name' => 'آيفون 15 برو ماكس', 'slug' => 'iphone-15-pro-max', 'brand' => 'Apple',
                'price' => 1999.00, 'stock' => 25, 'category_id' => $smartphones->id,
                'is_featured' => true, 'is_best_seller' => true, 'rating_avg' => 4.9, 'rating_count' => 200, 'sales_count' => 500,
                'thumbnail' => 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=400&h=400&fit=crop',
                'description' => 'أقوى آيفون على الإطلاق بشريحة A17 Pro',
                'specs' => ['الشاشة' => '6.7 بوصة Super Retina', 'المعالج' => 'A17 Pro', 'الذاكرة' => '8GB / 256GB', 'الكاميرا' => '48MP Pro', 'البطارية' => '4422mAh'],
            ],
            [
                'name' => 'شاومي 14 برو', 'slug' => 'xiaomi-14-pro', 'brand' => 'Xiaomi',
                'price' => 1100.00, 'stock' => 40, 'category_id' => $smartphones->id,
                'is_featured' => true, 'rating_avg' => 4.6, 'rating_count' => 89, 'sales_count' => 200,
                'thumbnail' => 'https://images.unsplash.com/photo-1598327105666-5b89351aff97?w=400&h=400&fit=crop',
                'description' => 'هاتف شاومي الرائد بكاميرا لايكا وشاشة AMOLED',
                'specs' => ['الشاشة' => '6.73 بوصة AMOLED', 'المعالج' => 'Snapdragon 8 Gen 3', 'الذاكرة' => '12GB / 256GB', 'الكاميرا' => '50MP Leica', 'البطارية' => '4880mAh'],
            ],
            [
                'name' => 'يوفي كلين L60 هايبريد', 'slug' => 'eufy-clean-l60', 'brand' => 'Eufy',
                'price' => 899.00, 'stock' => 15, 'category_id' => $home->id,
                'is_featured' => true, 'rating_avg' => 4.7, 'rating_count' => 89, 'sales_count' => 100,
                'thumbnail' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=400&h=400&fit=crop',
                'description' => 'مكنسة ذكية هجينة بتقنية SES المتطورة',
                'specs' => ['القوة' => '3000Pa', 'البطارية' => '5200mAh', 'المساحة' => '250m²', 'الخرائط' => 'ذكية', 'الاتصال' => 'WiFi'],
            ],
            [
                'name' => 'ماك بوك برو M3', 'slug' => 'macbook-pro-m3', 'brand' => 'Apple',
                'price' => 2499.00, 'stock' => 20, 'category_id' => $computers->id,
                'is_featured' => true, 'is_best_seller' => true, 'rating_avg' => 4.9, 'rating_count' => 156, 'sales_count' => 180,
                'thumbnail' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=400&h=400&fit=crop',
                'description' => 'لابتوب آبل الأقوى بشريحة M3',
                'specs' => ['المعالج' => 'Apple M3 Pro', 'الذاكرة' => '18GB RAM', 'التخزين' => '512GB SSD', 'الشاشة' => '14.2 بوصة', 'البطارية' => '18 ساعة'],
            ],
            [
                'name' => 'سماعة سوني WH-1000XM5', 'slug' => 'sony-wh-1000xm5', 'brand' => 'Sony',
                'price' => 399.00, 'stock' => 60, 'category_id' => $accessories->id,
                'is_best_seller' => true, 'rating_avg' => 4.8, 'rating_count' => 300, 'sales_count' => 450,
                'thumbnail' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&h=400&fit=crop',
                'description' => 'أفضل سماعة لإلغاء الضوضاء في العالم',
                'specs' => ['الاتصال' => 'Bluetooth 5.2', 'البطارية' => '30 ساعة', 'إلغاء الضوضاء' => 'نعم', 'الوزن' => '250g', 'الكودك' => 'LDAC'],
            ],
            [
                'name' => 'سامسونج جالاكسي S22S', 'slug' => 'samsung-galaxy-s22s-flash', 'brand' => 'Samsung',
                'price' => 1780.00, 'sale_price' => 1299.00, 'stock' => 10, 'category_id' => $smartphones->id,
                'is_flash_sale' => true, 'flash_sale_ends_at' => now()->addDays(2),
                'rating_avg' => 4.6, 'rating_count' => 45, 'sales_count' => 80,
                'thumbnail' => 'https://images.unsplash.com/photo-1567581935884-3349723552ca?w=400&h=400&fit=crop',
                'description' => 'عرض محدود - سامسونج S22S بسعر مخفض',
                'specs' => ['الشاشة' => '6.6 بوصة', 'المعالج' => 'Exynos 2200', 'الذاكرة' => '8GB / 256GB', 'الكاميرا' => '50MP', 'البطارية' => '3900mAh'],
            ],
        ];

        foreach ($products as $productData) {
            $productData['sku'] = 'SKU-' . strtoupper(Str::random(8));
            Product::create($productData);
        }

        Review::create([
            'product_id' => Product::first()->id,
            'user_id'    => $user->id,
            'rating'     => 5,
            'comment'    => 'تجربة ممتازة مع الجهاز، أنصح به بشدة!',
            'is_verified'=> true,
        ]);

        echo "✅ تم إنشاء البيانات بنجاح!\n";
        echo "📧 Admin: admin@store.com / password123\n";
        echo "📧 User:  ahmed@example.com / password123\n";
    }
}
 