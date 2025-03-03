@extends('backEnd.layouts.master')
@section('title','Purchase Reports')
@section('content')
@section('css')
<link href="{{asset('public/backEnd')}}/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('public/backEnd/')}}/assets/libs/flatpickr/flatpickr.min.css" rel="stylesheet" type="text/css" />
<style>
    p{
        margin:0;
    }
   @page { 
        margin: 50px 0px 0px 0px;
    }
   @media print {
    td{
        font-size: 18px;
    }
    p{
        margin:0;
    }
    title {
        font-size: 25px;
    }
    header,footer,.no-print,.left-side-menu,.navbar-custom {
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
                <div class="page-title-right">
                    <a href="{{route('purchase.create')}}" class="btn btn-danger rounded-pill"><i class="fe-shopping-cart"></i> Create Purchase</a>
                </div>
                <h4 class="page-title">Purchase Reports</h4>
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
                                <label for="category_id" class="form-label">Category </label>
                                <select class="form-control select2 @error('category_id') is-invalid @enderror" name="category_id" value="{{ old('category_id') }}" >
                                    <option value="">Select..</option>
                                    @foreach($pur_category as $key=>$value)
                                        <option value="{{$value->id}}" @if(request()->get('category_id') == $value->id) selected @endif>{{$value->name}}</option>
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
                                <label for="supplier_id" class="form-label">Supplier </label>
                                <select class="form-control select2 @error('supplier_id') is-invalid @enderror" name="supplier_id" value="{{ old('supplier_id') }}" >
                                    <option value="">Select..</option>
                                    @foreach($suppliers as $key=>$value)
                                        <option value="{{$value->id}}" @if(request()->get('supplier_id') == $value->id) selected @endif>{{$value->name}}</option>
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
                                <input type="date" value="{{request()->get('start_date')}}"  class="form-control flatdate" name="start_date">
                            </div>
                        </div>
                        <!--col-sm-3--> 
                        <div class="col-sm-3">
                            <div class="form-group">
                               <label for="end_date" class="form-label">End Date</label>
                                <input type="date" value="{{request()->get('end_date')}}" class="form-control flatdate" name="end_date">
                            </div>
                        </div>
                        <!--col-sm-3-->
                        <div class="col-sm-12">
                            <div class="form-group mb-3">
                                <button class="btn btn-primary">Submit</button>
                                <a href="{{route('admin.order_report')}}" class="btn btn-danger">Reset</a>
                            </div>
                        </div>
                        <!-- col end -->
                    </div>  
                </form>
                <div class="row mb-3">
                    <div class="col-sm-6 no-print">
                         {{$data->links('pagination::bootstrap-4')}}
                    </div>
                    <div class="col-sm-6">
                        <div class="export-print text-end">
                            <button onclick="printFunction()"class="no-print btn btn-success"><i class="fa fa-print"></i> Print</button>
                            <button id="export-excel-button" class="no-print btn btn-info"><i class="fas fa-file-export"></i> Export</button>
                        </div>
                    </div>
                </div>
                <div id="content-to-export">
                    <div class="table-responsive">
                        <table class="table nowrap w-100">
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Date</th>
                                <th>Supplier</th>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Discount</th>
                                <th>Quantity</th>
                                <th>Total</th>
                            </tr>
                        </thead>               
                    
                        <tbody>
                            @foreach($data as $key=>$value)
                            <tr>
                                <td><a href="{{ route('purchase.edit', $value->purchase?$value->purchase->invoice_id:'') }}">{{$value->purchase?$value->purchase->invoice_id:''}}</a></td>
                                 <td>{{date('d-m-Y', strtotime($value->updated_at))}}<br> {{date('h:i:s a', strtotime($value->updated_at))}}</td>
                                <td>{{$value->purchase?$value->purchase->supplier->name:''}}</td>
                                <td>{{$value->product?$value->product->name : ''}}</td>
                                <td>৳{{$value->purchase_price}}</td>
                                <td>৳{{$value->product_discount?$value->product_discount:0}}</td>
                                <td>{{$value->quantity}}</td>
                                <td>{{$value->purchase_price*$value->quantity}}</td>
                            </tr>
                            @endforeach
                         </tbody>
                         <tfoot>
                            <tr >
                                <td colspan="8">
                                    <p class="text-center"><strong>Total Purchase : {{$total_item}} pcs</strong></p>
                                    <p class="text-center"><strong>Total Purchase : {{$total_purchase}} Tk</strong></p>
                                </td>
                            </tr>
                        </tfoot>
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
<script src="{{asset('public/backEnd/')}}/assets/libs/select2/js/select2.min.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/js/pages/form-advanced.init.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/libs/flatpickr/flatpickr.min.js"></script>
<script src="https://cdn.rawgit.com/rainabba/jquery-table2excel/1.1.0/dist/jquery.table2excel.min.js"></script>

<script type="text/javascript">
    $(document).ready(function () {
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
