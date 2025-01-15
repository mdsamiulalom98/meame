@extends('backEnd.layouts.master')
@section('title', 'Purchase List')
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
                <h4 class="page-title">Purchase List</h4>
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
                                <div class="form-group">
                                    <label for="keyword" class="form-label">Keyword</label>
                                    <input type="text" value="{{ request()->get('keyword') }}" class="form-control"
                                        name="keyword">
                                </div>
                            </div>
                            <!--col-sm-3-->
                            <div class="col-sm-3">
                                <div class="form-group mb-3">
                                    <label for="supplier_id" class="form-label">Supplier </label>
                                    <select class="form-control select2 @error('supplier_id') is-invalid @enderror"
                                        name="supplier_id" value="{{ old('supplier_id') }}">
                                        <option value="">Select..</option>
                                        @foreach ($suppliers as $key => $value)
                                            <option value="{{ $value->id }}"
                                                @if (request()->get('supplier_id') == $value->id) selected @endif>{{ $value->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('supplier_id')
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
                                    <a href="{{ route('purchase.index') }}" class="btn btn-danger">Reset</a>
                                </div>
                            </div>
                            <!-- col end -->
                        </div>
                    </form>
                    <div class="row mb-3">
                        <div class="col-sm-6 no-print">
                            {{ $data->links('pagination::bootstrap-4') }}
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
                    <div id="content-to-export">
                        <div class="table-responsive">
                            <table id="datatable-buttons" class="table table-striped dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>Action</th>
                                        <th>Invoice</th>
                                        <th>Date</th>
                                        <th>Supplier</th>
                                        <th>Amount</th>
                                        <th>Discount</th>
                                        <th>Paid</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $key => $value)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <div class="button-list custom-btn-list">
                                                    <a href="{{ route('purchase.invoice', $value->invoice_id) }}"
                                                        title="Invoice"><i class="fe-eye"></i></a>
                                                    <a href="{{ route('purchase.edit', $value->invoice_id) }}" title="Edit"><i
                                                            class="fe-edit"></i></a>
                                                </div>
                                            </td>
                                            <td>{{ $value->invoice_id }}</td>
                                            <td>{{ date('d-m-Y', strtotime($value->updated_at)) }}<br>
                                                {{ date('h:i:s a', strtotime($value->updated_at)) }}</td>
                                            <td>{{ $value->supplier ? $value->supplier->name : '' }}</td>
                                            <td>{{ $value->amount }} Tk</td>
                                            <td>{{ $value->discount }} Tk</td>
                                            <td>{{ $value->paid }} Tk</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

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

@endsection
