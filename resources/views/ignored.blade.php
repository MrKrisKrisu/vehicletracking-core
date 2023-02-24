@extends('layout.app')

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h2>Ignorierte Netzwerke</h2>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Netzwerk</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bssid as $network)
                                <tr>
                                    <td>{{str_replace("\\x00", "", $network->ssid)}} ({{$network->bssid}})</td>
                                    <td>
                                        <form method="POST" action="{{route('unban.bssid')}}">
                                            @csrf
                                            <input type="hidden" name="bssid" value="{{$network->bssid}}"/>
                                            <button type="submit" class="btn btn-sm btn-primary">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{$bssid->links()}}
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h2>Ignorierte SSIDs</h2>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Netzwerk</th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ssid as $network)
                                <tr>
                                    <td>{{str_replace("\\x00", "", $network->ssid)}}</td>
                                    <td>
                                        @if($network->contains)
                                            <span class="badge badge-sm bg-success">contains</span>
                                        @else
                                            <span class="badge badge-sm bg-primary">1:1</span>
                                        @endif
                                    </td>
                                    <td>
                                        <form method="POST" action="{{route('unban.ssid')}}">
                                            @csrf
                                            <input type="hidden" name="ssid" value="{{$network->ssid}}"/>
                                            <button type="submit" class="btn btn-sm btn-primary">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{$ssid->links()}}
                    <hr/>
                    <h3>Hinzufügen</h3>
                    <form method="POST" action="{{route('ignoreDevice.add')}}">
                        @csrf
                        <div>
                            <label>SSID(-Teil)</label>
                            <input type="text" name="ssid" class="form-control" required/>
                        </div>
                        <div>
                            <input type="checkbox" name="contains"/>
                            <label>Nur Teil?</label>
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary">
                            Speichern
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
