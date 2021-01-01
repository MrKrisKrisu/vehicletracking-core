@extends('layout.app')

@section('title') Übersicht der unterstützten Verkehrsunternehmen @endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h2>Verkehrsunternehmen</h2>
                    <table class="table">
                        <tbody>
                            @foreach($companies as $company)
                                <tr>
                                    <td>{{$company->name}}</td>
                                    <td>{{$company->vehicles->count()}} Fahrzeuge</td>
                                    <td>
                                        <a href="{{route('company', ['id' => $company->id])}}">Anzeigen</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
