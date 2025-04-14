<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLinksTable extends Migration
{
    public function up(): void
    {
        Schema::create('links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_id')->constrained('files')->onDelete('cascade');
            $table->string('slug')->unique();
            $table->string('email');
            $table->string('password')->unique();
            $table->timestamp('first_used_at')->nullable(); // пази дата и час, nullable по подразбиране
            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('links');
    }
}
