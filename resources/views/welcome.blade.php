<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $siteName ?? config('app.name', 'HK Checklist') }} - Professional Housekeeping Management</title>
    <meta name="description" content="Streamline your vacation rental cleaning operations with automated checklists, photo documentation, and calendar sync with Airbnb, VRBO, and Booking.com">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Primary theme color -->
    <style>
        :root {
            --theme-primary: {!! \App\Models\Setting::get('theme_color', '#842eb8') !!};
            --button-primary-color: {!! \App\Models\Setting::get('button_primary_color') ?: \App\Models\Setting::get('theme_color', '#842eb8') !!};
        }
    </style>

    <!-- Favicon -->
    @php
        $faviconPath = \App\Models\Setting::get('favicon_path');
        if ($faviconPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($faviconPath)) {
            $faviconUrl = asset('storage/' . $faviconPath);
            $faviconExt = strtolower(pathinfo($faviconPath, PATHINFO_EXTENSION));
            $faviconType = match($faviconExt) {
                'ico' => 'image/x-icon',
                'png' => 'image/png',
                'svg' => 'image/svg+xml',
                'jpg', 'jpeg' => 'image/jpeg',
                default => 'image/x-icon',
            };
        }
    @endphp
    @if (isset($faviconUrl))
        <link rel="icon" type="{{ $faviconType }}" href="{{ $faviconUrl }}">
        <link rel="shortcut icon" type="{{ $faviconType }}" href="{{ $faviconUrl }}">
    @else
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600;700&display=swap" rel="stylesheet" />

    <!-- App assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased bg-gray-50 text-gray-900 dark:bg-gray-900 dark:text-gray-100">
    <!-- Top bar -->
    <header class="relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <a href="{{ url('/') }}" class="flex items-center gap-2">
                    <x-application-logo class="h-12 w-auto text-gray-900 dark:text-gray-100 fill-current" />
                    <span class="hidden sm:block font-semibold text-lg">{{ $siteName ?? config('app.name', 'HK Checklist') }}</span>
                </a>

                <div class="flex items-center gap-3">
                    <!-- Theme toggle -->
                    <button
                        @click="$store.theme?.toggle ? $store.theme.toggle() : (document.documentElement.classList.toggle('dark'))"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800"
                        title="Toggle theme" aria-label="Toggle theme">
                        <!-- simple icon -->
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path
                                d="M12 4a1 1 0 0 1 1 1v1h-2V5a1 1 0 0 1 1-1Zm0 14a1 1 0 0 1 1 1v1h-2v-1a1 1 0 0 1 1-1Zm8-6a1 1 0 0 1 1 1h1v-2h-1a1 1 0 0 1-1 1ZM3 13a1 1 0 0 1-1-1H1v2h1a1 1 0 0 1 1-1Zm13.95 6.536.707.707-1.414 1.414-.707-.707 1.414-1.414ZM6.05 3.05l.707.707L5.343 5.17l-.707-.707L6.05 3.05Zm13.607-.707.707.707-1.414 1.414-.707-.707 1.414-1.414ZM4.343 18.828l.707.707L3.636 20.95l-.707-.707 1.414-1.414Z" />
                        </svg>
                    </button>

                    @auth
                        <a href="{{ route('dashboard') }}"
                            class="hidden sm:inline-flex items-center rounded-md bg-theme-primary px-4 py-2 text-sm font-medium text-white hover:opacity-90 transition-opacity">
                            Go to Dashboard
                        </a>
                    @endauth

                    @guest
                        @if (Route::has('login'))
                            <a href="{{ route('login') }}"
                                class="inline-flex items-center rounded-md border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-800">
                                Log in
                            </a>
                        @endif
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                                class="hidden sm:inline-flex items-center rounded-md bg-theme-primary px-4 py-2 text-sm font-medium text-white hover:opacity-90 transition-opacity">
                                Get Started
                            </a>
                        @endif
                    @endguest
                </div>
            </div>
        </div>
    </header>

    <!-- Hero -->
    <section class="relative overflow-hidden">
        <div class="absolute inset-0 -z-10">
            <div class="h-[18rem]"
                style="background: linear-gradient(135deg, var(--theme-primary), color-mix(in srgb, var(--theme-primary) 88%, white));">
            </div>
            <svg class="w-full h-[6rem]" viewBox="0 0 1440 320" preserveAspectRatio="none"
                style="color: color-mix(in srgb, var(--theme-primary) 20%, transparent);">
                <path fill="currentColor"
                    d="M0,64L80,58.7C160,53,320,43,480,69.3C640,96,800,160,960,181.3C1120,203,1280,181,1360,170.7L1440,160L1440,0L1360,0C1280,0,1120,0,960,0C800,0,640,0,480,0C320,0,160,0,80,0L0,0Z">
                </path>
            </svg>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-10 pb-16 sm:pt-16">
            <div class="grid lg:grid-cols-2 items-center gap-8">
                <div>
                    <span
                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-white/70 ring-1 ring-white/60"
                        style="color: color-mix(in srgb, var(--theme-primary) 80%, black);">
                        Airbnb Housekeeping Checklist
                    </span>
                    <h1 class="mt-4 text-3xl sm:text-4xl font-semibold text-white">
                        Accountability that’s easy for owners,<br class="hidden sm:block" /> fast for housekeepers.
                    </h1>
                    <p class="mt-4 text-white/90">
                        Assign properties, complete room checklists, upload timestamped photos, and stay on schedule
                        with an integrated calendar.
                    </p>
                    <div class="mt-6 flex items-center gap-3">
                        @auth
                            <a href="{{ route('dashboard') }}"
                                class="inline-flex items-center rounded-md bg-white px-4 py-2 text-sm font-medium hover:bg-gray-100"
                                style="color: color-mix(in srgb, var(--theme-primary) 80%, black);">
                                Open Dashboard
                            </a>
                            <a href="{{ route('calendar.index') }}"
                                class="inline-flex items-center rounded-md bg-theme-primary px-4 py-2 text-sm font-medium text-white hover:opacity-90 transition-opacity">
                                View Calendar
                            </a>
                        @else
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                    class="inline-flex items-center rounded-md bg-white px-4 py-2 text-sm font-medium hover:bg-gray-100"
                                    style="color: color-mix(in srgb, var(--theme-primary) 80%, black);">
                                    Create an account
                                </a>
                            @endif
                            @if (Route::has('login'))
                                <a href="{{ route('login') }}"
                                    class="inline-flex items-center rounded-md bg-theme-primary px-4 py-2 text-sm font-medium text-white hover:opacity-90 transition-opacity">
                                    Log in
                                </a>
                            @endif
                        @endauth
                    </div>
                    <div class="mt-6 flex items-center gap-6 text-white/80 text-sm">
                        <div class="flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-emerald-300"></span>
                            GPS Gate</div>
                        <div class="flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-emerald-300"></span>
                            8+ Photos / Room</div>
                        <div class="flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-emerald-300"></span>
                            Room & Inventory</div>
                    </div>
                </div>

                <div class="relative">
                    <div
                        class="rounded-xl border border-white/20 bg-white/80 backdrop-blur shadow-xl overflow-hidden dark:bg-gray-900/70 dark:border-gray-700">
                        <div class="px-5 py-4 border-b border-gray-200/70 dark:border-gray-700 flex items-center gap-2">
                            <div class="h-3 w-3 rounded-full bg-red-400"></div>
                            <div class="h-3 w-3 rounded-full bg-yellow-400"></div>
                            <div class="h-3 w-3 rounded-full bg-green-400"></div>
                            <div class="ms-auto text-xs text-gray-500 dark:text-gray-400">Preview</div>
                        </div>
                        <div class="p-5 grid grid-cols-2 gap-4">
                            <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                                <div class="text-sm font-medium mb-1">Properties</div>
                                <p class="text-xs text-gray-600 dark:text-gray-400">Manage beds/baths, geo radius,
                                    rooms.</p>
                            </div>
                            <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                                <div class="text-sm font-medium mb-1">Rooms & Tasks</div>
                                <p class="text-xs text-gray-600 dark:text-gray-400">Room-by-room tasks, inventory
                                    checks.</p>
                            </div>
                            <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                                <div class="text-sm font-medium mb-1">Checklist Wizard</div>
                                <p class="text-xs text-gray-600 dark:text-gray-400">Rooms → Inventory → Photos.</p>
                            </div>
                            <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                                <div class="text-sm font-medium mb-1">Calendar</div>
                                <p class="text-xs text-gray-600 dark:text-gray-400">See scheduled dates at a glance.</p>
                            </div>
                        </div>
                    </div>
                    <div class="absolute -z-10 -left-10 -bottom-10 h-40 w-40 rounded-full blur-3xl"
                        style="background-color: color-mix(in srgb, var(--theme-primary) 25%, transparent);">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Trust Badges -->
    <section class="py-10 border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Integrates with your favorite platforms</p>
                <div class="flex flex-wrap justify-center items-center gap-8 opacity-60">
                    <span class="text-xl font-bold text-red-500">Airbnb</span>
                    <span class="text-xl font-bold text-blue-600">VRBO</span>
                    <span class="text-xl font-bold text-indigo-600">Booking.com</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                    Everything You Need to Manage Vacation Rental Cleaning
                </h2>
                <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                    From scheduling to photo documentation, our platform handles every aspect of housekeeping management.
                </p>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @php
                    $features = [
                        [
                            'icon' => '📋',
                            'title' => 'Smart Task Checklists',
                            'desc' => 'Create detailed room-by-room checklists with example photos and videos showing exactly how each task should be done.',
                        ],
                        [
                            'icon' => '📍',
                            'title' => 'GPS Verification',
                            'desc' => 'Ensure housekeepers are on-site before starting. GPS confirms location within your property radius.',
                        ],
                        [
                            'icon' => '📷',
                            'title' => 'Timestamped Photos',
                            'desc' => 'Every photo is automatically timestamped in the corner. Download high-res originals or view web-optimized versions.',
                        ],
                        [
                            'icon' => '📅',
                            'title' => 'Calendar Sync',
                            'desc' => 'Connect Airbnb, VRBO, and Booking.com calendars. Get automatic checkout alerts to schedule cleanings.',
                        ],
                        [
                            'icon' => '📊',
                            'title' => 'Detailed Reports',
                            'desc' => 'Generate beautiful reports with all tasks, notes, and photos. Share via email or text with a single click.',
                        ],
                        [
                            'icon' => '👥',
                            'title' => 'Multi-User Support',
                            'desc' => 'Perfect for property managers and cleaning companies. Add owners, manage housekeepers, keep everything organized.',
                        ],
                    ];
                @endphp

                @foreach ($features as $f)
                    <div class="rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg transition-shadow">
                        <div class="text-3xl mb-3">{{ $f['icon'] }}</div>
                        <div class="font-semibold text-lg text-gray-900 dark:text-gray-100 mb-2">{{ $f['title'] }}</div>
                        <p class="text-sm text-gray-600 dark:text-gray-300">{{ $f['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Problem/Solution Section -->
    <section class="py-16 bg-gradient-to-br from-gray-100 to-gray-50 dark:from-gray-800 dark:to-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-6">
                        Stop Chasing Housekeepers for Updates
                    </h2>
                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-6 h-6 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                                <span class="text-red-600 dark:text-red-400 text-sm">✕</span>
                            </div>
                            <p class="text-gray-600 dark:text-gray-300">Endless text messages asking "Are you done yet?"</p>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-6 h-6 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                                <span class="text-red-600 dark:text-red-400 text-sm">✕</span>
                            </div>
                            <p class="text-gray-600 dark:text-gray-300">No proof that work was actually completed properly</p>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-6 h-6 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                                <span class="text-red-600 dark:text-red-400 text-sm">✕</span>
                            </div>
                            <p class="text-gray-600 dark:text-gray-300">Forgetting to schedule cleanings after guest checkouts</p>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-6 h-6 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                                <span class="text-red-600 dark:text-red-400 text-sm">✕</span>
                            </div>
                            <p class="text-gray-600 dark:text-gray-300">Bad reviews because tasks were missed</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-xl">
                    <h3 class="font-semibold text-lg text-gray-900 dark:text-gray-100 mb-4">With {{ $siteName ?? config('app.name', 'HK Checklist') }}:</h3>
                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-6 h-6 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                <span class="text-green-600 dark:text-green-400 text-sm">✓</span>
                            </div>
                            <p class="text-gray-600 dark:text-gray-300">Real-time status updates as tasks are completed</p>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-6 h-6 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                <span class="text-green-600 dark:text-green-400 text-sm">✓</span>
                            </div>
                            <p class="text-gray-600 dark:text-gray-300">Timestamped photos prove work quality</p>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-6 h-6 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                <span class="text-green-600 dark:text-green-400 text-sm">✓</span>
                            </div>
                            <p class="text-gray-600 dark:text-gray-300">Automatic checkout alerts from your booking calendars</p>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-6 h-6 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                <span class="text-green-600 dark:text-green-400 text-sm">✓</span>
                            </div>
                            <p class="text-gray-600 dark:text-gray-300">Consistent quality with detailed checklists</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- For Property Managers Section -->
    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200 mb-4">
                    For Property Management Companies
                </span>
                <h2 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                    Scale Your Cleaning Operations
                </h2>
                <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                    Manage multiple property owners, their properties, and your cleaning team all in one place.
                </p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                        <span class="text-2xl">🏢</span>
                    </div>
                    <h3 class="font-semibold text-lg text-gray-900 dark:text-gray-100 mb-2">Company Dashboard</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        Add property owners under your company. They see only their properties while you see everything.
                    </p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                        <span class="text-2xl">👥</span>
                    </div>
                    <h3 class="font-semibold text-lg text-gray-900 dark:text-gray-100 mb-2">Team Management</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        Your housekeepers stay under your company. Owners can't see your other clients' staff.
                    </p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
                        <span class="text-2xl">📨</span>
                    </div>
                    <h3 class="font-semibold text-lg text-gray-900 dark:text-gray-100 mb-2">Owner Reports</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        Send professional cleaning reports to owners via email or text. Build trust with transparency.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Steps -->
    <section class="pb-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold">How it works</h3>
                <ol class="mt-4 grid sm:grid-cols-3 gap-6 text-sm">
                    <li class="rounded-lg p-4 bg-gray-50 dark:bg-gray-900/40">
                        <div class="font-medium">1. Set up</div>
                        <p class="mt-1 text-gray-600 dark:text-gray-300">Create properties, rooms, and default properties.tasks.
                        </p>
                    </li>
                    <li class="rounded-lg p-4 bg-gray-50 dark:bg-gray-900/40">
                        <div class="font-medium">2. Assign</div>
                        <p class="mt-1 text-gray-600 dark:text-gray-300">Schedule a housekeeper via Manage Sessions.
                        </p>
                    </li>
                    <li class="rounded-lg p-4 bg-gray-50 dark:bg-gray-900/40">
                        <div class="font-medium">3. Complete</div>
                        <p class="mt-1 text-gray-600 dark:text-gray-300">HK confirms GPS, completes rooms, uploads
                            photos, submits.</p>
                    </li>
                </ol>
                <div class="mt-6 flex flex-wrap items-center gap-3">
                    @auth
                        <a href="{{ route('manage.sessions.index') }}"
                            class="inline-flex items-center rounded-md bg-theme-primary px-4 py-2 text-sm font-medium text-white hover:opacity-90 transition-opacity">
                            Manage Sessions
                        </a>
                        <a href="{{ route('calendar.index') }}"
                            class="inline-flex items-center rounded-md border border-gray-300 dark:border-gray-700 px-4 py-2 text-sm hover:bg-gray-50 dark:hover:bg-gray-800">
                            View Calendar
                        </a>
                    @else
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                                class="inline-flex items-center rounded-md bg-theme-primary px-4 py-2 text-sm font-medium text-white hover:opacity-90 transition-opacity">
                                Get Started
                            </a>
                        @endif
                        @if (Route::has('login'))
                            <a href="{{ route('login') }}"
                                class="inline-flex items-center rounded-md border border-gray-300 dark:border-gray-700 px-4 py-2 text-sm hover:bg-gray-50 dark:hover:bg-gray-800">
                                Log in
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-8 border-t border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-sm text-gray-600 dark:text-gray-300">
            <div class="flex items-center justify-between">
                <p>© {{ date('Y') }} {{ $siteName ?? config('app.name', 'HK Checklist') }}. All rights reserved.</p>
                <div class="flex items-center gap-4">
                    <a href="{{ route('calendar.index') }}" class="hover:underline">Calendar</a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="hover:underline">Dashboard</a>
                    @else
                        @if (Route::has('login'))
                            <a href="{{ route('login') }}" class="hover:underline">Login</a>
                        @endif
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="hover:underline">Register</a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </footer>
</body>

</html>
