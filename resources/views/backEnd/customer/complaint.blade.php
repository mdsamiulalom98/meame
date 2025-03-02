@extends('backEnd.layouts.master')
@section('title', 'Post Details')

@section('content')
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <a href="{{ route('customers.complaint') }}" class="btn btn-primary rounded-pill">View</a>
                    </div>
                    <h4 class="page-title text-capitalize"> Complint Details</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-8">
                <div class="card">
                    <div class="card-body">
                        <table class="table table-responsive">
                            <tbody>
                                <tr>
                                    <td>Description</td>
                                    <td>{{ $show_data->description }}</td>
                                </tr>
                                <tr>
                                    <td>Name</td>
                                    <td>{{ $show_data->customer->name ?? '' }}</td>
                                </tr>

                                <tr>
                                    <td>Phone</td>
                                    <td>{{ $show_data->customer->phone ?? '' }}</td>
                                </tr>
                                <tr>
                                    <td>Email</td>
                                    <td>{{ $show_data->customer->phone ?? '' }}</td>
                                </tr>

                                <tr>
                                    <td class="d-flex gap-1">
                                        @foreach ($show_data->images as $image)
                                            <img src="{{ asset($image->image) }}" class="edit-image" alt="">
                                        @endforeach
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                    </div> <!-- end card body-->
                </div> <!-- end card -->
            </div><!-- end col-->

        </div>
    </div>
@endsection
