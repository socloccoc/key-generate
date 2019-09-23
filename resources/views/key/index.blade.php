@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            {{--<div class="row justify-content-center">--}}
            <div class="col-md-12">
                @include('errors.errorlist')
                <form method="GET" action="{{ route('key.index') }}">
                    <div class="col-sm-12 form-row" style="border: 1px solid #cccccc; padding: 15px; margin-bottom: 20px; border-radius: 10px;">
                        <div class="form-group col-sm-3" style="padding: 0px">
                            <label for="app">Status</label>
                            <select id="app" name="status" class="form-control form-inline">
                                <option {{ isset($status) && $status == 0 ? 'selected' : '' }} value="0">Chưa được dùng</option>
                                <option {{ isset($status) && $status == 1 ? 'selected' : '' }} value="1">Đã được dùng</option>
                                <option {{ isset($status) && $status == 2 ? 'selected' : '' }} value="2">Còn hạn</option>
                                <option {{ isset($status) && $status == 3 ? 'selected' : '' }} value="3">Hết hạn</option>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <button style="margin-top: 32px" type="submit" class="btn btn-primary">Tìm kiếm</button>
                        </div>
                    </div>
                </form>
                <table id="example" class="table table-striped table-bordered nowrap" style="width:100%">
                    <thead>
                    <tr>
                        <th>App</th>
                        <th>Key</th>
                        <th>Serial</th>
                        <th>Expire Time</th>
                        <th>Expire Date</th>
                        <th>Point</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($keys as $key)
                        <tr>
                            <td>{{ isset($key['app']['name']) ? $key['app']['name'] : '' }}</td>
                            <td>{{ isset($key['key']) ? $key['key'] : '' }}</td>
                            <td>{{ isset($key['serial_number']) ? $key['serial_number'] : '' }}</td>
                            <td>{{ isset($key['expire_time']) ? $key['expire_time'] : '' }}</td>
                            <td>{{ isset($key['expire_date']) ? $key['expire_date'] : '' }}</td>
                            <td>{{ isset($key['point']) ? $key['point'] : '' }}</td>
                            <td>
                                @if($key['expire_date'] != '')
                                    <a href="#" class="btn btn-info btn-lg loadModal"
                                       key_id="{{ isset($key['id']) ? $key['id'] : '' }}" data-toggle="modal"
                                       data-target="#updateExpireTimeModal">
                                        <span class="glyphicon glyphicon-edit"></span> Edit
                                    </a>
                                @endif
                                @if($key['expire_date'] >= \Carbon\Carbon::now()->format('Y-m-d H:i:s'))
                                    <a onclick="return confirm('Bạn có chắc chắn muốn xóa không ?')"
                                       class="btn btn-danger btn-lg"
                                       href="{{ route('key.deleteKey', ['id' => $key['id']]) }}">
                                        <span class="glyphicon glyphicon-trash"></span> Remove
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                    @endforelse
                    </tbody>
                    <tfoot>
                    <tr>
                        <th>App</th>
                        <th>Key</th>
                        <th>Serial</th>
                        <th>Expire Time</th>
                        <th>Expire Date</th>
                        <th>Point</th>
                        <th>Action</th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="updateExpireTimeModal" tabindex="-1" role="dialog"
         aria-labelledby="updateExpireTimeModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Update Expire Date</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="{{ route('key.updateExpireDate') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="Expire time">Expire Date</label>
                            <input type="text" class="form-control" id="expire_date" name="expire_date">
                            <input type="number" class="form-control modal-key-id" hidden name="modal_key_id">
                        </div>
                        <div class="form-group">
                            <label for="serial_number">Serial</label>
                            <input type="text" class="form-control" id="serial_number" name="serial_number">
                        </div>
                        <div class="form-group">
                            <label for="Point">Point</label>
                            <input type="number" class="form-control" id="point" name="point">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('javascript')
    <script>
        $(document).ready(function () {
            var table = $('#example').DataTable({
                responsive: true
            });
            new $.fn.dataTable.FixedHeader(table);

            $(document).ready(function () {
                $('.loadModal').each(function (index, elem) {
                    $(elem).unbind().click(function (e) {
                        e.preventDefault();
                        // ajax
                        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                        var key_id = $(this).attr('key_id');
                        $.ajax({
                            url: '/ajax/getKeyInfo',
                            type: 'POST',
                            data: {_token: CSRF_TOKEN, keyId: key_id},
                            dataType: 'JSON',
                            success: function (data) {
                                if (data !== null) {
                                    $('#expire_date').val(data.expire_date);
                                    $('#point').val(data.point);
                                    $('#serial_number').val(data.serial_number);
                                    $('.modal-key-id').val(data.id);
                                }
                            }
                        });
                    });
                });
            });
        });
    </script>
@endsection