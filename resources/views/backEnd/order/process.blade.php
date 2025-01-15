@extends('backEnd.layouts.master')
@section('title', 'Order Process')
@section('css')
    <style>
        .increment_btn,
        .remove_btn {
            margin-top: -17px;
            margin-bottom: 10px;
        }
    </style>
    <link href="{{ asset('public/backEnd') }}/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('public/backEnd') }}/assets/libs/summernote/summernote-lite.min.css" rel="stylesheet"
        type="text/css" />
@endsection
@section('content')
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title">Order Process [Invoice : #{{ $data->invoice_id }}]</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card">
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Image</th>
                                    <th>Product</th>
                                    <th>Qty</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data->orderdetails as $key => $product)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td><img src="{{ asset($product->image ? $product->image->image : '') }}"
                                                height="50" width="50" alt=""></td>
                                        <td>{{ $product->product_name }} <br>
                                            <div>
                                                @foreach ($product->product->stocks as $index => $stock)
                                                    
                                                    <span class="btn btn-info">

                                                        {{ $stock->warehouse->name }}
                                                        ({{ $stock->stock }})
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td>{{ $product->qty }}</td>

                                        <td>
                                            <form action="{{ route('admin.order.item_return') }}" method="POST" class=row
                                                data-parsley-validate="" name="editForm" enctype="multipart/form-data">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $product->id }}">
                                                <button
                                                    class="btn btn-danger rounded-pill waves-effect waves-light change-confirm extra-btn1"
                                                    {{ $product->is_replace == 1 ? 'disabled' : '' }}> <i
                                                        class="fe-rotate-ccw"></i> Return</button>
                                            </form>

                                            <form action="{{ route('admin.order.item_replace') }}" method="POST" class=row
                                                data-parsley-validate="" name="editForm" enctype="multipart/form-data">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $product->id }}">
                                                <button
                                                    class="btn btn-warning rounded-pill waves-effect waves-light change-confirm extra-btn1"
                                                    {{ $product->is_replace == 1 ? 'disabled' : '' }}> <i
                                                        class="fe-refresh-cw"></i> Replace</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.order_change') }}" method="POST" class=row data-parsley-validate=""
                            name="editForm" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" value="{{ $data->id }}">

                            <div class="col-sm-6">
                                <div class="form-group mb-3">
                                    <label for="name" class="form-label">Customer name </label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        name="name" id="name"
                                        value="{{ $data->shipping ? $data->shipping->name : '' }}" placeholder="Name">
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group mb-3">
                                    <label for="phone" class="form-label">Customer Phone </label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                        name="phone" id="phone"
                                        value="{{ $data->shipping ? $data->shipping->phone : '' }}"
                                        placeholder="Phone Number">
                                    @error('phone')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group mb-3">
                                    <label for="address" class="form-label">Customer Address </label>
                                    <textarea name="address" class="form-control @error('address') is-invalid @enderror">{{ $data->shipping ? $data->shipping->address : '' }}</textarea>
                                    @error('address')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group mb-3">
                                    <label for="area">Delivery Area *</label>
                                    <select type="area" id="area"
                                        class="form-control form-select @error('area') is-invalid @enderror" name="area"
                                        required>
                                        @foreach ($shippingcharge as $key => $value)
                                            <option @if ($data->shipping->area ?? 0 == $value->name) selected @endif
                                                value="{{ $value->id }}">{{ $value->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group mb-3">
                                    <label for="status" class="form-label">Order Status</label>
                                    <select class="form-control select2-multiple @error('status') is-invalid @enderror"
                                        value="{{ old('status') }}" name="status" data-toggle="select2"
                                        data-placeholder="Choose ..." required>
                                        <optgroup>
                                            <option value="">Select..</option>
                                            @foreach ($orderstatus as $value)
                                                <option value="{{ $value->id }}"
                                                    @if ($data->order_status == $value->id) selected @endif>{{ $value->name }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    </select>
                                    @error('status')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group mb-3">
                                        <label for="warehouse_id" class="form-label">Warehouse</label>
                                        <select class="form-control select2-multiple @error('status') is-invalid @enderror"
                                            value="{{ old('status') }}" name="warehouse_id" data-toggle="select2"
                                            data-placeholder="Choose ..." required>
                                            <optgroup>
                                                <option value="">Select..</option>
                                                @foreach ($warehouses as $value)
                                                    <option value="{{ $value->id }}">{{ $value->name }}</option>
                                                @endforeach
                                            </optgroup>
                                        </select>
                                        @error('status')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <!-- col end -->

                                <!-- col end -->
                                <div>
                                    <input type="submit" class="btn btn-success" value="Submit">
                                </div>

                        </form>

                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div> <!-- end col-->
        </div>
    </div>
@endsection


@section('script')
    <script src="{{ asset('public/backEnd/') }}/assets/libs/parsleyjs/parsley.min.js"></script>
    <script src="{{ asset('public/backEnd/') }}/assets/js/pages/form-validation.init.js"></script>
    <script src="{{ asset('public/backEnd/') }}/assets/libs/select2/js/select2.min.js"></script>
    <script src="{{ asset('public/backEnd/') }}/assets/js/pages/form-advanced.init.js"></script>
    <!-- Plugins js -->
    <script src="{{ asset('public/backEnd/') }}/assets/libs//summernote/summernote-lite.min.js"></script>
    <script>
        $(".summernote").summernote({
            placeholder: "Enter Your Text Here",

        });
    </script>
@endsection
