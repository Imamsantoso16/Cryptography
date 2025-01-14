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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->string('file_name');
            $table->string('encrypted_file');
            $table->string('decrypted_file');
            $table->string('password');
            $table->string('key');
            $table->string('keterangan');
            $table->bigInteger('file_size');
            $table->enum('status', ['Terenkripsi', 'Terdekripsi']);
            $table->date('tanggal');
            $table->date('tanggal_enkripsi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
