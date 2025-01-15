@extends('backEnd.layouts.master')
@section('title', 'Purchase Create')
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
                    <div class="page-title-right">
                        <form method="post" action="{{ route('admin.order.cart_clear') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-danger rounded-pill delete-confirm" title="Delete"><i
                                    class="fas fa-trash-alt"></i> Cart Clear</button>
                        </form>
                    </div>
                    <h4 class="page-title">Purchase Create</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('purchase.update') }}" method="POST" class="row pos_form"
                            data-parsley-validate="" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" value="{{ $purchase->id }}">
                            <div class="col-sm-12">
                                <div class="form-group mb-3">
                                    <label for="product_id" class="form-label">Products *</label>
                                    <select id="purchase_add"
                                        class="form-control select2 @error('product_id') is-invalid @enderror"
                                        value="{{ old('product_id') }}">
                                        <option value="">Select..</option>
                                        @foreach ($products as $value)
                                            <option value="{{ $value->id }}">{{ $value->name }} -
                                                {{ $value->retail_price }} , {{ $value->whole_price }}</option>
                                        @endforeach
                                    </select>
                                    @error('product_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <!-- col end -->
                            <div class="col-sm-12">
                                <table class="table table-bordered table-responsive-sm">
                                    <thead>
                                        <tr>
                                        <tr>
                                            <th style="width:10%">Image</th>
                                            <th style="width:25%">Name</th>
                                            <th style="width:15%">Quantity</th>
                                            <th style="width:15%">Sell Price</th>
                                            <th style="width:15%">Discount</th>
                                            <th style="width:15%">Sub Total</th>
                                            <th style="width:15%">Action</th>
                                        </tr>
                                        </tr>
                                    </thead>
                                    <tbody id="cartTable">
                                        @php
                                            $product_discount = 0;
                                        @endphp
                                        @foreach ($cartinfo as $key => $value)
                                            <tr>
                                                <td><img height="30" src="{{ asset($value->options->image) }}"></td>
                                                <td>{{ $value->name }}</td>
                                                <td>
                                                    <div class="qty-cart vcart-qty">
                                                        <div class="quantity">
                                                            <button class="minus cart_decrement"
                                                                value="{{ $value->qty }}"
                                                                data-id="{{ $value->rowId }}">-</button>
                                                            <input type="text" value="{{ $value->qty }}" readonly />
                                                            <button class="plus cart_increment" value="{{ $value->qty }}"
                                                                data-id="{{ $value->rowId }}">+</button>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ $value->price }} Tk</td>
                                                <td class="discount"><input type="number" class="product_discount"
                                                        value="{{ $value->options->product_discount }}" placeholder="0.00"
                                                        data-id="{{ $value->rowId }}">
                                                </td>
                                                <td>{{ ($value->price - $value->options->product_discount) * $value->qty }}
                                                    Tk</td>
                                                <td><button type="button" class="btn btn-danger btn-xs cart_remove"
                                                        data-id="{{ $value->rowId }}"><i class="fa fa-times"></i></button>
                                                </td>
                                            </tr>
                                            @php
                                                $product_discount += $value->options->product_discount * $value->qty;
                                                Session::put('product_discount', $product_discount);
                                            @endphp
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <!-- custome address -->
                            @php
                                $subtotal = Cart::instance('purchase')->subtotal();
                                $subtotal = str_replace(',', '', $subtotal);
                                $subtotal = str_replace('.00', '', $subtotal);
                                $shipping = Session::get('purchase') ? Session::get('purchase') : 0;
                                $paid = Session::get('paid') ? Session::get('paid') : 0;
                                $discount = Session::get('purchase_discount') + Session::get('product_discount');
                            @endphp
                            <div class="col-sm-6">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group mb-2">
                                            <select type="text" id="category_id"
                                                class="select2 form-control @error('category_id') is-invalid @enderror"
                                                name="category_id" value="{{ old('category_id') }}" required>
                                                <option value="">Select Category..</option>
                                                @foreach ($pur_categories as $category)
                                                    <option value="{{ $category->id }}" {{$purchase->category_id == $category->id ? 'selected' : ''}}>{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('category_id')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <!-- col-end -->
                                    <div class="col-sm-12">
                                        <div class="form-group mb-2">
                                            <select type="text" id="supplier_id"
                                                class="select2 form-control @error('supplier_id') is-invalid @enderror"
                                                name="supplier_id" value="{{ old('supplier') }}" required>
                                                <option value="">Select..</option>
                                                @foreach ($suppliers as $supplier)
                                                    <option value="{{ $supplier->id }}"
                                                        @if ($supplier->id == $purchase->supplier_id) selected @endif>
                                                        {{ $supplier->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('supplier_id')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <!-- col-end -->
                                    <div class="col-sm-12">
                                        <div class="form-group mb-2">
                                            <select type="text" id="warehouse_id"
                                                class="select2 form-control @error('warehouse_id') is-invalid @enderror"
                                                name="warehouse_id" value="{{ old('warehouse_id') }}" required>
                                                <option value="">Select Warehouse..</option>
                                                @foreach ($warehouses as $warehouse)
                                                    <option value="{{ $warehouse->id }}" {{$warehouse->id == $purchase->warehouse_id ? 'selected' : ''}}>{{ $warehouse->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('warehouse_id')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <!-- col-end -->
                                    <div class="col-sm-12">
                                        <div class="form-group mb-2">
                                            <input type="number" id="paid"
                                                class="form-control @error('paid') is-invalid @enderror"
                                                placeholder="Cash" name="paid" value="{{ $paid }}" required>
                                            @error('paid')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <!-- col-end -->
                                </div>
                            </div>
                            <!-- cart total -->
                            <div class="col-sm-6">
                                <table class="table table-bordered">
                                    <tbody id="cart_details">
                                        <tr>
                                            <td>Sub Total</td>
                                            <td>{{ $subtotal }} Tk</td>
                                        </tr>
                                        <tr>
                                            <td>Discount</td>
                                            <td>{{ $discount }} Tk</td>
                                        </tr>
                                        <tr>
                                            <td>Total</td>
                                            <td>{{ $subtotal + $shipping - $discount }} Tk</td>
                                        </tr>
                                        <tr>
                                            <td>Paid</td>
                                            <td>{{ $paid }} Tk</td>
                                        </tr>
                                        <tr>
                                            <td>Due Payment</td>
                                            <td>{{ $subtotal + $shipping - $discount - $paid }} Tk</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div>
                                <input type="submit" class="btn btn-success" value="Purchase Update" />
                            </div>
                        </form>
                    </div>
                    <!-- end card-body-->
                </div>
                <!-- end card-->
            </div>
            <!-- end col-->
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

    <script type="text/javascript">
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>
    <script>
        function cart_content() {
            $.ajax({
                type: "GET",
                url: "{{ route('purchase.cart_content') }}",
                dataType: "html",
                success: function(cartinfo) {
                    $('#cartTable').html(cartinfo)
                }
            });
        }

        function cart_details() {
            $.ajax({
                type: "GET",
                url: "{{ route('purchase.cart_details') }}",
                dataType: "html",
                success: function(cartinfo) {
                    $('#cart_details').html(cartinfo)
                }
            });
        }

        $('#purchase_add').on('change', function(e) {
            var id = $(this).val();
            if (id) {
                $.ajax({
                    cache: 'false',
                    type: "GET",
                    data: {
                        'id': id
                    },
                    url: "{{ route('purchase.add') }}",
                    dataType: "json",
                    success: function(cartinfo) {
                        return cart_content() + cart_details();
                    }
                });
            }
        });
        $(".cart_increment").click(function(e) {
            e.preventDefault();
            var id = $(this).data("id");
            var qty = $(this).val();
            if (id) {
                $.ajax({
                    cache: false,
                    data: {
                        'id': id,
                        'qty': qty
                    },
                    type: "GET",
                    url: "{{ route('purchase.cart_increment') }}",
                    dataType: "json",
                    success: function(cartinfo) {
                        return cart_content() + cart_details();
                    }
                });
            }
        });
        $(".cart_decrement").click(function(e) {
            e.preventDefault();
            var id = $(this).data("id");
            var qty = $(this).val();
            if (id) {
                $.ajax({
                    cache: false,
                    type: "GET",
                    data: {
                        'id': id,
                        'qty': qty
                    },
                    url: "{{ route('purchase.cart_decrement') }}",
                    dataType: "json",
                    success: function(cartinfo) {
                        return cart_content() + cart_details();
                    }
                });
            }
        });
        $(".cart_remove").click(function(e) {
            e.preventDefault();
            var id = $(this).data("id");
            if (id) {
                $.ajax({
                    cache: false,
                    type: "GET",
                    data: {
                        'id': id
                    },
                    url: "{{ route('purchase.cart_remove') }}",
                    dataType: "json",
                    success: function(cartinfo) {
                        return cart_content() + cart_details();
                    }
                });
            }
        });
        $(".product_discount").change(function() {
            var id = $(this).data("id");
            var discount = $(this).val();
            $.ajax({
                cache: false,
                type: "GET",
                data: {
                    'id': id,
                    'discount': discount
                },
                url: "{{ route('purchase.product_discount') }}",
                dataType: "json",
                success: function(cartinfo) {
                    return cart_content() + cart_details();
                }
            });
        });
        $("#paid").change(function() {
            var amount = $(this).val();
            $.ajax({
                cache: false,
                type: "GET",
                data: {
                    'amount': amount
                },
                url: "{{ route('purchase.paid') }}",
                dataType: "json",
                success: function(cartinfo) {
                    return cart_content() + cart_details();
                }
            });
        });
        $(".cartclear").click(function(e) {
            $.ajax({
                cache: false,
                type: "GET",
                url: "{{ route('purchase.cart_clear') }}",
                dataType: "json",
                success: function(cartinfo) {
                    return cart_content() + cart_details();
                }
            });
        }); // pshippingfee from total
        $("#area").on("change", function() {
            var id = $(this).val();
            $.ajax({
                type: "GET",
                data: {
                    id: id
                },
                url: "{{ route('purchase.cart_shipping') }}",
                dataType: "html",
                success: function(cartinfo) {
                    return cart_content() + cart_details();
                }
            });
        });
    </script>
@endsection
