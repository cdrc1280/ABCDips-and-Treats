<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @class(['scroll-smooth', 'dark' => ($appearance ?? 'system') === 'dark'])>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    {{-- Apply the system theme before paint when no explicit choice is stored (same as admin.blade.php) --}}
    <script>
        (function () {
            if ('{{ $appearance ?? 'system' }}' === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
    <style>html { background-color: #fbf6ee; } html.dark { background-color: #1a120d; }</style>
    <meta name="description" content="Handcrafted banana bread, cookies, brownies, cinnamon rolls, cakes, cheesecakes & more — baked fresh in Cavite. Order online for delivery or pickup." />
    <meta name="theme-color" content="#FBF6EE" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="ABCDips & Treats" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <link rel="icon" type="image/x-icon" href="/favicon.ico" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:ital,wght@0,400..700;1,400..700&family=Young+Serif&display=swap" rel="stylesheet">

    @viteReactRefresh
    @vite(['resources/js/storefront.tsx', "resources/js/pages/{$page['component']}.tsx"])
    @inertiaHead
</head>
<body class="min-h-screen bg-background text-foreground antialiased">
    @inertia
</body>
</html>
