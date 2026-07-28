<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <!-- SEO -->
    <title>ABCDips & Treats</title>
    <meta name="description" content="Handcrafted banana bread, cookies, brownies, cinnamon rolls, cakes, cheesecakes & more — baked with love. Order online for delivery or pickup." />
    <meta name="theme-color" content="#D9A876" />

    <!-- Open Graph -->
    <meta property="og:type" content="website" />
    <meta property="og:title" content="ABCDips & Treats" />
    <meta property="og:description" content="Handcrafted pastries baked fresh daily. Order online for delivery or pickup." />
    <meta property="og:url" content="{{ config('app.url') }}" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-surface text-ink antialiased">
    <div id="app"></div>
</body>
</html>
