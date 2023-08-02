@if(!empty($mensagem))
    {{-- <div class="alert alert-success alert-dismissible fade show" role="alert">
        <span id="mensagem">{{ $mensagem }}</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div> --}}
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="liveToast" class="toast align-items-center hide bg-success text-white" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div id="mensagem" class="toast-body">
                    {{ $mensagem }}
                </div>
                <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function(){
            $('#liveToast').toast('show');
        });
    </script>
@else
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="liveToast" class="toast align-items-center hide bg-success text-white" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div id="mensagem" class="toast-body">
                    Hello, world! This is a toast message.
                </div>
                <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
@endif
