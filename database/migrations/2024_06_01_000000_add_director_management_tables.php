<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('filieres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('name');                          // "Sciences Mathématiques"
            $table->string('code')->nullable();              // "SM"
            $table->string('level')->nullable();             // "Bac", "Tronc commun", etc.
            $table->text('description')->nullable();
            $table->enum('color', ['blue','saffron','atlas','terracotta'])->default('blue');
            $table->timestamps();
        });

        Schema::table('groups', function (Blueprint $table) {
            $table->foreignId('filiere_id')->nullable()->after('school_id')
                  ->constrained('filieres')->nullOnDelete();
            $table->string('academic_year')->nullable()->after('description'); // "2025-2026"
            $table->unsignedInteger('capacity')->default(35)->after('academic_year');
        });
    }

    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropForeign(['filiere_id']);
            $table->dropColumn(['filiere_id', 'academic_year', 'capacity']);
        });
        Schema::dropIfExists('filieres');
    }
};
