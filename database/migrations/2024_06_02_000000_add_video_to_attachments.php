<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{Schema, DB};

return new class extends Migration {
    public function up(): void {
        DB::statement("ALTER TABLE post_attachments MODIFY kind ENUM('image','video','file','poll') NOT NULL");
    }
    public function down(): void {
        DB::statement("ALTER TABLE post_attachments MODIFY kind ENUM('image','file','poll') NOT NULL");
    }
};
