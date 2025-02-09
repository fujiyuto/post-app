<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('restaurant_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->onDelete('cascade')->comment('店ID');
            $table->foreignId('image_category_id')->constrained()->onDelete('cascade')->comment('画像種別ID');
            $table->string('image_url')->comment('画像URL');
            $table->unsignedTinyInteger('is_thumbnail')->default(0)->comment('サムネイル画像(1:サムネ画像、0:サムネ画像以外)');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurant_restaurant_images');
    }
};
