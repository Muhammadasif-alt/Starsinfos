<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Lets a scheme show Open or Closed when it has no closing date, and gives scheme
 * subcategories icons for the new Schemes page. Icons are only filled where empty.
 */
return new class extends Migration
{
    /** Status confirmed on each official portal on 2 Nov 2026. */
    private const STATUSES = [
        'pm-youth-chromebook-scheme-eligibility-and-how-to-apply' => 'Open',
        'apni-chhat-apna-ghar-interest-free-home-construction-loan-in-punjab' => 'Open',
        'apni-chhat-mehfooz-chhat-interest-free-loan-for-roof-repair-or-an-extra-room' => 'Open',
        'cm-punjab-free-solar-panel-scheme-eligibility-and-application-result' => 'Closed',
    ];

    private const ICONS = [
        'government-financial-assistance' => '💰',
        'housing-schemes' => '🏠',
        'youth-loan-schemes' => '🌱',
        'solar-schemes' => '☀️',
        'student-scholarships' => '🎓',
        'business-support-programs' => '💼',
        'registration-guides' => '📝',
        'eligibility-application-process' => '✅',
        'bank-schemes-loans' => '🏦',
    ];

    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('application_status', 10)->nullable()->after('job_type');
        });

        foreach (self::STATUSES as $slug => $status) {
            DB::table('posts')->where('slug', $slug)->update(['application_status' => $status]);
        }

        foreach (self::ICONS as $slug => $icon) {
            DB::table('categories')->where('slug', $slug)->whereNull('icon')->update(['icon' => $icon]);
        }
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('application_status');
        });
    }
};
