<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'HouseScout') }} - UK Property Intelligence</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        <script>
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-white text-zinc-900 antialiased dark:bg-zinc-900 dark:text-zinc-100">
        <header class="sticky top-0 z-50 border-b border-zinc-100 bg-white/90 backdrop-blur-sm dark:border-zinc-700 dark:bg-zinc-900/90">
            <div class="mx-auto grid max-w-7xl grid-cols-3 items-center px-6 py-4">
                {{-- Left: Logo --}}
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <div class="flex size-9 items-center justify-center rounded-lg bg-blue-600">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5 text-white">
                            <path d="M11.47 3.841a.75.75 0 0 1 1.06 0l8.69 8.69a.75.75 0 1 0 1.06-1.061l-8.689-8.69a2.25 2.25 0 0 0-3.182 0l-8.69 8.69a.75.75 0 1 0 1.061 1.06l8.69-8.689Z" />
                            <path d="m12 5.432 8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 0 1-.75-.75v-4.5a.75.75 0 0 0-.75-.75h-3a.75.75 0 0 0-.75.75V21a.75.75 0 0 1-.75.75H5.625a1.875 1.875 0 0 1-1.875-1.875v-6.198a2.29 2.29 0 0 0 .091-.086L12 5.432Z" />
                        </svg>
                    </div>
                    <span class="text-xl font-semibold text-zinc-900 dark:text-white">HouseScout</span>
                </a>

                {{-- Center: Nav Links --}}
                <nav class="hidden items-center justify-center gap-6 sm:flex">
                    <a href="{{ route('about') }}" class="text-sm font-medium text-zinc-600 transition hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white">About</a>
                    <a href="{{ route('pricing') }}" class="text-sm font-medium text-zinc-600 transition hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white">Pricing</a>
                </nav>

                {{-- Right: Theme Toggle + Auth --}}
                <div class="flex items-center justify-end gap-3">
                    <button
                        id="theme-toggle"
                        type="button"
                        class="rounded-lg p-2 text-zinc-500 transition hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-white"
                        aria-label="Toggle dark mode"
                    >
                        <svg id="theme-icon-moon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="hidden size-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
                        </svg>
                        <svg id="theme-icon-sun" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="hidden size-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                        </svg>
                    </button>
                    <a href="/app/login" class="rounded-lg px-4 py-2 text-sm font-medium text-zinc-600 transition hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white">
                        Login
                    </a>
                    <a href="/app/register" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700">
                        Sign Up
                    </a>
                </div>
            </div>
        </header>

        <main>
            {{ $slot }}
        </main>

        <footer class="border-t border-zinc-200 bg-zinc-900 py-12 dark:border-zinc-700 dark:bg-zinc-950">
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
                        <a href="{{ route('about') }}" class="text-sm text-zinc-400 transition hover:text-white">About</a>
                        <a href="{{ route('pricing') }}" class="text-sm text-zinc-400 transition hover:text-white">Pricing</a>
                        <a href="{{ route('privacy') }}" class="text-sm text-zinc-400 transition hover:text-white">Privacy</a>
                        <a href="{{ route('terms') }}" class="text-sm text-zinc-400 transition hover:text-white">Terms</a>
                    </nav>
                </div>

                <div class="mt-8 border-t border-zinc-800 pt-8 text-center dark:border-zinc-700">
                    <p class="text-sm text-zinc-500">Built with official UK government data sources.</p>
                    <p class="mt-2 text-xs text-zinc-600">&copy; {{ date('Y') }} {{ config('app.name', 'HouseScout') }}. All rights reserved.</p>
                </div>
            </div>
        </footer>

        <script>
            (function () {
                const toggle = document.getElementById('theme-toggle');
                const moonIcon = document.getElementById('theme-icon-moon');
                const sunIcon = document.getElementById('theme-icon-sun');

                function updateIcons() {
                    if (document.documentElement.classList.contains('dark')) {
                        moonIcon.classList.add('hidden');
                        sunIcon.classList.remove('hidden');
                    } else {
                        moonIcon.classList.remove('hidden');
                        sunIcon.classList.add('hidden');
                    }
                }

                updateIcons();

                toggle.addEventListener('click', function () {
                    if (document.documentElement.classList.contains('dark')) {
                        document.documentElement.classList.remove('dark');
                        localStorage.theme = 'light';
                    } else {
                        document.documentElement.classList.add('dark');
                        localStorage.theme = 'dark';
                    }
                    updateIcons();
                });
            })();
        </script>
    </body>
</html>
