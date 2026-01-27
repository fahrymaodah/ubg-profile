<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Segera Hadir - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: .5; }
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-900 to-blue-700 flex items-center justify-center p-4">
    <div class="max-w-xl w-full text-center">
        <div class="bg-white rounded-2xl shadow-2xl p-8 md:p-12">
            <!-- Logo placeholder -->
            <div class="w-24 h-24 mx-auto mb-6 bg-blue-100 rounded-full flex items-center justify-center">
                <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>

            <!-- Coming Soon Badge -->
            <div class="inline-flex items-center px-4 py-2 bg-yellow-100 text-yellow-800 rounded-full text-sm font-semibold mb-6">
                <span class="w-2 h-2 bg-yellow-500 rounded-full mr-2 animate-pulse"></span>
                Segera Hadir
            </div>

            <!-- Message -->
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-4">
                Website Dalam Pengembangan
            </h1>
            
            <p class="text-gray-600 mb-8 leading-relaxed">
                {{ $message ?? 'Kami sedang mempersiapkan sesuatu yang luar biasa untuk Anda. Website ini akan segera aktif. Silakan kembali lagi nanti.' }}
            </p>

            <!-- Back to main site -->
            <a href="{{ config('app.url') }}" 
               class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Halaman Utama
            </a>
        </div>

        <!-- Footer -->
        <p class="text-blue-200 text-sm mt-8">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </p>
    </div>
</body>
</html>
