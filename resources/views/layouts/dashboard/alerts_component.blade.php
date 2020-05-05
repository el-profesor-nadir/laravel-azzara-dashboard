<div class="alert alert-{{$type}} alert-dismissible fade show alert-bottom" role="alert"
    data-aos="fade-left" data-aos-anchor-placement="top-bottom" data-aos-duration="3000">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    <h5><i class="icon fa {{$icon}}"></i> {{ $title }}</h5>
    {{ $slot }}
</div>