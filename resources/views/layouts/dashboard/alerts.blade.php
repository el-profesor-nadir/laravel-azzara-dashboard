@if(session()->has('success'))
    @component('layouts.dashboard.alerts_component', ['type' => 'success', 'title' => __('Congratulation!'), 'icon'=>'fa-check'])
        {{ session('success') }}
    @endcomponent
@endif

@if(session()->has('error'))
    @component('layouts.dashboard.alerts_component', ['type' => 'danger', 'title' => __('Alert!'), 'icon'=>'fa-ban'])
        {{ session('error') }}
    @endcomponent
@endif

@if(session()->has('warning'))
    @component('layouts.dashboard.alerts_component', ['type' => 'warning', 'title' => __('Warning!'), 'icon'=>'fa-warning'])
        {{ session('warning') }}
    @endcomponent
@endif

@if(session()->has('info'))
    @component('layouts.dashboard.alerts_component', ['type' => 'info', 'title' => __('Info!'), 'icon'=>'fa-info'])
        {{ session('info') }}
    @endcomponent
@endif
