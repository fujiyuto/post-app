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
        Schema::create('restaurant_holidays', function (Blueprint $table) {
            $table->comment('店休日テーブル');

            $table->id();
            $table->enum('holiday_type', ['DayOfWeek', 'SpecificDate'])->comment('休業日タイプ(DayOfWeek:曜日、SpecificDate:特定の日付)');
            $table->unsignedTinyInteger('day_of_week')->nullable()->comment('曜日(0:日曜、1:月曜、2:火曜、3:水曜、4:木曜、5:金曜、6:土曜)');
            $table->date('specific_date')->nullable()->comment('特定の日付');
            $table->foreignId('restaurant_id')->constrained('restaurants')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurant_holidays');
    }
};
