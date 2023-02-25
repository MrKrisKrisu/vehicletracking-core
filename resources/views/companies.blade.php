@extends('layout.app')

@section('title', 'Übersicht der unterstützten Verkehrsunternehmen')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h1 class="fs-5">Verkehrsunternehmen</h1>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
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
    </div>
@endsection
