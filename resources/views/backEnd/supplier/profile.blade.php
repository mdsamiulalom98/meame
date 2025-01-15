@extends('backEnd.layouts.master')
@section('title','Supplier Profile')
@section('css')
<link href="{{asset('public/backEnd')}}/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
@endsection
@section('content')
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{route('supplier.index')}}" class="btn btn-primary rounded-pill">Supplier List</a>
                </div>
                <h4 class="page-title">Supplier Profile</h4>
            </div>
        </div>
    </div>  
    <!-- end page title -->

    <div class="row">
        <div class="col-lg-4 col-xl-4">
            <div class="card text-center">
                <div class="card-body">
                    <img src="{{asset($profile->image)}}" class="rounded-circle avatar-lg img-thumbnail"
                    alt="profile-image">

                    <h4 class="mb-0">{{$profile->name}}</h4>

                    <a href="tel:{{$profile->phone}}" class="btn btn-success btn-xs waves-effect my-2 waves-light">{{$profile->phone}}</a>

                    <div class="text-start mt-3">
                        <h4 class="font-13 text-uppercase">About Supplier :</h4>
                        <table class="table table-bordered">
                            <tbody>
                            <tr class="mb-2">
                                <td>Full Name </td>
                                <td class="ms-2">{{$profile->name}}</td>
                            </tr>

                            <tr class="mb-2">
                                <td>Mobile </td>
                                <td class="ms-2">{{$profile->phone}}</td>
                            </tr>

                            <tr class="mb-2">
                                <td>Address </td> 
                                <td class="ms-2">{{$profile->address}}</td>
                            </tr>

                            <tr class="mb-2">
                                <td>Note </td> 
                                <td class="ms-2">{!!$profile->note!!}</td>
                            </tr>
                            <tr class="mb-2">
                                <td>Total Transaction</td>
                                <td class="ms-2">৳ {{$profile->amount}}</td>
                            </tr>
                            <tr class="mb-2">
                                <td>Paid Amount</td>
                                <td class="ms-2">৳ {{$profile->paid}}</td>
                            </tr>
                            <tr class="mb-2">
                                <td>Due Amount</td>
                                <td class="ms-2">৳ {{$profile->due}}</td>
                            </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div> <!-- end card -->

        </div> <!-- end col-->

        <div class="col-lg-8 col-xl-8">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-pills nav-fill navtab-bg">
                        
                        <li class="nav-item mt-2">
                            <a href="#purchase" data-bs-toggle="tab" aria-expanded="false" class="nav-link active">
                               Purchase
                            </a>
                        </li>
                        <li class="nav-item mt-2">
                            <a href="#payment" data-bs-toggle="tab" aria-expanded="false" class="nav-link">
                               Payment
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane show active" id="purchase">
                            <table id="datatable-buttons" class="table table-striped dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>Invoice</th>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Discount</th>
                                        <th>Qty</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($purchase as $key=>$value)
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td>{{$value->invoice_id}}</td>
                                        <td>{{date('d-m-Y', strtotime($value->created_at))}}</td>
                                        <td>৳ {{$value->amount}}</td>
                                        <td>৳ {{$value->discount}}</td>
                                        <td>{{$value->quantity}}</td>
                                        <td>৳ {{($value->amount)}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane" id="payment">
                            <table id="datatable-buttons" class="table table-striped dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>Name</th>
                                        <th>Date</th>
                                        <th>Method</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transaction as $key=>$value)
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td>{{$value->title}}</td>
                                        <td>{{date('d-m-Y', strtotime($value->created_at))}}</td>
                                        <td>{{$value->method}}</td>
                                        <td>৳ {{$value->amount}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- end  item-->
                    </div> <!-- end tab-content -->
                </div>
            </div> <!-- end card-->

        </div> <!-- end col -->
    </div>
    <!-- end row-->

</div> <!-- container -->

</div> <!-- content -->
@endsection


@section('script')
<script src="{{asset('public/backEnd/')}}/assets/libs/parsleyjs/parsley.min.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/js/pages/form-validation.init.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/libs/select2/js/select2.min.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/js/pages/form-advanced.init.js"></script>
@endsection