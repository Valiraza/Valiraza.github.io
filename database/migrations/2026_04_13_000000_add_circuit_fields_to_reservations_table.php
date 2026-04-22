<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->foreignId('circuit_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->string('circuit_nom')->nullable()->after('places');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('circuit_id');
            $table->dropColumn('circuit_nom');
        });
    }
};
