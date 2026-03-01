<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Traceability OPME') }}</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine JS -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Inter Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#2563EB',
                        'primary-light': '#DBEAFE',
                        surface: '#F8FAFC',
                        border: '#E2E8F0',
                        success: '#16A34A',
                        warning: '#F59E0B',
                        danger: '#DC2626',
                        'text-primary': '#0F172A',
                        'text-secondary': '#475569',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    borderRadius: {
                        '2xl': '1rem',
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #FFFFFF; color: #0F172A; }
    </style>
</head>
<body class="bg-white">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-surface border-r border-border p-6 flex flex-col gap-8">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center text-white font-bold">T</div>
                <h1 class="text-xl font-bold text-text-primary">Traceability</h1>
            </div>

            <nav class="flex flex-col gap-2">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 p-3 rounded-xl {{ request()->routeIs('dashboard') ? 'bg-primary text-white shadow-sm' : 'text-text-secondary hover:bg-white transition' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Dashboard
                </a>
                <a href="{{ route('materials.index') }}" class="flex items-center gap-3 p-3 rounded-xl {{ request()->routeIs('materials.*') ? 'bg-primary text-white shadow-sm' : 'text-text-secondary hover:bg-white transition' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    Materiais
                </a>
                <a href="{{ route('surgeries.index') }}" class="flex items-center gap-3 p-3 rounded-xl {{ request()->routeIs('surgeries.*') ? 'bg-primary text-white shadow-sm' : 'text-text-secondary hover:bg-white transition' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    Cirurgias
                </a>
                <a href="{{ route('audit.index') }}" class="flex items-center gap-3 p-3 rounded-xl {{ request()->routeIs('audit.*') ? 'bg-primary text-white shadow-sm' : 'text-text-secondary hover:bg-white transition' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Auditoria
                </a>
            </nav>

            <div class="mt-auto pt-6 border-t border-border">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-primary-light flex items-center justify-center text-primary font-semibold">
                            {{ Auth::check() ? strtoupper(substr(Auth::user()->name, 0, 2)) : '??' }}
                        </div>
                        <div>
                            <p class="text-sm font-bold text-text-primary">{{ Auth::check() ? Auth::user()->name : 'Visitante' }}</p>
                            <p class="text-xs text-text-secondary">Hospital Geral</p>
                        </div>
                    </div>
                    
                    @auth
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-text-secondary hover:text-danger transition" title="Sair">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        </button>
                    </form>
                    @endauth
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8 overflow-y-auto">
            @if(session('success'))
                <div class="mb-6 bg-green-50 text-success p-4 rounded-2xl border border-green-100 flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-50 text-danger p-4 rounded-2xl border border-red-100 flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ session('error') }}
                </div>
            @endif

            {{ $slot ?? '' }}
            @yield('content')
        </main>
    </div>
</body>
</html>
