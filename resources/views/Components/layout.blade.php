    @props([
        'pageTitle' => '',
        'users' => [],
        'currentUserId' => null,
        'userDocCounts' => [],
        'currentCategory' => null,
        'documents' => [],
    ]);

    <!DOCTYPE html>
    <html lang="de">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>MyDocs Document Management{{ $pageTitle ? ' | ' . $pageTitle : '' }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body>
        <div class="main-container">
            <x-header 
                :users="$users" 
                :currentUserId="$currentUserId" 
                :currentCategory="$currentCategory" 
                :userDocCounts="$userDocCounts" 
            />

            <main class="main-content">
                <x-left-menu />

                {{ $slot }}
            </main>
        </div>
    </body>

    </html>
