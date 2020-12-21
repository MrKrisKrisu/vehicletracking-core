@if ($errors->any())
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-danger">
                <h2 class="text-alert">Es sind Fehler aufgetreten:</h2>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif
@foreach (['danger', 'warning', 'success', 'info'] as $msg)
    @if(Session::has('alert-' . $msg))
        <div class="row">
            <div class="col-md-12">
                <p class="alert alert-{{ $msg }}">
                    {!! Session::get('alert-' . $msg) !!}
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                </p>
                <hr/>
            </div>
        </div>
    @endif
    {{ Session::forget('alert-' . $msg) }}
@endforeach
