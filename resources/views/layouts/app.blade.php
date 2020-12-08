<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <!--  My Function    -->
    <script src="{{ URL::asset('/js/plugins/myFun.js') }}"></script>

    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <script src="https://js.pusher.com/6.0/pusher.min.js"></script>
    @if(!auth()->guest())
    <script>
         // Enable pusher logging - don't include this in production
         Pusher.logToConsole = true;
 
         var pusher = new Pusher('{{config('broadcasting.connections.pusher.key')}}', {
         cluster: 'eu'
         });
 
         var channel = pusher.subscribe('usr_{{Auth::user()->id}}');
         channel.bind('my-event', function(data) {
         alert(JSON.stringify(data));
         });
    </script>
    @endif


    <!-- Styles -->
    <link href="/css/app.css" rel="stylesheet">
    <!-- Scripts -->
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
     <!-- This makes the current user's id available in javascript -->
    @if(!auth()->guest())
        <script>
            window.Laravel.userId = <?php echo auth()->user()->id; ?>
        </script>
    @endif
</head>
<body>
    <div id="app">
        @include ('layouts.partials.header')
        <div class="container">
            <div class="row">
                <div class="col-md-8 col-md-offset-2">
                    @include ('layouts.partials._notifications')
                </div>
            </div>
        </div>

        <div class="container">
            @yield('content')
        </div>
    </div>

    <!-- Scripts -->
    <script src="/js/app.js"></script>
</body>
</html>
