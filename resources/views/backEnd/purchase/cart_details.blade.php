@php
    $subtotal = Cart::instance('purchase')->subtotal();
    $subtotal = str_replace(',','',$subtotal);
    $subtotal = str_replace('.00', '',$subtotal);
    $discount = Session::get('purchase_discount')+Session::get('product_discount');
    $paid = Session::get('paid')?Session::get('paid'):0;
    $shipping = Session::get('shipping')?Session::get('shipping'):0;
@endphp
<tr>
    <td>Sub Total</td>
    <td>{{$subtotal}} Tk</td>
</tr>
<tr>
    <td>Discount</td>
    <td>{{$discount}} Tk</td>
</tr>
<tr>
    <td>Total</td>
    <td>{{($subtotal + $shipping)-$discount}} Tk</td>
</tr>
<tr>
    <td>Paid</td>
    <td>{{$paid}} Tk</td>
</tr>
<tr>
    <td>Due Payment</td>
    <td>{{(($subtotal + $shipping)- $discount)-$paid}} Tk</td>
</tr>