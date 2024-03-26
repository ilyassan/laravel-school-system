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
        /* Teacher Can Assigne Homework To A Class */

        Schema::create('homeworks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('teacher_id');
            $table->foreignId('class_id');
            $table->dateTime('end_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('homeworks');
    }
};
