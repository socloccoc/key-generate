@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Key Generate</div>
                    <div class="card-body">
                        @include('errors.errorlist')
                        <form method="POST" action="{{ route('key.store') }}">
                            @csrf
                            <div class="form-group">
                                <label for="app">App</label>
                                <select id="app" name="app" class="form-control">
                                    @forelse($apps as $app)
                                        <option value="{{ $app['id'] }}">{{ $app['name'] }}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="exampleInputEmail1">The number of</label>
                                <input type="number" class="form-control" id="number" name="number" placeholder="10">
                            </div>

                            <div class="form-group">
                                <label for="Expire time">Expire time</label>
                                <input type="number" class="form-control" id="expire_time" name="expire_time" placeholder="10">
                            </div>

                            <div class="form-group">
                                <label for="Point">Point</label>
                                <input type="number" class="form-control" id="point" name="point" placeholder="0">
                            </div>

                            <div class="col-md-12 text-right" style="padding: 0px">
                                <button type="submit" class="btn btn-primary">Key generate</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
