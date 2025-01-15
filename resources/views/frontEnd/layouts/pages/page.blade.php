@extends('frontEnd.layouts.master')
@section('title','Page')
@section('content')
<section class="createpage-section">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-content">
                    <div class="page-title mb-2">
                        <h5>{{$page->title}}</h5>
                    </div>
                    <div class="page-description">
                        {!! $page->description !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
