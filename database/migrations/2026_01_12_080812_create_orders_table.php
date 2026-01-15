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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('reg')->unique();
            $table->date('date');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('restrict');
            $table->enum('status', ['Pending', 'Paid', 'Cancelled'])->default('Pending');
            $table->decimal('total', 10, 2)->default(0);
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
