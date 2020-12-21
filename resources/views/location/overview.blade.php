@extends('layout.app')

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h2>GPX einlesen</h2>
                    <form method="POST" action="{{route('location.import')}}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label>Scanner</label>
                            <select class="form-control" name="device_id">
                                <option value="">bitte wählen</option>
                                @foreach(\App\ScanDevice::all() as $scanDevice)
                                    <option value="{{$scanDevice->id}}">{{$scanDevice->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <input name="file" type="file" class="form-control">
                        </div>
                        <button class="btn btn-primary" type="submit">Importieren</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
