@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">App</div>
                    <div class="card-body">
                        @include('errors.errorlist')
                        <form method="POST" action="{{ route('app.store') }}">
                            @csrf
                            <div class="form-group">
                                <label for="exampleInputEmail1">App name</label>
                                <input type="name" class="form-control" id="name" name="name" placeholder="App name">
                            </div>
                            <div class="col-md-12 text-right" style="padding: 0px">
                                <button type="submit" class="btn btn-primary">Add</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
