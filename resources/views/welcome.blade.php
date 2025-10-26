<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>hi .........</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
    <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css'>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
</head>
@vite('resources/js/app.js')

<body>
    <div id="app">
        {{-- <example-component :chats="{{ json_encode($chats) }}"></example-component> --}}
        <databse-component></databse-component>
    </div>

    <p>hi</p>
    {{-- databse --}}

</body>
{{-- <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script> --}}
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
{{-- <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script> --}}
<script>
    // Enable pusher logging - don 't include this in production
    Pusher.logToConsole = true;

    var pusher = new Pusher("{{ env('PUSHER_APP_KEY') }}", {
        cluster: 'mt1'
    });

    var channel = pusher.subscribe('users');
    channel.bind('App\\Events\\NewUserRegistered', function(data) {
        console.log(JSON.stringify(data));
        alert(JSON.stringify(data));
    });
</script>
<script>
    window.Laravel = {!! json_encode(['userId' => auth()->user()->id]) !!};
</script>
{{-- <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/3.0.0/handlebars.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/list.js/1.1.1/list.min.js'></script> --}}
{{-- <script src="{{ asset('script.js') }}"></script> --}}


</html>
