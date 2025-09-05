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
        Schema::create('edurank_m_peringkat', function (Blueprint $table) {
            $table->id()->primary();
            $table->string('nama_universitas', 200);
            $table->integer('peringkat_asia');
            $table->integer('peringkat_dunia');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('edurank_m_peringkat', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
