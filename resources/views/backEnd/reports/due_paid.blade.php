@extends('backEnd.layouts.master')
@section('title','Due Payment Reports')
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
                <h4 class="page-title">Due Payment Reports</h4>
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
                                <label for="user_id" class="form-label">User </label>
                                <select class="form-control select2 @error('user_id') is-invalid @enderror" name="user_id" value="{{ old('user_id') }}" >
                                    <option value="">Select..</option>
                                    @foreach($users as $key=>$value)
                                        <option value="{{$value->id}}" @if(request()->get('user_id') == $value->id) selected @endif>{{$value->name}}</option>
                                    @endforeach
                                </select>
                                @error('user_id')
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
                                <a href="{{route('admin.due_paid')}}" class="btn btn-danger">Reset</a>
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
                                <th>SL</th>
                                <th>Title</th>
                                <th>Date</th>
                                <th>User</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Sender</th>
                                <th>Trx ID</th>
                                <th>Ref</th>
                            </tr>
                        </thead>               
                    
                        <tbody>
                            @foreach($data as $key=>$value)
                            <tr>
                                <td>{{$key+1}}</td>
                                 <td><a href="{{route('admin.payment.edit',$value->id)}}" title="Edit">{{Str::limit($value->title,40)}}</a></td>
                                 <td>{{date('d-m-Y', strtotime($value->updated_at))}}<br> {{date('h:i:s a', strtotime($value->updated_at))}}</td>
                                <td>@if($value->user=='supplier'){{$value->supplier?$value->supplier->name:''}} @else {{$value->customer?$value->customer->name:''}} @endif</td>
                                <td>৳{{$value->amount}}</td>
                                <td>{{$value->method}}</td>
                                <td>{{$value->sender}}</td>
                                <td>{{$value->trx_id}}</td>
                                <td>{{$value->ref_id}}</td>
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