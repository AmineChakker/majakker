<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>{{ $title ?? config('app.name') }}</title>
<link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}"/>
<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Instrument+Serif:ital@0;1&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="{{ asset('css/tokens.css') }}"/>
<link rel="stylesheet" href="{{ asset('css/utilities.css') }}"/>
<link rel="stylesheet" href="{{ asset('css/animations.css') }}"/>
@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>{{ $slot }}</body>
</html>
