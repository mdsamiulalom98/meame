@extends('backEnd.layouts.master')
@section('title', 'Warehouse Profile')
@section('css')
    <link href="{{ asset('public/backEnd') }}/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <a href="{{ route('warehouses.index') }}" class="btn btn-primary rounded-pill">Warehouse List</a>
                        <a data-bs-toggle="modal" data-bs-target="#stockChange" class="btn btn-success">Stock Transfer</a>
                    </div>
                    <h4 class="page-title">Warehouse Profile</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-lg-4 col-xl-4">
                <div class="card text-center">
                    <div class="card-body">
                        <img src="{{ asset('public/backEnd/assets/images/meame-demo.png') }}"
                            class="rounded-circle avatar-lg img-thumbnail" alt="profile-image">

                        <h4 class="mb-0">{{ $profile->name }}</h4>

                        <a href="tel:{{ $profile->phone }}"
                            class="btn btn-success btn-xs waves-effect my-2 waves-light">{{ $profile->phone }}</a>

                        <div class="text-start mt-3">
                            <h4 class="font-13 text-uppercase">About Warehouse :</h4>
                            <table class="table table-bordered">
                                <tbody>
                                    <tr class="mb-2">
                                        <td>Full Name </td>
                                        <td class="ms-2">{{ $profile->name }}</td>
                                    </tr>

                                    <tr class="mb-2">
                                        <td>Mobile </td>
                                        <td class="ms-2">{{ $profile->phone }}</td>
                                    </tr>

                                    <tr class="mb-2">
                                        <td>Address </td>
                                        <td class="ms-2">{{ $profile->address }}</td>
                                    </tr>


                                    <tr class="mb-2">
                                        <td>Total Products</td>
                                        <td class="ms-2">{{ $profile->products ?? 0 }}</td>
                                    </tr>
                                    <tr class="mb-2">
                                        <td>Total Stock</td>
                                        <td class="ms-2">{{ $profile->stock ?? 0 }}</td>
                                    </tr>
                                    <tr class="mb-2">
                                        <td>Total Sold</td>
                                        <td class="ms-2">{{ $profile->sold ?? 0 }}</td>
                                    </tr>
                                    <tr class="mb-2">
                                        <td>Total Purchase</td>
                                        <td class="ms-2">{{ $profile->purchase ?? 0 }}</td>
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
                                <a href="#instock" data-bs-toggle="tab" aria-expanded="false" class="nav-link active">
                                    In Stock
                                </a>
                            </li>
                            <li class="nav-item mt-2">
                                <a href="#history" data-bs-toggle="tab" aria-expanded="false" class="nav-link">
                                    History
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane show active" id="instock">
                                <table id="datatable-buttons" class="table table-striped dt-responsive nowrap w-100">
                                    <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Product</th>
                                            <th>Stock</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($instocks as $key => $value)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $value->product->name ?? '' }}</td>
                                                <td>{{ $value->stock }}</td>
                                                <td>{{ date('d-m-Y', strtotime($value->updated_at)) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="tab-pane" id="history">
                                <table id="datatable-buttons" class="table table-striped dt-responsive nowrap w-100">
                                    <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Product</th>
                                            <th>Stock</th>
                                            <th>Sold</th>
                                            <th>Purchase</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($totalstocks as $key => $value)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $value->product->name ?? '' }}</td>
                                                <td>{{ $value->stock }}</td>
                                                <td>{{ $value->sold }}</td>
                                                <td>{{ $value->sold + $value->stock }}</td>
                                                <td>{{ date('d-m-Y', strtotime($value->updated_at)) }}</td>
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



    <div class="modal fade" id="stockChange" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Stock Transfer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('warehouses.stock_change') }}" id="order_assign" method="POST">
                    @csrf
                    <input name="from" value="{{ $profile->id }}" type="hidden" />
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="product_id" class="form-label">Products *</label>
                            <select name="product_id" id="product_id" class="form-control form-select">
                                <option value="">Select..</option>
                                @foreach ($instocks as $key => $value)
                                    <option value="{{ $value->product_id }}">{{ $value->product->name ?? '' }}({{ $value->stock }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="to" class="form-label">Warehouse To *</label>
                            <select name="to" id="to" class="form-control form-select">
                                <option value="">Select..</option>
                                @foreach ($allwarehouses as $key => $value)
                                    <option value="{{ $value->id }}">{{ $value->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="stock" class="form-label">Stock *</label>
                            <input type="number" class="form-control @error('stock') is-invalid @enderror" name="stock"
                                value="{{ old('stock') }}" id="stock" required="">
                            @error('stock')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Assign User End-->
@endsection


@section('script')
    <script src="{{ asset('public/backEnd/') }}/assets/libs/parsleyjs/parsley.min.js"></script>
    <script src="{{ asset('public/backEnd/') }}/assets/js/pages/form-validation.init.js"></script>
    <script src="{{ asset('public/backEnd/') }}/assets/libs/select2/js/select2.min.js"></script>
    <script src="{{ asset('public/backEnd/') }}/assets/js/pages/form-advanced.init.js"></script>
@endsection
