@extends('backEnd.layouts.master')
@section('title', 'Payment Entry')
@section('css')
    <link href="{{ asset('public/backEnd') }}/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('public/backEnd') }}/assets/css/switchery.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('public/backEnd') }}/assets/libs/flatpickr/flatpickr.min.css" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <a href="{{ route('admin.payment.index') }}" class="btn btn-primary rounded-pill">Manage</a>
                    </div>
                    <h4 class="page-title">Payment Entry</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.payment.store') }}" method="POST" class=row data-parsley-validate=""
                            enctype="multipart/form-data">
                            @csrf

                            <input type="hidden" name="user" id="user_type" value="{{ request()->get('user') }}">
                            <div class="col-sm-12">
                                <div class="form-group mb-2">
                                    <label for="title" class="form-label">Payment Title *</label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                                        name="title" value="{{ old('title') }}" id="title" required="">
                                    @error('title')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <!-- col-end -->
                            <div class="col-sm-12">
                                <div class="form-group mb-2">
                                    <label for="user_id"
                                        class="form-label">{{ request()->get('user') == 'supplier' ? 'Suppliers' : 'Customers' }}
                                        *</label>
                                    <select type="text" id="user_id"
                                        class="form-control @error('user_id') is-invalid @enderror select2" name="user_id"
                                        required>
                                        <option value="">Select..</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }} - {{ $user->phone }}
                                                (Due: {{ $user->due }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                            </div>
                            <!-- col-end -->
                            <div class="col-sm-6">
                                <div class="form-group mb-2">
                                    <label for="amount" class="form-label">Amount *</label>
                                    <input type="number" class="form-control @error('amount') is-invalid @enderror"
                                        name="amount" value="{{ old('amount') }}" id="amount" required="">
                                    @error('amount')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <!-- col-end -->
                            <div class="col-sm-6">
                                <div class="form-group mb-2">
                                    <label for="due" class="form-label">Due *</label>
                                    <input type="number" class="form-control @error('due') is-invalid @enderror"
                                        name="due" value="{{ old('due') }}" id="due" disabled>
                                    <input type="hidden" id="dueValue" value="" disabled>
                                    @error('due')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <!-- col-end -->

                            <div class="col-sm-12">
                                <div class="form-group mb-2">
                                    <label for="method" class="form-label">Method *</label>
                                    <select type="text" id="method"
                                        class="form-control @error('method') is-invalid @enderror select2" name="method"
                                        required>
                                        <option value="">Select Method..</option>
                                        <option value="bKash">bKash</option>
                                        <option value="Nagad">Nagad</option>
                                        <option value="Rocket">Rocket</option>
                                        <option value="Bank">Bank</option>
                                        <option value="Cash">Cash</option>
                                    </select>
                                    @error('method')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                            </div>
                            <!-- col-end -->
                            <div class="col-sm-12">
                                <div class="form-group mb-2">
                                    <label for="sender" class="form-label">Sender </label>
                                    <input type="text" class="form-control @error('sender') is-invalid @enderror"
                                        name="sender" value="{{ old('sender') }}" id="sender">
                                    @error('sender')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <!-- col-end -->
                            <div class="col-sm-12">
                                <div class="form-group mb-2">
                                    <label for="trx_id" class="form-label">Transaction ID </label>
                                    <input type="text" class="form-control @error('trx_id') is-invalid @enderror"
                                        name="trx_id" value="{{ old('trx_id') }}" id="trx_id">
                                    @error('trx_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <!-- col-end -->
                            <div class="col-sm-12">
                                <div class="form-group mb-2">
                                    <label for="carrier" class="form-label">Carrier </label>
                                    <input type="text" class="form-control @error('carrier') is-invalid @enderror"
                                        name="carrier" value="{{ old('carrier') }}" id="carrier">
                                    @error('carrier')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <!-- col-end -->
                            <div class="col-sm-12">
                                <div class="form-group mb-2">
                                    <label for="ref_id" class="form-label">Refference </label>
                                    <input type="text" class="form-control @error('ref_id') is-invalid @enderror"
                                        name="ref_id" value="{{ old('ref_id') }}" id="ref_id">
                                    @error('ref_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <!-- col-end -->
                            <div class="col-sm-12">
                                <div class="form-group mb-2">
                                    <label for="description" class="form-label">Description </label>
                                    <textarea type="text" class="form-control @error('description') is-invalid @enderror" name="description"
                                        value="{{ old('description') }}" id="description"></textarea>
                                    @error('description')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <!-- col-end -->
                            <div class="col-sm-12">
                                <div class="form-group mb-3">
                                    <label for="created_at" class="form-label">Date*</label>
                                    <input name="created_at" value="{{ old('created_at') }}" required
                                        class="form-control flatpickr @error('created_at') is-invalid @enderror">
                                    @error('created_at')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <!-- col-end -->
                            <div>
                                <input type="submit" class="btn btn-success" value="Submit">
                            </div>

                        </form>

                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div> <!-- end col-->
        </div>
    </div>
@endsection


@section('script')
    <script src="{{ asset('public/backEnd/') }}/assets/libs/parsleyjs/parsley.min.js"></script>
    <script src="{{ asset('public/backEnd/') }}/assets/js/pages/form-validation.init.js"></script>
    <script src="{{ asset('public/backEnd/') }}/assets/libs/select2/js/select2.min.js"></script>
    <script src="{{ asset('public/backEnd/') }}/assets/js/pages/form-advanced.init.js"></script>
    <script src="{{ asset('public/backEnd/') }}/assets/js/switchery.min.js"></script>
    <script src="{{ asset('public/backEnd') }}/assets/libs/flatpickr/flatpickr.min.js"></script>
    <script>
        $(document).ready(function() {
            var elem = document.querySelector('.js-switch');
            var init = new Switchery(elem);
        });
    </script>

    <script type="text/javascript">
        $(document).ready(function() {
            $('.select2').select2();
            $(".flatpickr").flatpickr({
                enableTime: true,
                dateFormat: "Y-m-d H:i:S",
                time_24hr: true,
                defaultDate: new Date(),
            });
        });
    </script>
    <script>
        $("#user_id").change(function() {
            var id = $(this).val();
            var user = $('#user_type').val();
            $.ajax({
                cache: false,
                type: "GET",
                data: {
                    'id': id,
                    'user': user
                },
                url: "{{ route('admin.user.select') }}",
                dataType: "json",
                success: function(user) {
                    $('#due').val(user.due);
                    $('#dueValue').val(user.due);
                }
            });
        });

        function calculateTotal() {
            var due = parseFloat($('#dueValue').val()) || 0;
            var anotherValue = parseFloat($('#amount').val()) || 0; // Assuming another input with id="amount"
            var total = due - anotherValue;
            $('#due').val(total); // Assuming another input with id="total"
        }

        $('#amount').on('input', function() {
            calculateTotal();
        });
    </script>
@endsection
