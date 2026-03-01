<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Traceability OPME') }}</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
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
        body { font-family: 'Inter', sans-serif; background-color: #F8FAFC; color: #0F172A; }
    </style>
</head>
<body class="antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <div class="mb-8 flex flex-col items-center gap-4">
            <div class="w-16 h-16 bg-primary rounded-2xl flex items-center justify-center text-white text-3xl font-bold shadow-lg">T</div>
            <h1 class="text-2xl font-bold text-text-primary tracking-tight">Traceability OPME</h1>
        </div>

        <div class="w-full sm:max-w-md px-10 py-12 bg-white border border-border shadow-2xl rounded-2xl">
            {{ $slot }}
        </div>
        
        <p class="mt-8 text-xs text-text-secondary uppercase tracking-widest font-bold opacity-50">Hospital Geral • Qualidade & Rastreabilidade</p>
    </div>
</body>
</html>
