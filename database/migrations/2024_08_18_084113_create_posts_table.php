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
        Schema::create('posts', function (Blueprint $table) {
            $table->id()->comment('投稿ID');
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->comment('ユーザーID');
            $table->foreignId('restaurant_id')->constrained()->onDelete('cascade')->comment('店ID');
            $table->string('title')->comment('タイトル');
            $table->text('content')->comment('内容');
            $table->date('visited_at')->nullable()->comment('訪問日');
            $table->unsignedTinyInteger('period_of_time')->comment('(1:昼、2:夜)');
            $table->double('points')->comment('点数');
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
        Schema::dropIfExists('posts');
    }
};
