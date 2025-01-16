@extends('backEnd.layouts.master')
@section('title', 'Order Create')
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
    @php
        $subtotal = Cart::instance('pos_shopping')->subtotal();
        $subtotal = str_replace(',', '', $subtotal);
        $subtotal = str_replace('.00', '', $subtotal);
        $shipping = Session::get('pos_shipping');
        $total_discount = Session::get('pos_discount') + Session::get('product_discount');
        $paid = Session::get('cpaid') ?? 0;
        $old_due = Session::get('old_due') ?? 0;
        $additional_shipping = Session::get('additional_shipping') ?? 0;
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
                    <h4 class="page-title">Order Create</h4>
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
                                    <select class="form-control select2 @error('category_id') is-invalid @enderror"
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
                        <form action="{{ route('admin.order.store') }}" method="POST" class="row pos_form"
                            data-parsley-validate="" enctype="multipart/form-data">
                            @csrf
                            <div class="col-sm-12">
                                <table class="table table-bordered table-responsive-sm">
                                    <thead>
                                        <tr></tr>
                                        <tr>
                                            <th style="width: 10%;">Image</th>
                                            <th style="width: 25%;">Name</th>
                                            <th style="width: 15%;">Quantity</th>
                                            <th style="width: 15%;">Sell Price</th>
                                            <th style="width: 15%;">Discount</th>
                                            <th style="width: 15%;">Sub Total</th>
                                            <th style="width: 15%;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="cartTable">
                                        @include('backEnd.order.cart_content')
                                    </tbody>
                                </table>
                            </div>
                            <!-- custome address -->
                            <div class="col-sm-6">
                                <div class="form-check mb-2">
                                    <label class="form-check-label" for="guest_customer">
                                        Guest Customer
                                    </label>
                                    <input class="form-check-input" type="checkbox" name="guest_customer" value="1"
                                        id="guest_customer">
                                </div>
                                <div class="row new_customer">

                                    <div class="col-sm-12">
                                        <div class="form-group mb-2">
                                            <select type="category_id" id="category_id"
                                                class="form-control @error('category_id') is-invalid @enderror"
                                                name="category_id" required>
                                                <option value="">Select Category....</option>
                                                @foreach ($ordercategory as $key => $value)
                                                    <option value="{{ $value->id }}">{{ $value->name }}</option>
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
                                            <input type="text" id="name"
                                                class="form-control @error('name') is-invalid @enderror"
                                                placeholder="Customer Name" name="name" value="" />
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
                                                placeholder="Customer Number" name="phone" value="" />
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
                                                value="" />
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
                                                class="form-control form-select @error('area') is-invalid @enderror"
                                                name="area" required>
                                                <option value="">Select....</option>
                                                @foreach ($shippingcharge as $key => $value)
                                                    <option value="{{ $value->id }}">{{ $value->name }}</option>
                                                @endforeach

                                            </select>
                                            @error('area')
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
                                                value="{{ $additional_shipping > 0 ? $additional_shipping : '' }}" required>
                                            @error('additional_shipping')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <!-- col-end -->
                                    <div class="col-sm-12">
                                        <label for="paid" class="form-label">Cash *</label>
                                        <div class="form-group mb-2">
                                            <input type="number" id="paid"
                                                class="form-control @error('paid') is-invalid @enderror"
                                                placeholder="Cash" name="paid" value="{{ $paid > 0 ? $paid : '' }}" required>
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
                                        @if ($old_due > 0)
                                            <tr>
                                                <td>Old Due</td>
                                                <td>{{ $old_due }}</td>
                                            </tr>
                                        @endif
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
                                        <textarea type="text" class=" form-control @error('admin_note') is-invalid @enderror" name="admin_note" rows="6" value="{{ old('admin_note') }}"  id="admin_note" required=""></textarea>
                                        @error('admin_note')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <!-- col-end -->
                            </div>
                            <div>
                                <input type="submit" class="btn btn-success" value="Order Submit" />
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
            $(".select2").select2();
        });
    </script>
    <script>
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
                },
            });
        });
        $(document).ready(function() {
            $('.search_click').focus();
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
    <script>
        $(document).ready(function() {
            $('#guest_customer').change(function() {
                if ($(this).is(':checked')) {
                    $('.new_customer').hide();
                } else {
                    $('.new_customer').show();
                }
            });
        });
    </script>
    <script>
        // category to sub
        $("#category_id").on("change", function() {
            var ajaxId = $(this).val();
            if (ajaxId) {
                $.ajax({
                    type: "GET",
                    url: "{{ url('ajax-product-subcategory') }}?category_id=" + ajaxId,
                    success: function(res) {
                        if (res) {
                            $("#subcategory_id").empty();
                            $("#subcategory_id").append('<option value="0">Choose...</option>');
                            $.each(res, function(key, value) {
                                $("#subcategory_id").append('<option value="' + key + '">' +
                                    value + "</option>");
                            });
                        } else {
                            $("#subcategory_id").empty();
                        }
                    },
                });
                return product_query();
            } else {
                $("#subcategory_id").empty();
            }
        });

        // subcategory to childcategory
        $("#subcategory_id").on("change", function() {
            var ajaxId = $(this).val();
            if (ajaxId) {
                $.ajax({
                    type: "GET",
                    url: "{{ url('ajax-product-childcategory') }}?subcategory_id=" + ajaxId,
                    success: function(res) {
                        if (res) {
                            $("#childcategory_id").empty();
                            $("#childcategory_id").append('<option value="0">Choose...</option>');
                            $.each(res, function(key, value) {
                                $("#childcategory_id").append('<option value="' + key + '">' +
                                    value + "</option>");
                            });
                        } else {
                            $("#childcategory_id").empty();
                        }
                    },
                });
                return product_query();
            } else {
                $("#childcategory_id").empty();
            }
        });
        // subcategory to childcategory
        $("#childcategory_id").on("change", function() {
            var ajaxId = $(this).val();
            if (ajaxId) {
                return product_query();
            } else {
                $("#childcategory_id").empty();
            }
        });


        $("#product_id").on("change", function(e) {
            var id = $(this).val();
            console.log(id);
            if (id) {
                $.ajax({
                    cache: "false",
                    type: "GET",
                    data: {
                        id: id
                    },
                    url: "{{ route('admin.order.cart_add') }}",
                    dataType: "json",
                    success: function(cartinfo) {
                        return cart_content() + cart_details() + search_clear();
                        $('#category_id').val('');
                        $('#subcategory_id').val('');
                        $('#childcategory_id').val('');
                    },
                });
            }
        });

        function product_query() {
            var category = $("#category_id").val();
            var subcategory = $("#subcategory_id").val();
            var childcategory = $("#childcategory_id").val();
            $.ajax({
                type: "GET",
                data: {
                    category: category,
                    subcategory: subcategory,
                    childcategory: childcategory,
                },
                url: "{{ route('admin.livesearch') }}",
                success: function(res) {
                    if (res) {
                        $("#product_id").empty();
                        $("#product_id").append('<option value="0">Choose...</option>');
                        $.each(res, function(key, value) {
                            $("#product_id").append('<option value="' + key + '">' +
                                value + "</option>");
                        });
                    } else {
                        $("#product_id").empty();
                    }
                },
            });
        }
    </script>
    <script>
        $(".search_click").on("keyup change", function() {
            var category = $("#category_id").val();
            var subcategory = $("#subcategory_id").val();
            var childcategory = $("#childcategory_id").val();
            var keyword = $(this).val();
            $.ajax({
                type: "GET",
                data: {
                    keyword: keyword,
                    category: category,
                    subcategory: subcategory,
                    childcategory: childcategory,
                },
                url: "{{ route('admin.livesearch') }}",
                success: function(products) {
                    if (products) {
                        $(".search_result").html(products);
                    } else {
                        $(".search_result").empty();
                    }
                },
            });
        });
    </script>
@endsection
