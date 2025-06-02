<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* You might want to add specific styles for the login page here if needed */
        .form-container {
            background-color: #ffffff;
        }
        .info-container {
            background-color: #2d3748; /* Corresponds to bg-gray-800 (dark sidebar color) */
        }
        /* If you want the green theme from the image for the info-container:
        .info-container {
            background: linear-gradient(to right, #00b09b, #96c93d);
        }
        */
    </style>
</head>
<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900 flex items-center justify-center min-h-screen">

    <div class="container mx-auto p-4 sm:p-0" style="max-width: 900px;">
        <div class="flex flex-col md:flex-row rounded-lg shadow-2xl overflow-hidden">
            <!-- Form Section (Left) -->
            <div class="w-full md:w-1/2 p-8 sm:p-12 form-container order-2 md:order-1">
                @yield('form-content')
            </div>

            <!-- Info Section (Right) -->
            <div class="w-full md:w-1/2 p-8 sm:p-12 flex flex-col justify-center items-center text-white info-container order-1 md:order-2">
                @yield('info-content')
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html> 