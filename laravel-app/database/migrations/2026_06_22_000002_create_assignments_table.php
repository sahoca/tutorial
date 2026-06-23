<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // SPK / BDDK gibi duzenleyici kurumlarin emlak degerleme sirketine
        // verdigi gorev/odev ve ilgili belgeler.
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('authority')->default('SPK'); // SPK | BDDK | TDUB | Diğer
            $table->string('reference_no')->nullable();   // tebliğ/görev no
            $table->date('assignment_date')->nullable();  // görev tarihi
            $table->date('due_date')->nullable();         // son tarih
            $table->longText('description')->nullable();
            $table->string('document_url')->nullable();   // PDF/belge bağlantısı
            $table->enum('status', ['active', 'completed', 'pending'])->default('active');
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
