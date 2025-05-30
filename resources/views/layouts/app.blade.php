<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $title ?? 'Perpustakaan' }}</title>
    <link rel="icon" href="{{ asset('assets/images/ic_Logo.png') }}" type="logo Perpustakaan" />
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>

<body>
    @include('layouts.navigation')

    {{ $slot }}
</body>

</html>
