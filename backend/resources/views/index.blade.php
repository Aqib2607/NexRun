<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NexRun API Server</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@700&display=swap');
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; }
        h1 { font-family: 'Space Grotesk', sans-serif; }
    </style>
    <!-- Redirect to frontend automatically after 3 seconds -->
    <meta http-equiv="refresh" content="3; url={{ env('FRONTEND_URL', 'http://localhost:5173') }}" />
</head>
<body class="bg-[#050505] text-white h-screen flex flex-col items-center justify-center">
    <div class="text-center">
        <h1 class="text-5xl md:text-7xl font-bold tracking-tight mb-4">
            Nex<span class="text-[#FF5A1F]">Run</span> API
        </h1>
        <p class="text-gray-400 text-lg md:text-xl mb-8">
            The backend engine is running smoothly.
        </p>
        <div class="inline-flex items-center justify-center gap-3">
            <svg class="animate-spin h-5 w-5 text-[#FF5A1F]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Redirecting to Frontend Application...</span>
        </div>
        <div class="mt-8">
            <a href="{{ env('FRONTEND_URL', 'http://localhost:5173') }}" class="text-[#FF5A1F] hover:text-white underline underline-offset-4 transition-colors">
                Click here if you are not redirected
            </a>
        </div>
    </div>
</body>
</html>
