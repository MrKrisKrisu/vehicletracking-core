@extends('layout.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4 box-shadow">
                <div class="card-body">
                    <h5 class="card-title">Kürzliche Erfassungen auf Gerät</h5>
                    <div class="d-flex justify-content-between align-items-center">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Erfassungszeitpunkt</th>
                                <th>Erkanntes Fahrzeug</th>
                            </tr>
                            </thead>
                            @foreach ($scans as $scan)
                                <tr>
                                    <td>{{$scan->created_at}}</td>
                                    <td>
                                        @if($scan->verifiedName != NULL)
                                            <p style="color: darkgreen;">{{$scan->verifiedName}} <small>(bestätigt)</small></p>
                                        @elseif($scan->possibleVehicles != NULL)
                                            <p style="color: darkorange;">{{$scan->possibleVehicles}} <small>(sehr ungenau, irgendwas davon)</small></p>
                                        @else
                                            <p style="color: #E70000;"><small>Aktuell komplett unbekannt</small></p>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
