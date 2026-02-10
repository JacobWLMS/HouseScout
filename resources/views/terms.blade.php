<x-layouts.marketing>
    {{-- Hero --}}
    <section class="bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 py-16 sm:py-20">
        <div class="mx-auto max-w-7xl px-6 text-center">
            <h1 class="text-4xl font-bold tracking-tight text-white sm:text-5xl">
                Terms of Service
            </h1>
            <p class="mx-auto mt-4 max-w-2xl text-lg text-blue-100">
                Last updated: {{ now()->format('j F Y') }}
            </p>
        </div>
    </section>

    {{-- Content --}}
    <section class="bg-white py-16 sm:py-20 dark:bg-zinc-900">
        <div class="prose prose-zinc mx-auto max-w-3xl px-6 dark:prose-invert">
            <h2>1. Acceptance of Terms</h2>
            <p>
                By accessing or using HouseScout ("the Service"), you agree to be bound by these Terms of Service. If you do not agree, you may not use the Service.
            </p>

            <h2>2. Service Description</h2>
            <p>
                HouseScout is a property intelligence platform that aggregates publicly available data from UK government sources to help homebuyers research properties. The Service provides information including energy performance data, flood risk assessments, crime statistics, planning history, and land registry records.
            </p>

            <h2>3. User Accounts</h2>
            <p>
                To use the Service, you must create an account with a valid email address. You are responsible for maintaining the confidentiality of your account credentials and for all activity that occurs under your account.
            </p>

            <h2>4. Acceptable Use</h2>
            <p>You agree not to:</p>
            <ul>
                <li>Use the Service for any unlawful purpose</li>
                <li>Attempt to scrape, crawl, or bulk-download data from the Service</li>
                <li>Interfere with or disrupt the Service or its infrastructure</li>
                <li>Create multiple accounts to circumvent usage limits</li>
                <li>Redistribute or commercially resell data obtained from the Service</li>
            </ul>

            <h2>5. Data Accuracy Disclaimer</h2>
            <p>
                HouseScout aggregates data from third-party government sources. While we strive to present this data accurately, we do not guarantee the completeness, accuracy, or timeliness of any information provided. Government data sources may contain errors, be outdated, or have gaps in coverage.
            </p>
            <p>
                <strong>The Service is for informational purposes only and should not be used as the sole basis for property purchasing decisions.</strong> We strongly recommend conducting your own independent research, obtaining professional surveys, and seeking qualified legal and financial advice before making any property purchase.
            </p>

            <h2>6. Limitation of Liability</h2>
            <p>
                To the maximum extent permitted by law, HouseScout and its operators shall not be liable for any indirect, incidental, special, consequential, or punitive damages arising from your use of the Service, including but not limited to:
            </p>
            <ul>
                <li>Decisions made based on data provided by the Service</li>
                <li>Inaccuracies or omissions in government data sources</li>
                <li>Service interruptions or downtime</li>
                <li>Loss of saved data or property assessments</li>
            </ul>

            <h2>7. Subscriptions and Billing</h2>
            <p>
                Paid plans are billed monthly. You may cancel at any time, and you will retain access to paid features until the end of your current billing period. Refunds are not provided for partial billing periods.
            </p>

            <h2>8. Intellectual Property</h2>
            <p>
                The HouseScout platform, including its design, code, and branding, is owned by us and protected by intellectual property laws. Government data presented through the Service remains subject to the terms of the respective government data licences (typically the Open Government Licence).
            </p>

            <h2>9. Termination</h2>
            <p>
                We reserve the right to suspend or terminate your account if you violate these terms. You may delete your account at any time through your account settings.
            </p>

            <h2>10. Changes to Terms</h2>
            <p>
                We may update these terms from time to time. Continued use of the Service after changes constitutes acceptance of the revised terms. We will notify registered users of significant changes by email.
            </p>

            <h2>11. Governing Law</h2>
            <p>
                These terms are governed by the laws of England and Wales. Any disputes shall be subject to the exclusive jurisdiction of the courts of England and Wales.
            </p>

            <h2>12. Contact</h2>
            <p>
                For questions about these terms, contact us at <a href="mailto:legal@housescout.co.uk">legal@housescout.co.uk</a>.
            </p>
        </div>
    </section>
</x-layouts.marketing>
