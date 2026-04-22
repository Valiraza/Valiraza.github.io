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
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('subject')->nullable()->after('email');
            $table->string('category')->default('Info')->after('subject');
            $table->string('status')->default('non_lu')->after('category');
            $table->json('admin_replies')->nullable()->after('status');
            $table->timestamp('replied_at')->nullable()->after('admin_replies');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn([
                'subject',
                'category',
                'status',
                'admin_replies',
                'replied_at',
            ]);
        });
    }
};
