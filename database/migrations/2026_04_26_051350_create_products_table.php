<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
         $table->id();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('description_en')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('sale_price', 10, 2)->nullable();  // سعر بعد الخصم
            $table->integer('stock')->default(0);
            $table->string('sku')->unique()->nullable();        // رمز المنتج
            $table->string('brand')->nullable();                // العلامة التجارية
            $table->string('model')->nullable();                // الموديل
            $table->json('specs')->nullable();                  // المواصفات التقنية
            $table->string('thumbnail')->nullable();            // الصورة الرئيسية
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);    // مقترح
            $table->boolean('is_best_seller')->default(false); // الأفضل مبيعاً
            $table->boolean('is_flash_sale')->default(false);  // Flash Sale
            $table->timestamp('flash_sale_ends_at')->nullable();
            $table->integer('views')->default(0);
            $table->integer('sales_count')->default(0);
            $table->decimal('rating_avg', 3, 2)->default(0);
            $table->integer('rating_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
