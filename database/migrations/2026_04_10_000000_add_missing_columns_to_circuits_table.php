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
        Schema::table('circuits', function (Blueprint $table) {
            if (!Schema::hasColumn('circuits', 'slug')) {
                $table->string('slug')->nullable()->unique()->after('nom');
            }
            if (!Schema::hasColumn('circuits', 'type')) {
                $table->string('type')->nullable()->after('statut');
            }
            if (!Schema::hasColumn('circuits', 'programme')) {
                $table->json('programme')->nullable()->after('type');
            }
            if (!Schema::hasColumn('circuits', 'detail')) {
                $table->json('detail')->nullable()->after('programme');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('circuits', function (Blueprint $table) {
            $table->dropColumn(['slug', 'type', 'programme', 'detail']);
        });
    }
};
