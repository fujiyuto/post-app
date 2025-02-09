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
            $table->string('address_detail')->nullable()->comment('住所詳細');
            $table->string('email')->nullable()->comment('メールアドレス');
            $table->string('tel_no')->comment('電話番号');
            $table->unsignedInteger('price_min')->nullable()->comment('価格（最小）');
            $table->unsignedInteger('price_max')->nullable()->comment('価格（最大）');
            $table->integer('post_num')->default(0)->comment('投稿数');
            $table->float('point_avg', 2, 1)->default(0)->comment('平均点数');
            $table->float('seating_duration', 4, 2)->default(1.0)->comment('席時間');
            $table->boolean('is_reservable')->default(false)->comment('予約可能');
            $table->integer('capacity')->unsigned()->default(0)->comment('上限客数');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
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
