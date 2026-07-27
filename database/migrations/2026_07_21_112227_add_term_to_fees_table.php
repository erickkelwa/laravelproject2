<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fees', function (Blueprint $table) {
            // Term the payment belongs to: Term 1, Term 2, Term 3
            $table->string('term')
                  ->nullable()
                  ->default('Term 1');

            // Per-term fee target (how much is due for that term)
            $table->decimal('term_fee', 10, 2)
                  ->nullable()
                  ->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('fees', function (Blueprint $table) {
            $table->dropColumn(['term', 'term_fee']);
        });
    }
};
