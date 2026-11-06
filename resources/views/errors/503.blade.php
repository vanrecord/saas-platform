<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $company ?? 'Domain Unavailable' }} - 503 Offline</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col items-center justify-center p-4">
    <div class="max-w-md w-full text-center">
        <!-- Status Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-8 sm:p-10">
            <!-- Icon -->
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-rose-50 text-rose-500 mb-6">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>

            <!-- Clinic / Organization Badge -->
            <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700 mb-3">
                {{ $company ?? 'Dental Clinic' }}
            </div>

            <!-- Heading -->
            <h1 class="text-2xl font-bold text-slate-900 mb-2">Domain Temporarily Offline</h1>
            
            <!-- Message -->
            <p class="text-sm text-slate-600 mb-6 leading-relaxed">
                {{ $message ?? 'This clinic management system has been stopped by the administrator and is currently offline.' }}
            </p>

            <!-- Domain Info -->
            <div class="bg-slate-50 rounded-xl p-3.5 border border-slate-200/60 mb-6 text-left">
                <div class="flex items-center justify-between text-xs text-slate-500 mb-1">
                    <span>Target Domain</span>
                    <span class="inline-flex items-center gap-1 font-semibold text-rose-600">
                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Stopped
                    </span>
                </div>
                <div class="text-sm font-mono font-medium text-slate-800 truncate">
                    {{ $domain ?? request()->getHost() }}
                </div>
            </div>

            <!-- Information notice -->
            <p class="text-xs text-slate-400">
                If you believe this is an error, please contact your SaaS administrator or clinic supervisor.
            </p>
        </div>

        <!-- Footer -->
        <div class="mt-6 text-xs text-slate-400">
            HTTP 503 &bull; Service Temporarily Offline
        </div>
    </div>
</body>
</html>

