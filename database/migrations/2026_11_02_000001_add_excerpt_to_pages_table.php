<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const EXCERPTS = [
        'about-us' => 'Clear, practical information on jobs, government schemes, education and home decor for people across Pakistan.',
        'contact-us' => 'Have a question, a correction or a suggestion? We would like to hear from you.',
        'privacy-policy' => 'What information we collect, how we use it and how we protect it.',
        'disclaimer' => 'Important information about the purpose and limits of this website.',
        'terms-and-conditions' => 'The terms that apply when you use this website.',
        'editorial-policy' => 'How we research, check and update the information we publish.',
        'write-for-us' => 'Share your knowledge on jobs, education, careers and home decor with our readers.',
    ];

    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->string('excerpt', 500)->nullable()->after('slug');
        });

        foreach (self::EXCERPTS as $slug => $excerpt) {
            DB::table('pages')->where('slug', $slug)->update(['excerpt' => $excerpt]);
        }
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('excerpt');
        });
    }
};
