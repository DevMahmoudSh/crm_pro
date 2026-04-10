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
        // Schema::create('orders', function (Blueprint $table) {
        //     $table->id();
            
        //     // ربط مع العميل
        //     $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            
        //     $table->decimal('total_amount', 10, 2);
            
        //     // الأعمدة الجديدة
        //     $table->text('details')->nullable(); // التفاصيل
        //     $table->string('status')->default('pending'); // الحالة
            
        //     // إذا كان لديك payment_status قديماً يمكنك إبقاؤه أو حذفه حسب رغبتك
        //     $table->enum('payment_status', ['paid', 'unpaid', 'partial'])->default('unpaid');
            
        //     $table->timestamps();
        // });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete(); // الربط مع جدول العملاء
            $table->string('details')->nullable();
            $table->decimal('total_amount', 10, 2);
            $table->string('payment_method')->nullable(); // paymentMethod
            $table->string('payment_status')->nullable(); // paymentStatus
            $table->string('stage')->default('pending'); // orderStage
            $table->string('external_order_id')->nullable(); // لحفظ id الطلب من الجيسون
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
