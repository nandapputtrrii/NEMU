<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barangs', function (Blueprint $table): void {
            $table->id();
            $table->string('legacy_id')->nullable()->unique();
            $table->string('nama');
            $table->string('kategori');
            $table->string('lokasi');
            $table->date('tanggal');
            $table->time('jam')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('status', 20);
            $table->string('nama_pelapor');
            $table->string('telepon', 20);
            $table->text('foto')->nullable();
            $table->string('penyimpanan')->nullable();
            $table->timestamps();

            $table->index(['status', 'tanggal']);
            $table->index('kategori');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barangs');
    }
};
