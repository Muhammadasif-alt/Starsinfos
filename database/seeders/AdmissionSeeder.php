<?php

namespace Database\Seeders;

use App\Models\Admission;
use Illuminate\Database\Seeder;

class AdmissionSeeder extends Seeder
{
    /** [program, type, last date in days, entry test in days or null] */
    private const ADMISSIONS = [
        ['BS Computer Science', 'University', 10, 20],
        ['BS Education', 'University', 18, 28],
        ['Intermediate (FSc / ICS)', 'College', 25, null],
        ['Medical & Engineering', 'Entry Test', 30, 45],
        ['BBA & Commerce Programs', 'University', 35, 42],
    ];

    public function run(): void
    {
        foreach (self::ADMISSIONS as $order => [$program, $type, $lastDate, $testDate]) {
            Admission::updateOrCreate(['program' => $program], [
                'type' => $type,
                'last_date' => now()->addDays($lastDate),
                'test_date' => $testDate ? now()->addDays($testDate) : null,
                'sort_order' => $order,
            ]);
        }
    }
}
