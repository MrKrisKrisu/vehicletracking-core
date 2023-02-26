@extends('layout.app')

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-2">
                <div class="card-body">
                    <h2 class="fs-5">Airport Utility Export importieren</h2>

                    <form method="POST" action="{{route('import.airport')}}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-floating mb-2">
                            <input type="date" name="date" value="{{\Carbon\Carbon::today()->toDateString()}}"
                                   required class="form-control"/>
                            <label>Datum</label>
                        </div>
                        <div class="form-floating mb-2">
                            <input type="text" name="vehicle_name" placeholder="9101, 427 041, etc."
                                   required class="form-control"/>
                            <label>Was? <small>mehrere Fahrzeuge durch Komma trennen</small></label>
                        </div>
                        <div class="form-floating mb-2">
                            <input type="text" name="location" placeholder="Hannover Hbf, Haltenhoffstraße, etc."
                                   required class="form-control"/>
                            <label>Aufnahmeort (Text)</label>
                        </div>

                        <div class="row mb-2">
                            <div class="col">
                                <div class="form-floating">
                                    <input class="form-control" type="text" name="latitude" placeholder="Latitude"/>
                                    <label>Latitude</label>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-floating">
                                    <input class="form-control" type="text" name="longitude" placeholder="Longitude"/>
                                    <label>Longitude</label>
                                </div>
                            </div>
                        </div>

                        <input name="file" type="file" class="form-control mb-2">

                        <button class="btn btn-primary" type="submit">Importieren</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-2">
                <div class="card-body">
                    <h2 class="fs-5">GPX importieren</h2>

                    <form method="POST" action="{{route('location.import')}}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-floating mb-2">
                            <select class="form-control" name="device_id">
                                <option value="">bitte wählen</option>
                                @foreach(auth()->user()->scanDevices as $scanDevice)
                                    <option value="{{$scanDevice->id}}">{{$scanDevice->name}}</option>
                                @endforeach
                            </select>
                            <label>Scanner</label>
                        </div>

                        <input name="file" type="file" class="form-control mb-2">

                        <button class="btn btn-primary" type="submit">Importieren</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
