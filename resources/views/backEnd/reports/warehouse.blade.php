@extends('backEnd.layouts.master')
@section('title', 'Warehouse Stock Report')
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
                <h4 class="page-title">Warehouse Stock Report</h4>
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

                            <div class="col-sm-3">
                                <div class="form-group mb-3">
                                    <label for="product_id" class="form-label">Product </label>
                                    <select class="form-control select2 @error('product_id') is-invalid @enderror"
                                        name="product_id" id="product_id" value="{{ old('product_id') }}">
                                        <option value="">Select..</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}"
                                                @if (request()->get('product_id') == $product->id) selected @endif>{{ $product->name }}
                                            </option>
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
                            <div class="col-sm-3">
                                <div class="form-group mb-3">
                                    <label for="warehouse_id" class="form-label">Warehouse </label>
                                    <select class="form-control select2 @error('warehouse_id') is-invalid @enderror"
                                        name="warehouse_id" id="warehouse_id" value="{{ old('warehouse_id') }}">
                                        <option value="">Select..</option>
                                        @foreach ($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}"
                                                @if (request()->get('warehouse_id') == $warehouse->id) selected @endif>{{ $warehouse->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('warehouse_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <!-- col end -->

                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="start_date" class="form-label">Start Date</label>
                                    <input type="date" value="{{ request()->get('start_date') }}"
                                        class="form-control flatdate" name="start_date">
                                </div>
                            </div>
                            <!--col-sm-3-->
                            <div class="col-sm-3">
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
                            {{ $stocks->links('pagination::bootstrap-4') }}
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
                                    <th style="width:20%">Name</th>
                                    <th style="width:20%">Warehouse</th>
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
                                @foreach ($stocks as $key => $value)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $value->product->name ?? '' }}</td>
                                        <td>{{ $value->warehouse->name ?? '' }}</td>
                                        <td>{{ $value->product->new_price ?? '' }}</td>
                                        <td>{{ $value->stock }}</td>
                                        <td>{{ $value->stock * $value->product->new_price ?? 0 }}</td>
                                    </tr>
                                    @php
                                        $stock += $value->stock;
                                        $total += $value->stock * $value->product->new_price ?? 0;
                                    @endphp
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-end"><strong>Total</strong></td>
                                    <td><strong>{{ $stock }} Pcs</strong></td>
                                    <td><strong>{{ $total }} Tk</strong></td>
                                </tr>
                                <tr>
                                    <td colspan="6" class="text-center">

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
