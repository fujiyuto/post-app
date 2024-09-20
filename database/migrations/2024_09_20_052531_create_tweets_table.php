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
        Schema::create('tweets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bulletin_board_id')->constrained()->onDelete('cascade')->comment('掲示板ID');
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->comment('会員ID');
            $table->string('message')->comment('メッセージ');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tweet');
    }
};