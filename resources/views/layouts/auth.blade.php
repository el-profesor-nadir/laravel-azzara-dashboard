<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
	<link rel="icon" href="{{ asset('templates/dashboard/assets/img/icon.ico') }}" type="image/x-icon"/>

	<!-- Fonts and icons -->
	<script src="{{ asset('templates/dashboard/assets/js/plugin/webfont/webfont.min.js') }}"></script>
	<script>
		WebFont.load({
			google: {"families":["Open+Sans:300,400,600,700"]},
			custom: {"families":["Flaticon", "Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands"], urls: ['{{ asset('templates/dashboard/assets/css/fonts.css') }}']},
			active: function() {
				sessionStorage.fonts = true;
			}
		});
	</script>

	<!-- CSS Files -->
	<link rel="stylesheet" href="{{ asset('templates/dashboard/assets/css/bootstrap.min.css') }}">
	<link rel="stylesheet" href="{{ asset('templates/dashboard/assets/css/azzara.min.css') }}">

    @yield('styles')

</head>
<body class="login">
	<div class="wrapper wrapper-login">
        @yield('content')
	</div>
	<script src="{{ asset('templates/dashboard/assets/js/core/jquery.3.2.1.min.js') }}"></script>
	<script src="{{ asset('templates/dashboard/assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js') }}"></script>
	<script src="{{ asset('templates/dashboard/assets/js/core/popper.min.js') }}"></script>
	<script src="{{ asset('templates/dashboard/assets/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('templates/dashboard/assets/js/ready.js') }}"></script>
    <script>
        var containerSignIn = $('.container-login');
        containerSignUp.css('display', 'block');
    </script>
</body>
</html>
