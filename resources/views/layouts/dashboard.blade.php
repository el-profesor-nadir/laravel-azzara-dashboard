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

    <style>
        .dropdown-user {
            width: 280px;
        }

    </style>

</head>
<body>
	<div class="wrapper" id="app">

        @include('layouts.dashboard.header')

		@include('layouts.dashboard.sidebar')

		<div class="main-panel">
			<div class="content">
                <div class="page-inner">
                    @yield('page-header')
                    <div class="row">
                        <div class="col-md-12">
                            @include('layouts.dashboard.alerts')
                        </div>
                    </div>
                    @yield('content')
                </div>
			</div>
		</div>
        <!-- Maodals -->
        @include('layouts.dashboard.modals')
	</div>

    <!--   Core JS Files   -->
    <script src="{{ asset('templates/dashboard/assets/js/core/jquery.3.2.1.min.js') }}"></script>
    <script src="{{ asset('templates/dashboard/assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('templates/dashboard/assets/js/core/bootstrap.min.js') }}"></script>

    <!-- jQuery UI -->
    <script src="{{ asset('templates/dashboard/assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('templates/dashboard/assets/js/plugin/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js') }}"></script>

    <!-- jQuery Scrollbar -->
    <script src="{{ asset('templates/dashboard/assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>

    <!-- Moment JS -->
    <script src="{{ asset('templates/dashboard/assets/js/plugin/moment/moment.min.js') }}"></script>

    <!-- Chart JS -->
    <script src="{{ asset('templates/dashboard/assets/js/plugin/chart.js/chart.min.js') }}"></script>

    <!-- jQuery Sparkline -->
    <script src="{{ asset('templates/dashboard/assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js') }}"></script>

    <!-- Chart Circle -->
    <script src="{{ asset('templates/dashboard/assets/js/plugin/chart-circle/circles.min.js') }}"></script>

    <!-- Datatables -->
    <script src="{{ asset('templates/dashboard/assets/js/plugin/datatables/datatables.min.js') }}"></script>

    <!-- Bootstrap Notify -->
    <script src="{{ asset('templates/dashboard/assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js') }}"></script>

    <!-- Bootstrap Toggle -->
    <script src="{{ asset('templates/dashboard/assets/js/plugin/bootstrap-toggle/bootstrap-toggle.min.js') }}"></script>

    <!-- Google Maps Plugin -->
    <script src="{{ asset('templates/dashboard/assets/js/plugin/gmaps/gmaps.js') }}"></script>

    <!-- Sweet Alert -->
    <script src="{{ asset('templates/dashboard/assets/js/plugin/sweetalert/sweetalert.min.js') }}"></script>

    <!-- Azzara JS -->
    <script src="{{ asset('templates/dashboard/assets/js/ready.min.js') }}"></script>

    <script src="{{ asset('templates/shared/js/modals.js') }}"></script>

    @yield('scripts')

</body>
</html>
