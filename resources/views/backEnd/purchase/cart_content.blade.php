@php
    $product_discount = 0;
@endphp
@foreach ($cartinfo as $key => $value)
    <tr>
        <td><img height="30" src="{{ asset($value->options->image) }}"></td>
        <td>{{ $value->name }}</td>
        <td>
            <div class="qty-cart vcart-qty">
                <div class="quantity">
                    <button class="minus cart_decrement" value="{{ $value->qty }}"
                        data-id="{{ $value->rowId }}">-</button>
                    <input type="number" value="{{ $value->qty }}" class="product_quantity" min="1"
                        data-id="{{ $value->rowId }}" />
                    <button class="plus cart_increment" value="{{ $value->qty }}"
                        data-id="{{ $value->rowId }}">+</button>
                </div>
            </div>
        </td>
        <td>{{ $value->price }} Tk</td>
        <td class="discount">
            <input type="number" class="product_discount" value="{{ $value->options->product_discount }}"
                placeholder="0.00" data-id="{{ $value->rowId }}">
        </td>
        <td>{{ ($value->price - $value->options->product_discount) * $value->qty }} Tk</td>
        <td>{{ $value->options->warehouse_stock ?? 0 }}</td>
        <td>
            <button type="button" class="btn btn-danger btn-xs cart_remove" data-id="{{ $value->rowId }}">
                <i class="fa fa-times"></i>
            </button>
        </td>
    </tr>

    @php
        $product_discount += $value->options->product_discount * $value->qty;
        Session::put('product_discount', $product_discount);
    @endphp
@endforeach

<script>
    function cart_content() {
        $.ajax({
            type: "GET",
            url: "{{ route('purchase.cart_content') }}",
            dataType: "html",
            success: function(cartinfo) {
                $('#cartTable').html(cartinfo)
            }
        });
    }

    function cart_details() {
        $.ajax({
            type: "GET",
            url: "{{ route('purchase.cart_details') }}",
            dataType: "html",
            success: function(cartinfo) {
                $('#cart_details').html(cartinfo)
            }
        });
    }
    $(".cart_increment").click(function(e) {
        e.preventDefault();
        var id = $(this).data("id");
        var qty = $(this).val();
        if (id) {
            $.ajax({
                cache: false,
                data: {
                    'id': id,
                    'qty': qty
                },
                type: "GET",
                url: "{{ route('purchase.cart_increment') }}",
                dataType: "json",
                success: function(cartinfo) {
                    return cart_content() + cart_details();
                }
            });
        }
    });
    $(".cart_decrement").click(function(e) {
        e.preventDefault();
        var id = $(this).data("id");
        var qty = $(this).val();
        if (id) {
            $.ajax({
                cache: false,
                type: "GET",
                data: {
                    'id': id,
                    'qty': qty
                },
                url: "{{ route('purchase.cart_decrement') }}",
                dataType: "json",
                success: function(cartinfo) {
                    return cart_content() + cart_details();
                }
            });
        }
    });
    $(".cart_remove").click(function(e) {
        e.preventDefault();
        var id = $(this).data("id");
        if (id) {
            $.ajax({
                cache: false,
                type: "GET",
                data: {
                    'id': id
                },
                url: "{{ route('purchase.cart_remove') }}",
                dataType: "json",
                success: function(cartinfo) {
                    return cart_content() + cart_details();
                }
            });
        }
    });

    $("#paid").change(function() {
        var amount = $(this).val();
        $.ajax({
            cache: false,
            type: "GET",
            data: {
                'amount': amount
            },
            url: "{{ route('purchase.paid') }}",
            dataType: "json",
            success: function(cartinfo) {
                return cart_content() + cart_details();
            }
        });
    });
    $(".product_discount").change(function() {
        var id = $(this).data("id");
        var discount = $(this).val();
        $.ajax({
            cache: false,
            type: "GET",
            data: {
                'id': id,
                'discount': discount
            },
            url: "{{ route('purchase.product_discount') }}",
            dataType: "json",
            success: function(cartinfo) {
                return cart_content() + cart_details();
            }
        });
    });

    $(".product_quantity").change(function() {
        var id = $(this).data("id");
        var quantity = $(this).val();
        $.ajax({
            cache: false,
            type: "GET",
            data: {
                id: id,
                quantity: quantity
            },
            url: "{{ route('purchase.product_quantity') }}",
            dataType: "json",
            success: function(cartinfo) {
                return cart_content() + cart_details();
            },
        });
    });
    $(".cartclear").click(function(e) {
        $.ajax({
            cache: false,
            type: "GET",
            url: "{{ route('purchase.cart_clear') }}",
            dataType: "json",
            success: function(cartinfo) {
                return cart_content() + cart_details();
            }
        });
    }); // pshippingfee from total
    $("#area").on("change", function() {
        var id = $(this).val();
        $.ajax({
            type: "GET",
            data: {
                id: id
            },
            url: "{{ route('purchase.cart_shipping') }}",
            dataType: "html",
            success: function(cartinfo) {
                return cart_content() + cart_details();
            }
        });
    });
</script>
