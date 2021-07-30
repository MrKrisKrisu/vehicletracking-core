@extends('layout.app')

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h3>GPX importieren</h3>
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
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h3>Airport Utility Export importieren</h3>
                    <form method="POST" action="{{route('import.airport')}}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label>Datum</label>
                            <input type="date" name="date" value="{{\Carbon\Carbon::today()->toDateString()}}"
                                   required class="form-control"/>
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
