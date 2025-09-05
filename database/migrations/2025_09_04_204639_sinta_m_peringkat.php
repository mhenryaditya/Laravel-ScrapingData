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
        Schema::create('sinta_m_peringkat', function (Blueprint $table) {
            $table->id()->primary();
            $table->string('nama_universitas', 200);
            $table->integer('sinta_score_3_yr');
            $table->integer('sinta_score_overall');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sinta_m_peringkat', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
