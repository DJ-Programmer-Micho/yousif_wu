<!DOCTYPE html>
<html dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="../assets/images/logo-icon.png">
    <link rel="shortcut icon" href="../assets/images/logo-icon.png">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="../assets/images/logo-icon.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="../assets/images/logo-icon.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="../assets/images/logo-icon.png">
    <link rel="apple-touch-icon-precomposed" sizes="57x57" href="../assets/images/logo-icon.png">
    <link rel="apple-touch-icon-precomposed" href="../assets/images/logo-icon.png">
    <title>Iraq Remit</title>
    <!-- Custom CSS -->
    <link href="{{ asset('assets/css/style.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/toaster.css') }}" rel="stylesheet" type="text/css">
    <script src="https://www.google.com/recaptcha/api.js?render={{ env('GOOGLE_RECAPTCHA_KEY') }}"></script>
    <style>
        .btn-log{
            color: white;
            background-color: #9c4e30;
        }
        .btn-log:hover{
            color: white;
            background-color: #e2905b;

        }
    </style>
</head>

<body>
    <div class="main-wrapper">
        <!-- ============================================================== -->
        <!-- Preloader - style you can find in spinners.css -->
        <!-- ============================================================== -->
        <div class="preloader">
            <div class="lds-ripple">
                <div class="lds-pos"></div>
                <div class="lds-pos"></div>
            </div>
        </div>
        <!-- ============================================================== -->
        <!-- Preloader - style you can find in spinners.css -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Login box.scss -->
        <!-- ============================================================== -->
        <div class="auth-wrapper d-flex no-block justify-content-center align-items-center position-relative"
            style="background:url(../assets/images/big/auth-bg.jpg) no-repeat center center;">
            <div class="auth-box row">
                <div class="col-12 bg-white">
                    <div class="p-3">
                        <div class="text-center">
                            <img src="../assets/images/big-logo.png" width="130" alt="wrapkit">
                        </div>
                        {{-- @php
                            print(Hash::make('Reg11234'));
                        @endphp --}}
                        <h2 class="mt-3 text-center" style="color: #9c4e30">SIGN IN</h2>
                        {{-- @php
                            print(Hash::make('asdasdasd'));
                        @endphp --}}
                        <p class="text-center" style="color: #9c4e30">Enter your email address and password to access admin panel.</p>
                            <form class="mt-4" method="POST" action="{{ route('auth.login.action') }}">
                            @csrf

                            <div class="row">
                                <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="text-dark" for="login">Email</label>
                                    <input
                                    class="form-control @error('login') is-invalid @enderror"
                                    id="login" name="login" type="text"
                                    value="{{ old('login') }}"
                                    placeholder="email" required>
                                    @error('login') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                </div>

                                <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="text-dark" for="password">Password</label>
                                    <input
                                    class="form-control @error('password') is-invalid @enderror"
                                    id="password" name="password" type="password"
                                    placeholder="enter your password" required>
                                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                </div>

                                <div class="col-lg-12">
                                <div class="form-group">
                                    <label style="color: #37399e" for="password">Recaptcha</label>
                                    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
                                    <div class="g-recaptcha" id="feedback-recaptcha" data-sitekey="{!! env('GOOGLE_RECAPTCHA_KEY') !!}"></div>
                                    @error('g-recaptcha-response')
                                    <span class="text-danger" style="font-size: 12px">{{__('Please Check reCaptcha')}}</span><br>
                                    @enderror
                                </div>
                                </div>

                                <div class="col-lg-12 text-center">
                                <button type="submit" class="btn btn-block btn-log">Sign In</button>
                                </div>
                            </div>
                            </form>

                    </div>
                </div>
            </div>
        </div>
        <!-- ============================================================== -->
        <!-- Login box.scss -->
        <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- All Required js -->
    <!-- ============================================================== -->
    <script src="../assets/libs/jquery/dist/jquery.min.js "></script>
    <!-- Bootstrap tether Core JavaScript -->
    <script src="../assets/libs/popper.js/dist/umd/popper.min.js "></script>
    <script src="../assets/libs/bootstrap/dist/js/bootstrap.min.js "></script>
    <!-- ============================================================== -->
    <!-- This page plugin js -->
    <!-- ============================================================== -->
    <script>
        $(".preloader ").fadeOut();
    </script>
</body>

</html>