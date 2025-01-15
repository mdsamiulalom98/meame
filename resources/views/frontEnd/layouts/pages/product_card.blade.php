<div class="product_item_inner">
    @if($value->old_price)
    <div class="sale-badge">
        <div class="sale-badge-inner">
            <div class="sale-badge-box">
                <span class="sale-badge-text">
                    <p>@php $discount=(((($value->old_price)-($value->new_price))*100) / ($value->old_price)) @endphp {{ number_format($discount, 0) }}%</p>
                    Off
                </span>
            </div>
        </div>
    </div>
    @endif
     @if($value->pro_unit)
     <div class="pro_unit">
         {{$value->pro_unit}}
     </div>
     @endif
    <div class="pro_img">
        <a href="{{ route('product', $value->slug) }}">
            <img src="{{ asset($value->image ? $value->image->image : '') }}"
              width="182" height="192"  alt="{{ $value->name }}" />
        </a>
    </div>
    <div class="pro_des">
        <div class="pro_name">
            <a
                href="{{ route('product', $value->slug) }}">{{ Str::limit($value->name, 80) }}</a>
        </div>
        <div class="pro_price">
            <p>
                @if($value->old_price) <del>৳ {{ $value->old_price }}</del> @endif
                ৳ {{ $value->new_price }}
               
            </p>
        </div>
    </div>
</div>
@if ($value->stock > 0)
    <div class="pro_btn">
    @if ($value->prosizes_count > 0 || $value->prosizes_count > 0)
        <div class="cart_btn">
            <a href="{{ route('product', $value->slug) }}" class="addcartbutton">কার্ট</a>
        </div>
        <div class="cart_btn order_button">
            <a href="{{ route('product', $value->slug) }}"
                class="addcartbutton">অর্ডার</a>
        </div>
    @else
        <div class="cart_btn">
            <a data-id="{{ $value->id }}" class="addcartbutton">কার্ট</a>
        </div>
        <form action="{{ route('cart.store') }}" method="POST">
            @csrf
            <input type="hidden" name="id" value="{{ $value->id }}" />
            <input type="hidden" name="qty" value="1" />
            <input type="hidden" name="order_now" value="1" />
            <button type="submit">অর্ডার</button>
        </form>
    @endif
    </div>
   @elseif($value->stock < 1 || $value->whatsapp == 1)
    <div class="stock_out">
        <a href="https://api.whatsapp.com/send?phone={{$contact->whatsapp2}}" target="_blank"><i class="fa-brands fa-whatsapp"></i> Negotiated item</a>
    </div>
@endif