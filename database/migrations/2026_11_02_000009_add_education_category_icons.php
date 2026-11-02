<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/** Gives education subcategories line icons for the Education & Admissions page. Only fills empty icons. */
return new class extends Migration
{
    private const ICONS = [
        'bs-admissions' => 'graduation',
        'university-admissions' => 'landmark',
        'college-admissions' => 'building',
        'entry-tests' => 'pen',
        'results-merit-lists' => 'chart',
        'scholarships' => 'award',
        'matric-intermediate' => 'book',
        'degree-programs' => 'file',
    ];

    public function up(): void
    {
        foreach (self::ICONS as $slug => $icon) {
            DB::table('categories')->where('slug', $slug)->whereNull('icon')->update(['icon' => $icon]);
        }
    }
};
