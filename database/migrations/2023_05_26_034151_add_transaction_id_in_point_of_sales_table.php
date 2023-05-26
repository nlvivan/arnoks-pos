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
        Schema::table('point_of_sales', function (Blueprint $table) {
            $table->foreignId('transaction_id')->nullable()->constrained('transactions')->nullOnDelete();
        });
    }
};
