@extends('layout.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h2>Benachrichtigungen</h2>
                    <table class="table">
                        <tbody>
                            @foreach($scanDevices as $scanDevice)
                                <tr>
                                    <td>{{$scanDevice->name}}</td>
                                    <td>
                                        @if($scanDevice->notify == 1)
                                            <small class="text-success">aktiviert</small>
                                        @elseif($scanDevice->notify == 0)
                                            <small class="text-danger">deaktiviert</small>
                                        @endif
                                    </td>
                                    <td>
                                        <form method="POST">
                                            @csrf
                                            <input type="hidden" name="id" value="{{$scanDevice->id}}"/>
                                            <button class="btn btn-sm btn-primary">
                                                @if($scanDevice->notify == 0)
                                                    <i class="fas fa-toggle-off"></i>
                                                @elseif($scanDevice->notify == 1)
                                                    <i class="fas fa-toggle-on"></i>
                                                @endif
                                            </button>
                                        </form>
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
