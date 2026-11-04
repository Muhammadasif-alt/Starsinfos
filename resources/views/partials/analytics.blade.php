{{-- Google Analytics 4. Only the live site reports, so local testing does not count as visits. --}}
@production
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-4TX2QXN2HF"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-4TX2QXN2HF');
    </script>
@endproduction
