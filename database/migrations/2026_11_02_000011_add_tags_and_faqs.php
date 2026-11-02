<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['posts', 'blog_posts'] as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->json('tags')->nullable()->after('meta_description');
                $table->json('faqs')->nullable()->after('tags');
            });
        }
    }

    public function down(): void
    {
        foreach (['posts', 'blog_posts'] as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn(['tags', 'faqs']);
            });
        }
    }
};
