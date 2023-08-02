@if(!empty($rota))
    @if(!empty($param))
        <a class="btn btn-link btn-sm" href="{{ route($rota,$param) }}"><i class="fas fa-chevron-left fa-2x"></i></a>
    @else
        <a class="btn btn-link btn-sm" href="{{ route($rota) }}"><i class="fas fa-chevron-left fa-2x"></i></a>
    @endif
@endif
{{ $titulo }}
