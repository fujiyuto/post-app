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
        Schema::create('restaurant_reservation_status', function (Blueprint $table) {
            $table->comment('予約状況テーブル');

            $table->id();
            $table->foreignId('restaurant_id')->constrained()->onUpdate('cascade')->onDelete('cascade')->comment('店ID');
            $table->date('reserve_date')->comment('予約日');
            $table->json('reserve_status_info')->comment('予約状況の情報');
            $table->boolean('is_reservable')->default(false)->comment('予約可能か');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index('reserve_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurant_reservation_status');
    }
};
