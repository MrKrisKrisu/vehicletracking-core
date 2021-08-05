@extends('layout.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4 box-shadow">
                <div class="card-body">
                    <h5 class="card-title">Passwort ändern</h5>
                    <form method="POST" action="{{route('user.settings.password')}}">
                        @csrf
                        <div class="form-group row">
                            <label for="password"
                                   class="col-md-4 col-form-label text-md-right">Aktuelles Passwort</label>

                            <div class="col-md-8">
                                <input id="password" type="password" class="form-control" name="current_password"
                                       autocomplete="current-password" required/>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password"
                                   class="col-md-4 col-form-label text-md-right">Neues Passwort</label>

                            <div class="col-md-8">
                                <input id="new_password" type="password" class="form-control" name="new_password"
                                       autocomplete="current-password" required/>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password"
                                   class="col-md-4 col-form-label text-md-right">Neues Passwort wiederholen</label>

                            <div class="col-md-8">
                                <input id="new_confirm_password" type="password" class="form-control"
                                       name="new_confirm_password" autocomplete="current-password" required/>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Speichern</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
