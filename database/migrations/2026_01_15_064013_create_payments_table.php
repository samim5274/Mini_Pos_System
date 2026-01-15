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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('reg')->index();
            $table->date('date');

            $table->string('name');
            $table->string('phone');
            $table->text('address');

            $table->decimal('total', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);

            $table->decimal('payable', 10, 2);
            $table->decimal('pay', 10, 2)->default(0);

            $table->enum('duestatus', ['paid', 'due'])->default('due');
            $table->decimal('due', 10, 2)->default(0);
            $table->decimal('return', 10, 2)->default(0);

            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->foreignId('userId')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
