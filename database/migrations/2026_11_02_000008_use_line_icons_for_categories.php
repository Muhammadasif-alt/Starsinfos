<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/** Replaces category emoji with names of the single-colour line icons in App\Support\Icons. */
return new class extends Migration
{
    private const ICONS = [
        'jobs' => 'briefcase',
        'government-schemes' => 'landmark',
        'bisp-8171' => 'card',
        'news' => 'newspaper',
        'education' => 'graduation',
        'government-jobs' => 'landmark',
        'private-jobs' => 'building',
        'bank-jobs' => 'banknote',
        'army-police-jobs' => 'shield',
        'teaching-jobs' => 'book',
        'wapda-railway-jobs' => 'zap',
        'fpsc-ppsc-jobs' => 'file',
        'online-work-from-home-jobs' => 'laptop',
        'latest-jobs-in-pakistan' => 'bell',
        'government-financial-assistance' => 'wallet',
        'housing-schemes' => 'home',
        'youth-loan-schemes' => 'sprout',
        'solar-schemes' => 'sun',
        'student-scholarships' => 'graduation',
        'business-support-programs' => 'users',
        'registration-guides' => 'clipboard',
        'eligibility-application-process' => 'check',
        'bank-schemes-loans' => 'banknote',
    ];

    public function up(): void
    {
        DB::table('categories')->update(['icon' => null]);

        foreach (self::ICONS as $slug => $icon) {
            DB::table('categories')->where('slug', $slug)->update(['icon' => $icon]);
        }
    }
};
