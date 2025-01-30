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
        Schema::create('reservations', function (Blueprint $table) {
            $table->comment('予約テーブル');

            $table->id();
            $table->foreignId('reserved_by')->constrained(
                table: 'users', indexName: 'reservations_reserved_by'
            )->onDelete('cascade')->comment('予約者ID');
            $table->foreignId('restaurant_id')->constrained()->onDelete('cascade')->comment('店ID');
            $table->date('reserve_date')->comment('予約日');
            $table->foreignId('time_slot_id')->constrained()->onDelete('cascade')->comment('予約時間ID');
            $table->integer('num_of_people')->comment('予約人数');
            $table->enum('status', ['PENDING', 'CONFIRMED', 'CANCELLED', 'COMPLETED'])->comment('予約状況');
            $table->text('notes')->nullable()->comment('特記事項');
            $table->foreignId('updated_by')->nullable()->constrained(
                table: 'users', indexName: 'reservations_updated_by'
            )->onDelete('cascade')->comment('更新者ID');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
