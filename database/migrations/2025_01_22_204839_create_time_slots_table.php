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

        Schema::create('time_slots', function (Blueprint $table) {
            $table->comment('時間帯マスタテーブル');

            $table->id();
            $table->integer('hour')->comment('時間');
            $table->integer('minute')->comment('分');
            $table->timestamps();

            $table->index(['hour', 'minute']);
            $table->unique(['hour', 'minute']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_slots');
    }
};
