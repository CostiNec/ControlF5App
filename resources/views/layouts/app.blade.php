<!DOCTYPE html>
<html>
    <head>
        <title>{{ env('APP_NAME') }}</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" integrity="sha256-h20CPZ0QyXlBuAw7A+KluUYx/3pK+c7lYEpqLTlxjYQ=" crossorigin="anonymous" />

        <script src="{{ asset('js/app.js') }}"></script>

    </head>
    <body>
    @include('layouts.navbar')
    <div id="content">
        <div id="put-grey">
        </div>
        @yield('content')
    </div>
    </body>
</html>
