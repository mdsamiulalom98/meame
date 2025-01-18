@extends('backEnd.layouts.master')
@section('title', 'Stock Report')
@section('content')
@section('css')
    <link href="{{ asset('public/backEnd') }}/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('public/backEnd/') }}/assets/libs/flatpickr/flatpickr.min.css" rel="stylesheet" type="text/css" />
    <style>
        p {
            margin: 0;
        }

        @page {
            margin: 50px 0px 0px 0px;
        }

        @media print {
            td {
                font-size: 18px;
            }

            p {
                margin: 0;
            }

            title {
                font-size: 25px;
            }

            header,
            footer,
            .no-print,
            .left-side-menu,
            .navbar-custom {
                display: none !important;
            }
        }
    </style>
@endsection
<div class="container-fluid">

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Stock Report</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form class="no-print">
                        <div class="row">
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="keyword" class="form-label">Keyword</label>
                                    <input type="text" value="{{ request()->get('keyword') }}" class="form-control"
                                        name="keyword">
                                </div>
                            </div>
                            <!--col-sm-3-->
                            <div class="col-sm-2">
                                <div class="form-group mb-3">
                                    <label for="category_id" class="form-label">Categories </label>
                                    <select class="form-control select2 @error('category_id') is-invalid @enderror"
                                        name="category_id" id="category_id" value="{{ old('category_id') }}">
                                        <option value="">Select..</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                @if (request()->get('category_id') == $category->id) selected @endif>{{ $category->name }}
                                            </option>
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
                            <div class="col-sm-2">
                                <div class="form-group mb-3">
                                    <label for="subcategory_id" class="form-label">SubCategories</label>
                                    <select class="form-control form-select @error('subcategory_id') is-invalid @enderror"
                                        id="subcategory_id"  name="subcategory_id" data-placeholder="Choose ...">
                                        <optgroup>
                                            <option value="">Select..</option>
                                            @foreach ($subcategories as $key => $value)
                                            <option value="{{ $value->id }}" @if (request()->get('subcategory_id') == $value->id) selected @endif>{{ $value->subcategoryName }}</option>
                                            @endforeach
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
                            <div class="col-sm-2">
                                <div class="form-group mb-3">
                                    <label for="childcategory_id" class="form-label">Child Categories</label>
                                    <select class="form-control form-select @error('childcategory_id') is-invalid @enderror"
                                        id="childcategory_id" name="childcategory_id" data-placeholder="Choose ...">
                                        <optgroup>
                                            <option value="">Select..</option>
                                            @foreach ($childcategories as $key => $value)
                                            <option value="{{ $value->id }}" @if (request()->get('childcategory_id') == $value->id) selected @endif>{{ $value->childcategoryName }}</option>
                                            @endforeach
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
                            <!-- col end -->
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="start_date" class="form-label">Start Date</label>
                                    <input type="date" value="{{ request()->get('start_date') }}"
                                        class="form-control flatdate" name="start_date">
                                </div>
                            </div>
                            <!--col-sm-3-->
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="end_date" class="form-label">End Date</label>
                                    <input type="date" value="{{ request()->get('end_date') }}"
                                        class="form-control flatdate" name="end_date">
                                </div>
                            </div>
                            <!--col-sm-3-->
                            <div class="col-sm-12">
                                <div class="form-group mb-3">
                                    <button class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                            <!-- col end -->
                        </div>
                    </form>
                    <div class="row mb-3">
                        <div class="col-sm-6 no-print">
                            {{ $products->links('pagination::bootstrap-4') }}
                        </div>
                        <div class="col-sm-6">
                            <div class="export-print text-end">
                                <button onclick="printFunction()"class="no-print btn btn-success"><i
                                        class="fa fa-print"></i> Print</button>
                                <button id="export-excel-button" class="no-print btn btn-info"><i
                                        class="fas fa-file-export"></i> Export</button>
                            </div>
                        </div>
                    </div>
                    <div id="content-to-export" class="table-responsive">
                        <table class="table nowrap w-100">
                            <thead>
                                <tr>
                                    <th style="width:5%">SL</th>
                                    <th style="width:30%">Name</th>
                                    <th style="width:10%">Price</th>
                                    <th style="width:10%">Stock</th>
                                    <th style="width:10%">Total</th>
                                </tr>
                            </thead>

                            <tbody>
                                @php
                                    $stock = 0;
                                    $total = 0;
                                @endphp
                                @foreach ($products as $key => $value)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><a href="{{ route('purchase.create') }}"
                                                title="Update">{{ $value->name }}</a></td>
                                        <td>{{ $value->new_price }}</td>
                                        <td>{{ $value->stock }}</td>
                                        <td>{{ $value->stock * $value->new_price }}</td>
                                    </tr>
                                    @php
                                        $stock += $value->stock;
                                        $total += $value->stock * $value->new_price;
                                    @endphp
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Total</strong></td>
                                    <td><strong>{{ $stock }} Pcs</strong></td>
                                    <td><strong>{{ $total }} Tk</strong></td>
                                </tr>
                                <tr>
                                    <td colspan="6" class="text-center">
                                        <h5><strong>Total Purchase = {{ $total_purchase }}</strong></h5>
                                        <h5><strong>Total Stock = {{ $total_stock }} Pcs</strong></h5>
                                        <h5><strong>Total Price = {{ $total_price }} Tk</strong></h5>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->
    </div>
</div>
@endsection
@section('script')
<script src="{{ asset('public/backEnd/') }}/assets/libs/select2/js/select2.min.js"></script>
<script src="{{ asset('public/backEnd/') }}/assets/js/pages/form-advanced.init.js"></script>
<script src="{{ asset('public/backEnd/') }}/assets/libs/flatpickr/flatpickr.min.js"></script>
<script src="https://cdn.rawgit.com/rainabba/jquery-table2excel/1.1.0/dist/jquery.table2excel.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('.select2').select2();
        flatpickr(".flatdate", {});
    });
</script>
<script>
    function printFunction() {
        window.print();
    }
</script>
<script>
    $(document).ready(function() {
        $('#export-excel-button').on('click', function() {
            var contentToExport = $('#content-to-export').html();
            var tempElement = $('<div>');
            tempElement.html(contentToExport);
            tempElement.find('.table').table2excel({
                exclude: ".no-export",
                name: "Order Report"
            });
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
        } else {
            $("#childcategory_id").empty();
        }
    });
</script>
@endsection
