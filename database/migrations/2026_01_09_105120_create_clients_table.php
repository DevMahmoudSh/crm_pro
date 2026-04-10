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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // الاسم
            // هذا العمود ضروري جداً للربط
            $table->string('external_id')->nullable()->index();
            $table->string('phone')->nullable(); // رقم الهاتف
            $table->timestamps(); // ينشئ created_at و updated_at تلقائياً
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};