<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap.ndtic.css') }}" rel="stylesheet">
    <link href="{{ asset('css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/ndtic.css') }}" rel="stylesheet">
    <link href="{{ asset('css/multi-select.css') }}" rel="stylesheet">
    <link href="{{ asset('css/tempus-dominus.css') }}" rel="stylesheet">
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/jquery.mask.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/jquery.quicksearch.js') }}"></script>
    <script src="{{ asset('js/jquery.multi-select.js') }}"></script>
    <script src="{{ asset('js/xlsx.core.min.js') }}"></script>
    <script src="{{ asset('js/FileSaver.js') }}"></script>
    <script src="{{ asset('js/tableexport.js') }}"></script>
    <script src="{{ asset('js/tools.js') }}"></script>
    <script src="{{ asset('js/popper.min.js') }}"></script>
    <script src="{{ asset('js/tempus-dominus.js') }}"></script>
    <title>{{ config('app.name', 'NDTIC WEB App') }}</title>
</head>
<body>
    <header>
    <nav  class="navbar navbar-expand-lg navbar-dark bg-secondary mb-5 fixed-top">
        <div class="container-fluid" data-bs-theme="light">
            <a class="ndtic-brand navbar-brand mb-0" href="{{ route('home') }}">
                <!--<img src="{{ asset('img/horizontal_branco.png') }}" width="180px" height="auto">-->
                {{ config('app.name', 'NDTIC WEB App') }} <!-- Caso tenha uma imagem de logo, descomentar a linha acima e remover esta aqui -->
            </a>
            <div class="collapse navbar-collapse" id="navbarText">
            </div>
            <div class="d-flex">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item navbar-text fs-5 me-3">
                        @auth
                        Olá, {{ Auth::user()->name }}
                        @endauth

                        @guest
                        Entrar
                        @endguest
                    </li>
                    @auth
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('trocasenha') }}" title="Alterar Senha"><i class="fas fa-user-shield fa-2x"></i></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('sair') }}" title="Sair do Sistema"><i class="fas fa-sign-out-alt fa-2x"></i></a>
                    </li>
                    @endauth
                    @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('entrar') }}" title="Entrar do Sistema"><i class="fas fa-sign-in-alt fa-2x" style="color: white;"></i></a>
                    </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>
    </header>

    <div id="spinner-div" class="pt-5">
        <div class="spinner-border text-success align-middle" role="status">
        </div>
    </div>

    <div class="d-flex flex-column" style="min-height: 100vh;">
        <div class="container mt-5 pt-5 pb-5" style="flex: 1 0 auto;">
            <h2 class="mb-4">@yield('cabecalho')</h2>
            @yield('conteudo')
        </div>

        <footer class="footer d-flex flex-shrink-0 justify-content-center align-items-center bg-dark pt-2 pb-3">
            <div>Desenvolvido pela NDTIC - SVMA</div>
            <div class="ms-3"><img src="{{ asset('img/Logo64_original.png') }}" height="24px" width="24px" class="ml-3"/></div>
        </footer>
    </div>
</body>
</html>
