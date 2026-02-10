<x-layouts.marketing>
    {{-- Hero Section --}}
    <section class="relative overflow-hidden bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800">
        <div class="absolute inset-0 opacity-10">
            <svg class="h-full w-full" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                        <path d="M 40 0 L 0 0 0 40" fill="none" stroke="white" stroke-width="0.5"/>
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#grid)" />
            </svg>
        </div>

        <div class="relative mx-auto max-w-7xl px-6 py-24 sm:py-32 lg:py-40">
            <div class="mx-auto max-w-3xl text-center">
                <h1 class="text-4xl font-bold tracking-tight text-white sm:text-5xl lg:text-6xl">
                    Know Your Next Home Inside Out
                </h1>
                <p class="mx-auto mt-6 max-w-2xl text-lg text-blue-100">
                    Comprehensive UK property intelligence from official government sources. EPC ratings, flood risk, crime stats, planning history — all in one place.
                </p>

                <div class="mx-auto mt-10 max-w-xl">
                    <a href="/app/register" class="group block">
                        <div class="flex items-center rounded-xl bg-white/10 p-2 shadow-lg ring-1 ring-white/20 backdrop-blur-sm transition hover:bg-white/15">
                            <div class="flex flex-1 items-center gap-3 rounded-lg bg-white px-4 py-3">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5 text-zinc-400">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                </svg>
                                <span class="text-zinc-400">Enter a UK postcode or address...</span>
                            </div>
                            <div class="ml-2 rounded-lg bg-blue-500 px-5 py-3 text-sm font-semibold text-white transition group-hover:bg-blue-400">
                                Search
                            </div>
                        </div>
                    </a>
                    <p class="mt-3 text-sm text-blue-200">Free to use — sign up to start researching properties</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Features Grid --}}
    <section class="bg-white py-20 sm:py-28">
        <div class="mx-auto max-w-7xl px-6">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="text-3xl font-bold tracking-tight text-zinc-900 sm:text-4xl">
                    Everything you need to know about a property
                </h2>
                <p class="mt-4 text-lg text-zinc-600">
                    We gather data from official UK government sources so you can make informed decisions.
                </p>
            </div>

            <div class="mx-auto mt-16 grid max-w-5xl gap-8 sm:grid-cols-2 lg:grid-cols-3">
                {{-- EPC Ratings --}}
                <div class="rounded-xl border border-zinc-100 bg-white p-6 shadow-sm transition hover:shadow-md">
                    <div class="flex size-12 items-center justify-center rounded-lg bg-green-50">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-green-600">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-semibold text-zinc-900">EPC Ratings</h3>
                    <p class="mt-2 text-sm text-zinc-600">Understand energy efficiency, running costs, and environmental impact.</p>
                </div>

                {{-- Planning History --}}
                <div class="rounded-xl border border-zinc-100 bg-white p-6 shadow-sm transition hover:shadow-md">
                    <div class="flex size-12 items-center justify-center rounded-lg bg-blue-50">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-blue-600">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-semibold text-zinc-900">Planning History</h3>
                    <p class="mt-2 text-sm text-zinc-600">View local planning applications and development activity.</p>
                </div>

                {{-- Flood Risk --}}
                <div class="rounded-xl border border-zinc-100 bg-white p-6 shadow-sm transition hover:shadow-md">
                    <div class="flex size-12 items-center justify-center rounded-lg bg-cyan-50">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-cyan-600">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418" />
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-semibold text-zinc-900">Flood Risk</h3>
                    <p class="mt-2 text-sm text-zinc-600">Check river, sea, and surface water flood risk levels.</p>
                </div>

                {{-- Crime Statistics --}}
                <div class="rounded-xl border border-zinc-100 bg-white p-6 shadow-sm transition hover:shadow-md">
                    <div class="flex size-12 items-center justify-center rounded-lg bg-red-50">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-red-600">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0-10.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285zm0 13.036h.008v.008H12v-.008z" />
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-semibold text-zinc-900">Crime Statistics</h3>
                    <p class="mt-2 text-sm text-zinc-600">Browse street-level crime data by category and month.</p>
                </div>

                {{-- Land Registry --}}
                <div class="rounded-xl border border-zinc-100 bg-white p-6 shadow-sm transition hover:shadow-md">
                    <div class="flex size-12 items-center justify-center rounded-lg bg-amber-50">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-amber-600">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3H21m-3.75 3H21" />
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-semibold text-zinc-900">Land Registry</h3>
                    <p class="mt-2 text-sm text-zinc-600">Access ownership details and historical sale prices.</p>
                </div>

                {{-- Demand Tracking --}}
                <div class="rounded-xl border border-zinc-100 bg-white p-6 shadow-sm transition hover:shadow-md">
                    <div class="flex size-12 items-center justify-center rounded-lg bg-purple-50">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-purple-600">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-semibold text-zinc-900">Demand Tracking</h3>
                    <p class="mt-2 text-sm text-zinc-600">See how many people are researching the same property.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- How It Works --}}
    <section class="bg-zinc-50 py-20 sm:py-28">
        <div class="mx-auto max-w-7xl px-6">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="text-3xl font-bold tracking-tight text-zinc-900 sm:text-4xl">
                    How it works
                </h2>
                <p class="mt-4 text-lg text-zinc-600">
                    Get comprehensive property intelligence in three simple steps.
                </p>
            </div>

            <div class="mx-auto mt-16 grid max-w-4xl gap-8 sm:grid-cols-3">
                {{-- Step 1 --}}
                <div class="text-center">
                    <div class="mx-auto flex size-16 items-center justify-center rounded-full bg-blue-600 text-2xl font-bold text-white">
                        1
                    </div>
                    <h3 class="mt-6 text-xl font-semibold text-zinc-900">Search</h3>
                    <p class="mt-3 text-sm text-zinc-600">Enter any UK address or postcode to get started.</p>
                </div>

                {{-- Step 2 --}}
                <div class="text-center">
                    <div class="mx-auto flex size-16 items-center justify-center rounded-full bg-blue-600 text-2xl font-bold text-white">
                        2
                    </div>
                    <h3 class="mt-6 text-xl font-semibold text-zinc-900">Analyse</h3>
                    <p class="mt-3 text-sm text-zinc-600">We gather data from 5+ official government sources.</p>
                </div>

                {{-- Step 3 --}}
                <div class="text-center">
                    <div class="mx-auto flex size-16 items-center justify-center rounded-full bg-blue-600 text-2xl font-bold text-white">
                        3
                    </div>
                    <h3 class="mt-6 text-xl font-semibold text-zinc-900">Decide</h3>
                    <p class="mt-3 text-sm text-zinc-600">Make informed decisions backed by comprehensive data.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Stats / Social Proof --}}
    <section class="border-y border-zinc-100 bg-white py-16">
        <div class="mx-auto max-w-7xl px-6">
            <div class="grid grid-cols-2 gap-8 sm:grid-cols-4">
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600">5+</div>
                    <p class="mt-2 text-sm font-medium text-zinc-600">Official Data Sources</p>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-green-600">Free</div>
                    <p class="mt-2 text-sm font-medium text-zinc-600">Free to Use</p>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-indigo-600">Daily</div>
                    <p class="mt-2 text-sm font-medium text-zinc-600">Updated Daily</p>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-amber-600">100%</div>
                    <p class="mt-2 text-sm font-medium text-zinc-600">Government Data</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Final CTA --}}
    <section class="bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 py-20 sm:py-28">
        <div class="mx-auto max-w-7xl px-6 text-center">
            <h2 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">
                Start Your Property Research Today
            </h2>
            <p class="mx-auto mt-4 max-w-xl text-lg text-blue-100">
                Get instant access to comprehensive property intelligence. Free to use, powered by official UK government data.
            </p>
            <a href="/app/register" class="mt-8 inline-block rounded-lg bg-white px-8 py-3.5 text-sm font-semibold text-blue-700 shadow-md transition hover:bg-blue-50">
                Create Your Free Account
            </a>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="bg-zinc-900 py-12">
        <div class="mx-auto max-w-7xl px-6">
            <div class="flex flex-col items-center gap-6 sm:flex-row sm:justify-between">
                <div class="flex items-center gap-2">
                    <div class="flex size-8 items-center justify-center rounded-lg bg-blue-600">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-4 text-white">
                            <path d="M11.47 3.841a.75.75 0 0 1 1.06 0l8.69 8.69a.75.75 0 1 0 1.06-1.061l-8.689-8.69a2.25 2.25 0 0 0-3.182 0l-8.69 8.69a.75.75 0 1 0 1.061 1.06l8.69-8.689Z" />
                            <path d="m12 5.432 8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 0 1-.75-.75v-4.5a.75.75 0 0 0-.75-.75h-3a.75.75 0 0 0-.75.75V21a.75.75 0 0 1-.75.75H5.625a1.875 1.875 0 0 1-1.875-1.875v-6.198a2.29 2.29 0 0 0 .091-.086L12 5.432Z" />
                        </svg>
                    </div>
                    <span class="text-sm font-semibold text-white">HouseScout</span>
                </div>

                <nav class="flex items-center gap-6">
                    <a href="#" class="text-sm text-zinc-400 transition hover:text-white">About</a>
                    <a href="#" class="text-sm text-zinc-400 transition hover:text-white">Privacy</a>
                    <a href="#" class="text-sm text-zinc-400 transition hover:text-white">Terms</a>
                </nav>
            </div>

            <div class="mt-8 border-t border-zinc-800 pt-8 text-center">
                <p class="text-sm text-zinc-500">Built with official UK government data sources.</p>
                <p class="mt-2 text-xs text-zinc-600">&copy; {{ date('Y') }} {{ config('app.name', 'HouseScout') }}. All rights reserved.</p>
            </div>
        </div>
    </footer>
</x-layouts.marketing>
