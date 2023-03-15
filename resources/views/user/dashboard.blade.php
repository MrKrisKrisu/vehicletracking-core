@extends('layout.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4 box-shadow">
                <div class="card-body">
                    <h1 class="fs-5">Meine letzten Scans</h1>
                    @foreach(\App\ScanDevice::where('user_id', auth()->user()->id)->get() as $device)
                        <a href="?device={{$device->id}}" class="btn btn-sm btn-primary">{{$device->name}}</a>
                    @endforeach
                    <hr/>
                    <form method="POST" action="{{route('save-to-session')}}">
                        @csrf
                        <button class="btn btn-sm {{session()->get('show-verified') == '1' ? 'btn-success' : 'btn-danger'}}"
                                name="show-verified" value="{{session()->get('show-verified') == '1' ? '0' : '1'}}">
                            verifizierte
                        </button>
                        <button class="btn btn-sm {{session()->get('show-hidden') == '1' ? 'btn-success' : 'btn-danger'}}"
                                name="show-hidden" value="{{session()->get('show-hidden') == '1' ? '0' : '1'}}">
                            versteckte
                        </button>
                        <button class="btn btn-sm {{session()->get('show-ignored') == '1' ? 'btn-success' : 'btn-danger'}}"
                                name="show-ignored" value="{{session()->get('show-ignored') == '1' ? '0' : '1'}}">
                            ignorierte
                        </button>
                    </form>
                    <hr/>

                    @if($lastScan->count() === 0)
                        <p class="fw-bold text-success">
                            <i class="fas fa-check"></i> Alles abgearbeitet.
                        </p>
                    @else
                        <div class="table-responsive">
                            <table class="table">
                                @foreach ($lastScan as $scan)
                                    <tr id="scan{{$scan->id}}" data-ssid="{{$scan->ssid}}"
                                        data-deviceid="{{$scan->device->id}}">
                                        <td>
                                            {{stripcslashes($scan->ssid)}}<br/>
                                            @isset($possibleVehicles[$scan->bssid])
                                                @if(!isset($scan->device) || !isset($scan->device->vehicle))
                                                    @foreach($possibleVehicles[$scan->bssid] as $p)
                                                        <small><i>- {{$p}}</i></small><br/>
                                                    @endforeach
                                                @endif
                                            @endisset

                                            @if(isset($scan->device) && isset($scan->device->vehicle))
                                                <small class="text-success">Verifiziert:
                                                    {{$scan->device->vehicle->vehicle_name}},
                                                    {{$scan->device->vehicle->company->name}}</small><br/>
                                            @else
                                                <small class="text-danger">Unverifiziert</small><br/>
                                            @endif

                                            @isset($scan->scanDevice)
                                                <small>
                                                    <i class="fas fa-wifi"></i> {{$scan->scanDevice->name}}
                                                </small><br/>
                                            @endisset
                                        </td>
                                        <td style="min-width: 50%;">
                                            {{$scan->created_at->diffForHumans()}}
                                            <small>({{$scan->created_at->format('H:i:s')}})</small>
                                            <br/>
                                            @isset($scan->latitude)
                                                <small class="text-info">
                                                    <i class="fas fa-location-arrow"></i>
                                                    {{$scan->latitude}}, {{$scan->longitude}}
                                                </small>
                                            @else
                                                <small class="text-danger">
                                                    <i class="fas fa-times"></i>
                                                    kein Standort
                                                </small>
                                            @endisset
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                        {{$lastScan->links()}}
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
