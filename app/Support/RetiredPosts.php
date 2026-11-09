<?php

namespace App\Support;

/**
 * Short posts that duplicated a fuller guide on the same topic. They were removed
 * and their old URLs send visitors (and search engines) to the guide with a 301.
 */
class RetiredPosts
{
    public const REDIRECTS = [
        '/jobs/pakistan-army-soldier-recruitment-registration-open' => '/jobs/pakistan-army-soldier-registration-2026',
        '/government-schemes/cm-punjab-laptop-program-phase-2-eligibility-and-latest-status' => '/government-schemes/cm-punjab-laptop-program-next-phase-what-should-students-do-now-latest-status-guide',
        '/government-schemes/cm-punjab-free-solar-panel-scheme-eligibility-and-application-result' => '/government-schemes/free-solar-panel-scheme-result-check-cnic',
        '/government-schemes/pave-electric-bike-and-rickshaw-program-subsidy-and-eligibility' => '/government-schemes/pave-electric-bike-scheme-installments-subsidy-amount-dealer-process-step-by-step',
        '/government-schemes/cm-punjab-e-bike-scheme-phase-2-for-students-terms-and-status' => '/government-schemes/cm-punjab-e-bike-scheme-delivery-payment-status',
        '/government-schemes/apni-chhat-apna-ghar-interest-free-home-construction-loan-in-punjab' => '/government-schemes/apni-chhat-apna-ghar-required-documents',
        '/jobs/junior-auditor-jobs-bps-11-controller-general-of-accounts-balochistan' => '/jobs/junior-auditor-bps-11-jobs-test-syllabus-preparation',
        '/jobs/data-entry-operator-bps-14-drug-regulatory-authority-balochistan' => '/jobs/data-entry-operator-bps-14-jobs-typing-test-speed-requirements-preparation-tips',
        '/jobs/pma-159-long-course-join-pakistan-army-as-an-officer' => '/jobs/pma-159-long-course-initial-test-syllabus-medical-issb',
    ];

    /** Posts that still exist but moved to a different section. */
    public const MOVED = [
        '/jobs/cm-punjab-laptop-program-next-phase-what-should-students-do-now-latest-status-guide' => '/government-schemes/cm-punjab-laptop-program-next-phase-what-should-students-do-now-latest-status-guide',
    ];

    public static function target(string $path): ?string
    {
        $path = '/'.ltrim($path, '/');

        return self::REDIRECTS[$path] ?? self::MOVED[$path] ?? null;
    }

    /** @return list<string> */
    public static function slugs(): array
    {
        return array_map(fn (string $path) => basename($path), array_keys(self::REDIRECTS));
    }
}
