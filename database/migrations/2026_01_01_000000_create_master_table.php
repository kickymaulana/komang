<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migration ini GUARDED: tabel `master` sudah dibuat manual di database.
     * Migration hanya bertugas membuat struktur saat fresh setup (DB baru).
     */
    public function up(): void
    {
        if (Schema::hasTable('master')) {
            return;
        }

        Schema::create('master', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('idpel', 12);
            $table->string('nama', 120);
            $table->string('alamat', 120);
            $table->string('trf', 60);
            $table->string('daya', 10);
            $table->string('kddk', 12);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master');
    }
};
