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
    Schema::create('reading_sessions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('book_title');
        $table->integer('duration_minutes');
        $table->date('session_date');
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('reading_sessions');
}
};
