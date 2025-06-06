<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#ffffff">
        <meta name="apple-mobile-web-app-status-bar-style" content="white-translucent">
        <meta name="msapplication-navbutton-color" content="#ffffff">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="mobile-web-app-capable" content="yes">
        
        <title>Apik - @yield('title')</title>
        <link rel="icon" href="favicon.ico" type="image/x-icon">
        <link rel="manifest" href="{{ asset('manifest.json') }}">

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    </head>
    <body style="max-width: 460px;" class="mx-auto bg-dark">
        @yield('navbar')
        @yield('content')
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        <script>
            function updateClock() {
                const clockElement = document.getElementById('realtime-clock');
                if (!clockElement) return;
                const dateElement = document.getElementById('realtime-date');
                if (!dateElement) return;
                const now = new Date();

                // Format jam
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const seconds = String(now.getSeconds()).padStart(2, '0');
                clockElement.textContent = `${hours}:${minutes}:${seconds}`;

                // Array nama hari dan bulan
                const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                const months = [
                    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                ];

                // Format tanggal
                const dayName = days[now.getDay()];
                const day = String(now.getDate()).padStart(2, '0');
                const monthName = months[now.getMonth()];
                const year = now.getFullYear();
                dateElement.textContent = `${dayName}, ${day} ${monthName} ${year}`;
            }

            setInterval(updateClock, 1000);
            updateClock();
        </script>
    </body>
</html>
