<x-layouts.marketing>
    {{-- Hero --}}
    <section class="bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 py-16 sm:py-20">
        <div class="mx-auto max-w-7xl px-6 text-center">
            <h1 class="text-4xl font-bold tracking-tight text-white sm:text-5xl">
                Privacy Policy
            </h1>
            <p class="mx-auto mt-4 max-w-2xl text-lg text-blue-100">
                Last updated: {{ now()->format('j F Y') }}
            </p>
        </div>
    </section>

    {{-- Content --}}
    <section class="bg-white py-16 sm:py-20 dark:bg-zinc-900">
        <div class="prose prose-zinc mx-auto max-w-3xl px-6 dark:prose-invert">
            <h2>Introduction</h2>
            <p>
                HouseScout ("we", "our", "us") is committed to protecting your privacy. This policy explains how we collect, use, and safeguard your personal information when you use our property intelligence platform.
            </p>

            <h2>What Data We Collect</h2>
            <h3>Account Information</h3>
            <p>When you create an account, we collect your name and email address. This is used to authenticate you and manage your account.</p>

            <h3>Property Searches</h3>
            <p>We store the postcodes and addresses you search for, along with the properties you save and your checklist assessments. This data is necessary to provide our core service.</p>

            <h3>Usage Data</h3>
            <p>We collect anonymous usage analytics to improve our service, including pages visited, features used, and general interaction patterns. We do not track you across other websites.</p>

            <h2>How We Use Your Data</h2>
            <ul>
                <li>To provide property intelligence reports based on your searches</li>
                <li>To save your property assessments and checklists</li>
                <li>To send important account-related notifications</li>
                <li>To improve and develop new features</li>
                <li>To detect and prevent abuse of our service</li>
            </ul>

            <h2>Third-Party APIs</h2>
            <p>
                To provide property data, we query the following government APIs on your behalf. We send only the minimum information required (typically a postcode or coordinates):
            </p>
            <ul>
                <li><strong>EPC Register</strong> (Open Data Communities) — for energy performance data</li>
                <li><strong>Environment Agency</strong> — for flood risk assessments</li>
                <li><strong>Police UK API</strong> — for street-level crime statistics</li>
                <li><strong>Planning Data Service</strong> — for planning application records</li>
                <li><strong>HM Land Registry</strong> — for sale price and ownership data</li>
                <li><strong>Google Maps</strong> — for map displays and satellite imagery</li>
            </ul>
            <p>These services have their own privacy policies. We do not share your personal account information with these services.</p>

            <h2>Cookies</h2>
            <p>
                We use essential cookies to maintain your session and remember your preferences (such as dark mode). We do not use third-party advertising or tracking cookies.
            </p>

            <h2>Data Retention</h2>
            <p>
                Your account data is retained for as long as your account is active. Property search data older than 90 days is automatically cleaned up. If you delete your account, all associated data will be permanently removed within 30 days.
            </p>

            <h2>Your Rights (GDPR)</h2>
            <p>Under UK GDPR, you have the right to:</p>
            <ul>
                <li><strong>Access</strong> — request a copy of all personal data we hold about you</li>
                <li><strong>Rectification</strong> — correct any inaccurate personal data</li>
                <li><strong>Erasure</strong> — request deletion of your personal data</li>
                <li><strong>Portability</strong> — receive your data in a structured, machine-readable format</li>
                <li><strong>Object</strong> — object to processing of your personal data</li>
                <li><strong>Restrict</strong> — request restriction of processing</li>
            </ul>
            <p>To exercise any of these rights, please contact us at <a href="mailto:privacy@housescout.co.uk">privacy@housescout.co.uk</a>.</p>

            <h2>Data Security</h2>
            <p>
                We use industry-standard security measures to protect your data, including encrypted connections (HTTPS), secure password hashing, and regular security audits. Your data is stored on servers within the United Kingdom.
            </p>

            <h2>Changes to This Policy</h2>
            <p>
                We may update this privacy policy from time to time. We will notify registered users of any significant changes by email. The "last updated" date at the top of this page indicates when the policy was last revised.
            </p>

            <h2>Contact Us</h2>
            <p>
                If you have questions about this privacy policy or our data practices, contact us at <a href="mailto:privacy@housescout.co.uk">privacy@housescout.co.uk</a>.
            </p>
        </div>
    </section>
</x-layouts.marketing>
