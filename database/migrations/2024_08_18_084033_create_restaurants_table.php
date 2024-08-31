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
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id()->comment('店ID');
            $table->string('restaurant_name')->comment('名前');
            $table->string('zip_cd', 7)->comment('郵便番号');
            $table->string('address')->comment('住所');
            $table->string('email')->nullable()->comment('メールアドレス');
            $table->string('tel_no')->comment('電話番号');
            $table->unsignedInteger('price_min')->nullable()->comment('価格（最小）');
            $table->unsignedInteger('price_max')->nullable()->comment('価格（最大）');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurants');
    }
};
