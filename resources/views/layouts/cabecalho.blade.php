@if(!empty($rota))
    @if(!empty($param))
        <a href="{{ route($rota,$param) }}"><i class="fas fa-chevron-left text-dark"></i></a>
    @else
        <a href="{{ route($rota) }}"><i class="fas fa-chevron-left text-dark"></i></a>
    @endif
@endif
{{ $titulo }}
