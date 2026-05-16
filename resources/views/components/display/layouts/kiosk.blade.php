<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'OrtoTraceability') }} | Kiosk Display</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Outfit', sans-serif; }
        .font-mono-tech { font-family: 'Orbitron', sans-serif; }
    </style>
</head>
<body class="bg-[#0f172a] text-slate-200 h-full overflow-hidden antialiased border-[12px] border-slate-800">
    <div class="h-full flex flex-col">
        <!-- Technical status bar top -->
        <div class="bg-slate-800 px-8 py-3 flex justify-between items-center border-b border-slate-700">
            <div class="flex items-center space-x-4">
                <div class="w-3 h-3 bg-emerald-500 rounded-full animate-pulse shadow-[0_0_10px_#10b981]"></div>
                <span class="text-xs font-bold tracking-widest text-slate-400 uppercase font-mono-tech">System Active</span>
            </div>
            <div class="text-xs font-bold tracking-widest text-slate-400 uppercase font-mono-tech" id="clock">
                {{ now()->format('H:i:s') }}
            </div>
        </div>

        <!-- Main content area -->
        <main class="flex-grow p-10 overflow-hidden">
            {{ $slot }}
        </main>

        <!-- Technical footer -->
        <div class="bg-slate-800 px-8 py-3 text-[10px] text-slate-500 flex justify-between items-center border-t border-slate-700">
            <div>ORTO-TRACEABILITY v2.0 // MD-DOMAIN-DRIVEN</div>
            <div class="text-emerald-500/50 uppercase tracking-tighter">Verified Traceability Protocol 812-B</div>
        </div>
    </div>

    <script>
        // Real-time clock for the kiosk
        setInterval(() => {
            const now = new Date();
            document.getElementById('clock').textContent = now.toLocaleTimeString('pt-BR');
        }, 1000);
    </script>
</body>
</html>
