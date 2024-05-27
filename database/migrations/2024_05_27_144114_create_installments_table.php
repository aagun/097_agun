<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('installments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedInteger('installment_number')->nullable();
            $table->string('debt_id', 36)->nullable();
            $table->bigInteger('amount');
            $table->dateTime('due_date');
            $table->enum('status', ['paid', 'unpaid']);

            $table->foreign('debt_id')
                ->references('id')
                ->on('debts')
                ->nullOnDelete();

            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('installments');
    }
};
