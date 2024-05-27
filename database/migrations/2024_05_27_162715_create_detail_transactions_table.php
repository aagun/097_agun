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
        Schema::create('detail_transactions', function (Blueprint $table) {
            $table->string('debt_id', 36);
            $table->string('transaction_id', 36);
            $table->primary(['debt_id', 'transaction_id']);
            $table->foreign('debt_id')->on('debts')->references('id');
            $table->foreign('transaction_id')->on('transactions')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_transactions');
    }
};
