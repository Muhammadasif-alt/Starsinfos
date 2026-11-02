<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/** Adds a fifth "How to Apply" step when the seeded four-step list is still in place. */
return new class extends Migration
{
    public function up(): void
    {
        $steps = json_decode((string) DB::table('settings')->where('key', 'steps')->value('value'), true);

        if (is_array($steps) && count($steps) === 4 && ($steps[3]['title'] ?? null) === 'Apply') {
            $steps[] = ['title' => 'Track Your Application', 'text' => 'Save your application slip and check the portal for test and interview dates.'];
            DB::table('settings')->where('key', 'steps')->update(['value' => json_encode($steps)]);
        }
    }
};
