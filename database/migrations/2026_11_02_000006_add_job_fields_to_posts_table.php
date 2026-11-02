<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Values for the job listings already published, keyed by slug: [department, location, job type]. */
    private const EXISTING_JOBS = [
        'islamabad-police-dolphin-force-jobs-2026-executive-staff' => ['Islamabad Capital Territory Police', 'Islamabad', 'Permanent'],
        'pakistan-army-soldier-recruitment-registration-open' => ['Pakistan Army', 'All Pakistan', 'Permanent'],
        'pma-159-long-course-join-pakistan-army-as-an-officer' => ['Pakistan Army', 'All Pakistan', 'Permanent'],
        'junior-auditor-jobs-bps-11-controller-general-of-accounts-balochistan' => ['Office of the Controller General of Accounts', 'Balochistan', 'Permanent'],
        'assistant-divisional-engineer-bps-17-ntc-balochistan' => ['National Telecommunication Corporation', 'Balochistan', 'Permanent'],
        'assistant-private-secretary-bps-16-ntc-balochistan' => ['National Telecommunication Corporation', 'Balochistan', 'Permanent'],
        'superintendent-bps-16-drug-regulatory-authority-balochistan' => ['Drug Regulatory Authority of Pakistan', 'Balochistan', 'Permanent'],
        'lower-division-clerk-bps-09-drug-regulatory-authority-balochistan' => ['Drug Regulatory Authority of Pakistan', 'Balochistan', 'Permanent'],
        'data-entry-operator-bps-14-drug-regulatory-authority-balochistan' => ['Drug Regulatory Authority of Pakistan', 'Balochistan', 'Permanent'],
        'meteorological-assistant-bs-14-pmd-balochistan' => ['Pakistan Meteorological Department', 'Balochistan', 'Permanent'],
        'staff-car-driver-bps-04-cabinet-division-balochistan' => ['Cabinet Division', 'Balochistan', 'Permanent'],
        'stenotypist-bps-14-cabinet-division-balochistan' => ['Cabinet Division', 'Balochistan', 'Permanent'],
        'junior-accountant-bps-11-cadet-college-hasanabdal' => ['Cadet College Hasanabdal', 'Hasan Abdal', 'Contract'],
        'manager-legal-pakistan-national-shipping-corporation' => ['Pakistan National Shipping Corporation', null, 'Contract'],
        'financial-analyst-gas-petroleum-division' => ['Petroleum Division, Ministry of Energy', null, 'Contract'],
        'senior-officer-finance-accounts-exim-bank-of-pakistan' => ['EXIM Bank of Pakistan', 'Islamabad', 'Permanent'],
    ];

    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('department')->nullable()->after('source_url');
            $table->string('location')->nullable()->after('department');
            $table->string('job_type', 20)->nullable()->after('location');
        });

        foreach (self::EXISTING_JOBS as $slug => [$department, $location, $type]) {
            DB::table('posts')->where('slug', $slug)->update([
                'department' => $department,
                'location' => $location,
                'job_type' => $type,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['department', 'location', 'job_type']);
        });
    }
};
