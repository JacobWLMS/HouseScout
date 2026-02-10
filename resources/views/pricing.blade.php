<x-layouts.marketing>
    {{-- Hero --}}
    <section class="bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 py-16 sm:py-20">
        <div class="mx-auto max-w-7xl px-6 text-center">
            <h1 class="text-4xl font-bold tracking-tight text-white sm:text-5xl">
                Simple, Transparent Pricing
            </h1>
            <p class="mx-auto mt-4 max-w-2xl text-lg text-blue-100">
                Start free and upgrade when you need more. Every plan includes access to official UK government data.
            </p>
        </div>
    </section>

    {{-- Pricing Cards --}}
    <section class="bg-white py-20 sm:py-28 dark:bg-zinc-900">
        <div class="mx-auto max-w-7xl px-6">
            <div class="mx-auto grid max-w-5xl gap-8 lg:grid-cols-3">
                {{-- Free Plan --}}
                <div class="rounded-2xl border border-zinc-200 bg-white p-8 dark:border-zinc-700 dark:bg-zinc-800">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Free</h3>
                    <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">Get started with essential property data at no cost.</p>

                    <div class="mt-6">
                        <span class="text-4xl font-bold text-zinc-900 dark:text-white">&pound;0</span>
                        <span class="text-sm text-zinc-500 dark:text-zinc-400">/month</span>
                    </div>

                    <a href="/app/register" class="mt-8 block rounded-lg bg-zinc-100 px-4 py-2.5 text-center text-sm font-semibold text-zinc-900 transition hover:bg-zinc-200 dark:bg-zinc-700 dark:text-white dark:hover:bg-zinc-600">
                        Get Started Free
                    </a>

                    <ul class="mt-8 space-y-3 text-sm text-zinc-600 dark:text-zinc-400">
                        <li class="flex items-start gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="mt-0.5 size-4 shrink-0 text-green-500"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" /></svg>
                            Search any UK property
                        </li>
                        <li class="flex items-start gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="mt-0.5 size-4 shrink-0 text-green-500"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" /></svg>
                            EPC ratings &amp; energy data
                        </li>
                        <li class="flex items-start gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="mt-0.5 size-4 shrink-0 text-green-500"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" /></svg>
                            Flood risk assessment
                        </li>
                        <li class="flex items-start gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="mt-0.5 size-4 shrink-0 text-green-500"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" /></svg>
                            Save up to 3 properties
                        </li>
                        <li class="flex items-start gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="mt-0.5 size-4 shrink-0 text-green-500"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" /></svg>
                            Basic checklist
                        </li>
                        <li class="flex items-start gap-3 text-zinc-400 dark:text-zinc-500">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="mt-0.5 size-4 shrink-0"><path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" /></svg>
                            Crime statistics
                        </li>
                        <li class="flex items-start gap-3 text-zinc-400 dark:text-zinc-500">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="mt-0.5 size-4 shrink-0"><path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" /></svg>
                            Planning history
                        </li>
                        <li class="flex items-start gap-3 text-zinc-400 dark:text-zinc-500">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="mt-0.5 size-4 shrink-0"><path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" /></svg>
                            Land registry data
                        </li>
                    </ul>
                </div>

                {{-- Pro Plan (Highlighted) --}}
                <div class="relative rounded-2xl border-2 border-blue-600 bg-white p-8 shadow-lg dark:bg-zinc-800">
                    <div class="absolute -top-4 left-1/2 -translate-x-1/2">
                        <span class="rounded-full bg-blue-600 px-4 py-1 text-xs font-semibold text-white">Most Popular</span>
                    </div>

                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Pro</h3>
                    <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">Full property intelligence for serious house hunters.</p>

                    <div class="mt-6">
                        <span class="text-4xl font-bold text-zinc-900 dark:text-white">&pound;9.99</span>
                        <span class="text-sm text-zinc-500 dark:text-zinc-400">/month</span>
                    </div>

                    <a href="/app/register" class="mt-8 block rounded-lg bg-blue-600 px-4 py-2.5 text-center text-sm font-semibold text-white transition hover:bg-blue-700">
                        Start Pro Trial
                    </a>

                    <ul class="mt-8 space-y-3 text-sm text-zinc-600 dark:text-zinc-400">
                        <li class="flex items-start gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="mt-0.5 size-4 shrink-0 text-green-500"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" /></svg>
                            <span><strong class="text-zinc-900 dark:text-white">Everything in Free</strong></span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="mt-0.5 size-4 shrink-0 text-green-500"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" /></svg>
                            Unlimited saved properties
                        </li>
                        <li class="flex items-start gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="mt-0.5 size-4 shrink-0 text-green-500"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" /></svg>
                            Crime statistics
                        </li>
                        <li class="flex items-start gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="mt-0.5 size-4 shrink-0 text-green-500"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" /></svg>
                            Planning history
                        </li>
                        <li class="flex items-start gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="mt-0.5 size-4 shrink-0 text-green-500"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" /></svg>
                            Land registry &amp; sales history
                        </li>
                        <li class="flex items-start gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="mt-0.5 size-4 shrink-0 text-green-500"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" /></svg>
                            Full checklist with deal-breakers
                        </li>
                        <li class="flex items-start gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="mt-0.5 size-4 shrink-0 text-green-500"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" /></svg>
                            Compare properties side by side
                        </li>
                        <li class="flex items-start gap-3 text-zinc-400 dark:text-zinc-500">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="mt-0.5 size-4 shrink-0"><path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" /></svg>
                            Export PDF reports
                        </li>
                    </ul>
                </div>

                {{-- Team Plan --}}
                <div class="rounded-2xl border border-zinc-200 bg-white p-8 dark:border-zinc-700 dark:bg-zinc-800">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Team</h3>
                    <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">For professionals and property advisors.</p>

                    <div class="mt-6">
                        <span class="text-4xl font-bold text-zinc-900 dark:text-white">&pound;24.99</span>
                        <span class="text-sm text-zinc-500 dark:text-zinc-400">/month</span>
                    </div>

                    <a href="/app/register" class="mt-8 block rounded-lg bg-zinc-100 px-4 py-2.5 text-center text-sm font-semibold text-zinc-900 transition hover:bg-zinc-200 dark:bg-zinc-700 dark:text-white dark:hover:bg-zinc-600">
                        Contact Us
                    </a>

                    <ul class="mt-8 space-y-3 text-sm text-zinc-600 dark:text-zinc-400">
                        <li class="flex items-start gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="mt-0.5 size-4 shrink-0 text-green-500"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" /></svg>
                            <span><strong class="text-zinc-900 dark:text-white">Everything in Pro</strong></span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="mt-0.5 size-4 shrink-0 text-green-500"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" /></svg>
                            Export PDF reports
                        </li>
                        <li class="flex items-start gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="mt-0.5 size-4 shrink-0 text-green-500"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" /></svg>
                            Share reports with partner or advisor
                        </li>
                        <li class="flex items-start gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="mt-0.5 size-4 shrink-0 text-green-500"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" /></svg>
                            Priority data refresh
                        </li>
                        <li class="flex items-start gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="mt-0.5 size-4 shrink-0 text-green-500"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" /></svg>
                            Up to 5 team members
                        </li>
                        <li class="flex items-start gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="mt-0.5 size-4 shrink-0 text-green-500"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" /></svg>
                            Priority email support
                        </li>
                    </ul>
                </div>
            </div>

            {{-- FAQ --}}
            <div class="mx-auto mt-20 max-w-3xl">
                <h2 class="text-center text-2xl font-bold text-zinc-900 dark:text-white">Frequently Asked Questions</h2>
                <div class="mt-10 space-y-6">
                    <div>
                        <h3 class="font-semibold text-zinc-900 dark:text-white">Can I cancel at any time?</h3>
                        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">Yes. There are no contracts or commitments. Cancel your subscription at any time and you'll retain access until the end of your billing period.</p>
                    </div>
                    <div>
                        <h3 class="font-semibold text-zinc-900 dark:text-white">Where does the data come from?</h3>
                        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">All data comes from official UK government sources including the EPC Register, Environment Agency, Police UK, the Planning Data Service, and HM Land Registry.</p>
                    </div>
                    <div>
                        <h3 class="font-semibold text-zinc-900 dark:text-white">How often is the data updated?</h3>
                        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">We refresh property data daily from government APIs. Team plan users get priority data refreshes for the most up-to-date information.</p>
                    </div>
                    <div>
                        <h3 class="font-semibold text-zinc-900 dark:text-white">Is there a free trial for Pro?</h3>
                        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">Yes. Start a 14-day free trial of Pro with no credit card required. You'll be moved to the Free plan automatically if you don't subscribe.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 py-16 sm:py-20">
        <div class="mx-auto max-w-7xl px-6 text-center">
            <h2 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">
                Ready to Research Smarter?
            </h2>
            <p class="mx-auto mt-4 max-w-xl text-lg text-blue-100">
                Join thousands of UK homebuyers making informed property decisions.
            </p>
            <a href="/app/register" class="mt-8 inline-block rounded-lg bg-white px-8 py-3.5 text-sm font-semibold text-blue-700 shadow-md transition hover:bg-blue-50">
                Create Your Free Account
            </a>
        </div>
    </section>
</x-layouts.marketing>
