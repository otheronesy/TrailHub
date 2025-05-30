<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('user_oauth_tokens', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id');
        $table->string('provider'); // e.g., 'google', 'slack'
        $table->text('access_token');
        $table->text('refresh_token')->nullable();
        $table->timestamp('expires_at')->nullable();
        $table->timestamps();

        $table->unique(['user_id', 'provider']);
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_oauth_tokens');
    }
};
