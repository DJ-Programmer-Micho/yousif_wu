<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="{{ asset('assets/css/style.min.css') }}" rel="stylesheet">
    <link rel="icon" type="image/png" sizes="16x16" href="../assets/images/logo-icon.png">
    <link rel="shortcut icon" href="../assets/images/logo-icon.png">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="../assets/images/big-logo.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="../assets/images/big-logo.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="../assets/images/big-logo.png">
    <link rel="apple-touch-icon-precomposed" sizes="57x57" href="../assets/images/big-logo.png">
    <link rel="apple-touch-icon-precomposed" href="../assets/images/big-logo.png">
    <title>IRAQ REMIT</title>
</head>
<body>
    <div class="container mt-4">
        <a class="navbar-brand" href="/"> 
            <img src="../assets/images/big-logo.png" alt="Italian Coffee" width="120">
        </a>	
        <hr class="underline_Logo" style="height: 5px; background-color: #9c4e30">
        @yield('law')
    </div>
    @yield('app')

    <script src="{{ asset('assets/libs/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/popper.js/dist/umd/popper.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.min.js') }}"></script>
</body>
</html>