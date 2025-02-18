@extends('backEnd.layouts.master')
@section('title', 'Order Edit')
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
    <link href="{{ asset('public/backEnd') }}/assets/libs/flatpickr/flatpickr.min.css" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    @php
        $subtotal = \Gloudemans\Shoppingcart\Facades\Cart::instance('pos_shopping')->subtotal();
        $subtotal = str_replace(',', '', $subtotal);
        $subtotal = str_replace('.00', '', $subtotal);
        $shipping = Session::get('pos_shipping');
        $old_due = Session::get('old_due') ?? 0;
        $paid = Session::get('cpaid') ?? 0;
        $additional_shipping = Session::get('additional_shipping') ?? 0;
        $total_discount = Session::get('pos_discount') + Session::get('product_discount');
    @endphp
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
                    <h4 class="page-title">Order Edit</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group mb-3">
                                    <label for="category_id" class="form-label">Categories *</label>
                                    <select
                                        class="form-control form-select select2 @error('category_id') is-invalid @enderror"
                                        name="category_id" value="{{ old('category_id') }}" id="category_id" required>
                                        <option value="">Select..</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <!-- col end -->
                            <div class="col-sm-3">
                                <div class="form-group mb-3">
                                    <label for="subcategory_id" class="form-label">SubCategories (Optional)</label>
                                    <select class="form-control select2 @error('subcategory_id') is-invalid @enderror"
                                        id="subcategory_id" name="subcategory_id" data-placeholder="Choose ...">
                                        <optgroup>
                                            <option value="">Select..</option>
                                        </optgroup>
                                    </select>
                                    @error('subcategory_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <!-- col end -->
                            <div class="col-sm-3">
                                <div class="form-group mb-3">
                                    <label for="childcategory_id" class="form-label">Child Categories (Optional)</label>
                                    <select class="form-control select2 @error('childcategory_id') is-invalid @enderror"
                                        id="childcategory_id" name="childcategory_id" data-placeholder="Choose ...">
                                        <optgroup>
                                            <option value="">Select..</option>
                                        </optgroup>
                                    </select>
                                    @error('childcategory_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <!-- col end -->
                            <div class="col-sm-3 ">
                                <div class="form-group mb-3">
                                    <label for="product_id" class="form-label">Products *</label>
                                    <select class="form-control select2 @error('product_id') is-invalid @enderror"
                                        id="product_id" name="product_id" data-placeholder="Choose ...">
                                        <optgroup>
                                            <option value="">Select..</option>
                                        </optgroup>
                                    </select>

                                    @error('product_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <!-- col end -->
                        <form action="{{ route('admin.order.update') }}" method="POST" class="row pos_form"
                            data-parsley-validate="" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" value="{{ $order->id }}" name="order_id">
                            <div class="col-sm-12">
                                <table class="table table-bordered table-responsive-sm">
                                    <thead>
                                        <tr>
                                            <th style="width:10%">Image</th>
                                            <th style="width:25%">Name</th>
                                            <th style="width:15%">Quantity</th>
                                            <th style="width:15%">Sell Price</th>
                                            <th style="width:15%">Discount</th>
                                            <th style="width:15%">Sub Total</th>
                                            <th style="width:15%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="cartTable">
                                        @include('backEnd.order.cart_content')
                                    </tbody>
                                </table>
                            </div>
                            <!-- custome address -->
                            <div class="col-sm-6">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group mb-2">
                                            <select type="category_id" id="category_id"
                                                class="form-control @error('category_id') is-invalid @enderror"
                                                name="category_id" required>
                                                <option value="">Select Category....</option>
                                                @foreach ($ordercategory as $key => $value)
                                                    <option value="{{ $value->id }}"
                                                        {{ $value->id == $order->category_id ? 'selected' : '' }}>
                                                        {{ $value->name }}</option>
                                                @endforeach

                                            </select>

                                            @error('category_id')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group mb-2">
                                            <select type="warehouse_id" id="warehouse_id"
                                                class="form-control form-select @error('warehouse_id') is-invalid @enderror"
                                                name="warehouse_id" required>
                                                <option value="">Select Warehouse....</option>
                                                @foreach ($warehouses as $key => $value)
                                                    <option {{ $value->id == $order->warehouse_id ? 'selected' : '' }} value="{{ $value->id }}">{{ $value->name }}</option>
                                                @endforeach

                                            </select>
                                            @error('warehouse_id')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group mb-2">
                                            <select id="customer_id"
                                                class="form-control select2 @error('customer_id') is-invalid @enderror"
                                                name="customer_id" required>
                                                <option value="">Select Customer ...</option>
                                                @foreach ($customers as $key => $value)
                                                    <option  {{ $value->id == $order->customer_id ? 'selected' : '' }}  value="{{ $value->id }}">{{ $value->name }} -
                                                        {{ $value->phone }}</option>
                                                @endforeach

                                            </select>
                                            @error('customer_id')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group mb-2">
                                            <input type="text" id="name"
                                                class="form-control @error('name') is-invalid @enderror"
                                                placeholder="Customer Name" name="name"
                                                value="{{ $shippinginfo->name ?? '' }}" required>
                                            @error('name')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <!-- col-end -->
                                    <div class="col-sm-12">
                                        <div class="form-group mb-2">
                                            <input type="number" id="phone"
                                                class="form-control @error('phone') is-invalid @enderror"
                                                placeholder="Customer Number" name="phone"
                                                value="{{ $shippinginfo->phone ?? '' }}" required>
                                            @error('phone')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <!-- col-end -->
                                    <div class="col-sm-12">
                                        <div class="form-group mb-3">
                                            <input type="address" placeholder="Address" id="address"
                                                class="form-control @error('address') is-invalid @enderror" name="address"
                                                value="{{ $shippinginfo->address ?? '' }}" required>
                                            @error('email')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group mb-3">
                                            <select type="area" id="area"
                                                class="form-control @error('area') is-invalid @enderror" name="area"
                                                required>
                                                <option value="">Select....</option>
                                                @foreach ($shippingcharge as $key => $value)
                                                    <option value="{{ $value->id }}"
                                                        @if ($shippinginfo->area == $value->name) selected @endif>
                                                        {{ $value->name }}</option>
                                                @endforeach

                                            </select>
                                            @error('email')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <!-- col-end -->
                                    <div class="col-sm-12">
                                        <label for="additional_shipping" class="form-label">Additional Shipping *</label>
                                        <div class="form-group mb-2">
                                            <input type="number" id="additional_shipping"
                                                class="form-control @error('additional_shipping') is-invalid @enderror"
                                                placeholder="Additional Shipping" name="additional_shipping"
                                                value="{{ $order->additional_shipping ?? 0 }}" required>
                                            @error('additional_shipping')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <!-- col-end -->
                                    <div class="col-sm-12">
                                        <label for="paid" class="form-label">Paid *</label>
                                        <div class="form-group mb-2">
                                            <input type="number" id="paid"
                                                class="form-control @error('paid') is-invalid @enderror"
                                                placeholder="Cash" name="paid" value="{{ $order->paid }}" required>
                                            @error('paid')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <!-- col-end -->


                                    <!-- col-end -->
                                </div>
                            </div>
                            <!-- cart total -->
                            <div class="col-sm-6">
                                <table class="table table-bordered">
                                    <tbody id="cart_details">

                                        <tr>
                                            <td>Sub Total</td>
                                            <td>{{ $subtotal }}</td>
                                        </tr>
                                        <tr>
                                            <td>Shipping Fee</td>
                                            <td>{{ $shipping + $additional_shipping }}</td>
                                        </tr>
                                        <tr>
                                            <td>Discount</td>
                                            <td>{{ $total_discount }}</td>
                                        </tr>
                                        <tr>
                                            <td>Paid</td>
                                            <td>{{ $paid }}</td>
                                        </tr>
                                        <tr>
                                            <td>Old Due</td>
                                            <td>{{ $old_due }}</td>
                                        </tr>
                                        <tr>
                                            <td>Due Bill</td>
                                            <td>{{ $subtotal + $shipping + $additional_shipping + $old_due - ($total_discount + $paid) }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="col-sm-12">
                                    <div class="form-group mb-3">
                                        <label for="admin_note" class="form-label">Description*</label>
                                        <textarea type="text" class=" form-control @error('admin_note') is-invalid @enderror" name="admin_note"
                                            rows="6" value="{{ $order->admin_note ?? old('admin_note') }}" id="admin_note" required="">{{ $order->admin_note }}</textarea>
                                        @error('admin_note')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <!-- col-end -->
                                <div class="col-sm-12">
                                    <div class="form-group mb-3">
                                        <label for="order_date" class="form-label">Date*</label>
                                        <input name="order_date" value="{{ $order->created_at }}" required
                                            class="form-control flatpickr @error('order_date') is-invalid @enderror">
                                        @error('order_date')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <!-- col-end -->
                            </div>
                            <div>
                                <input type="submit" class="btn btn-success" value="Update Order" />
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
    <script src="{{ asset('public/backEnd') }}/assets/libs/flatpickr/flatpickr.min.js"></script>
    <script src="{{ asset('public/backEnd/') }}/assets/js/pages/form-advanced.init.js"></script>
    <!-- Plugins js -->
    <script src="{{ asset('public/backEnd/') }}/assets/libs//summernote/summernote-lite.min.js"></script>
    <script>
        $(".summernote").summernote({
            placeholder: "Enter Your Text Here",
        });
        $(".flatpickr").flatpickr({
            enableTime: true,
            dateFormat: "Y-m-d H:i:S",
            time_24hr: true,
            defaultDate: new Date(),
        });
    </script>

    <script type="text/javascript">
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>
    <script>
        $('#cart_add').on('change', function(e) {
            var id = $(this).val();
            if (id) {
                $.ajax({
                    cache: 'false',
                    type: "GET",
                    data: {
                        'id': id
                    },
                    url: "{{ route('admin.order.cart_add') }}",
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
                url: "{{ route('admin.order.product_discount') }}",
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
                url: "{{ route('admin.order.cart_clear') }}",
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
                url: "{{ route('admin.order.cart_shipping') }}",
                dataType: "html",
                success: function(cartinfo) {
                    return cart_content() + cart_details();
                }
            });
        });
        $("#paid").change(function() {
            var amount = $(this).val();
            var phone = $("#phone").val();
            $.ajax({
                cache: false,
                type: "GET",
                data: {
                    'amount': amount,
                    'phone': phone,
                },
                url: "{{ route('admin.order.paid') }}",
                dataType: "json",
                success: function(cartinfo) {
                    return cart_content() + cart_details();
                }
            });
        });
        $("#additional_shipping").change(function() {
            var amount = $(this).val();
            $.ajax({
                cache: false,
                type: "GET",
                data: {
                    'amount': amount,
                },
                url: "{{ route('admin.order.additional_shipping') }}",
                dataType: "json",
                success: function(cartinfo) {
                    return cart_content() + cart_details();
                }
            });
        });
    </script>
@endsection
