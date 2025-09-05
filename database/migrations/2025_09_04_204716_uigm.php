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
        Schema::create('uigm_r_metriks', function (Blueprint $table) {
            $table->id()->primary();
            $table->string('nama_metriks_lengkap', 50);
            $table->string('nama_metriks_singkat', 5);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('uigm_m_peringkat', function (Blueprint $table) {
            $table->id()->primary();
            $table->string('nama_universitas', 200);
            $table->unsignedBigInteger('id_metriks');
            $table->foreign('id_metriks')->references('id')->on('uigm_r_metriks')->restrictOnDelete()->cascadeOnUpdate();
            $table->integer('skor');
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
        Schema::table('uigm_r_peringkat', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('uigm_m_peringkat', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
