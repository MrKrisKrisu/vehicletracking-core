@extends('layout.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4 box-shadow">
                <div class="card-body">
                    <form method="post" accept-charset="utf-8" id="main" action="{{route('scans.assign')}}">@csrf</form>

                    <input type="text" class="form-control" id="name" placeholder="FzgNr." name="vehicle_name"
                           form="main" @if(session()->has('query')) value="{{session()->get('query')}}" @endif/>
                    <hr/>

                    <button type="button" onclick="$('.scan-check').prop('checked', true)"
                            class="btn btn-secondary btn-sm float-end">
                        <i class="far fa-check-square"></i>
                    </button>
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
                                                        <small>{{$p}}</small><br/>
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
                                                <small><i class="fas fa-wifi"></i> {{$scan->scanDevice->name}}
                                                </small><br/>
                                            @endisset

                                            <div class="btn-group">
                                                <button class="btn btn-sm btn-primary hideScan" data-id="{{$scan->id}}">
                                                    <i class="fas fa-eye-slash"></i>
                                                </button>

                                                @if(auth()->user()->id === 1)
                                                    <button class="btn btn-sm btn-secondary hideDevice"
                                                            onclick="hideDevice('{{$scan->device->id}}')">
                                                        <i class="fas fa-ban"></i> <i class="fas fa-code"></i>
                                                    </button>
                                                    @if(strlen(str_replace("\\x00", "", $scan->ssid)) > 0)
                                                        <button class="btn btn-sm btn-danger hideNetwork"
                                                                onclick="hideNetwork('{{str_replace("'","\\'",$scan->ssid)}}')">
                                                            <i class="fas fa-ban"></i> <i class="fas fa-tag"></i>
                                                        </button>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                        <td style="min-width: 50%;">
                                            <div class="form-check">
                                                <input class="form-check-input scan-check" type="checkbox"
                                                       name="scans[{{$scan->id}}]"
                                                       form="main">
                                                <label class="form-check-label"><small>{{$scan->vehicle_name}}</small></label>
                                            </div>
                                            <span>
                                        {{$scan->created_at->diffForHumans()}}
                                        <small>({{$scan->created_at->format('H:i:s')}})</small>
                                    </span><br/>
                                            @isset($scan->latitude)
                                                <small class="text-info">
                                                    <i class="fas fa-location-arrow"></i>
                                                    {{$scan->latitude}}, {{$scan->longitude}}
                                                </small>
                                            @else
                                                <small class="text-danger"><i class="fas fa-times"></i> kein
                                                    Standort</small>
                                            @endisset
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                        {{$lastScan->links()}}

                        @if(isset(request()->device))
                            <hr/>
                            <form method="POST" action="{{route('hide-all')}}">
                                @csrf
                                <input type="hidden" name="scanDeviceId" value="{{request()->device}}"/>
                                <button type="submit" class="btn btn-sm btn-secondary">Alle verstecken</button>
                                <button type="submit" class="btn btn-sm btn-secondary" name="onlyWithName" value="1">
                                    Gefundene verstecken
                                </button>
                            </form>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        $('.hideScan').on('click', hideScan);

        function hideScan() {
            let hideButton = $(this);
            let scanId = hideButton.data('id');
            $.ajax({
                url: '{{route('scans.update')}}',
                type: "POST",
                data: {
                    id: scanId,
                    hidden: 1
                },
                success: function () {
                    $('#scan' + scanId).remove();
                }
            });
        }

        function hideNetwork(ssid, contains = 0, id = 0) {
            $('*[data-ssid="' + ssid + '"] .hideNetwork').prop("disabled", true);

            $.ajax({
                url: '{{route('ignoredNetwork.create')}}',
                type: "POST",
                data: {
                    ssid: ssid,
                    contains: contains
                },
                success: function (data) {
                    console.log(data);
                    $('*[data-ssid="' + ssid + '"]').remove();
                }
            });
        }

        function hideDevice(id) {
            $('*[data-deviceid="' + id + '"] .hideDevice').prop("disabled", true);

            $.ajax({
                url: '{{route('old.device.update')}}',
                type: "POST",
                data: {
                    id: id,
                    ignore: 1
                },
                success: function (data) {
                    console.log(data);
                    $('*[data-deviceid="' + id + '"]').remove();
                }
            });
        }
    </script>
@endsection
